<?php

namespace Likee\Exchange\Task;

use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Catalog\StoreTable;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Sale\Internals\BasketTable;
use Bitrix\Sale\Internals\StoreProductTable;
use Bitrix\Sale;
use CIBlockElement;
use CModule;
use Likee\Exchange\Config;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Result;
use Likee\Exchange\Task;
use Likee\Exchange\XMLReader;

/**
 * Класс для работы с импортом заказов.
 *
 * @package Likee\Exchange\Task
 */
class Order20 extends Task
{
    /**
     * @var array Словарь
     */
    public $dictionary = [];
    /**
     * @var string xml для импорта
     */
    public $xml = 'order.xml';
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
    private $files;
    private string $tempPath;

    public function __construct()
    {
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
        $this->log("=============");
        $this->log("Импорт заказов");
        $this->log("=============");

        $this->log("Получаем список файлов заказов");
        $this->getFiles();

        if (($count = count($this->files)) == 0) {
            $this->log("Файлов для обработки нет\n");

            throw new ExchangeException(
                'Файлов для обработки нет',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        $this->log("Количество файлов заказов: " . $count);
        $this->log("Обработка файлов");
        $this->apply();
        // следующий код не относится к задаче
        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно' . ($this->log ? "\n\n" . implode("\n", $this->log) : ''),
        ]);

        return $this->result;
    }

    private function getFiles()
    {
        $this->tempPath = $_SERVER['DOCUMENT_ROOT'] . Option::get('likee.exchange', 'PATH', '/upload/1c_catalog/') . 'tempPath/';

        $this->files = glob($this->tempPath . '*_order_*.xml', GLOB_NOSORT | GLOB_ERR);
    }

