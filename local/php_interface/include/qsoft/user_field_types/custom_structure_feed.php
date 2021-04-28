<?php
AddEventHandler("iblock", "OnIBlockPropertyBuildList", array("customStructureFeed", "GetUserTypeDescription"));

class customStructureFeed
{
    public function GetUserTypeDescription()
    {
        return array(
            "PROPERTY_TYPE" => "S", #-----один из стандартных типов
            "USER_TYPE" => "CUSTOM_STRUCTURE_FEED", #-----идентификатор типа свойства
            "DESCRIPTION" => "Уникальная структура каталога",
            "GetPropertyFieldHtml" => array("customStructureFeed", "GetPropertyFieldHtml"),
            "ConvertToDB" => array("customStructureFeed", "ConvertToDB"),
            "ConvertFromDB" => array("customStructureFeed", "ConvertFromDB"),
        );
    }

    public function ConvertToDB($arProperty, $value)
    {
        $value['VALUE'] = serialize($value['VALUE']);
        return $value;
    }

    public function ConvertFromDB($arProperty, $value)
    {
        $value['VALUE'] = unserialize($value['VALUE']);
        return $value;
    }

    // Функция вызывается при выводе формы редактирования значения свойства
    // @return string - HTML для вывода
    public function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        ob_start();

        $rsSection = CIBlockSection::GetList([], ['IBLOCK_ID' => IBLOCK_CATALOG], '', ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'DEPTH_LEVEL']);
        $tmpDeptLevel2 = [];
        while ($arSection = $rsSection->Fetch()) {
            if ($arSection['DEPTH_LEVEL'] == 1) {
                $arCatalogStructure[$arSection['ID']]['NAME'] = $arSection['NAME'];
            }
            if ($arSection['DEPTH_LEVEL'] == 2) {
                $arCatalogStructure[$arSection['IBLOCK_SECTION_ID']]['CHILD'][$arSection['ID']] = [
                    'NAME' => $arSection['NAME'],
                ];
                $tmpDeptLevel2[$arSection['ID']] = $arSection['IBLOCK_SECTION_ID'];
            }
            if ($arSection['DEPTH_LEVEL'] == 3) {
                $arCatalogStructure[$tmpDeptLevel2[$arSection['IBLOCK_SECTION_ID']]]['CHILD'][$arSection['IBLOCK_SECTION_ID']]['CHILD'][$arSection['ID']] = [
                    'NAME' => $arSection['NAME'],
                ];
            }
        }
        ?>
        <table>
            <? foreach ($arCatalogStructure as $idDeptLevel1 => $arDeptLevel1) : ?>
                <tr>
                    <td style="padding-right: 10px"><?= $idDeptLevel1; ?></td>
                    <td><?= $arDeptLevel1['NAME']; ?></td>
                    <td><input type="text" name="<?= $strHTMLControlName['VALUE'] . '[' . $idDeptLevel1 . ']' ?>"
                               value="<?= $value['VALUE'][$idDeptLevel1] ?>"></td>
                </tr>
                <? foreach ($arDeptLevel1['CHILD'] as $idDeptLevel2 => $arDeptLevel2) : ?>
                    <tr>
                        <td style="padding-right: 10px"><?= $idDeptLevel2; ?></td>
                        <td style="padding-left: 40px"><?= $arDeptLevel2['NAME']; ?></td>
                        <td><input type="text" name="<?= $strHTMLControlName['VALUE'] . '[' . $idDeptLevel2 . ']' ?>"
                                   value="<?= $value['VALUE'][$idDeptLevel2] ?>"></td>
                    </tr>
                    <? foreach ($arDeptLevel2['CHILD'] as $idDeptLevel3 => $arDeptLevel3) : ?>
                        <tr>
                            <td style="padding-right: 10px"><?= $idDeptLevel3; ?></td>
                            <td style="padding-left: 80px"><?= $arDeptLevel3['NAME']; ?></td>
                            <td><input type="text"
                                       name="<?= $strHTMLControlName['VALUE'] . '[' . $idDeptLevel3 . ']' ?>"
                                       value="<?= $value['VALUE'][$idDeptLevel3] ?>"></td>
                        </tr>
                    <? endforeach; ?>
                <? endforeach; ?>
            <? endforeach; ?>
        </table>
        <?
        return ob_get_clean();
    }
}

?>
