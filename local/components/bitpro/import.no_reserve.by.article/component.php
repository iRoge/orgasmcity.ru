<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

global $USER;
global $CACHE_MANAGER;

$supportedProps = [
    'DISABLE_DELIVERY' => 'Доставки',
    'NO_RESERVE' => 'Резервирования',
];


$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);
$arParams['IMPORT']['TEMP_FILE_DIR'] = ($arParams['IMPORT']['TEMP_FILE_DIR'] == "" ? "/upload/" : $arParams['IMPORT']['TEMP_FILE_DIR']);
$arParams["PROPERTY_NAME"] = trim($arParams["PROPERTY_NAME"]);

if (empty($arParams['IMPORT']['DURATION'])) {
    $arParams['IMPORT']['DURATION'] = 60;
}

$dir = $_SERVER["DOCUMENT_ROOT"] . $arParams['IMPORT']['TEMP_FILE_DIR'];
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$arResult = array();

$iBlockIdAds = $arParams["IBLOCK_ID"];      // ID инфоблока Объявлений

$arResult["ERRORS"] = [];

// свойство
$noReservePropertyId = $noReservePropertyValueId = false;
$filter = [
    'IBLOCK_ID' => IBLOCK_CATALOG,
    'CODE' => $arParams["PROPERTY_NAME"]
];
$aItem = CIBlockProperty::GetList(array('SORT' => 'ASC'), $filter)->Fetch();
if ($aItem && isset($aItem['ID'])) {
    $noReservePropertyId = $aItem['ID'];

    $dbEnumList = CIBlockProperty::GetPropertyEnum(
        $arParams["PROPERTY_NAME"],
        [],
        ['IBLOCK_ID' => $arParams["IBLOCK_ID"], 'XML_ID' => 'Y']
    );
    if ($arEnumList = $dbEnumList->GetNext()) {
        $noReservePropertyValueId = $arEnumList['ID'];
    }
}

if (!$noReservePropertyId || !$noReservePropertyValueId) {
    $propName = $supportedProps[$arParams["PROPERTY_NAME"]];
    $arResult["ERRORS"][] = Loc::getMessage('NO_PROP', ['#PROP_NAME#' => $propName]);
}


if (empty($arResult["ERRORS"]) && CModule::IncludeModule("iblock")) {
    $obCFile = new CFile();
    $obCIBlockElement = new CIBlockElement();
    $obCIBlockSection = new CIBlockSection();
    $obCUser = new CUser();

    $arrCities = [];
    $arrRubrics = [];

    if ($_REQUEST['config_import'] == 'Y') {
        $arFile = $_FILES[$arParams['IMPORT']['FILE_NAME']];

        if (!empty($arFile['name'])) {
            if (mb_strtolower(mb_substr($arFile["name"], -4)) != ".csv") {
                $arResult["ERRORS"][] = "Ошибка! Неправильный формат файла";
            } else {
                $uploadfile = $dir . $arFile['name'];
                move_uploaded_file($arFile['tmp_name'], $uploadfile);
                $file = fopen($uploadfile, "r");

                while (($row = fgetcsv($file, '', $arParams['IMPORT']['DELIMITER'])) !== false) {
                    $arResult['DATA'][] = $row;
                    $arResult['IDS'][] = $row[0];
                }
                $arResult['FILE_HASH'] = md5_file($uploadfile);

                fclose($file);
                unlink($uploadfile);

                if (!empty($arResult['DATA'])) {
                    //GET PRODUCT ITEMS
                    $productsArr = [];
                    $arFilterProd = [
                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                        "=PROPERTY_ARTICLE" => $arResult['IDS'],
                    ];
                    $arSelectProd = [
                        "ID",
                        "NAME",
                        "PROPERTY_ARTICLE",
                        "PROPERTY_" . $arParams["PROPERTY_NAME"]
                    ];
                    $arSortProd = [
                        "SORT" => "ASC"
                    ];

                    $res = $obCIBlockElement->GetList(
                        $arSortProd,
                        $arFilterProd,
                        false,
                        false,
                        $arSelectProd
                    );
                    while ($ar_res = $res->GetNext()) {
                        $productsArr[$ar_res["PROPERTY_ARTICLE_VALUE"]] = $ar_res;
                    }

                    $arResult['ITEMS'] = [];
                    foreach ($arResult['DATA'] as $n => $adInfo) {
                        foreach ($adInfo as $i => $str) {
                            if (empty(mb_detect_encoding($str, array("ANSII", "UTF-8"), true))) {
                                $adInfo[$i] = iconv('cp1251', 'utf-8', $str);
                            }
                        }

                        // Проверка на артикул
                        if ($adInfo[0] != '') {
                            if (isset($productsArr[$adInfo[0]])) {
                                $arResult['ITEMS'][$n]['ID'] = $productsArr[$adInfo[0]]['ID'];
                                $arResult['ITEMS'][$n]['ITEM'] = $productsArr[$adInfo[0]]['NAME'];
                                $arResult['ITEMS'][$n]['ART'] = $productsArr[$adInfo[0]]['PROPERTY_ARTICLE_VALUE'];
                                $arResult['ITEMS'][$n][$arParams["PROPERTY_NAME"]] = $productsArr[$adInfo[0]]['PROPERTY_' . $arParams["PROPERTY_NAME"] . '_VALUE'];
                            } else {
                                $arResult["ERRORS"][$adInfo[0]] = "Ошибка! Товар с артикулом «" . $adInfo[0] . "» не найден";
                                $arResult["ITEMS"][$n]['RUBRIC'] = "<span class='r-text'>Ошибка! Артикул «" . $adInfo[0] . "» не найден</span>";
                                $arResult['ITEMS'][$n]['ERROR'] = "Y";
                            }
                        } else {
                            $arResult['ITEMS'][$n]['ERROR'] = "Y";
                        }
                    }

                    $resArr = [];
                    foreach ($arResult['ITEMS'] as $item) {
                        if ($item['ERROR'] != "Y" && empty($item[$arParams["PROPERTY_NAME"]])) {
                            $resArr[] = $item;
                        }
                    }

                    $tmpfname = tempnam($dir, "IMP");
                    $handle = fopen($tmpfname, "w");
                    fwrite($handle, serialize($resArr));
                    fclose($handle);

                    $arResult['ITEMS_COUNT'] = count($resArr);
                    $arResult['TMP_FILE'] = $tmpfname;
                }
            }
        } else {
            $arResult["ERRORS"][] = "Выберите файл для импорта";
        }
    } elseif ($_REQUEST['start_import'] == 'Y') {
        if (!empty($_REQUEST['tpm_file'])) {
            $resArr = file_get_contents($_REQUEST['tpm_file']);
            $adsInfo = unserialize($resArr);

            $arResult['TOTAL'] = [];

            $connection = \Bitrix\Main\Application::getConnection();

            foreach ($adsInfo as $info) {
                if (!empty($info[$arParams["PROPERTY_NAME"]])) {
                    continue;
                }

                $ELEMENT_ID = $info['ID'];

                $sql = "UPDATE 
                b_iblock_element_prop_s16 SET PROPERTY_{$noReservePropertyId} = {$noReservePropertyValueId}
                WHERE IBLOCK_ELEMENT_ID = {$ELEMENT_ID}";

                $arResult['TOTAL'][] = (bool)$connection->query($sql);
            }

            unlink($_REQUEST['tpm_file']);
            $CACHE_MANAGER->ClearByTag("iblock_id_" . $arParams["IBLOCK_ID"]);
            $CACHE_MANAGER->ClearByTag("catalogAll");
        }
    }
}

$this->IncludeComponentTemplate();
