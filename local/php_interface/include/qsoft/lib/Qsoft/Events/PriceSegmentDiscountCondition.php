<?php

namespace Qsoft\Events;

use Bitrix\Main\Application;
use CGlobalCondCtrlComplex;

class PriceSegmentDiscountCondition extends CGlobalCondCtrlComplex
{
    const PRICE_SEGMENT_ID = [
        'Red' => 'Красный',
        'White' => 'Белый',
        'Yellow' => 'Желтый'
    ];

    const PRODUCT_PRICE_TABLE = 'b_respect_product_price';

    public static function GetClassName()
    {
        return static::class;
    }

    public static function GetControlID()
    {
        return ['CondPriceSegment'];
    }

    public static function GetShowIn($controls)
    {
        return ['ActSaleBsktGrp'];
    }

    public static function GetControlShow($params)
    {
        $controls = static::GetControls();
        $shownGroups = static::GetShowIn($params['SHOW_IN_GROUPS']);

        $result = [
            'controlgroup' => true,
            'group' => true,
            'label' => 'Пользовательские свойства',
            'showIn' => $shownGroups,
            'children' => [],
        ];

        foreach ($controls as $control) {
            $result['children'][] = [
                'controlId' => $control['ID'],
                'group' => false,
                'label' => $control['LABEL'],
                'showIn' => $shownGroups,
                'control' => [
                    [
                        'id' => 'prefix',
                        'type' => 'prefix',
                        'text' => $control['PREFIX'],
                    ],
                    static::GetLogicAtom($control['LOGIC']),
                    static::GetValueAtom($control['JS_VALUE']),
                ],
            ];
        }

        return $result;
    }

    public static function GetControls($id = false)
    {
        $controls = [
            'CondPriceSegment' => [
                'ID' => 'CondPriceSegment',
                'FIELD' => 'PRICE_SEGMENT',
                'FIELD_TYPE' => 'text',
                'GROUP' => 'N',
                'LABEL' => 'Цвет ценового сегмента',
                'PREFIX' => 'ценовой сегмент',
                'LOGIC' => static::GetLogic([BT_COND_LOGIC_EQ, BT_COND_LOGIC_NOT_EQ]),
                'JS_VALUE' => [
                    'type' => 'select',
                    'values' => static::PRICE_SEGMENT_ID,
                    'multiple' => 'N',
                    'show_value' => 'Y',
                ],
                'PARENT' => true,
                'MULTIPLE' => 'N',
            ],
        ];

        return ($id && $controls[$id] ? $controls[$id] : $controls);
    }

    public static function Generate($condition, $params, $control, $subs = false)
    {
        $result = '';

        if (!is_array($control)) {
            $control = static::GetControls();
        }

        if (empty($control) || empty($condition['value'])) {
            return $result;
        }

        return static::class . "::checkProductPriceSegment(\$row, {$condition['value']}) == " . ($condition['logic'] == 'Equal' ? 'true' : 'false');
    }

    public static function checkProductPriceSegment($arProduct, $segmentID)
    {
        if (isset($arProduct['CATALOG']['PARENT_ID'])) {
            $id = $arProduct['CATALOG']['PARENT_ID'];
        } else {
            $id = $arProduct['PRODUCT_PRICE_ID'];
        }

        return static::getProductPriceSegmentID($id, $arProduct['PROPERTIES']['IS_LOCAL']['VALUE']) == $segmentID;
    }

    private static function getProductPriceSegmentID($productID, $isLocal)
    {
        global $LOCATION;
        $database = Application::getConnection();
        $branch = $isLocal == 'Y' ? $LOCATION->getBranch() : $LOCATION->DEFAULT_BRANCH;
        return $database->query(
            "SELECT PRICE_SEGMENT_ID FROM " . static::PRODUCT_PRICE_TABLE . " WHERE PRODUCT_ID = $productID AND BRANCH_ID = {$branch} LIMIT 1;"
        )->fetch()['PRICE_SEGMENT_ID'];
    }
}
