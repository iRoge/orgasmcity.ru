<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange\Task;

use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Helper;
use Likee\Exchange\Task;

/**
 * Класс для работы с импортом словарей.
 *
 * @package Likee\Exchange\Task
 */
class References extends Task
{
    /**
     * @public string элемент xml
     */
    public $node = 'references';
    /**
     * @public string xml для импорта
     */
    public $xml = 'references.xml';
    /**
     * @public array Словарь
     */
    public $dictionary = [];
    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {

        $this->log("===========");
        $this->log("Справочники");
        $this->log("===========");

        $this->log("Чтение файла...");
        $this->read();

        if (count($this->dictionary) == 0) {
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
            ExecuteModuleEventEx($arEvent, ['TASK' => 'references']);
        }
        $this->log("Конец событий OnSuccessImport");

        $this->log("Начало архивации файла");
        $this->arhivate();
        $this->log("Конец архивации файла");

        $this->log("Конец\n");

        $this->result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно',
        ]);

        return $this->result;
    }
    /**
     * Читает xml файл
     *
     * @throws ExchangeException
     */
    private function read()
    {

        $this->reader->setExpandedNodes([
            'references',
        ]);

        $this->reader->on('reference', function ($reader, $xml) {
            $reference = Helper::xml2array(simplexml_load_string($xml));
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Читаем из файла словарь с ID ".$reference['id']);
            }

            if (empty($reference['id'])) {
                throw new ExchangeException(
                    'Отсутствует id словаря',
                    ExchangeException::$ERR_EMPTY_FIELD
                );
            }

            $properties = [];
            foreach ($reference['properties']['property'] as $property) {
                if (empty($property['id'])) {
                    throw new ExchangeException(
                        "Отсутствует поле id словаря $reference[id]",
                        ExchangeException::$ERR_EMPTY_FIELD
                    );
                }

                if (mb_strtolower($property['id']) == 'code') {
                    $properties[mb_strtolower($property['id'])] = [
                        'ID' => 'UF_XML_ID',
                        'TYPE' => $property['type'],
                        'NAME' => $property['name'],
                    ];
                } else {
                    $properties[mb_strtolower($property['id'])] = [
                        'ID' => 'UF_' . mb_strtoupper($property['id']),
                        'TYPE' => $property['type'],
                        'NAME' => $property['name'],
                    ];
                }
            }

            if (empty($properties['code'])) {
                throw new ExchangeException("Отсутствует поле code у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);
            }

            if (!$reference['elements']['element'][0]) {
                $reference['elements']['element'] = [
                    $reference['elements']['element']
                ];
            }

            $elements = [];
            foreach ($reference['elements']['element'] as $element) {
                $data = [];
                foreach ($element['property'] as $property) {
                    if (empty($property['id'])) {
                        throw new ExchangeException("Отсутствует поле id у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);
                    }


                    if (mb_strtoupper($property['id']) == 'CODE') {
                        $data['UF_XML_ID'] = $property['value'];
                    } else {
                        $data['UF_' . mb_strtoupper($property['id'])] = $property['value'];
                    }
                }
                $elements[] = $data;
            }

            $this->dictionary[] = [
                'ID' => $this->getHighloadBlockName($reference['id']),
                'TABLE_NAME' => 'b_1c_dict_' . mb_strtolower($reference['id']),
                'PROPERTIES' => $properties,
                'ELEMENTS' => $elements
            ];
        });
        $this->reader->read();
    }
    /**
     * Применяет изменения в базе
     *
     * @throws ExchangeException
     */
    private function apply()
    {

        global $APPLICATION, $DB;

        $obField = new \CUserTypeEntity();

        foreach ($this->dictionary as $dictionary) {
            if (defined("IMPORT_LOG") && IMPORT_LOG == "FULL") {
                $this->log("Обновляем в БД словарь с ID ".$dictionary['ID']);
            }

            //фикс бага с ошибкой Class '\EO_Collection' not found (0)
            if ($dictionary['ID'] == 'collection' || $dictionary['ID'] == 'Collection') {
                $dictionary['ID'] = 'Collectionhb';
            }

            $block = HL::getRow([
                'filter' => [
                    'NAME' => $dictionary['ID']
                ]
            ]);
            if ($block) {
                $HLBLOCK_ID = $block['ID'];
            } else {
                $res = HL::add([
                    'NAME' => $dictionary['ID'],
                    'TABLE_NAME' => $dictionary['TABLE_NAME']
                ]);
                $HLBLOCK_ID = $res->getId();
            }

            $block = HL::getById($HLBLOCK_ID)->fetch();
            foreach ($dictionary['PROPERTIES'] as $field) {
                $aUserFields = [
                    'ENTITY_ID' => 'HLBLOCK_' . $HLBLOCK_ID,
                    'FIELD_NAME' => $field['ID'],
                    'USER_TYPE_ID' => $field['TYPE'],
                    'EDIT_FORM_LABEL' => [
                        'ru' => $field['NAME'],
                    ],
                    'LIST_COLUMN_LABEL' => [
                        'ru' => $field['NAME'],
                    ],
                    'LIST_FILTER_LABEL' => [
                        'ru' => $field['NAME'],
                    ],
                    'ERROR_MESSAGE' => [
                        'ru' => $field['NAME'],
                    ]
                ];

                $arField = $obField->GetList([], [
                    'ENTITY_ID' => 'HLBLOCK_' . $HLBLOCK_ID,
                    'FIELD_NAME' => $field['ID'],
                ])->Fetch();

                if ($arField) {
                    $obField->Update($arField['ID'], $aUserFields);
                } else {
                    $ID = $obField->Add($aUserFields);
                }

                if ($ex = $APPLICATION->GetException()) {
                    throw new ExchangeException($ex->GetString(), ExchangeException::$ERR_CREATE_UPDATE);
                }
            }

            $class = HL::compileEntity($block)->getDataClass();
            foreach ($dictionary['ELEMENTS'] as $data) {
                if (count($data)) {
                    $row = $class::getRow([
                        'filter' => [
                            'UF_XML_ID' => $data['UF_XML_ID']
                        ]
                    ]);
                    if ($row) {
                        $class::update($row['ID'], $data);
                    } else {
                        $class::add($data);
                    }
                }
            }
        }
    }
}
