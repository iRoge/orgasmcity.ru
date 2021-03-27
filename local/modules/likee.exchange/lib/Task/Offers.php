<?php
/**
 * Project: respect
 * Date: 27.08.2019
 *
 * @author: qsoft.ru
 */

namespace Likee\Exchange\Task;

use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Iblock\PropertyIndex\Manager;
use Bitrix\Iblock\ElementTable;
use Bitrix\Main\Application;
use CIBlock;
use CIBlockElement;
use CIBlockProperty;
use CIBlockPropertyEnum;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;
use Qsoft\Helpers\TextHelper;
use Qsoft\Helpers\EventHelper;

/**
 * Класс для работы с импортом предложений.
 *
 * @package Likee\Exchange\Task
 */
class Offers extends Task
{
    /**
     * @public string xml для импорта
     */
    public $xml = 'offers.xml';
    /**
     * @protected array Свойства из файла + дополнительные
     */
    protected $properties = array();
    /**
     * @private array Свойства из файла, которые имеют тип "справочник"
     */
    private $reference = array();
    /**
     * @private array Свойства из файла, которые имеют тип "список"
     */
    private $list = array();
    /**
     * @private array Свойства из БД
     */
    private $propertiesDB = array();
    /**
     * @private array Предложения из файла
     */
    private $fileOffers = array();
    /**
     * @private array Товары из БД
     */
    private $products = array();
    /**
     * @private array Предложения из БД
     */
    private $offers = array();
    /**
     * @private bool Только изменения
     */
    private $only_changes = true;
    /**
     * @private array Дополнительные сообщения результата импорта
     */
    private $log = [];

    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {
        if (!$this->config['IBLOCK_ID']) {
            throw new ExchangeException(
                'В настройках не указан инфоблок товаров',
                ExchangeException::$ERR_NO_PRODUCTS_IBLOCK
            );
        }

        if (!$this->config['OFFERS_IBLOCK_ID']) {
            throw new ExchangeException(
                'В настройках не указан инфоблок предложений',
                ExchangeException::$ERR_NO_OFFERS_IBLOCK
            );
        }

        $this->log("===========");
        $this->log("Предложения");
        $this->log("===========");

        // Отключаем кеш тегов перед работой с БД
        CIBlock::disableClearTagCache();

        $this->log("Чтение файла...");
        $this->read();

        $this->log("Загрузка из БД...");
        $this->load();

        $this->log("Запись в БД...");
        $this->apply();

        $this->log("Начало архивации файла");
        $this->arhivate();
        $this->log("Конец архивации файла");

        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно'.($this->log ? "\n".implode("\n", $this->log) : ''),
        ]);

        return $this->result;
    }

    /**
     * Читает xml файл
     */
    private function read()
    {
        $this->reader->setExpandedNodes([
            'properties',
            'offers',
        ]);

        // Обработчик чтения флага только изменения
        $this->reader->on('only_changes', function ($reader, $xml) {
            $xml = Helper::xml2array(simplexml_load_string($xml));
            $this->only_changes = $xml[0] == 1;
        });

        // Обработчик чтения свойств предложений
        $this->reader->on('property', function ($reader, $xml) {
            $property = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла свойство с ID ".$property['id']);
            }
            $nameHB = $this->getHighloadBlockName($property['id']);
            $property['id'] = mb_strtoupper($nameHB);
            if (!empty($this->properties[$property['id']])) {
                $this->log[] = 'Дублирующийся id свойства '.$property['id'];
                return;
            }
            $this->properties[$property['id']] = [
                'NAME' => $property['name'],
                'PROPERTY_TYPE' => $property['type'],
                'MULTIPLE' => $property['multiple'] == 1 ? 'Y' : 'N',
            ];
            // Записываем два временных массива
            if ($property['type'] == 'reference') {
                $this->reference[] = $nameHB;
            }
            if ($property['type'] == 'list') {
                $this->list[] = $property['id'];
            }
        });

        // Обработчик чтения предложений
        $this->reader->on('offer', function ($reader, $xml) {
            $offer = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла предложение с ID ".$offer['id']);
            }
            if (empty($offer['id'])) {
                $this->log[] = "У предложения не указано поле id";
                return;
            }
            if (empty($offer['parent_id'])) {
                $this->log[] = "У предложения c id ".$offer['id']." не указано поле parent_id";
                return;
            }
            if (empty($offer['name'])) {
                $this->log[] = "У предложения c id ".$offer['id']." не указано поле name";
                return;
            }
            $properties = [];
            foreach ($offer['properties']['property'] as $property) {
                $property['id'] = mb_strtoupper($this->getHighloadBlockName($property['id']));
                if ($this->properties[$property['id']]['MULTIPLE'] == 'Y') {
                    // Для полей multiply создаем запись формата [COLOR*multi*000000538] => 000000538
                    // Для того чтобы на все элементы находились на 1 уровне вложенности и простоты сравнения массивов
                    if (is_array($property['values']['value'])) {
                        foreach ($property['values']['value'] as $item) {
                            if (empty($item)) {
                                continue;
                            }
                            $properties[$property['id'].'*multi*'.$item] = $item;
                        }
                    } else {
                        $item = $property['values']['value'];
                        if (empty($item)) {
                            continue;
                        }
                        $properties[$property['id'].'*multi*'.$item] = $item;
                    }
                } else {
                    if (empty($property['value'])) {
                        continue;
                    }
                    $properties[$property['id']] = $property['value'];
                }
            }
            $this->fileOffers[$offer['id']] = [
                'ACTIVE' => 'Y',
                'NAME' => $offer['name'],
                'CODE' => TextHelper::myTranslit($offer['name'], 'ru'),
                'DETAIL_TEXT' => !empty($offer['descriptions']['full_description']) ? $offer['descriptions']['full_description'] : '',
                'PREVIEW_TEXT' => !empty($offer['descriptions']['short_description']) ? $offer['descriptions']['short_description'] : '',
                'PARENT_ID' => $offer['parent_id'],
            ];
            $this->fileOffers[$offer['id']] = array_merge($this->fileOffers[$offer['id']], $properties);
        });

        $this->reader->read();

        $this->log("Прочитано ".count($this->properties)." свойств");
        $this->log("Прочитано ".count($this->fileOffers)." предложений");

        if (count($this->fileOffers) == 0) {
            $this->log("Файл пустой или отсутствует\n");
            throw new ExchangeException(
                'Файл пустой или отсутствует',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        // Добавляем дополнительные нужные свойства, которые мы заполним сами далее
        $this->properties['CML2_LINK'] = [
            'NAME' => 'Элемент каталога',
            'PROPERTY_TYPE' => 'system',
            'MULTIPLE' => 'N',
        ];
    }

    /**
     * Загружает предложения
     */
    private function load()
    {
        if (!empty($this->reference)) {
            $this->log("Загрузка справочников");
            $rsReferences = HL::getList([
                'filter' => [
                    'NAME' => $this->reference,
                ]
            ]);
            // Получаем справочники и их возможные значения
            while ($arReference = $rsReferences->fetch()) {
                $arReference['NAME'] = mb_strtoupper($arReference['NAME']);
                $this->properties[$arReference['NAME']]["CLASS"] = HL::compileEntity($arReference)->getDataClass();
                $rsValues = $this->properties[$arReference['NAME']]["CLASS"]::getList([
                    'select' => [
                        'UF_XML_ID',
                    ]
                ]);
                while ($arValue = $rsValues->fetch()) {
                    $this->properties[$arReference['NAME']]['VALUES'][$arValue['UF_XML_ID']] = $arValue['UF_XML_ID'];
                }
            }
            foreach ($this->reference as $key => $item) {
                $item = mb_strtoupper($item);
                if (empty($this->properties[$item]['VALUES'])) {
                    $this->log[] = "Значения справочника ".$item." не найдены";
                    unset($this->reference[$key]);
                } else {
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $this->log("Справочник ".$item.". Загружено ".count($this->properties[$item]['VALUES'])." значений");
                    }
                }
            }
            $this->log("Загружено ".count($this->reference)." справочников");
            unset($class);
            unset($rsReferences);
            unset($rsValues);
            unset($this->reference);
        }

        if (!empty($this->list)) {
            $this->log("Загрузка значений свойства типа Список");
            $propEnums = CIBlockPropertyEnum::GetList(
                [
                    "SORT" => "ASC",
                ],
                [
                    "IBLOCK_ID" => $this->config['OFFERS_IBLOCK_ID'],
                    "CODE" => $this->list,
                ]
            );
            while ($enum_fields = $propEnums->fetch()) {
                $this->properties[$enum_fields['PROPERTY_CODE']]['VALUES'][$enum_fields['VALUE']] = $enum_fields['ID'];
            }
            foreach ($this->list as $key => $item) {
                if (empty($this->properties[$item]['VALUES'])) {
                    $this->log[] = "Значения свойства список ".$item." не найдены";
                    unset($this->list[$key]);
                } else {
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $this->log("Свойство список ".$item.". Загружено ".count($this->properties[$item]['VALUES'])." значений");
                    }
                }
            }
            $this->log("Загружено ".count($this->list)." свойств типа список");
            unset($propEnums);
            unset($this->list);
        }

        if (!empty($this->properties)) {
            $this->log("Загрузка свойств предложений");
            $propList = CIBlockProperty::GetList(
                [
                    "SORT" => "ASC",
                ],
                [
                    "IBLOCK_ID" => $this->config['OFFERS_IBLOCK_ID'],
                ]
            );
            while ($oneProp = $propList->fetch()) {
                if (empty($this->properties[$oneProp['CODE']])) {
                    continue;
                }
                $this->propertiesDB[$oneProp['CODE']] = [
                    'ID' => $oneProp['ID'],
                    'NAME' => $oneProp['NAME'],
                    'MULTIPLE' => $oneProp['MULTIPLE'],
                    'PROPERTY_TYPE' => $oneProp['PROPERTY_TYPE'],
                    'USER_TYPE' => $oneProp['USER_TYPE'],
                    'USER_TYPE_SETTINGS' => $oneProp['USER_TYPE_SETTINGS'],
                ];
            }
            $this->log("Загружено ".count($this->propertiesDB)." свойств");
        }

        $this->log("Загрузка товаров");
        $rsElements = ElementTable::getList([
            'select' => [
                'ID',
                'XML_ID',
                'IBLOCK_ID',
            ],
            'filter' => [
                'IBLOCK_ID' => $this->config['IBLOCK_ID'],
                'ACTIVE' => 'Y',
            ]
        ]);
        while ($arElement = $rsElements->fetch()) {
            $this->products[$arElement['XML_ID']] = $arElement['ID'];
        }
        $this->log("Загружено ".count($this->products)." товаров");

        $this->log("Загрузка предложений");
        $arSelect = [
            'IBLOCK_ID',
            'XML_ID',
            'ID',
            'NAME',
            'CODE',
            'ACTIVE',
            'DETAIL_TEXT',
            'PREVIEW_TEXT',
        ];
        foreach ($this->properties as $oneProp => $valProp) {
            $arSelect[] = 'PROPERTY_'.$oneProp;
        }
        $rsElements = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
            ],
            false,
            false,
            $arSelect
        );
        while ($arElement = $rsElements->fetch()) {
            // Поля
            $this->offers[$arElement['XML_ID']] = [
                'ID' => $arElement['ID'],
                'NAME' => $arElement['NAME'],
                'CODE' => $arElement['CODE'],
                'ACTIVE' => $arElement['ACTIVE'],
                'DETAIL_TEXT' => $arElement['DETAIL_TEXT'],
                'PREVIEW_TEXT' => $arElement['PREVIEW_TEXT'],
            ];
            // Свойства
            foreach ($this->properties as $oneProp => $valProp) {
                $propVal = $arElement['PROPERTY_'.$oneProp.'_VALUE'];
                if (empty($propVal)) {
                    continue;
                }
                if ($this->properties[$oneProp]['MULTIPLE'] == 'Y') {
                    if (empty($propVal)) {
                        $this->products[$arElement['XML_ID']][$oneProp] = [];
                        continue;
                    }
                    foreach ($propVal as $val) {
                        // Для полей multiply создаем запись формата [COLOR*multi*000000538] => 000000538
                        // Для того чтобы на все элементы находились на 1 уровне вложенности и простоты сравнения массивов
                        $this->offers[$arElement['XML_ID']][$oneProp.'*multi*'.$val] = $val;
                    }
                } else {
                    $this->offers[$arElement['XML_ID']][$oneProp] = $propVal;
                }
            }
        }
        $this->log("Загружено ".count($this->offers)." предложений");
    }

    /**
     * Применяет изменения к базе
     */
    private function apply()
    {
        $obElement = new CIBlockElement();
        Manager::enableDeferredIndexing();

        // Убиваем все ненужные события для сокращения запросов БД
        $eventsListKill = [
            'OnIBlockElementUpdate',
            'OnAfterIBlockElementUpdate',
            'OnBeforeIBlockElementUpdate',
            'OnAfterIBlockElementAdd',
            'OnIBlockElementAdd',
            'OnAfterIBlockElementSetPropertyValues',
            'OnAfterIBlockElementSetPropertyValuesEx',
            'OnIBlockElementSetPropertyValues',
        ];
        EventHelper::killEvents($eventsListKill, "iblock");
        unset($eventsListKill);

        if (!empty($this->properties)) {
            $this->log("Сравнение свойств в файле и БД");
            $stat = array(
                "ADD" => 0,
                "CHANGE" => 0,
                "NO" => 0,
                "SKIP" => 0,
                "SKIP_ERROR" => 0,
            );
            $ibp = new CIBlockProperty;
            foreach ($this->properties as $key => $value) {
                // Соответсвие типов в XML и БД
                if ($value['PROPERTY_TYPE'] == 'reference') {
                    if (!empty($value["CLASS"])) {
                        $value['PROPERTY_TYPE'] = 'S';
                        $value['USER_TYPE'] = 'directory';
                        $value['USER_TYPE_SETTINGS'] = [
                            'TABLE_NAME' => $value["CLASS"]::getTableName()
                        ];
                        unset($value["CLASS"]);
                    } else {
                        $this->log[] = "Не найден класс справочника - ".$key;
                        continue;
                    }
                } elseif ($value['PROPERTY_TYPE'] == 'list') {
                    $value['PROPERTY_TYPE'] = 'L';
                    $value['USER_TYPE'] = false;
                } elseif ($value['PROPERTY_TYPE'] == 'file') {
                    $value['PROPERTY_TYPE'] = 'F';
                    $value['USER_TYPE'] = false;
                } elseif ($value['PROPERTY_TYPE'] == 'number') {
                    $value['PROPERTY_TYPE'] = 'N';
                    $value['USER_TYPE'] = false;
                } elseif ($value['PROPERTY_TYPE'] == 'string') {
                    $value['PROPERTY_TYPE'] = 'S';
                    $value['USER_TYPE'] = false;
                } elseif ($value['PROPERTY_TYPE'] == 'system') {
                    $stat["SKIP"]++;
                    continue;
                } else {
                    $stat["SKIP_ERROR"]++;
                    $this->log[] = "Не известный тип свойства - ".$key;
                    continue;
                }
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $log = "Свойство ".$key;
                }
                if (!isset($this->propertiesDB[$key])) {
                    $stat["ADD"]++;
                    // Если свойства не существует - добавляем
                    $fields = [
                        "IBLOCK_ID" => $this->config['OFFERS_IBLOCK_ID'],
                        "ACTIVE" => "Y",
                        "CODE" => $key,
                    ];
                    $id = $ibp->Add(array_merge($fields, $value));
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $log .= ". Добавлено новое свойство ID = ".$id;
                    }
                } else {
                    if (isset($this->propertiesDB[$key]['ID'])) {
                        // Сохраняем ID для обновления
                        $idProp = $this->propertiesDB[$key]['ID'];
                        unset($this->propertiesDB[$key]['ID']);
                        // Убираем значение свойств так как их не сравниваем
                        if (isset($value['VALUES'])) {
                            unset($value['VALUES']);
                        }
                        // Сравниваем свойства, если есть разница - обновляем
                        $result = array_diff_assoc($value, $this->propertiesDB[$key]);
                        if (!empty($result)) {
                            $stat["CHANGE"]++;
                            $ibp->Update($idProp, $value);
                            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                                $log .= ". Обновлены поля";
                            }
                        } else {
                            $stat["NO"]++;
                            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                                $log .= ". Нечего обновлять";
                            }
                        }
                    } else {
                        $this->log("ERROR Не найден ID свойства ".$value['NAME']);
                    }
                }
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log($log);
                }
            }
            $this->log("Сравнение свойств завершено");
            $this->log("Добавлено ".$stat["ADD"]." свойств");
            $this->log("Обновлено ".$stat["CHANGE"]." свойств");
            $this->log("Не изменилось ".$stat["NO"]." свойств");
            $this->log("Пропущенно ".$stat["SKIP"]." свойств");
            $this->log("Пропущенно с ошибкой ".$stat["SKIP_ERROR"]." свойств");
        }
        unset($fields);
        unset($this->propertiesDB);

        $this->log("Сравнение предложений в файле и БД");
        $stat = array(
            "ADD" => 0,
            "CHANGE" => 0,
            "NO" => 0,
            "SKIP_ERROR" => 0,
        );
        foreach ($this->fileOffers as $offerXmlId => $arFileOffer) {
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $log = "Обрабатываем предложение ".$offerXmlId;
            }
            if (isset($this->products[$arFileOffer['PARENT_ID']])) {
                // Меняем PARENT_ID на CML2_LINK
                $arFileOffer['CML2_LINK'] = $this->products[$arFileOffer['PARENT_ID']];
                unset($arFileOffer['PARENT_ID']);
            } else {
                $stat["SKIP_ERROR"]++;
                $this->log[] = "Не найден родитель предложения ".$offerXmlId;
                continue;
            }
            // Сверяем XML и БД
            if (isset($this->offers[$offerXmlId])) {
                // ID в сравнении нам не нужен, сохраняем для обновления
                $offerId = $this->offers[$offerXmlId]['ID'];
                unset($this->offers[$offerXmlId]['ID']);
                // Получаем разницу между файлом и БД
                $result = array_diff_assoc($arFileOffer, $this->offers[$offerXmlId]);
                $resultRev = array_diff_assoc($this->offers[$offerXmlId], $arFileOffer);
                if (!empty($result) || !empty($resultRev)) {
                    $stat["CHANGE"]++;
                    // Есть разница
                    $arFieldAndProp = $this->magicFieldAndProp($arFileOffer, $offerId);
                    // Обновляем поля если есть изменения
                    if (!empty(array_diff_assoc($arFieldAndProp['FIELD'], $this->offers[$offerXmlId]))) {
                        $obElement->Update(
                            $offerId,
                            $arFieldAndProp['FIELD'],
                            false,
                            false,
                            false
                        );
                        if ($obElement->LAST_ERROR) {
                            $this->log[] = 'Ошибка обновления предложения ID = '.$offerId.' ERROR: '.$obElement->LAST_ERROR;
                        } else {
                            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                                $log .= ". Обновлены поля";
                            }
                        }
                    }
                    unset($arFieldAndProp['FIELD']);
                    // Для мультиполей и листов собираю массив
                    // Требуется для избежания лишнего обновления полей
                    $checkProp = $arFieldAndProp['PROPERTIES'];
                    foreach ($arFieldAndProp['PROPERTIES'] as $key => $item) {
                        if ($this->properties[$key]['PROPERTY_TYPE'] == 'L' && $this->properties[$key]['MULTIPLE'] == 'N') {
                            $this->offers[$offerXmlId][$key] = $this->properties[$key]['VALUES'][$this->offers[$offerXmlId][$key]];
                            $checkProp[$key] = $checkProp[$key]['VALUE'];
                            continue;
                        }
                        if ($this->properties[$key]['MULTIPLE'] == 'Y') {
                            unset($checkProp[$key]);
                            if (empty($item)) {
                                $checkProp[$key] = [];
                                continue;
                            }
                            foreach ($item as $val) {
                                $checkProp[$key . '*multi*' . $val] = $val;
                            }
                        }
                    }
                    // Обновляем свойства если есть изменения
                    if (!empty(array_diff_assoc($checkProp, $this->offers[$offerXmlId]))) {
                        $obElement->SetPropertyValuesEx(
                            $offerId,
                            $this->config['OFFERS_IBLOCK_ID'],
                            $arFieldAndProp['PROPERTIES'],
                            'DoNotValidateLists'
                        );
                        if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                            $log .= ". Обновлены свойства";
                        }
                    }
                    unset($checkProp);
                } else {
                    $stat["NO"]++;
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $log .= ". Нечего обновлять";
                    }
                }
                // В любом случае unset из $this->fileOffers и $this->offers
                unset($this->fileOffers[$offerXmlId]);
                unset($this->offers[$offerXmlId]);
            } else {
                // Добавляем новое предложение
                // Собираем поля и свойства
                $arFieldAndProp = $this->magicFieldAndProp($arFileOffer, $offerXmlId);
                $fields = [
                    'IBLOCK_ID' => $this->config['OFFERS_IBLOCK_ID'],
                    'XML_ID' => $offerXmlId,
                    'PROPERTY_VALUES' => $arFieldAndProp['PROPERTIES'],
                ];
                $ID = $obElement->Add(
                    array_merge($fields, $arFieldAndProp['FIELD']),
                    false,
                    false,
                    false
                );
                if ($obElement->LAST_ERROR) {
                    $stat["SKIP_ERROR"]++;
                    $this->log[] = 'Ошибка добавления предложения '.$offerXmlId.' ERROR: '.$obElement->LAST_ERROR;
                } else {
                    $stat["ADD"]++;
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $log .= ". Добавлено новое предложение ID = ".$ID;
                    }
                }
                unset($fields);
                unset($this->fileOffers[$offerXmlId]);
            }
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log($log);
            }
        }
        unset($log);
        unset($this->fileOffers);
        unset($this->properties);
        unset($this->products);
        $this->log("Сравнение предложений завершено");
        $this->log("Добавлено ".$stat["ADD"]." предложений");
        $this->log("Обновлено ".$stat["CHANGE"]." предложений");
        $this->log("Не изменилось ".$stat["NO"]." предложений");
        $this->log("Пропущенно с ошибкой ".$stat["SKIP_ERROR"]." предложений");

        if (!$this->only_changes) {
            $this->log("Деактивируем активные предложения");
            $IDs = [];
            foreach ($this->offers as $offer) {
                if ($offer['ACTIVE'] == 'Y') {
                    $IDs[$offer['ID']] = $offer['ID'];
                }
            }
            unset($this->offers);
            if (!empty($IDs)) {
                $connection = Application::getConnection();
                $connection->query("UPDATE b_iblock_element SET ACTIVE = 'N' WHERE ID IN (".implode(',', $IDs).") AND IBLOCK_ID = ".$this->config['OFFERS_IBLOCK_ID']);
                $this->log("Деактивировано ".count($IDs)." предложений");
            } else {
                $this->log("Предложения для деактивации не найдены");
            }
        }
    }
}
