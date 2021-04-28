<?
AddEventHandler('main', 'OnUserTypeBuildList', array('allRegionLink', 'GetUserTypeDescription'));
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("CIBlockNewProperty", "GetUserTypeDescription"));
class allRegionLink extends \Bitrix\Main\UserField\TypeBase
{
    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    public function GetUserTypeDescription()
    {
        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'all_region_link',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'allRegionLink',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Все местоположения',
            // базовый тип на котором будут основаны операции фильтра
            'BASE_TYPE' => 'string',
        );
    }
    // Функция вызывается при добавлении нового свойства
    // для конструирования SQL запроса создания столбца значений свойства
    // @return string
    public function GetDBColumnType($arUserField)
    {
        global $DB;
        switch (strtolower($DB->type)) {
            case "mysql":
                return "text";
            case "oracle":
                return "varchar2(2555 char)";
            case "mssql":
                return "varchar(2555)";
        }
    }
    // Функция вызывается при выводе формы редактирования значения свойства
    // @return string - HTML для вывода
    public function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        global $DB;
        $i = 0;
        // view - то что видно в редактировании и списке (Регион).
        // loc - код местоположения.
        ob_start();
        if ($arUserField["VALUE"]) {
            $name = $DB->Query('select ln.name from b_sale_location sl inner join b_sale_loc_name ln on sl.id = ln.location_id where sl.code = ' . $arUserField['VALUE'])->Fetch()['name'];
        }
        ?>
        <lable id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left"><?= $name??$arUserField['VALUE'] ?: "Выберите регион" ?></lable>
        <input class="region-btn" type="button" onClick="open_region_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
        <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>" name="<?= $arHtmlControl["NAME"] ?>" value="<?= $arUserField['VALUE'] ?>">
        <script type="text/javascript">
            function open_region_win_<?= $arHtmlControl["NAME"] ?>(num){ window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&find_region=0&field_name=<?= $arHtmlControl["NAME"] ?>&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
        </script>
        <?
        return ob_get_clean();
    }
    // Функция вызывается при построении фильтра, если стоит настройка показывать в фильтре
    public function GetFilterData($arUserField, $arHtmlControl)
    {
        return array(
            "id" => $arHtmlControl["ID"],
            "name" => $arHtmlControl["NAME"],
            "type" => "string",
            "filterable" => "",
        );
    }
}
class CIBlockNewProperty
{
    public function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE"        => "S", #-----один из стандартных типов
            "USER_TYPE"            => "ALL_REGION_CODE", #-----идентификатор типа свойства
            "DESCRIPTION"          => "Местоположение",
            "GetPropertyFieldHtml" => array("CIBlockNewProperty", "GetPropertyFieldHtml"),
        );
    }
    // Функция вызывается при выводе формы редактирования значения свойства
    // @return string - HTML для вывода
    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        global $DB;
        // view - то что видно в редактировании и списке (Регион).
        // loc - код местоположения.
        ob_start();
        if ($value["VALUE"]) {
            $name = $DB->Query('select ln.name from b_sale_location sl inner join b_sale_loc_name ln on sl.id = ln.location_id where sl.code = ' . $value['VALUE'])->Fetch()['name'];
        }
        ?>
        <label id="<?= $strHTMLControlName['VALUE'] ?>_view_0" style="display:inline-block;min-width: 200px"><?= $name??$value['VALUE'] ?: "Выберите регион" ?></label>
        <input class="region-btn" type="button" onClick="open_region_win('<?= $strHTMLControlName['VALUE'] ?>')" value="...">
        <input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()">
        <input type="hidden" id="<?= $strHTMLControlName['VALUE'] ?>_loc_0" name="<?= $strHTMLControlName['VALUE'] ?>" value="<?= $value['VALUE'] ?>">
        <script type="text/javascript">
            function open_region_win(num){ window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&find_region=0&field_name=' + num + '&field_num=0', '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
        </script>
        <?
        return ob_get_clean();
    }
}