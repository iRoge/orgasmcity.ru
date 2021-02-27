<?php

use Bitrix\Sale\Location\TypeTable;

AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockPropertyLocationLink", "GetUserTypeDescription"));

class CIBlockPropertyLocationLink
{
    const LOC_TYPE = 'LOC_TYPE';

    public function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "LocationLink",
            "DESCRIPTION" => "Привязка к местоположению",
            "GetPropertyFieldHtml" => array(__CLASS__, "GetPropertyFieldHtml"),
            "GetPublicViewHTML" => array(__CLASS__, "GetPublicViewHTML"),
            "GetAdminListViewHTML" => array(__CLASS__, "GetAdminListViewHTML"),
            "GetSettingsHTML" => array(__CLASS__, "GetSettingsHTML"),
            "PrepareSettings" =>array(__CLASS__, "PrepareSettings"),
        );
    }

    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $matches = [];
        preg_match('/PROP\[\d+\]\[n?(\d+)\]/', $strHTMLControlName["VALUE"], $matches);
        $num = intval($matches[1]);
        $id = str_replace(['[', ']'], '', $strHTMLControlName["VALUE"]);
        $settings = CIBlockPropertyLocationLink::PrepareSettings($arProperty);
        $loc_type = ($settings[self::LOC_TYPE] > 0) ? $settings[self::LOC_TYPE] : '';
        ob_start(); ?>
        <input type="hidden" value="<?= $value['VALUE'] ?>" name="<?= $strHTMLControlName["VALUE"] ?>"
               id="<?= $id ?>_loc_<?= $num ?>">
        <div style="display: inline-block"
             id="<?= $id ?>_view_<?= $num ?>"><? echo !empty($value['VALUE']) ? $value['VALUE'] : 'Местоположение' ?></div>
        <div style="display: inline-block">
            <input class="tablebodybutton" type="button" OnClick="open_win_<?= $num ?>(<?= $num ?>)" value="...">
            <input type="button" value="Удалить" onclick="remove_<?= $num ?>()">
        </div>
        <script>
            function remove_<?=$num?>() {
                $('#<?= $id ?>_view_<?= $num ?>').text('Местоположение');
                $('#<?= $id ?>_loc_<?= $num ?>').val('');
            }

            function open_win_<?= $num ?>(num) {
                window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&value_type=name&field_name=<?= $id ?><?= (!empty($loc_type)) ? '&find_region=' . $loc_type : ''?>&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top=' + Math.floor((screen.height - 560) / 2 - 14) + ',left=' + Math.floor((screen.width - 760) / 2 - 5));
            }
        </script>
        <!--TODO - верстка-->
        <?
        return ob_get_clean();
    }


    public function GetAdminListViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if (strlen($value["VALUE"]) > 0) {
            return str_replace(" ", "&nbsp;", htmlspecialcharsex($value["VALUE"]));
        } else {
            return '&nbsp;';
        }
    }


    public function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        if (strlen($value["VALUE"]) > 0) {
            return str_replace(" ", "&nbsp;", htmlspecialcharsex($value["VALUE"]));
        } else {
            return '';
        }
    }

    public function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $arPropertyFields = array(
            "USER_TYPE_SETTINGS_TITLE" => "Настройка типа Местоположения"
        );

        $arLocTypes = TypeTable::getList([
            'filter' => ['NAME.LANGUAGE_ID' => LANGUAGE_ID],
            'select' => ['NAME_RU' => 'NAME.NAME', 'ID']
        ])->fetchAll();

        $settings = CIBlockPropertyLocationLink::PrepareSettings($arProperty);
        $returnStr = '<tr><td>Выберите тип местоположения:</td>'
        . '<td><select name="' . $strHTMLControlName['NAME'] . '[' . self::LOC_TYPE . ']">'
        . '<option disabled selected>Выберите тип местоположения</option>';

        foreach ($arLocTypes as $arLocType) {
            $selected = ($arLocType['ID'] == $settings[self::LOC_TYPE]) ? 'selected' : '';
            $returnStr .= '<option value="' . $arLocType['ID'] . '" ' . $selected . '>' . $arLocType['NAME_RU'] . '</option>';
        }

        $returnStr .= '</select></td></tr>';

        return $returnStr;
    }

    public static function PrepareSettings($arProperty)
    {
        $loc_type = 0;
        if (is_array($arProperty["USER_TYPE_SETTINGS"])) {
             $loc_type = intval($arProperty["USER_TYPE_SETTINGS"][self::LOC_TYPE]);
        }

        return array(
            self::LOC_TYPE =>  $loc_type,
        );
    }
}