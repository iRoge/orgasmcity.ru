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
use Bitrix\Main\Application;
use Bitrix\Main\FileTable;
use CFile;
use CIBlock;
use CIBlockElement;
use CIBlockProperty;
use CIBlockPropertyEnum;
use CIBlockSection;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;
use Qsoft\Helpers\TextHelper;
use Qsoft\Helpers\EventHelper;

/**
 * Класс для работы с импортом товаров.
 *
 * @package Likee\Exchange\Task
 */
class Import extends Task
{
    /**
     * @public string xml для импорта
     */
    public $xml = 'import.xml';
    /**
     * @protected array Свойства из файла + дополнительные
     */
    protected $properties = array();
    /**
     * @protected array Сборка брендов
     */
    protected $brands = array();
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
     * @private array Товары из файла
     */
    private $fileProducts = array();
    /**
     * @private array Товары из БД
     */
    private $products = array();
    /**
     * @private array Изображения товаров
     */
    private $images = array();
    /**
     * @private array Разделы товаров
     */
    private $sections = array();
    /**
     * @private bool Только изменения
     */
    private $only_changes = true;
    /**
     * @public array Дополнительные сообщения результата импорта
     */
    public $log = [];
    /**
     * @private array Массив значений высоты каблука
     */
    private $arHLHeelHeight = [];
    /**
     * @private array Массив значений габаритов
     */
    private $arDimensions = [];
    /**
     * @private array Массив ссылок на youtube
     */
    private $arYoutubeLink = [];

    private const FILE_TABLE = 'b_file';
    private const READ_LEN = 4096;
    private const IBLOCK_COLLECTION = 22;

    /**
     * @private string пути до картинок
     */
    private $basePath;
    private $basePathImport;

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
        $this->log("======");
        $this->log("Товары");
        $this->log("======");