    private function apply()
    {
        foreach ($this->files as $fileOrder) {
            $matches = [];
            $fileName = str_replace($this->tempPath, '', $fileOrder);

            if (!preg_match('/^(\d+)_(\d+)_order_(\d+)\.xml$/i', $fileName, $matches)) {
                $this->log('Файл ' . $fileName . ' не соответствует шаблону');
            }

            $fileOrderData['DATE'] = $matches[1];
            $fileOrderData['TIME'] = $matches[2];
            $fileOrderData['ORDER_ID'] = $matches[3];
            $this->log('Обрабатываем файл ' . $fileName);

            $reader = new XMLReader($fileOrder);

            $reader->setExpandedNodes([
                'order',
                'products',
            ]);

            $importOrder = [];

            $reader->on('id', function ($reader, $xml) use (&$importOrder, $fileOrderData) {
                $arId = Helper::xml2array(simplexml_load_string($xml));

                if ($arId[0] != $fileOrderData['ORDER_ID']) {
                    $this->log("ID заказа в файле (" . $arId[0] . ") не совпадает с ID заказ в названии файла (" . $fileOrderData['ORDER_ID'] . ")");
                    return;
                }

                $this->log('Читаем из файла заказ ' . $arId[0]);
                $importOrder['ID'] = $arId[0];
            });

            $importPayment = [];

            $reader->on('pay', function ($reader, $xml) use (&$importPayment, &$importOrder) {
                $importPayment = Helper::xml2array(simplexml_load_string($xml));
            });


            $reader->on('product', function ($reader, $xml) use (&$importOrder) {
                if (empty($importOrder['ID'])) {
                    return;
                }

                $arProduct = Helper::xml2array(simplexml_load_string($xml));

                if (empty($arProduct)) {
                    $this->log('Данные по товарам в заказе отсутствуют');
                    return;
                }

                $arProduct['product_xml_id'] = $arProduct['id'];

                unset($arProduct['id']);

                $size = is_array($arProduct['size']) ? '' : $arProduct['size'];

                $xmlId = $arProduct['product_xml_id'] . '-' . $size;
                $importOrder['PRODUCTS'][$xmlId] = $arProduct;
                $importOrder['PRODUCTS'][$xmlId]['xml_id'] = $xmlId;
            });

            $reader->read();

            if (!empty($importOrder['PRODUCTS'])) {
                $this->log('В заказе из 1С найдено ' . count($importOrder['PRODUCTS']) . ' товаров.');
                $xmlIds = array_keys($importOrder['PRODUCTS']);
                $arSelect = array("ID", 'XML_ID');

                $rsElements = CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                        'XML_ID' => $xmlIds,
                    ],
                    false,
                    false,
                    $arSelect,
                );

                while ($item = $rsElements->GetNext()) {
                    $importOrder['AR_PROD'][$item['ID']] = $importOrder['PRODUCTS'][$item['XML_ID']];
                    $importOrder['AR_PROD'][$item['ID']]['id'] = $item['ID'];

                    unset($importOrder['PRODUCTS'][$item['XML_ID']]);
                }

                if (count($importOrder['PRODUCTS']) > 0) {
                    $this->log('В базе не найдены товары: ' . implode(', ', array_keys($importOrder['PRODUCTS'])) . '.');
                }

                $order = Sale\Order::load($importOrder['ID']);

                if (is_object($order)) {
                    $this->log('Заказ ' . $order->getId() . ' найден в базе');
                    $basket = $order->getBasket();
                    $this->log('Обрабатывается корзина заказа ' . $order->getId() . '.');

                    foreach ($basket as $basketItem) {
                        if (empty($importOrder['AR_PROD'][$basketItem->getProductId()])) {
                            $this->log('Товар ' . $basketItem->getProductId() . ' не найден в заказе из 1С. Удаляем');
                            $basketItem->delete();

                            unset($importOrder['AR_PROD'][$basketItem->getProductId()]);
                        } elseif ($importOrder['AR_PROD'][$basketItem->getProductId()]['count'] <= 0) {
                            $this->log('Количество товара ' . $basketItem->getProductId() . ' = 0. Удаляем');
                            $basketItem->delete();

                            unset($importOrder['AR_PROD'][$basketItem->getProductId()]);
                        } else {
                            $this->log('Товар ' . $basketItem->getProductId() . ' найден в заказе из 1С.');
                            $currentPrice = $this->roundPrice($basketItem->getField('PRICE'));
                            $importPrice = $this->roundPrice($importOrder['AR_PROD'][$basketItem->getProductId()]['total_price']);
                            $this->log('Проверяем цену товара ' . $basketItem->getProductId() . '. Текущая цена: ' . $currentPrice . '. Цена из 1С: ' . $importPrice . '.');

                            if ($currentPrice != $importPrice) {
                                $this->log('Цена товара ' . $basketItem->getProductId() . ' изменилась. Устанавливаем новую цену.');

                                $basketItem->setFields([
                                    'PRICE' => $importPrice,
                                    'CUSTOM_PRICE' => 'Y',
                                ]);
                            } else {
                                $this->log('Цена товара ' . $basketItem->getProductId() . ' не изменилась.');
                            }

                            $basketPropertyCollection = $basketItem->getPropertyCollection();
                            $arProps = $basketPropertyCollection->getPropertyValues();

                            if (!empty($importOrder['AR_PROD'][$basketItem->getProductId()]['received_money'])) {
                                $factPrice = $this->roundPrice($importOrder['AR_PROD'][$basketItem->getProductId()]['received_money']);
                                $this->log('Записываем значение фактически полученной стоимости товара ' . $basketItem->getProductId() . ' -> ' . $factPrice . '.');

                                $arProps['RECIEVED_MONEY'] = array(
                                    'NAME' => 'Фактически получено',
                                    'CODE' => 'RECIEVED_MONEY',
                                    'VALUE' => $factPrice,
                                    'SORT' => 4,
                                );
                            }

                            $basketPropertyCollection->setProperty($arProps);

                            unset($importOrder['AR_PROD'][$basketItem->getProductId()]);
                        }
                    }

                    $this->log('Надо добавить в заказ ' . count($importOrder['AR_PROD']) . ' товаров.');

                    foreach ($importOrder['AR_PROD'] as $key => $item) {
                        $importPrice = $this->roundPrice($importOrder['AR_PROD'][$key]['total_price']);
                        $this->log('Добавляем в заказ товар ' . $key . ' с ценой ' . $importPrice . '.');
                        $basketItem = $basket->createItem('catalog', $key);
                        $arProps = $this->getOfferProps($key);

                        if (!empty($importOrder['AR_PROD'][$key]['received_money'])) {
                            $factPrice = $this->roundPrice($importOrder['AR_PROD'][$key]['received_money']);
                            $this->log('Записываем значение фактически полученной стоимости товара ' . $key . ' -> ' . $factPrice . '.');
                            $arProps['PROPS']['RECIEVED_MONEY'] = array(
                                'NAME' => 'Фактически получено',
                                'CODE' => 'RECIEVED_MONEY',
                                'VALUE' => $factPrice,
                                'SORT' => 4,
                            );
                        }

                        $arImportFields = [
                            'QUANTITY' => $item['count'],
                            'CURRENCY' => CurrencyManager::getBaseCurrency(),
                            'LID' => SITE_ID,
                            'PRICE' => $importPrice,
                            'CUSTOM_PRICE' => 'Y',
                            'PRODUCT_PRICE_ID' => $arProps["PRODUCT_ID"],
                            'PRODUCT_PROVIDER_CLASS' => '\Bitrix\Catalog\Product\CatalogProvider',
                        ];

                        $basketItem->setFields($arImportFields);

                        if (!empty($arProps['PROPS'])) {
                            $prop = $basketItem->getPropertyCollection();
                            $prop->redefine($arProps['PROPS']);
                        }
                    }

                    $basket->refresh();
                    $paymentCollection = $order->getPaymentCollection();

                    foreach ($paymentCollection as $payment) {
                        if ($payment->getField('PAY_SYSTEM_ID') == $importPayment['id']) {
                            $this->log('ID оплаты не изменился: ' . $payment->getField('PAY_SYSTEM_ID') . '.');
                        } else {
                            $this->log('ID оплаты изменился: ' . $payment->getField('PAY_SYSTEM_ID') . ' -> ' . $importPayment['id'] . '.');
                            $r = $payment->delete();

                            if (!$r->isSuccess()) {
                                $this->log('Не удалось удалить оплату - ' . $r->getErrorMessages() . '.');
                            }

                            $paySystemService = Sale\PaySystem\Manager::getObjectById($importPayment['id']);
                            $payment = $paymentCollection->createItem($paySystemService);
                            $payment->setField('SUM', $order->getPrice());
                        }
                    }

                    $order->doFinalAction(true);
                    $order->save();
                } else {
                    $this->log('В базе нет заказа с ID ' . $importOrder['ID']);
                }

                unset($reader, $importOrder, $xmlIds, $basket, $order, $importPayment);
            }

