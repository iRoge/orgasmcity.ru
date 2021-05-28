<?php
/**
 * Project: respect
 * Date: 04.02.19
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Likee\Exchange\Task;

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Likee\Exchange\Config;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Result;
use Likee\Exchange\Tables\BranchTable;
use Likee\Exchange\Tables\BranchProductPricesTable;
use Likee\Exchange\Task;
use Likee\Exchange\XMLReader;

class Branch extends Task
{
    protected $branches = [];
    protected $elements = [];
    protected $log = [];

    
    public function __construct()
    {
        \Bitrix\Main\Loader::includeModule("iblock");
        $connection = Application::getConnection();
        $connection->query('SET wait_timeout=14400;');
        $this->tracker = $connection->getTracker();
        $this->query_num = $this->tracker->getCounter();
        $this->log_time = microtime(true);

        $this->result = new Result();
        $this->config = Config::get();
    }

    public function import()
    {
        $result = new Result();

        $this->log("=============");
        $this->log("Цены филиалов");
        $this->log("=============");

        $this->log("Загрузка данных из БД...");
        $this->load();
        $this->log("Запись в БД...");
        $this->apply();
        $this->log("Сброс кеша цен");
        $this->clearCache();
        $this->log("Конец\n");

        $result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно' . ($this->log ? "\n\n".implode("\n", $this->log) : ''),
        ]);

        return $result;
    }

    private function load()
    {
        if (!$this->config['IBLOCK_ID']) {
            throw new ExchangeException(
                'В настройках не указан инфоблок товаров',
                ExchangeException::$ERR_NO_PRODUCTS_IBLOCK
            );
        }

        $this->log("Загрузка филиалов");
        $rsBranches = BranchTable::getList();
        while ($arBranch = $rsBranches->fetch()) {
            $this->branches[$arBranch['xml_id']] = $arBranch['id'];
        }
        $this->log("Загружено ".count($this->branches)." филиалов");

        //загрузка товаров
        /*$rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID']
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_CML2_LINK.XML_ID'
            ]
        );

        while ($arElement = $rsElements->Fetch()) {
            var_export($arElement);
            exit;
            $this->offers[$arElement['PROPERTY_CML2_LINK_XML_ID']][] = $arElement['ID'];
        }*/

        $this->log("Загрузка товаров");
        //загрузка товаров
        $rsElements = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['IBLOCK_ID']
            ],
            false,
            false,
            [
                'ID',
                'XML_ID'
            ]
        );

        while ($arElement = $rsElements->Fetch()) {
            $this->elements[$arElement['XML_ID']] = $arElement['ID'];
        }
        $this->log("Загружено ".count($this->elements)." товаров");
    }

    private function apply()
    {
        $files = [];

        $arConfig = Config::get();
        $sPath = $arConfig['PATH'] . 'tempPath/';

        foreach (new \DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . $sPath) as $fileInfo) {
            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }
            if (preg_match('/^prices_\d+\.xml$/i', $fileInfo->getFilename())) {
                $files[] = $fileInfo->getPathname();
                $this->log[] = 'Обработан файл '.$fileInfo->getFilename();
            }
        }

        if (count($files) == 0) {
            $this->log("Файлов для обработки нет\n");
            throw new ExchangeException(
                'Файлов для обработки нет',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        foreach ($files as $filePath) {
            $branchId = false;
            $branchPrices = [];

            $reader = new XMLReader($filePath);
            $reader->setExpandedNodes([
                'prices',
                'offers',
            ]);

            $reader->on('Filial', function ($reader, $xml) use (&$branchId, &$branchPrices) {
                $filial = Helper::xml2array(simplexml_load_string($xml));
                $this->log("Читаем из файла филиал '".$filial['name']."'");

                $arFilial = [
                    'xml_id' => $filial['id'],
                    'name' => $filial['name']
                ];

                if (!empty($this->branches[$filial['id']])) {
                    $rs = BranchTable::update($this->branches[$filial['id']], $arFilial);
                } else {
                    $rs = BranchTable::add($arFilial);
                    if ($rs->isSuccess()) {
                        $this->branches[$filial['id']] = $rs->getId();
                    }
                }

                if (!$rs->isSuccess()) {
                    throw new ExchangeException(
                        reset($rs->getErrorMessages()),
                        ExchangeException::$ERR_CREATE_UPDATE
                    );
                }

                $branchId = (int) $this->branches[$filial['id']];

                $rsBranchPrices = BranchProductPricesTable::getList([
                    'filter' => [
                        'branch_id' => $branchId
                    ]
                ]);
                while ($arBranchPrice = $rsBranchPrices->fetch()) {
                    $branchPrices[$arBranchPrice['product_id']] = $arBranchPrice['id'];
                }
            });

            $reader->on('offer', function ($reader, $xml) use (&$branchId, &$branchPrices) {
                $offer = Helper::xml2array(simplexml_load_string($xml));
                $offerProductId = $this->elements[$offer['id']] ?: false;
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log("Читаем из файла товар с ID ".$offer['id']);
                }

                if (!$offerProductId) {
                    $this->log[] = "Товар $offer[id] не найден";
                    return;
                }

                $arOffer = [
                    'price_segment_id' => $offer['PriceSegmentID'],
                    'max_disc_bp' => $offer['MaxDiscBP'],
                ];
                foreach ($offer['prices']['price'] as $price) {
                    $arOffer[$price['id']] = $price['value'];
                }

                if (!empty($branchPrices[$offerProductId])) {
                    $rs = BranchProductPricesTable::update($branchPrices[$offerProductId], $arOffer);
                } else {
                    $arOffer['branch_id'] = $branchId;
                    $arOffer['product_id'] = $offerProductId;
                    $rs = BranchProductPricesTable::add($arOffer);
                }

                if (!$rs->isSuccess()) {
                    throw new ExchangeException(
                        reset($rs->getErrorMessages()),
                        ExchangeException::$ERR_CREATE_UPDATE
                    );
                }
            });

            $reader->read();
            
            unset($reader, $branchId, $branchPrices);
            // Архивируем и удаляем исходники
            $file = explode('/', $filePath);
            $file = array_pop($file);
            $this->log('Архивируем ' . $file);
            $this->arhivate($file);
        }
    }
}
