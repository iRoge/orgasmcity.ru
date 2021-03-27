<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange\Task;

use Bitrix\Highloadblock\HighloadBlockTable as HL;
use Bitrix\Main\Loader;
use Likee\Exchange\ExchangeException;
use Likee\Exchange\Result;
use Likee\Exchange\Task;

/**
 * Класс для работы с импортом рекомендаций.
 *
 * @package Likee\Exchange\Task
 */
class Recomended extends Task
{
    /**
     * @var string элемент xml
     */
    var $node = 'recomended';
    /**
     * @var string xml для импорта
     */
    var $xml = 'recomended.xml';
    /**
     * @var array Словарь
     */
    var $dictionary = [];

    /**
     * Выполняет импорт
     *
     * @return \Likee\Exchange\Result Результат импорта
     * @throws ExchangeException Ошибка обмена
     */
    public function import()
    {
        Loader::includeModule('highloadblock');

        $result = new Result();

        $this->loadXML();
        $this->prepare();
        $this->apply();

        $result->setData([
            'status' => 'success',
            'text' => 'Обработка прошла успешно',
        ]);

        return $result;
    }

    /**
     * Подготавливает словарь
     *
     * @throws ExchangeException
     */
    private function prepare()
    {
        foreach ($this->data['reference'] as $reference) {
            if (empty($reference['id']))
                throw new ExchangeException(
                    'Отсутствует id словаря',
                    ExchangeException::$ERR_EMPTY_FIELD
                );

            $properties = [];
            foreach ($reference['properties']['property'] as $property) {
                if (empty($property['id']))
                    throw new ExchangeException(
                        "Отсутствует поле id словаря $reference[id]",
                        ExchangeException::$ERR_EMPTY_FIELD
                    );

                if (strtolower($property['id']) == 'code') {
                    $properties[strtolower($property['id'])] = [
                        'ID' => 'UF_XML_ID',
                        'TYPE' => $property['type'],
                        'NAME' => $property['name'],
                    ];
                } else {
                    $properties[strtolower($property['id'])] = [
                        'ID' => 'UF_' . strtoupper($property['id']),
                        'TYPE' => $property['type'],
                        'NAME' => $property['name'],
                    ];
                }
            }

            if (empty($properties['code']))
                throw new ExchangeException("Отсутствует поле code у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);

            $elements = [];
            foreach ($reference['elements']['element'] as $element) {
                $data = [];
                foreach ($element['property'] as $property) {
                    if (empty($property['id']))
                        throw new ExchangeException("Отсутствует поле id у словаря $reference[id]", ExchangeException::$ERR_EMPTY_FIELD);


                    if (strtoupper($property['id']) == 'CODE')
                        $data['UF_XML_ID'] = $property['value'];
                    else
                        $data['UF_' . strtoupper($property['id'])] = $property['value'];
                }
                $elements[] = $data;
            }

            $this->dictionary[] = [
                'ID' => $this->getHighloadBlockName($reference['id']),
                'TABLE_NAME' => 'b_1c_dict_' . strtolower($reference['id']),
                'PROPERTIES' => $properties,
                'ELEMENTS' => $elements
            ];
        }
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

                if ($arField)
                    $obField->Update($arField['ID'], $aUserFields);
                else
                    $ID = $obField->Add($aUserFields);

                if ($ex = $APPLICATION->GetException())
                    throw new ExchangeException($ex->GetString(), ExchangeException::$ERR_CREATE_UPDATE);

            }

            $class = HL::compileEntity($block)->getDataClass();
            foreach ($dictionary['ELEMENTS'] as $data) {
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