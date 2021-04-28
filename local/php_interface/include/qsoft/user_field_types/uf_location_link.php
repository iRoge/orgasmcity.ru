<?
AddEventHandler('main', 'OnUserTypeBuildList', array('locationLink', 'GetUserTypeDescription'));
class locationLink extends \Bitrix\Main\UserField\TypeBase
{
    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    public function GetUserTypeDescription()
    {
        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'location_link',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'locationLink',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Привязка к местоположению (для складов)',
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
    // Функция вызывается перед сохранением метаданных (настроек) свойства в БД
    // @return array - массив уникальных метаданных для свойства, будет сериализован и сохранен в БД
    public function PrepareSettings($arUserField)
    {
        // Нет доп настроек
        return array();
    }
    // Функция вызывается при выводе формы метаданных (настроек) свойства
    // @param bool $bVarsFromForm - флаг отправки формы
    // @return string - HTML для вывода
    public function GetSettingsHTML($arUserField, $arHtmlControl, $bVarsFromForm)
    {
        // Нет доп настроек
        return "";
    }
    // Получаем значение для данного поля из отдельной таблицы тут
    // return array - значения поля
    private function GetLocationLink($storage)
    {
        global $DB;
        $res = $DB->query("SELECT `LOCATION_CODE`, `DELIVERY`, `RESERVE` FROM b_respect_location_link WHERE `STORAGE_ID` = ".$storage);
        while ($item = $res->Fetch()) {
            $loc_links[] = array($item["LOCATION_CODE"], $item["DELIVERY"], $item["RESERVE"]);
        }
        return $loc_links;
    }
    // Функция вызывается при выводе формы редактирования значения свойства
    // @return string - HTML для вывода
    public function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        $loc_links = self::GetLocationLink($arUserField["VALUE_ID"]);
        $values = explode(", ", $arHtmlControl["VALUE"]);
        $i = 0;
        // view - то что видно в редактировании и списке (название местоположения). Сохраняется в битрикс через запятую.
        // loc - код местоположения. Сохраняется в отдельную таблицу.
        // del - галочка доставки для данной связи. Сохраняется в отдельную таблицу.
        // res - шалочка резервирования для данной связи. Сохраняется в отдельную таблицу.
        ob_start(); ?>
        <table class="location_table">
            <tr>
                <td class="align_left" colspan="2">Местоположение</td>
                <td>Доставка</td>
                <td>Резерв</td>
                <td></td>
            </tr>
        <? foreach ($loc_links as $key => $value) : ?>
            <tr class="<?= $arHtmlControl["NAME"] ?>_row" data-num="<?= $i ?>">
                <td id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left"><?= $values[$key] ?></td>
                <td><input class="tablebodybutton" type="button" OnClick="open_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
                    <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>" value="<?= $value[0] ?>"></td>
                <td><input type="checkbox" id="<?= $arHtmlControl["NAME"] ?>_del_<?= $i ?>" <?= $value[1] ? "checked" : "" ?>></td>
                <td><input type="checkbox" id="<?= $arHtmlControl["NAME"] ?>_res_<?= $i ?>" <?= $value[2] ? "checked" : "" ?>></td>
                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
            </tr>
            <? $i++; ?>
        <? endforeach ?>
            <tr class="<?= $arHtmlControl["NAME"] ?>_row" data-num="<?= $i ?>">
                <td id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left">Выберите местоположение:</td>
                <td><input class="tablebodybutton" type="button" onClick="open_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
                    <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>"></td>
                <td><input type="checkbox" id="<?= $arHtmlControl["NAME"] ?>_del_<?= $i ?>"></td>
                <td><input type="checkbox" id="<?= $arHtmlControl["NAME"] ?>_res_<?= $i ?>"></td>
                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
            </tr>
        </table>
        <input type="button" id="<?= $arHtmlControl["NAME"] ?>_but" data-next="<?= $i + 1 ?>" value="Ещё" onClick="loc_link_next('<?= $arHtmlControl["NAME"] ?>')">
        <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>" name="<?= $arHtmlControl["NAME"] ?>">
        <script type="text/javascript">
        $(document).ready(function(){
            $("#<?= $arHtmlControl["NAME"] ?>").parents("form").on("submit", function(){
                var output = [];
                $(".<?= $arHtmlControl["NAME"] ?>_row").each(function(){
                    var i = $(this).data("num");
                    if($("#<?= $arHtmlControl["NAME"] ?>_loc_" + i).val().trim() != "") {
                        output.push([
                            $("#<?= $arHtmlControl["NAME"] ?>_view_" + i).html(),
                            $("#<?= $arHtmlControl["NAME"] ?>_loc_" + i).val(),
                            llc("#<?= $arHtmlControl["NAME"] ?>_del_" + i),
                            llc("#<?= $arHtmlControl["NAME"] ?>_res_" + i)
                        ]);
                    }
                });
                $("#<?= $arHtmlControl["NAME"] ?>").val(JSON.stringify(output));
            });
        });
        function loc_link_next(name) {
            var el = $("#" + name + "_but");
            var num = el.data("next");
            //тут мы сразу добавляем label, потому что битрикс их генерит, чтобы поменять отображение
            el.prev("table").append(`<tr class="` + name + `_row" data-num="` + num + `">
                <td id="` + name + `_view_` + num + `" class="align_left">Выберите местоположение:</td>
                <td><input class="tablebodybutton" type="button" onClick="open_win_` + name + `(` + num + `)" value="...">
                    <input type="hidden" id="` + name + `_loc_` + num + `"></td>
                <td><input type="checkbox" id="` + name + `_del_` + num + `" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="` + name + `_del_` + num + `"></label></td>
                <td><input type="checkbox" id="` + name + `_res_` + num + `" class="adm-designed-checkbox">
                    <label class="adm-designed-checkbox-label" for="` + name + `_res_` + num + `"></label></td>
                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
            </tr>`);
            el.data("next", num + 1);
        }
        function llc(el) {return $(el).prop("checked") === true ? 1 : 0}
        function open_win_<?= $arHtmlControl["NAME"] ?>(num){ window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&field_name=<?= $arHtmlControl["NAME"] ?>&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
        </script>
        <style>.location_table td{text-align:center}.<?= $arHtmlControl["NAME"] ?>_row td:first-child{min-width:171px}.location_table .align_left{text-align:left!important}</style>
        <?
        return ob_get_clean();
    }
    // Функция вызывается при выводе формы редактирования значения множественного свойства
    // @return string - HTML для вывода
    public function GetEditFormHTMLMulty($arUserField, $arHtmlControl)
    {
        return "Это свойство должно быть НЕ множественным (оно множественное по умолчанию). Пересоздайте свойство.";
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
    // Функция вызывается при выводе фильтра на странице списка
    // @return string - HTML для вывода
    // НЕ РАБОТАЕТ В СКЛАДАХ (Bitrix 18.0.9)
    public function GetFilterHTML($arUserField, $arHtmlControl)
    {
        return "1a1";
    }
    // Функция вызывается при выводе значения множественного свойства в списке элементов
    // @return string - HTML для вывода
    // НЕ РАБОТАЕТ В СКЛАДАХ (Bitrix 18.0.9)
    public function GetAdminListViewHTMLMulty($arUserField, $arHtmlControl)
    {
        return "3c3";
    }
    // Функция вызывается при выводе значения свойства в списке элементов в режиме редактирования
    // @return string - HTML для вывода
    // НЕ РАБОТАЕТ В СКЛАДАХ (Bitrix 18.0.9)
    public function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return "4d4";
    }
    // Функция вызывается при выводе множественного значения свойства в списке элементов в режиме редактирования
    // @return string - HTML для вывода
    // НЕ РАБОТАЕТ В СКЛАДАХ (Bitrix 18.0.9)
    public function GetAdminListEditHTMLMulty($arUserField, $arHtmlControl)
    {
        return "5e5";
    }
    // Функция должна вернуть представление значения поля для поиска
    // @return string - посковое содержимое
    // НЕ РАБОТАЕТ В СКЛАДАХ (Bitrix 18.0.9)
    public function OnSearchIndex($arUserField)
    {
        return "6f6";
    }
    // Функция вызывается перед сохранением значений в БД
    // @param mixed $value - значение свойства
    // @return string - значение для вставки в БД
    public function OnBeforeSave($arUserField, $value)
    {
        //Поскольку для битрикса свойство НЕ множественное, то функцию вызовет только 1 раз и мы можем сохранить всё нормально в отдельную таблицу, а то что нужно - отобразить
        global $DB;
        //склад
        $arUserField["VALUE_ID"] = intval($arUserField["VALUE_ID"]);
        //значения
        $value = json_decode($value);
        //обработка значений для запроса
        $view = array();
        $insert = array();
        foreach ($value as $element) {
            $element[2] = intval($element[2]);
            $element[3] = intval($element[3]);
            if ($element[2] == 0 && $element[3] == 0) {
                continue;
            }
            $element[1] = $DB->ForSql($element[1]);
            if ($insert[$element[1]]) {
                continue;
            }
            $view[] = $DB->ForSql($element[0]);
            $insert[$element[1]] = "(".$arUserField["VALUE_ID"].",'".$element[1]."','".$element[2]."','".$element[3]."')";
        }
        $DB->query("DELETE FROM b_respect_location_link WHERE `STORAGE_ID` = ". $arUserField["VALUE_ID"]);
        if (!empty($insert)) {
            $DB->query("INSERT INTO b_respect_location_link(`STORAGE_ID`, `LOCATION_CODE`, `DELIVERY`, `RESERVE`) VALUES ".implode(",", $insert));
        }
        return implode(", ", $view);
    }
}
