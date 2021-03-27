<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/tools/SimpleXLSX.php';
global $USER;

$arParams['IMPORT']['TEMP_FILE_DIR'] = (empty($arParams['IMPORT']['TEMP_FILE_DIR']) ? "/upload/" : $arParams['IMPORT']['TEMP_FILE_DIR']);

$dir = $_SERVER["DOCUMENT_ROOT"].$arParams['IMPORT']['TEMP_FILE_DIR'];
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$arResult=array();


if (CModule::IncludeModule("sale")) {
    if ($_REQUEST['config_import'] == 'Y') {
        $arFile = $_FILES[$arParams['IMPORT']['FILE_NAME']];
        if (!empty($arFile['name'])) {
            if (strtolower(substr($arFile["name"], -5)) != ".xlsx") {
                $arResult["ERRORS"][] = "Ошибка! Неправильный формат файла";
            } else {
                $uploadfile = $dir.$arFile['name'];
                move_uploaded_file($arFile['tmp_name'], $uploadfile);

                $xlsx = SimpleXLSX::parse($uploadfile);

                $arRow = $xlsx->rows();

                foreach ($arRow as $row) {
                    if (empty($arResult['HEADER'])) {
                        $arResult['HEADER'] = $row;
                    } else {
                        $arResult['DATA'][] = $row;
                    }
                }
                $arResult['FILE_HASH'] = md5_file($uploadfile);
                unlink($uploadfile);
                
                if (!empty($arResult['DATA'])) {
                    $arResult['ITEMS'] = [];
                    foreach ($arResult['DATA'] as $n => $adInfo) {
                        foreach ($adInfo as $i => $str) {
                            if (empty(mb_detect_encoding($str, array("ANSII","UTF-8"), true))) {
                                $adInfo[$i] = iconv('cp1251', 'utf-8', $str);
                            }
                        }
                        $item = [
                            'ID' => $adInfo[0],
                        ];

                        $arResult['ITEMS'][] = $item;
                    }

                    $resArr = [];
                    foreach ($arResult['ITEMS'] as $item) {
                        if ($item['ERROR'] != "Y") {
                            $resArr[] = $item;
                        }
                    }
                    
                    $tmpfname = tempnam($dir, "TMP");
                    $handle = fopen($tmpfname, "w");
                    fwrite($handle, serialize($resArr));
                    fclose($handle);
                    
                    $arResult['ITEMS_COUNT'] = count($resArr);
                    $arResult['TMP_FILE'] = $tmpfname;
                }
            }
        } else {
            $arResult["ERRORS"][] = "Выберите файл с заказами";
        }
    }
}

$this->IncludeComponentTemplate();
