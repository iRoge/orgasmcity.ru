<?
AddEventHandler('main', 'OnUserTypeBuildList', array('stockGroupName', 'GetUserTypeDescription'));
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

class stockGroupName extends \Bitrix\Main\UserField\TypeBase
{
    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    public function GetUserTypeDescription()
    {
        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'stock_group_name',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'stockGroupName',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Группы складов',
            // базовый тип на котором будут основаны операции фильтра
            'BASE_TYPE' => 'string',
        );
    }
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
        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList([
            'filter' => ['=NAME' => 'StockGroups']
        ])->fetch();
        if (isset($hlblock['ID'])) {
            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $res = $entity_data_class::getList(array('filter'=>array()));
            $arAllRows = $res->fetchAll();
        }
        ob_start();
        ?>
        <select name="UF_STOCK_GROUP">
            <option value="">нет</option>
            <? foreach ($arAllRows as $groupName) :?>
                <option <?= $groupName["UF_NAME"] == $arUserField["VALUE"] ? 'selected' : ''?> value="<?= $groupName["UF_NAME"];?>"><?= $groupName["UF_NAME"];?></option>
            <? endforeach; ?>
        </select>
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