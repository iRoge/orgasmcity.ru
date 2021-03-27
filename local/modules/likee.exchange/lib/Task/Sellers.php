<?php

namespace Likee\Exchange\Task;

use Bitrix\Catalog\StoreTable;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;
use Likee\Site\Helpers\HL;

/**
 * Класс для работы с импортом заказов.
 *
 * @package Likee\Exchange\Task
 */
class Sellers extends Task
{
    /**
     * @var array Словарь
     */
    public $dictionary = [];
    /**
     * @var string xml для импорта
     */
    public $xml = 'sellers.xml';
    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    protected $log = [];
    /**
     * @var array|false
     */
    private $sellers;
    private $stores;
    private $dbSellers;
    private $sellerClass;

    public function import()
    {
        $this->log("=============");
        $this->log("Импорт продавцов");
        $this->log("=============");

        $this->log("Получаем список продавцов из файла");
        $this->read();

        if (($count = count($this->sellers)) == 0) {
            $this->log("Файл пуст");

            throw new ExchangeException(
                'Файл пуст',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        $this->log("Прочитано " . $count . " продавцов");

        $this->log("Получаем данные из БД");
        $this->load();

        $this->log("Записываем в БД");
        $this->apply();

        $this->log("Начало архивации файла");
        $this->arhivate();
        $this->log("Конец архивации файла");

        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно' . ($this->log ? "\n\n" . implode("\n", $this->log) : ''),
        ]);

        return $this->result;
    }

    private function read()
    {
        $this->reader->setExpandedNodes([
            'sellers',
        ]);

        $this->reader->on('seller', function ($reader, $xml) {
            $seller = Helper::xml2array(simplexml_load_string($xml));
            if (!empty($seller['id']) && !empty($seller['personal_code']) && !empty($seller['store_code'])) {
                $this->sellers[$seller['id']] = $seller;
            } else {
                $this->log('У сотрудника ' . $seller['id'] . ' недостаточно данных');
            }
        });

        $this->reader->read();
    }

    private function apply()
    {
        $counts = [
            'update' => 0,
            'add' => 0,
        ];

        foreach ($this->sellers as $seller) {
            if ($dbSeller = $this->dbSellers[$seller['id']]) {
                $data = $this->getSellerData($seller, $dbSeller);

                $this->sellerClass::update($dbSeller['ID'], $data);

                $counts['update'] += 1;
            } else {
                $data = $this->getSellerData($seller);

                $this->sellerClass::add($data);

                $counts['add'] += 1;
            }
        }

        $this->log('Добавлено ' . $counts['add'] . ' - Обновлено ' . $counts['update']);
    }

    private function load()
    {
        $this->log("Получаем магазины");
        $this->stores = $this->getStoreList();
        $this->log('Загружено ' . count($this->stores) . ' магазинов');

        $this->log("Получаем сотрудников");
        $this->dbSellers = $this->getDBSellers();
        $this->log('Загружено ' . count($this->dbSellers) . ' сотрудников');
    }

    private function getStoreList()
    {
        $resStore = StoreTable::getList(['order' => ['TITLE' => 'ASC']]);
        $arStores = [];

        while ($store = $resStore->fetch()) {
            $arStore = [
                'ID' => $store['ID'],
                'TITLE' => $store['TITLE'],
                'ADDRESS' => $store['ADDRESS'],
                'XML_ID' => $store['XML_ID'],
            ];
            $arStores[$store['XML_ID']] = $arStore;
        }

        return $arStores;
    }

    private function getDBSellers()
    {
        $obSellers = HL::getEntityClassByHLName('Sellers');
        $arSellers = [];

        if ($obSellers && is_object($obSellers)) {
            $this->sellerClass = $obSellers->getDataClass();

            $res = $this->sellerClass::getList();

            while ($seller = $res->fetch()) {
                $arSellers[$seller['UF_SELLER_ID']] = $seller;
            }
        }

        return $arSellers;
    }

    private function getSellerData($seller, $dbSeller = [])
    {
        $dbSeller['UF_STORE_ID'] = $this->stores[$seller['store_code']]['ID'];
        $dbSeller['UF_SELLER_ID'] = $seller['id'];
        $dbSeller['UF_SELLER_SELF_CODE'] = $seller['personal_code'];
        $dbSeller['UF_FULL_NAME'] = '';

        if (!empty($seller['surname'])) {
            $dbSeller['UF_SURNAME'] = $seller['surname'];
            $dbSeller['UF_FULL_NAME'] .= $seller['surname'];
        }

        if (!empty($seller['name'])) {
            $dbSeller['UF_NAME'] = $seller['name'];
            $dbSeller['UF_FULL_NAME'] .= !empty($dbSeller['UF_FULL_NAME']) ? ' ' : '';
            $dbSeller['UF_FULL_NAME'] .= $seller['name'];
        }

        if (!empty($seller['patronymic'])) {
            $dbSeller['UF_PATRONYMIC'] = $seller['patronymic'];
            $dbSeller['UF_FULL_NAME'] .= !empty($dbSeller['UF_FULL_NAME']) ? ' ' : '';
            $dbSeller['UF_FULL_NAME'] .= $seller['patronymic'];
        }

        $dbSeller['UF_FIRED'] = $seller['fired'] == 'true' ? '1' : '';

        return $dbSeller;
    }
}
