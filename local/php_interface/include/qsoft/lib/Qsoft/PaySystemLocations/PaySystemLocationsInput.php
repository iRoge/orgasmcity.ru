<?php
namespace Qsoft\PaySystemLocations;

use Bitrix\Sale\Internals\Input;

class PaySystemLocationsInput extends Input\Base
{
    public static function getViewHtml(array $input, $value = null)
    {
        $result = "";

        $res = PaySystemLocationTable::getConnectedLocations(
            $input["PAY_SYSTEM_ID"],
            array(
            'select' => array('LNAME' => 'NAME.NAME'),
            'filter' => array('NAME.LANGUAGE_ID' => LANGUAGE_ID)
            )
        );

        while ($loc = $res->fetch()) {
            $result .= htmlspecialcharsbx($loc["LNAME"])."<br>\n";
        }

        $res = PaySystemLocationTable::getConnectedGroups(
            $input["PAY_SYSTEM_ID"],
            array(
            'select' => array('LNAME' => 'NAME.NAME'),
            'filter' => array('NAME.LANGUAGE_ID' => LANGUAGE_ID)
            )
        );

        while ($loc = $res->fetch()) {
            $result .= htmlspecialcharsbx($loc["LNAME"])."<br>\n";
        }

        return $result;
    }

    public static function getEditHtml($name, array $input, $values = null)
    {
        global $APPLICATION;

        ob_start();

        $APPLICATION->IncludeComponent(
            "bitrix:sale.location.selector.system",
            "",
            array(
            "ENTITY_PRIMARY" => $input["PAY_SYSTEM_ID"],
            "LINK_ENTITY_NAME" => '\Qsoft\PaySystemLocations\PaySystemLocation',
            "INPUT_NAME" => $name
            ),
            false
        );

        $result = ob_get_contents();
        $result = '
<script type="text/javascript">
    var bxInputdeliveryLocMultiStep3 = function()
    {
        BX.loadScript("/bitrix/components/bitrix/sale.location.selector.system/templates/.default/script.js", function(){
            BX.onCustomEvent("paySystemGetRestrictionHtmlScriptsReady");
        });
    };

    var bxInputdeliveryLocMultiStep2 = function()
    {
        BX.load([
                "/bitrix/js/sale/core_ui_etc.js",
                "/bitrix/js/sale/core_ui_autocomplete.js",
                "/bitrix/js/sale/core_ui_itemtree.js"
            ],
            bxInputdeliveryLocMultiStep3
        );
    };

    BX.loadScript("/bitrix/js/sale/core_ui_widget.js", bxInputdeliveryLocMultiStep2);

    //at first we must load some scripts in the right order
    window["paySystemGetRestrictionHtmlScriptsLoadingStarted"] = true;

</script>

<link rel="stylesheet" type="text/css" href="/bitrix/panel/main/adminstyles_fixed.css">
<link rel="stylesheet" type="text/css" href="/bitrix/panel/main/admin.css">
<link rel="stylesheet" type="text/css" href="/bitrix/panel/main/admin-public.css">
<link rel="stylesheet" type="text/css" href="/bitrix/components/bitrix/sale.location.selector.system/templates/.default/style.css">
'.
        $result;
        ob_end_clean();
        return $result;
    }

    public static function getError(array $input, $values)
    {
        return array();
    }


    public static function getValueSingle(array $input, $userValue)
    {
        return $userValue;
    }

    public static function getSettings(array $input, $reload)
    {
        return array();
    }
}
