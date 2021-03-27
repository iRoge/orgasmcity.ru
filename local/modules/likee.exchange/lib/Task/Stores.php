<?php

namespace Likee\Exchange\Task;

use Bitrix\Catalog\StoreTable;
use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Main\Loader;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Result;
use Likee\Exchange\Task;

class Stores extends Task
{
    public $node = 'stores';
    public $xml = 'stores.xml';
    public $elements = [];
    public $stores = [];
    /**
     * @private array Дополнительные сообщения результата импорта
     */
    private $log = [];

    public function import()
    {
        Loader::includeModule('highloadblock');
        Loader::includeModule('catalog');

        $result = new Result();

        $this->log("========");
        $this->log("Магазины");
        $this->log("========");

        $this->log("Загрузка из БД...");
        $this->load();
        $this->log("Создание свойств...");
        $this->createFields();
        $this->log("Чтение файла...");
        $this->read();
        $this->log("Прочитано ".count($this->stores)." складов");

        if (count($this->stores) == 0) {
            $this->log("Файл пустой или отсутствует\n");
            throw new ExchangeException(
                'Файл пустой или отсутствует',
                ExchangeException::$ERR_FILE_IS_EMPTY
            );
        }

        $this->log("Запись в БД...");
        $this->apply();

        $this->log("События OnSuccessImport");
        foreach (GetModuleEvents('likee.exchange', 'OnSuccessImport', true) as $arEvent) {
            $this->log("Событие ".$arEvent["TO_NAME"]);
            ExecuteModuleEventEx($arEvent, ['TASK' => 'stores']);
        }
        $this->log("Конец событий OnSuccessImport");

        $this->log("Начало архивации файла");
        $this->arhivate();
        $this->log("Конец архивации файла");

        $this->log("Конец\n");

        $result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно'.(!empty($this->log) ? "\n".implode("\n", $this->log) : ''),
        ]);

        return $result;
    }

    private function load()
    {
        $this->log("Загружаем склады");
        //загрузка складов
        $rsStores = StoreTable::getList(['select' => ['ID', 'XML_ID', 'IMAGE_ID', 'UF_PICTURES']]);
        while ($arStore = $rsStores->fetch()) {
            $this->elements[$arStore['XML_ID']] = $arStore;
        }
        $this->log("Загружено ".count($this->elements)." складов");
    }

    private function read()
    {
        $this->reader->setExpandedNodes([
            'stores'
        ]);

        $branchOffices = $this->getBranchOffices();

        $this->reader->on('store', function ($reader, $xml) use ($branchOffices) {
            $store = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла склад с ID ".$store['id']);
            }

            if (!$store['id']) {
                throw new ExchangeException(
                    'У склада не указано поле id',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }
            if (!$store['name']) {
                throw new ExchangeException(
                    'У склада не указано поле name',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            $arAddress = [];
            if ($store['address']['city']) {
                $arAddress[] = $store['address']['city'];
            }
            if ($store['address']['street']) {
                $arAddress[] = $store['address']['street'];
            }
            if ($store['address']['home']) {
                $arAddress[] = $store['address']['home'];
            }

            if (count($arAddress)) {
                $address = implode(', ', $arAddress);
            } else {
                $address = 'Отсутствует';
            }

            $arSubways = $arPhones = [];
            $block = HL::getRow([
                'filter' => [
                    'NAME' => 'Metro'
                ]
            ]);

            $arValues = [];
            $class = HL::compileEntity($block)->getDataClass();
            $rsValues = $class::getList();
            while ($arValue = $rsValues->fetch()) {
                $arValues[$arValue['UF_XML_ID']] = $arValue;
            }

            if (!$store['address']['subways']['subway'][0]) {
                $store['address']['subways']['subway'] = [
                    $store['address']['subways']['subway']
                ];
            }

            foreach ($store['address']['subways']['subway'] as $subway) {
                if (!$arValues[$subway['id']]) {
                    continue;
                }
                $arSubways[] = $arValues[$subway['id']]['ID'];
            }

            if (!is_array($store['phones']['phone'])) {
                $store['phones']['phone'] = [
                    $store['phones']['phone']
                ];
            }

            foreach ($store['phones']['phone'] as $phone) {
                $arPhones[] = $phone;
            }

            $arPictures = [];
            if ($store['pictures']['picture']) {
                foreach ($store['pictures']['picture'] as $picture) {
                    $path = ($_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . $picture);
                    if (is_file($path)) {
                        $arPictures[$picture] = \CFile::MakeFileArray($path);
                    }
                }
            }

            ksort($arPictures, SORT_STRING);

            $arPictures = array_values($arPictures);

            $arScheme = [];
            if ($store['address']['scheme']) {
                $path = ($_SERVER['DOCUMENT_ROOT'] . $this->config['PATH'] . $store['address']['scheme']);
                if (is_file($path)) {
                    $arScheme = \CFile::MakeFileArray($path);
                }
            }

            $store['worktime'] = $store['modes']['mode'];
            $store['worktimedays'] = [];
            foreach ($store['modes'] as $key => $mode) {
                if ($key != 'mode') {
                    $store['worktimedays'][$key] = array_combine(['from', 'to'], explode("-", str_replace(' ', '', $mode)));
                }
            }

            if (count($arPictures)) {
                $arDetailPicture = array_shift($arPictures);
            }

            if (empty($store['filial']) || empty($branchOffices[$store['filial']])) {
                $this->log[] = "У склада [".$store['id']."] ".$store['name']." не установлено свойство филиал";
            }

            $this->stores[] = [
                'XML_ID' => $store['id'],
                'TITLE' => $store['name'],
                'GPS_N' => $store['coordinates']['latitude'],
                'GPS_S' => $store['coordinates']['longitude'],
                'ADDRESS' => $address,
                'SCHEDULE' => $store['worktime'],
                'UF_METRO' => $arSubways,
                'UF_CITY' => $store['address']['city'],
                'UF_STREET' => $store['address']['street'],
                'UF_HOME' => $store['address']['home'],
                'UF_STATUS' => $store['status'],
                'UF_PHONES' => $arPhones,
                'UF_PICTURES' => $arPictures,
                'IMAGE_ID' => $arDetailPicture,
                'UF_DRIVING' => $arScheme,
                'UF_FILIAL' => $branchOffices[$store['filial']],
                'UF_ETAZ_TC' => $store['address']['Properties']['EtazTC'],
                'UF_YOUTUTBE_LINK' => $store['address']['Properties']['YouTubeLink'],
                'UF_OT_OSTANOVKI' => $store['address']['Properties']['otOstanovki'],
                'UF_VNUTRI_TC' => $store['address']['Properties']['vnutriTC'],
                'UF_INN' => $store['inn'],
                'UF_YURIDICHESKOE_LICO' => $store['yuridicheskoeLico'],
                'UF_GR_PO_DNYAM' => serialize($store['worktimedays']),
            ];
        });

        $this->reader->read();
    }

    private function apply()
    {
        foreach ($this->stores as $store) {
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Обновляем в БД склад с ID ".$store['XML_ID']);
            }
            $ID = $this->elements[$store['XML_ID']]['ID'];
            if ($ID) {
                $arStore = StoreTable::GetList([
                    'select' => ['*', 'UF_*'],
                    'filter' => ['ID' => $ID],
                    ])->Fetch();
                $iImage = false;
                if ($store['IMAGE_ID']) {
                    \CFile::Delete($this->elements[$store['XML_ID']]['IMAGE_ID']);
                    $iImage = \CFile::SaveFile($store['IMAGE_ID'], 'stores');
                }

                $store['IMAGE_ID'] = $iImage;

                if (count($store['UF_PICTURES']) > 0) {
                    foreach ($this->elements[$store['XML_ID']]['UF_PICTURES'] as $UF_PICTURE) {
                        \CFile::delete($UF_PICTURE);
                    }
                }

                if ('Отсутствует' == $store['ADDRESS'] && !empty($arStore['ADDRESS'])) {
                    unset($store['ADDRESS']);
                }
                unset($store['UF_CITY']);

                if (empty($store['UF_STORE_PVZ_TEXT'])) {
                    $store['UF_STORE_PVZ_TEXT'] = $arStore['UF_STORE_PVZ_TEXT'];
                }
                if (empty($store['UF_OT_OSTANOVKI'])) {
                    $store['UF_OT_OSTANOVKI'] = $arStore['UF_OT_OSTANOVKI'];
                }
                if (empty($store['UF_VNUTRI_TC'])) {
                    $store['UF_VNUTRI_TC'] = $arStore['UF_VNUTRI_TC'];
                }

                $rs = StoreTable::update($ID, $store);
            } else {
                $rs = StoreTable::add($store);
            }

            if (!$rs->isSuccess()) {
                throw new ExchangeException(
                    reset($rs->getErrorMessages()),
                    ExchangeException::$ERR_CREATE_UPDATE
                );
            }
        }
    }

    /**
     * @throws ExchangeException
     */
    private function createFields()
    {
        global $APPLICATION, $USER_FIELD_MANAGER;

        $obField = new \CUserTypeEntity();

        $aUserFields = [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_PHONES',
            'MULTIPLE' => 'Y',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Телефоны'
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Телефоны'
            ],
            'LIST_FILTER_LABEL' => [
                'ru' => 'Телефоны'
            ],
            'ERROR_MESSAGE' => [
                'ru' => 'Телефоны'
            ]
        ];

        $arField = $obField->GetList([], [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_PHONES'
        ])->Fetch();

        if ($arField) {
            $obField->Update($arField['ID'], $aUserFields);
        } else {
            $ID = $obField->Add($aUserFields);
        }

        $aUserFields = [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_HOME',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Дом'
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Дом'
            ],
            'LIST_FILTER_LABEL' => [
                'ru' => 'Дом'
            ],
            'ERROR_MESSAGE' => [
                'ru' => 'Дом'
            ]
        ];

        $arField = $obField->GetList([], [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_HOME'
        ])->Fetch();

        if ($arField) {
            $obField->Update($arField['ID'], $aUserFields);
        } else {
            $ID = $obField->Add($aUserFields);
        }

        $aUserFields = [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STREET',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Улица'
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Улица'
            ],
            'LIST_FILTER_LABEL' => [
                'ru' => 'Улица'
            ],
            'ERROR_MESSAGE' => [
                'ru' => 'Улица'
            ]
        ];

        $arField = $obField->GetList([], [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STREET'
        ])->Fetch();

        if ($arField) {
            $obField->Update($arField['ID'], $aUserFields);
        } else {
            $ID = $obField->Add($aUserFields);
        }

        $aUserFields = [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_CITY',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Город'
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Город'
            ],
            'LIST_FILTER_LABEL' => [
                'ru' => 'Город'
            ],
            'ERROR_MESSAGE' => [
                'ru' => 'Город'
            ]
        ];

        $arField = $obField->GetList([], [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_CITY'
        ])->Fetch();

        if ($arField) {
            $obField->Update($arField['ID'], $aUserFields);
        } else {
            $ID = $obField->Add($aUserFields);
        }

        $aUserFields = [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STATUS',
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL' => [
                'ru' => 'Статус'
            ],
            'LIST_COLUMN_LABEL' => [
                'ru' => 'Статус'
            ],
            'LIST_FILTER_LABEL' => [
                'ru' => 'Статус'
            ],
            'ERROR_MESSAGE' => [
                'ru' => 'Статус'
            ]
        ];

        $arField = $obField->GetList([], [
            'ENTITY_ID' => 'CAT_STORE',
            'FIELD_NAME' => 'UF_STATUS'
        ])->Fetch();

        if ($arField) {
            $obField->Update($arField['ID'], $aUserFields);
        } else {
            $ID = $obField->Add($aUserFields);
        }

        $block = HL::getRow([
            'filter' => [
                'NAME' => 'Metro'
            ]
        ]);
        if ($block) {
            $arField = $obField->GetList([], [
                'ENTITY_ID' => 'HLBLOCK_' . $block['ID'],
                'FIELD_NAME' => 'UF_NAME'
            ])->Fetch();

            $aUserFields = [
                'ENTITY_ID' => 'CAT_STORE',
                'FIELD_NAME' => 'UF_METRO',
                'MULTIPLE' => 'Y',
                'USER_TYPE_ID' => 'hlblock',
                'SETTINGS' => [
                    'HLBLOCK_ID' => $block['ID'],
                    'HLFIELD_ID' => $arField['ID'],
                    'LIST_HEIGHT' => 10,
                ],
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Метро'
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'Метро'
                ],
                'LIST_FILTER_LABEL' => [
                    'ru' => 'Метро'
                ],
                'ERROR_MESSAGE' => [
                    'ru' => 'Метро'
                ]
            ];

            $arField = $obField->GetList([], [
                'ENTITY_ID' => 'CAT_STORE',
                'FIELD_NAME' => 'UF_METRO'
            ])->Fetch();

            if ($arField) {
                $obField->Update($arField['ID'], $aUserFields);
            } else {
                $ID = $obField->Add($aUserFields);
            }
        } else {
            throw new ExchangeException(
                'Отсутствует справочник метро',
                ExchangeException::$ERR_INCORRECT_LINK
            );
        }
    }

    private function getBranchOffices()
    {
        global $DB;
        $dbResponse = $DB->Query('SELECT xml_id, name FROM b_respect_branch');
        $result = [];

        while ($branchOffice = $dbResponse->Fetch()) {
            $result[$branchOffice['xml_id']] = $branchOffice['name'];
        }

        return $result;
    }
}
