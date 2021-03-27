<?php
/**
 * User: Azovcev Artem
 * Date: 05.03.17
 * Time: 0:26
 */

namespace Likee\Site\Helpers;

use Bitrix\Highloadblock\HighloadBlockTable;

/**
 * Класс для работы с highload блоками. Содержит методы для получения данных из HL блока.
 *
 * @package Likee\Site\Helpers
 */
class HL
{
    /**
     * Получить объект базы по имени таблицы
     *
     * @param string $sTable Имя таблицы
     * @return \Bitrix\Main\Entity\Base
     */
    public static function getEntityClassByTableName($sTable)
    {
        static $arClasses = [];

        if (!array_key_exists($sTable, $arClasses)) {
            $arClasses[$sTable] = '';

            $arHLData = HighloadBlockTable::getRow([
                'filter' => ['TABLE_NAME' => $sTable]
            ]);

            if ($arHLData) {
                $arClasses[$sTable] = HighloadBlockTable::compileEntity($arHLData);
            }
        }

        return $arClasses[$sTable];
    }

    /**
     * Получить объект базы по имени highload блока
     *
     * @param string $sName Имя блока
     * @return \Bitrix\Main\Entity\Base
     */
    public static function getEntityClassByHLName($sName)
    {
        static $arClasses = [];

        if (!array_key_exists($sName, $arClasses)) {
            $arClasses[$sName] = '';

            $arHLData = HighloadBlockTable::getRow([
                'filter' => ['NAME' => $sName]
            ]);

            if ($arHLData) {
                $arClasses[$sName] = HighloadBlockTable::compileEntity($arHLData);
            }
        }

        return $arClasses[$sName];
    }

    /**
     * Возвращает значение поля по свойству
     *
     * @param array $arProp Свойство
     * @param string $sField Поле
     * @return string Значение
     */
    public static function getFieldValueByProp($arProp, $sField)
    {
        if ($arProp['PROPERTY_TYPE'] != 'S')
            return '';

        $sTableName = $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'];
        $obEntity = self::getEntityClassByTableName($sTableName);

        if (!$obEntity || !is_object($obEntity))
            return '';

        if (!$obEntity->hasField('UF_XML_ID') || !$obEntity->hasField($sField))
            return '';

        $sClass = $obEntity->getDataClass();

        $arPropValue = $sClass::getRow([
            'filter' => ['UF_XML_ID' => $arProp['~VALUE']],
            'select' => [$sField]
        ]);

        return $arPropValue[$sField];
    }

    /**
     * Возвращает поле по свойству
     *
     * @param array $arProp Свойство
     * @return array Поле
     */
    public static function getFileByProp($arProp)
    {
        $iFile = self::getFieldValueByProp($arProp, 'UF_FILE');

        if (!$iFile)
            return [];

        $arFile = \CFile::GetByID($iFile)->Fetch();
        $arFile['SRC'] = \CFile::GetFileSRC($arFile);

        return $arFile;
    }
}