        $this->basePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/';
        $this->basePathImport = $_SERVER['DOCUMENT_ROOT'] . '/upload/1c_catalog/';
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
            'text' => 'Обработка прошла успешно' . ($this->log ? "\n" . implode("\n", $this->log) : ''),
        ]);

        return $this->result;
    }

    /**
     * Читает xml файл
     */
    private function read()
    {
        $this->reader->setExpandedNodes([
            'import',
            'properties',
            'products',
        ]);

        // Обработчик чтения флага только изменения
        $this->reader->on('only_changes', function ($reader, $xml) {
            $xml = Helper::xml2array(simplexml_load_string($xml));
            $this->only_changes = $xml[0] == 1;
        });

        // Обработчик чтения свойств товаров
        $this->reader->on('property', function ($reader, $xml) {
            $property = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла свойство с ID " . $property['id']);
            }
            $nameHB = $this->getHighloadBlockName($property['id']);
            $property['id'] = mb_strtoupper($nameHB);

            if (!empty($this->properties[$property['id']])) {
                $this->log[] = 'Дублирующийся id свойства ' . $property['id'];
                return;
            }
            $this->properties[$property['id']] = [
                'NAME' => $property['name'],
                'PROPERTY_TYPE' => $property['type'],
                'MULTIPLE' => $property['multiple'] == 1 ? 'Y' : 'N',
            ];
            // Записываем два временных массива
            if ($property['type'] == 'reference') {
                if ($nameHB == 'Collection') {
                    $nameHB = 'Collectionhb';
                }
                $this->reference[] = $nameHB;
            }
            if ($property['type'] == 'list') {
                $this->list[] = $property['id'];
            }
        });

        // Обработчик чтения товаров
        $this->reader->on('product', function ($reader, $xml) {
            $product = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла товар с ID " . $product['id']);
            }
            if (empty($product['id'])) {
                $this->log[] = "У товара не указано поле id";
                return;
            }

            $properties = [];
            if (empty($product['name'])) {
                $product['name'] = $product['article'];
                $properties['NAME_FOR_INTERNET_SHOP'] = '';
            } else {
                $properties['NAME_FOR_INTERNET_SHOP'] = $product['name'];
            }

            $imPropBrand = '';
            foreach ($product['properties']['property'] as $property) {
                $property['id'] = mb_strtoupper($this->getHighloadBlockName($property['id']));
                if ($this->properties[$property['id']]['MULTIPLE'] == 'Y') {
                    // Для полей multiply создаем запись формата [COLOR*multi*000000538] => 000000538
                    // Для того чтобы на все элементы находились на 1 уровне вложенности и простоты сравнения массивов
                    if (is_array($property['values']['value'])) {
                        foreach ($property['values']['value'] as $item) {
                            if (empty($item)) {
                                continue;
                            }
                            $properties[$property['id'] . '*multi*' . $item] = $item;
                        }
                    } else {
                        $item = $property['values']['value'];
                        if (empty($item)) {
                            continue;
                        }
                        $properties[$property['id'] . '*multi*' . $item] = $item;
                    }
                } else {
                    if (empty($property['value'])) {
                        continue;
                    }
                    if ($property['id'] == 'BRAND') {
                        $imPropBrand = $property['value'];
                    }
                    $properties[$property['id']] = $property['value'];
                }
            }
            $properties['ARTICLE'] = $product['article'];
            $properties['KOD_1S'] = $product['kod_1s'];
            $arPictures = [];
            if (!empty($product['pictures']['picture'])) {
                if (!is_array($product['pictures']['picture'])) {
                    $arPictures = [$product['pictures']['picture']];
                } else {
                    $arPictures = $product['pictures']['picture'];
                }
                $this->brands['IMPORT'][$imPropBrand] = $imPropBrand;
            }
            $this->fileProducts[$product['id']] = [
                'ACTIVE' => 'Y',
                'NAME' => $product['name'],
                'CODE' => TextHelper::myTranslit($product['article'], 'ru'),
                //'DETAIL_TEXT' =>
                //                'PREVIEW_TEXT' => !empty($product['descriptions']['short_description']) ? $product['descriptions']['short_description'] : '',
                'PICTURES' => $arPictures,
            ];
            if (!empty($product['descriptions']['full_description'])) {
                $this->fileProducts[$product['id']]['DETAIL_TEXT'] = $product['descriptions']['full_description'];
            }
            $this->fileProducts[$product['id']] = array_merge($this->fileProducts[$product['id']], $properties);
        });

        $this->reader->read();

        $this->log("Прочитано " . count($this->properties) . " свойств");
        $this->log("Прочитано " . count($this->fileProducts) . " товаров");
        $this->log("Прочитано " . count($this->brands['IMPORT']) . " кодов брендов");

        if (count($this->fileProducts) == 0) {
            $this->log("Файл пустой или отсутствует\n");
            throw new ExchangeException(
                'Файл пустой или отсутствует',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        // Добавляем дополнительные нужные свойства, которые мы заполним сами далее
        $this->properties['ARTICLE'] = [
            'NAME' => 'Артикул',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['KOD_1S'] = [
            'NAME' => 'Код 1С',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['COLLECTION_SORT'] = [
            'NAME' => 'Сортировка коллекции',
            'PROPERTY_TYPE' => 'number',
            'MULTIPLE' => 'N',
        ];
        $this->properties['MORE_PHOTO'] = [
            'NAME' => 'Картинки',
            'PROPERTY_TYPE' => 'file',
            'MULTIPLE' => 'Y',
        ];
        $this->properties['HEELHEIGHT_TYPE'] = [
            'NAME' => 'Cтепень высоты каблука',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['NAME_FOR_INTERNET_SHOP'] = [
            'NAME' => 'Наименование для интернет магазина',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['LENGTH'] = [
            'NAME' => 'Длина сайт',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['WIDTH'] = [
            'NAME' => 'Ширина сайт',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['HEIGHT'] = [
            'NAME' => 'Высота сайт',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
        $this->properties['YOUTUBE_LINK'] = [
            'NAME' => 'Ссылка на Ютуб номенклтуру сайт',
            'PROPERTY_TYPE' => 'string',
            'MULTIPLE' => 'N',
        ];
    }

    /**
     * Загружает товары
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
                if ($arReference['NAME'] == 'COLLECTIONHB') {
                    $this->properties['COLLECTION']["CLASS"] = HL::compileEntity($arReference)->getDataClass();
                } else {
                    $this->properties[$arReference['NAME']]["CLASS"] = HL::compileEntity($arReference)->getDataClass();
                }
                $select = ['UF_XML_ID'];
                // Добавляем числовое значение высоты каблука к селекту, если блок каблуков или габаритов
                if ($arReference['NAME'] == 'HEELHEIGHT' ||
                    $arReference['NAME'] == 'VISOTATOVAR' ||
                    $arReference['NAME'] == 'DLINATOVAR' ||
                    $arReference['NAME'] == 'SHIRINATOVAR' ||
                    $arReference['NAME'] == 'YOUTUBETOVAR' ||
                    $arReference['NAME'] == 'BRAND'
                ) {
                    $select[] = 'UF_NAME';
                }
                if ($arReference['NAME'] == 'COLLECTIONHB') {
                    $rsValues = $this->properties['COLLECTION']["CLASS"]::getList([
                        'select' => $select
                    ]);
                } else {
                    $rsValues = $this->properties[$arReference['NAME']]["CLASS"]::getList([
                        'select' => $select
                    ]);
                }

                while ($arValue = $rsValues->fetch()) {
                    if ($arReference['NAME'] == 'COLLECTIONHB') {
                        $this->properties['COLLECTION']['VALUES'][$arValue['UF_XML_ID']] = $arValue['UF_XML_ID'];
                    } else {
                        $this->properties[$arReference['NAME']]['VALUES'][$arValue['UF_XML_ID']] = $arValue['UF_XML_ID'];
                        if ($arReference['NAME'] == 'BRAND' && !empty($this->brands['IMPORT'][$arValue['UF_XML_ID']])) {
                            $this->brands['IMPORT'][$arValue['UF_NAME']][$arValue['UF_XML_ID']] = $arValue['UF_XML_ID'];
                            //$this->brands['IMPORT'][$arValue['UF_XML_ID']] = $arValue['UF_NAME'];
                            unset($this->brands['IMPORT'][$arValue['UF_XML_ID']]);
                        }
                    }

                    // Создаем массив значений каблуков
                    if ($arReference['NAME'] == 'HEELHEIGHT') {
                        $this->arHLHeelHeight[$arValue['UF_XML_ID']]['UF_NAME'] = $arValue['UF_NAME'];
                    }
                    // Создаем массив значений габаритов
                    if ($arReference['NAME'] == 'VISOTATOVAR' ||
                        $arReference['NAME'] == 'DLINATOVAR' ||
                        $arReference['NAME'] == 'SHIRINATOVAR') {
                        $this->arDimensions[$arReference['NAME']][$arValue['UF_XML_ID']] = $arValue['UF_NAME'];
                    }
                    if ($arReference['NAME'] == 'YOUTUBETOVAR') {
                        $this->arYoutubeLink[$arValue['UF_XML_ID']] = $arValue['UF_NAME'];
                    }
                }
            }
            foreach ($this->reference as $key => $item) {
                $item = mb_strtoupper($item);
                if ($item == 'COLLECTIONHB') {
                    $item = 'COLLECTION';
                }
                if (empty($this->properties[$item]['VALUES'])) {
                    $this->log[] = "Значения справочника " . $item . " не найдены";
                    unset($this->reference[$key]);
                } else {
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $this->log("Справочник " . $item . ". Загружено " . count($this->properties[$item]['VALUES']) . " значений");
                    }
                }
            }
            $this->log("Загружено " . count($this->reference) . " справочников");
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
                    "IBLOCK_ID" => $this->config['IBLOCK_ID'],
                    "CODE" => $this->list,
                ]
            );
            while ($enum_fields = $propEnums->fetch()) {
                $this->properties[$enum_fields['PROPERTY_CODE']]['VALUES'][$enum_fields['VALUE']] = $enum_fields['ID'];
            }
            foreach ($this->list as $key => $item) {
                if (empty($this->properties[$item]['VALUES'])) {
                    $this->log[] = "Значения свойства список " . $item . " не найдены";
                    unset($this->list[$key]);
                } else {
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $this->log("Свойство список " . $item . ". Загружено " . count($this->properties[$item]['VALUES']) . " значений");
                    }
                }
            }
            $this->log("Загружено " . count($this->list) . " свойств типа список");
            unset($propEnums);
            unset($this->list);
        }

        if (!empty($this->properties)) {
            $this->log("Загрузка свойств товаров");
            $propList = CIBlockProperty::GetList(
                [
                    "SORT" => "ASC",
                ],
                [
                    "IBLOCK_ID" => $this->config['IBLOCK_ID'],
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
            $this->log("Загружено " . count($this->propertiesDB) . " свойств");
        }

        $this->log('Загрузка брендов');
        $arSelect = [
            'IBLOCK_ID',
            'XML_ID',
            'ID',
            'NAME',
            'CODE',
            'ACTIVE',
            'UF_XML_BRANDS',
            'DEPTH_LEVEL',
        ];
        $arFilter = [
            'IBLOCK_ID' => \Functions::getEnvKey('IBLOCK_BRANDS'),
        ];
        $rsBrands = CIBlockSection::GetList([], $arFilter, false, $arSelect);
        while ($section = $rsBrands->Fetch()) {
            if ($section['DEPTH_LEVEL'] == 1) {
                $this->brands['MAIN_SECTION'] = $section;
            } else {
                $this->brands['DB'][$section['NAME']] = $section;
            }
        }

        $this->log("Загружено " . count($this->brands['DB']) . " брендов");

        $this->log("Загрузка товаров");
        $arSelect = [
            'IBLOCK_ID',
            'XML_ID',
            'ID',
            'NAME',
            'CODE',
            'ACTIVE',
            'DETAIL_TEXT',
            'PREVIEW_TEXT',
            'DETAIL_PICTURE',
            'PREVIEW_PICTURE',
            'IBLOCK_SECTION_ID',
        ];
        foreach ($this->properties as $oneProp => $valProp) {
            $arSelect[] = 'PROPERTY_' . $oneProp;
        }
        $rsElements = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->config['IBLOCK_ID'],
            ],
            false,
            false,
            $arSelect
        );
        while ($arElement = $rsElements->fetch()) {
            // Поля
            $this->products[$arElement['XML_ID']] = [
                'ID' => $arElement['ID'],
                'NAME' => $arElement['NAME'],
                'CODE' => $arElement['CODE'],
                'ACTIVE' => $arElement['ACTIVE'],
                'DETAIL_TEXT' => $arElement['DETAIL_TEXT'],
                'PREVIEW_TEXT' => $arElement['PREVIEW_TEXT'],
                'IBLOCK_SECTION_ID' => $arElement['IBLOCK_SECTION_ID'],
            ];
            // Картинки
            // Даже если ничего нет, то записываем пустоту в ключ, чтобы данные добавились
            $this->images["REF"][$arElement["ID"]]["DETAIL"] = $arElement['DETAIL_PICTURE'];
            $this->images["REF"][$arElement["ID"]]["PREVIEW"] = $arElement['PREVIEW_PICTURE'];
            // А Id записываем только существующие
            if (!empty($arElement['DETAIL_PICTURE'])) {
                $this->images["ID"][] = $arElement['DETAIL_PICTURE'];
            }
            if (!empty($arElement['PREVIEW_PICTURE'])) {
                $this->images["ID"][] = $arElement['PREVIEW_PICTURE'];
            }
            // Свойства
            foreach ($this->properties as $oneProp => $valProp) {
                $propVal = $arElement['PROPERTY_' . $oneProp . '_VALUE'];
                // Мы сюда точно попадём, тк в массиве $this->properties есть такое свойство, мы его добавили сами
                if ($oneProp == 'MORE_PHOTO') {
                    // Сохраняем связи ID товара -> ID свойства -> ID файла и отдельно ID файлов
                    $this->images["REF"][$arElement["ID"]]["MORE_PHOTO"] = array_combine($propVal, $arElement['PROPERTY_' . $oneProp . '_PROPERTY_VALUE_ID']);
                    $this->images["ID"] = array_merge($this->images["ID"], $propVal);
                    continue;
                }
                if (empty($propVal)) {
                    continue;
                }
                if ($this->properties[$oneProp]['MULTIPLE'] == 'Y') {
                    foreach ($propVal as $val) {
                        // Для полей multiply создаем запись формата [COLOR*multi*000000538] => 000000538
                        // Для того чтобы на все элементы находились на 1 уровне вложенности и простоты сравнения массивов
                        $this->products[$arElement['XML_ID']][$oneProp . '*multi*' . $val] = $val;
                    }
                } else {
                    $this->products[$arElement['XML_ID']][$oneProp] = $propVal;
                }
            }
        }
        $this->log("Загружено " . count($this->products) . " товаров");

        $this->log("Загрузка картинок товаров");
        $res = FileTable::getList(array(
            "select" => array(
                "ID",
                "SUBDIR",
                "FILE_NAME",
            ),
            "filter" => array(
                "ID" => $this->images["ID"],
            ),
        ));
        while ($arItem = $res->fetch()) {
            $this->images['PICTURE'][$arItem['ID']] = $arItem['SUBDIR'] . "/" . $arItem['FILE_NAME'];
        }
        unset($this->images["ID"]);
        $this->log("Загружено " . count($this->images['PICTURE']) . " картинок");

        $this->log("Загрузка информации о коллекциях товаров");
        $rsElements = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => self::IBLOCK_COLLECTION,
            ],
            false,
            false,
            [
                'ID',
                'IBLOCK_ID',
                'SORT',
                'PROPERTY_COLLECTION',
            ]
        );
        while ($arElement = $rsElements->fetch()) {
            $this->properties['COLLECTION_SORT']['VALUES'][$arElement['PROPERTY_COLLECTION_VALUE']] = $arElement['SORT'];
        }
        $this->log("Загружено " . count($this->properties['COLLECTION_SORT']['VALUES']) . " коллекций");

        $this->log("Загрузка разделов товаров");
        $rsElements = CIBlockSection::GetList(
            [
                'depth_level' => 'asc',
            ],
            [
                'IBLOCK_ID' => $this->config['IBLOCK_ID'],
            ],
            false,
            [
                'ID',
                'IBLOCK_ID',
                'XML_ID',
                'IBLOCK_SECTION_ID',
            ],
            false
        );
        while ($arElement = $rsElements->fetch()) {
            if ($arElement['DEPTH_LEVEL'] == 1) {
                // Для первого уровня просто XML_ID
                $this->sections[$arElement['XML_ID']] = $arElement['ID'];
            } else {
                // Для остальных уровней ID родителя + XML_ID
                $this->sections[$arElement['IBLOCK_SECTION_ID']][$arElement['XML_ID']] = $arElement['ID'];
            }
        }
        $this->log("Загружено " . count($this->sections) . " разделов");
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
            'OnBeforeIBlockSectionUpdate',
            'OnAfterIBlockSectionUpdate',
            'OnBeforeIBlockSectionAdd',
            'OnAfterIBlockSectionAdd',
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
        $eventsListKill = [
            'OnFileSave',
            'OnFileDelete',
        ];
        EventHelper::killEvents($eventsListKill, "main");
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
                        $this->log[] = "Не найден класс справочника - " . $key;
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
                    $this->log[] = "Не известный тип свойства - " . $key;
                    continue;
                }
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $log = "Свойство " . $key;
                }
                if (!isset($this->propertiesDB[$key])) {
                    $stat["ADD"]++;
                    // Если свойства не существует - добавляем
                    $fields = [
                        "IBLOCK_ID" => $this->config['IBLOCK_ID'],
                        "ACTIVE" => "Y",
                        "CODE" => $key,
                    ];
                    $id = $ibp->Add(array_merge($fields, $value));
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $log .= ". Добавлено новое свойство ID = " . $id;
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
                        $this->log("ERROR Не найден ID свойства " . $value['NAME']);
                    }
                }
                if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                    $this->log($log);
                }
            }
            $this->log("Сравнение свойств завершено");
            $this->log("Добавлено " . $stat["ADD"] . " свойств");
            $this->log("Обновлено " . $stat["CHANGE"] . " свойств");
            $this->log("Не изменилось " . $stat["NO"] . " свойств");
            $this->log("Пропущенно " . $stat["SKIP"] . " свойств");
            $this->log("Пропущенно с ошибкой " . $stat["SKIP_ERROR"] . " свойств");
        }
        unset($fields);
        unset($this->propertiesDB);

        $brandStats = [];
        $oSection = new CIBlockSection;
        foreach ($this->brands['IMPORT'] as $brandName => $xmls) {
            if ($section = $this->brands['DB'][$brandName]) {
                $arFields = [
                    "IBLOCK_ID" => \Functions::getEnvKey('IBLOCK_BRANDS'),
                    'IBLOCK_SECTION_ID' => $this->brands['MAIN_SECTION']['ID'],
                    'ACTIVE' => 'Y',
                    'UF_XML_BRANDS' => $xmls,
                    'CODE' => TextHelper::myTranslit($brandName, 'ru', [
                        "replace_space" => '-',
                        "replace_other" => '-',
                    ])
                ];

                $res = $oSection->Update($section['ID'], $arFields, true, false, false);
                if ($res) {
                    $brandStats['UPD']++;
                } else {
                    $this->log('Не удалось обновить бренд  ' . $brandName);
                }

                unset($this->brands['DB'][$brandName]);
            } else {
                $arFields = [
                    "IBLOCK_ID" => \Functions::getEnvKey('IBLOCK_BRANDS'),
                    'IBLOCK_SECTION_ID' => $this->brands['MAIN_SECTION']['ID'],
                    'NAME' => $brandName,
                    'ACTIVE' => 'Y',
                    'UF_XML_BRANDS' => $xmls,
                    'CODE' => TextHelper::myTranslit($brandName, 'ru', [
                        "replace_space" => '-',
                        "replace_other" => '-',
                    ])
                ];

                $res = $oSection->Add($arFields, true, false, false);
                if ($res) {
                    $brandStats['ADD']++;
                } else {
                    $this->log('Не удалось создать бренд  ' . $brandName);
                }
                unset($this->brands['DB'][$brandName]);
            }
        }

        foreach ($this->brands['DB'] as $section) {
            $arFields = [
                'ACTIVE' => 'N'
            ];

            $res = $oSection->Update($section['ID'], $arFields, true, false, false);
            if ($res) {
                $brandStats['DCT']++;
            } else {
                $this->log('Не удалось деактивировать бренд  ' . $section['NAME']);
            }
            unset($this->brands['DB'][$section['NAME']]);
        }
        $this->log("Бренды: создано " . $brandStats['ADD'] . ', обновлено ' . $brandStats['UPD'] . ', деактивировано ' . $brandStats['DCT']);

        $this->log("Сравнение товаров в файле и БД");
        $stat = array(
            "ADD" => 0,
            "CHANGE" => 0,
            "NO" => 0,
            "SKIP_ERROR" => 0,
        );
        // Подставляем степень высоты каблука и меняем габариты, youtube на адекватные значения
        foreach ($this->fileProducts as &$fileProduct) {
            if (!empty($fileProduct['DLINATOVAR'])) {
                $fileProduct['LENGTH'] = $this->arDimensions['DLINATOVAR'][$fileProduct['DLINATOVAR']];
            }
            if (!empty($fileProduct['SHIRINATOVAR'])) {
                $fileProduct['WIDTH'] = $this->arDimensions['SHIRINATOVAR'][$fileProduct['SHIRINATOVAR']];
            }
            if (!empty($fileProduct['VISOTATOVAR'])) {
                $fileProduct['HEIGHT'] = $this->arDimensions['VISOTATOVAR'][$fileProduct['VISOTATOVAR']];
            }
            if (!empty($fileProduct['YOUTUBETOVAR'])) {
                $fileProduct['YOUTUBE_LINK'] = $this->arYoutubeLink[$fileProduct['YOUTUBETOVAR']];
            }

            if (!isset($fileProduct['HEELHEIGHT'])) {
                continue;
            }
            $heelHeight = $this->arHLHeelHeight[$fileProduct['HEELHEIGHT']]['UF_NAME'];
            if ($fileProduct['RHODEPRODUCT'] == '000000254') {
                $fileProduct['HEELHEIGHT_TYPE'] = 'Без каблука';
            } elseif ($heelHeight >= 6 && $heelHeight < 41) {
                $fileProduct['HEELHEIGHT_TYPE'] = 'Низкий';
            } elseif ($heelHeight >= 41 && $heelHeight < 81) {
                $fileProduct['HEELHEIGHT_TYPE'] = 'Средний';
            } elseif ($heelHeight >= 81) {
                $fileProduct['HEELHEIGHT_TYPE'] = 'Высокий';
            } else {
                $fileProduct['HEELHEIGHT_TYPE'] = 'Без каблука';
            }
        }
        foreach ($this->fileProducts as $productXmlId => $arFileProduct) {
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $log = "Обрабатываем товар " . $productXmlId;
            }
            // Прописываем сортировку коллекции и раздел
            $arFileProduct['COLLECTION_SORT'] = $this->properties['COLLECTION_SORT']['VALUES'][$arFileProduct['COLLECTION']] ?? 500;
            $arFileProduct['IBLOCK_SECTION_ID'] = $this->sectionsTree($arFileProduct);
            if (!$arFileProduct['IBLOCK_SECTION_ID']) {
                $this->log[] = "У товара " . $productXmlId . " не указаны или указанны не корректно данные для установки раздела";
            }
            if (isset($this->products[$productXmlId])) {
                // ID в сравнении нам не нужен, сохраняем для обновления
                $productId = $this->products[$productXmlId]['ID'];
                unset($this->products[$productXmlId]['ID']);
                // Обрабатываем картинки, если нужно добавить/удалить деталку или анонс, их вернет функция. Всё остальное обрабатывается внутри
                $arPictures = $this->processImages($arFileProduct['PICTURES'], $productId);
                unset($arFileProduct['PICTURES']);
                if (!empty($arPictures)) {
                    $arFileProduct = array_merge($arFileProduct, $arPictures);
                }
                // Получаем разницу между файлом и БД
                $result = array_diff_assoc($arFileProduct, $this->products[$productXmlId]);
                $resultRev = array_diff_assoc($this->products[$productXmlId], $arFileProduct);
                if (!empty($result) || !empty($resultRev)) {
                    $stat["CHANGE"]++;
                    // Есть разница
                    $arFieldAndProp = $this->magicFieldAndProp($arFileProduct, $productId);
                    // Обновляем поля если есть изменения
                    if (!empty(array_diff_assoc($arFieldAndProp['FIELD'], $this->products[$productXmlId]))) {
                        $obElement->Update(
                            $productId,
                            $arFieldAndProp['FIELD'],
                            false,
                            false,
                            false
                        );
                        if ($obElement->LAST_ERROR) {
                            $this->log[] = 'Ошибка обновления товара ID = ' . $productId . ' ERROR: ' . $obElement->LAST_ERROR;
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
                            $this->products[$productXmlId][$key] = $this->properties[$key]['VALUES'][$this->products[$productXmlId][$key]];
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
                    if (!empty(array_diff_assoc($checkProp, $this->products[$productXmlId]))) {
                        $obElement->SetPropertyValuesEx(
                            $productId,
                            $this->config['IBLOCK_ID'],
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
                // В любом случае unset из $this->fileProducts и $this->products
                unset($this->fileProducts[$productXmlId]);
                unset($this->products[$productXmlId]);
            } else {
                // Добавляем новый товар
                // Обновляем фотки, деталка и анонс при изменении вернуться в $arFileProduct
                $arPictures = $this->processImages($arFileProduct['PICTURES']);
                unset($arFileProduct['PICTURES']);
                if (!empty($arPictures)) {
                    $arFileProduct = array_merge($arFileProduct, $arPictures);
                }
                // Собираем поля и свойства
                $arFieldAndProp = $this->magicFieldAndProp($arFileProduct, $productXmlId);
                $fields = [
                    'IBLOCK_ID' => $this->config['IBLOCK_ID'],
                    'XML_ID' => $productXmlId,
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
                    $this->log[] = 'Ошибка добавления товара ' . $productXmlId . ' ERROR: ' . $obElement->LAST_ERROR;
                } else {
                    $stat["ADD"]++;
                    if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                        $log .= ". Добавлен новый товар ID = " . $ID;
                    }
                }
                unset($fields);
                unset($this->fileProducts[$productXmlId]);
            }
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log($log);
            }
        }
        unset($log);
        unset($this->fileProducts);
        unset($this->properties);
        unset($this->sections);
        $this->log("Сравнение товаров завершено");
        $this->log("Добавлено " . $stat["ADD"] . " товаров");
        $this->log("Обновлено " . $stat["CHANGE"] . " товаров");
        $this->log("Не изменилось " . $stat["NO"] . " товаров");
        $this->log("Пропущенно с ошибкой " . $stat["SKIP_ERROR"] . " товаров");

        if (!$this->only_changes) {
            $this->log("Деактивируем активные товары");
            $IDs = [];
            foreach ($this->products as $product) {
                if ($product['ACTIVE'] == 'Y') {
                    $IDs[$product['ID']] = $product['ID'];
                }
            }
            unset($this->products);
            if (!empty($IDs)) {
                $connection = Application::getConnection();
                $connection->query("UPDATE b_iblock_element SET ACTIVE = 'N' WHERE ID IN (" . implode(',', $IDs) . ") AND IBLOCK_ID = " . $this->config['IBLOCK_ID']);
                $this->log("Деактивировано " . count($IDs) . " товаров");
            } else {
                $this->log("Товары для деактивации не найдены");
            }
        }
    }

    /**
     * Принимает элемент и возвращает его раздел
     * @param array $arElem Массив с полями товара
     * @return integer $id ИД раздела
     */
    private function sectionsTree($arElem)
    {
        if (empty($arElem)) {
            return false;
        }

        if ($arElem['VID'] == 'УК0000003') {
            // 1lvl
            $id = $this->sections[$arElem['VID']];
            if (empty($id)) {
                return false;
            }
            // 2lvl
            $id2 = $this->sections[$id][$arElem['TYPEPRODUCT']];
            if (empty($id2)) {
                return $id;
            }
            // 3lvl
            $id3 = $this->sections[$id2][$arElem['SUBTYPEPRODUCT']];
            if (empty($id3)) {
                return $id2;
            }
            return $id3;
        } else {
            // 1lvl
            if ($arElem['RHODEPRODUCT'] == 'УК0009665') {
                $arElem['RHODEPRODUCT'] = '000000199';
            }
            $id = $this->sections[$arElem['RHODEPRODUCT']];
            if (empty($id)) {
                return false;
            }
            // 2lvl
            $id2 = $this->sections[$id][$arElem['VID']];
            if (empty($id2)) {
                return $id;
            }
            // 3lvl
            $id3 = $this->sections[$id2][$arElem['TYPEPRODUCT']];
            if (empty($id3)) {
                return $id2;
            }
            return $id3;
        }
    }

    /**
     * Обработка изображений
     */
    private function processImages($arPictures, $productId = false)
    {
        $arPictures = $this->getPicturesType($arPictures);
        sort($arPictures['MORE_PHOTO'], SORT_NATURAL);
        if ($productId) {
            return $this->comparePictures($arPictures, $productId);
        } else {
            $arResult = array();
            foreach ($arPictures as $type => $value) {
                switch ($type) {
                    case "DETAIL":
                    case "PREVIEW":
                        $fileArray = CFile::MakeFileArray($this->basePathImport . $value);
                        if (empty($fileArray)) {
                            $this->log[] = "Не существует картинки " . $this->basePathImport . $value;
                            continue;
                        }
                        $arResult[$type . "_PICTURE"] = $fileArray;
                        break;
                    case "MORE_PHOTO":
                        foreach ($value as $picture) {
                            $fileArray = CFile::MakeFileArray($this->basePathImport . $picture);
                            if (empty($fileArray)) {
                                $this->log[] = "Не существует картинки " . $this->basePathImport . $picture;
                                continue;
                            }
                            $arResult["MORE_PHOTO"][] = $fileArray;
                        }
                        break;
                }
            }
            return $arResult;
        }
    }
    private function getPicturesType($arPictures)
    {
        usort($arPictures, function ($a, $b) {
            $a = strrev($a);
            $startA = mb_strpos($a, '.') + 1;
            $endA = mb_strpos($a, '_');
            $numA = intval(mb_substr($a, $startA, $endA - $startA));
            if ($numA == 2) {
                return -1;
            }
            $b = strrev($b);
            $startB = mb_strpos($b, '.') + 1;
            $endB = mb_strpos($b, '_');
            $numB = intval(mb_substr($b, $startB, $endB - $startB));
            if ($numB == 2) {
                return 1;
            }
            return $numA > $numB ? 1 : -1;
        });
        $arResult = array();
        foreach ($arPictures as $picture) {
            if (empty($arResult["DETAIL"])) {
                $arResult["DETAIL"] = $picture;
                continue;
            }
            if (empty($arResult["PREVIEW"])) {
                $arResult["PREVIEW"] = $picture;
                continue;
            }
            $arResult["MORE_PHOTO"][] = $picture;
        }
        return $arResult;
    }
    private function comparePictures($arPictures, $elementId)
    {
        // Мы пройдем по всем 3 ключам в любом случае
        // Есть картинки в БД или нет проверит $this->checkPath и добавит на добавление или обновление
        $arResult = array();
        foreach ($this->images["REF"][$elementId] as $type => $value) {
            if ($type == "MORE_PHOTO") {
                $i = 0;
                $temp = array();
                // Проверяем существующие по массиву из БД
                foreach ($value as $fileId => $propId) {
                    $res = $this->checkPath($arPictures[$type][$i], $this->images["PICTURE"][$fileId]);
                    if ($res) {
                        if ($res == "upd") {
                            $this->updatePicture($arPictures[$type][$i], $this->images["PICTURE"][$fileId], $fileId);
                        } elseif ($res == "add") {
                            // $this->checkPath проверит, что картинка уже есть в папке и тут не нужно проверять, вернет ли массив CFile::MakeFileArray
                            $temp[] = CFile::MakeFileArray($this->basePathImport . $arPictures[$type][$i]);
                        } else {
                            // Важно передать ключем $propId (НЕ $fileId), тогда битрикс отработает корректно
                            $temp[$propId] = array(
                                "VALUE" => array(
                                    "del" => "Y",
                                )
                            );
                        }
                    }
                    $i++;
                }
                // Если в файле их больше, просто добавляем
                while ($arPictures[$type][$i]) {
                    $fileArray = CFile::MakeFileArray($this->basePathImport . $arPictures[$type][$i]);
                    $i++;
                    if (empty($fileArray)) {
                        $this->log[] = "Не существует картинки " . $this->basePathImport . $arPictures[$type][$i];
                        continue;
                    }
                    $temp[] = $fileArray;
                }
                // Если есть, что добавить или удалить (обновление в $this->updatePicture), то сразу пишем в базу и забываем про это свойство
                if (!empty($temp)) {
                    CIBlockElement::SetPropertyValues($elementId, $this->config['IBLOCK_ID'], $temp, 'MORE_PHOTO');
                }
            } else {
                $res = $this->checkPath($arPictures[$type], $this->images["PICTURE"][$value]);
                if ($res) {
                    if ($res == "add" || $res == "del") {
                        if ($res == "add") {
                            // $this->checkPath проверит, что картинка уже есть в папке и тут не нужно проверять, вернет ли массив CFile::MakeFileArray
                            $arResult[$type . "_PICTURE"] = CFile::MakeFileArray($this->basePathImport . $arPictures[$type]);
                        } else {
                            $arResult[$type . "_PICTURE"] = array(
                                "del" => "Y",
                            );
                        }
                    } else {
                        $this->updatePicture($arPictures[$type], $this->images["PICTURE"][$value], $value);
                    }
                }
            }
        }
        return $arResult;
    }
    private function checkPath($fPath, $dbPath)
    {
        if (empty($fPath)) {
            if (!empty($dbPath)) {
                return 'del';
            }
        } else {
            if (!file_exists($this->basePathImport . $fPath)) {
                $this->log[] = "Не существует картинки " . $this->basePathImport . $fPath;
                return false;
            }

            if (!empty($dbPath)) {
                $same = $this->compareFiles($this->basePathImport . $fPath, $this->basePath . $dbPath);
                if (!$same) {
                    return "upd";
                }
            } else {
                return 'add';
            }
        }
        return false;
    }
    private function compareFiles($fn1, $fn2)
    {
        if (filetype($fn1) !== filetype($fn2)) {
            return false;
        }
        if (filesize($fn1) !== filesize($fn2)) {
            return false;
        }
        if (!$fp1 = fopen($fn1, 'rb')) {
            return false;
        }
        if (!$fp2 = fopen($fn2, 'rb')) {
            fclose($fp1);
            return false;
        }
        $same = true;
        while (!feof($fp1) and !feof($fp2)) {
            if (fread($fp1, self::READ_LEN) !== fread($fp2, self::READ_LEN)) {
                $same = false;
                break;
            }
        }
        if (feof($fp1) !== feof($fp2)) {
            $same = false;
        }
        fclose($fp1);
        fclose($fp2);
        return $same;
    }
    private function updatePicture($fPath, $dbPath, $dbId)
    {
        $subdir = strrev(explode("/", strrev($dbPath), 2)[1]);
        $fPath = $this->basePathImport . $fPath;
        $dbPath = $this->basePath . $dbPath;
        copy($fPath, $dbPath);
        $this->rmdirRecursive($this->basePath . 'resize_cache/' . $subdir);
        CFile::CleanCache($dbId);
    }
    private function rmdirRecursive($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->rmdirRecursive($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}
