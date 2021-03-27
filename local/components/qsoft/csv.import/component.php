<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

global $USER;

$arParams['IMPORT']['TEMP_FILE_DIR'] = (empty($arParams['IMPORT']['TEMP_FILE_DIR']) ? "/upload/" : $arParams['IMPORT']['TEMP_FILE_DIR']);

$dir = $_SERVER["DOCUMENT_ROOT"].$arParams['IMPORT']['TEMP_FILE_DIR'];
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
$arResult=array();


if (CModule::IncludeModule("sale")) {
    $arStatuses = [];
    $dbStatuses = CSaleStatus::GetList([], [], false, false, ['ID']);
    while ($st = $dbStatuses->Fetch()) {
        $arStatuses[] = $st['ID'];
    }
    
    if ($_REQUEST['config_import'] == 'Y') {
        $arFile = $_FILES[$arParams['IMPORT']['FILE_NAME']];
        
        if (!empty($arFile['name'])) {
            if (strtolower(substr($arFile["name"], -4)) != ".csv") {
                $arResult["ERRORS"][] = "Ошибка! Неправильный формат файла";
            } else {
                $uploadfile = $dir.$arFile['name'];
                move_uploaded_file($arFile['tmp_name'], $uploadfile);
                $file = fopen($uploadfile, "r");
                
                while (($row = fgetcsv($file, '', $arParams['IMPORT']['DELIMITER'])) !== false) {
                    if (empty($arResult['HEADER'])) {
                        $arResult['HEADER'] = $row;
                    } else {
                        $arResult['DATA'][] = $row;
                    }
                }
                $arResult['FILE_HASH'] = md5_file($uploadfile);

                fclose($file);
                unlink($uploadfile);
                
                if (!empty($arResult['DATA'])) {
                    $arResult['ITEMS'] = [];
                    foreach ($arResult['DATA'] as $n => $adInfo) {
                        foreach ($adInfo as $i => $str) {
                            if (empty(mb_detect_encoding($str, array("ANSII","UTF-8"), true))) {
                                $adInfo[$i] = iconv('cp1251', 'utf-8', $str);
                            }
                        }
                        $revenueStr = str_replace(',', '.', $adInfo[2]);
                        $revenue = round(floatval($revenueStr), 0);

                        $item = [
                            'ID' => $adInfo[0],
                            'STATUS' => $adInfo[1],
                            'REVENUE' => $revenue
                        ];
                        if (!in_array($adInfo[1], $arStatuses)) {
                            $item['ERROR'] = 'Y';
                            $arResult['ERRORS'][] = 'Некорректный статус заказа ' . $adInfo[1] . ' для заказа №' . $adInfo[0];
                        }

                        $arResult['ITEMS'][] = $item;
                    }
                    
                    $resArr = [];
                    foreach ($arResult['ITEMS'] as $item) {
                        if ($item['ERROR'] != "Y") {
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
    }
}

$this->IncludeComponentTemplate();
