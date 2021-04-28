<?
AddEventHandler('main', 'OnUserTypeBuildList', array('MultiLocationLink', 'GetUserTypeDescription'));
class MultiLocationLink extends \Bitrix\Main\UserField\TypeBase
{
    // Функция регистрируется в качестве обработчика события OnUserTypeBuildList
    public function GetUserTypeDescription()
    {
        return array(
            // уникальный идентификатор
            'USER_TYPE_ID' => 'multi_location_link',
            // имя класса, методы которого формируют поведение типа
            'CLASS_NAME' => 'MultiLocationLink',
            // название для показа в списке типов пользовательских свойств
            'DESCRIPTION' => 'Множественное местоположение',
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

    // Функция вызывается при выводе формы редактирования значения свойства
    // @return string - HTML для вывода
    public function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        if (!empty($arUserField["VALUE"])) {
            $values = unserialize($arUserField["VALUE"]);
        }

        $i = 0;
        ob_start(); ?>
        <table class="location_table">
            <? foreach ($values as $key => $value) : ?>
                <tr class="<?= $arHtmlControl["NAME"] ?>_row" data-num="<?= $i ?>">
                    <td id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left"><?= $value[0] ?></td>
                    <td><input class="tablebodybutton" type="button" OnClick="open_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
                        <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>" value="<?= $value[1] ?>"></td>
                    <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
                </tr>
                <? $i++; ?>
            <? endforeach ?>
            <tr class="<?= $arHtmlControl["NAME"] ?>_row" data-num="<?= $i ?>">
                <td id="<?= $arHtmlControl["NAME"] ?>_view_<?= $i ?>" class="align_left">Выберите местоположение:</td>
                <td><input class="tablebodybutton" type="button" onClick="open_win_<?= $arHtmlControl["NAME"] ?>(<?= $i ?>)" value="...">
                    <input type="hidden" id="<?= $arHtmlControl["NAME"] ?>_loc_<?= $i ?>"></td>
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
                            ]);
                        }
                    });
                    $("#<?= $arHtmlControl["NAME"] ?>").val(JSON.stringify(output));
                });
            });
            function loc_link_next(name) {
                var el = $("#" + name + "_but");
                var num = el.data("next");
                el.prev("table").append(`<tr class="` + name + `_row" data-num="` + num + `">
                <td id="` + name + `_view_` + num + `" class="align_left">Выберите местоположение:</td>
                <td><input class="tablebodybutton" type="button" onClick="open_win_` + name + `(` + num + `)" value="...">
                    <input type="hidden" id="` + name + `_loc_` + num + `"></td>
                <td><input type="button" value="Удалить" onClick="$(this).parent('td').parent('tr').remove()"></td>
            </tr>`);
                el.data("next", num + 1);
            }

            function open_win_<?= $arHtmlControl["NAME"] ?>(num){ window.open('/local/php_interface/include/qsoft/tools/location_search.php?lang=ru&field_name=<?= $arHtmlControl["NAME"] ?>&field_num=' + num, '', 'scrollbars=yes,resizable=yes,width=760,height=500,top='+Math.floor((screen.height - 560)/2-14)+',left='+Math.floor((screen.width - 760)/2-5));}
        </script>
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
    // Функция вызывается перед сохранением значений в БД
    // @param mixed $value - значение свойства
    // @return string - значение для вставки в БД
    public function OnBeforeSave($arUserField, $value)
    {
        return serialize(json_decode($value));
    }
}