            $file = explode('/', $fileOrder);
            $file = array_pop($file);
            $this->log('Архивируем файл заказа ' . $file);
            $this->arhivate($file);
        }
    }

    private function getOfferProps($offerId)
    {
        // получаем свойства из ТП
        $arOffer = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $offerId,
                "IBLOCK_ID" => IBLOCK_OFFERS,
                "ACTIVE" => "Y",
            ),
            false,
            array(
                "nTopCount" => 1,
            ),
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_CML2_LINK",
                "PROPERTY_SIZE",
            )
        )->Fetch();

        if (!$arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            return false;
        }

        // получаем свойства из товара
        $arProd = CIBlockElement::GetList(
            array(),
            array(
                "ID" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "IBLOCK_ID" => IBLOCK_CATALOG,
                "ACTIVE" => "Y",
            ),
            false,
            array(
                "nTopCount" => 1,
            ),
            array(
                "ID",
                "IBLOCK_ID",
                "PROPERTY_ARTICLE",
                "PROPERTY_COLOR",
                "PROPERTY_KOD_1S",
            )
        )->Fetch();

        $arProps = [];

        if ($arProd["PROPERTY_ARTICLE_VALUE"]) {
            $arProps["PROPS"]["ARTICLE"] = array(
                "CODE" => "ARTICLE",
                "NAME" => "Артикул",
                "VALUE" => $arProd["PROPERTY_ARTICLE_VALUE"],
                "SORT" => 1,
            );
        }

        if ($arProd["PROPERTY_COLOR_VALUE"]) {
            CModule::IncludeModule('highloadblock');

            $hlblock = HLBT::getList(array('filter' => array('=NAME' => 'Color')))->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();

            $color = $entity_data_class::getList(array(
                'filter' => array('UF_XML_ID' => $arProd["PROPERTY_COLOR_VALUE"]),
                'select' => array('UF_NAME'),
            ))->fetch()['UF_NAME'];

            $arProps["PROPS"]["COLOR"] = array(
                "CODE" => "COLOR",
                "NAME" => "Цвет",
                "VALUE" => $color,
                "SORT" => 2,
            );
        }

        if ($arProd["PROPERTY_KOD_1S_VALUE"]) {
            $arProps["PROPS"]["KOD_1S"] = array(
                "CODE" => "KOD_1S",
                "NAME" => "Код 1С",
                "VALUE" => $arProd["PROPERTY_KOD_1S_VALUE"],
                "SORT" => 3,
            );
        }

        // добавляем свойства из ТП, если оно есть
        if ($arOffer["PROPERTY_SIZE_VALUE"]) {
            $arProps["PROPS"]["SIZE"] = array(
                "CODE" => "SIZE",
                "NAME" => "Размер",
                "VALUE" => $arOffer["PROPERTY_SIZE_VALUE"],
                "SORT" => count($arProps) + 1,
            );
        }

        if ($arOffer["PROPERTY_CML2_LINK_VALUE"]) {
            $arProps["PROPS"]["PROD_ID"] = array(
                "CODE" => "PRODUCT_ID",
                "NAME" => "ID продукта",
                "VALUE" => $arOffer["PROPERTY_CML2_LINK_VALUE"],
                "SORT" => count($arProps) + 1,
            );
        }

        $arProps["PRODUCT_ID"] = $arOffer["PROPERTY_CML2_LINK_VALUE"];

        return $arProps;
    }

    private function roundPrice($price)
    {
        return round($price, 0, PHP_ROUND_HALF_UP);
    }
}
