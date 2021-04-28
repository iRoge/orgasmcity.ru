<?
AddEventHandler('main', 'OnUserTypeBuildList', array('regionLink', 'GetUserTypeDescription'));
class regionLink extends \Bitrix\Main\UserField\TypeBase
{
    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    public function GetUserTypeDescription()
    {
        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'region_link',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'regionLink',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Регионы',
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
        $i = 0;
        // view - то что видно в редактировании и списке (Регион).
        // loc - код местоположения.
        ob_start(); ?>
            <lable id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left"><?= $arUserField['VALUE'] ?: "Выберите регион" ?></lable>
            <input class="region-btn" type="button" onClick="open_region_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
            <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>" name="<?= $arHtmlControl["NAME"] ?>" value="<?= $arUserField['VALUE'] ?>">
        <script type="text/javascript">
            function open_region_win_<?= $arHtmlControl["NAME"] ?>(num){ window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&find_region=3&field_name=<?= $arHtmlControl["NAME"] ?>&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
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
