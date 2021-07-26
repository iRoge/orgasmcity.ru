<?php

use Bitrix\Main\Config\Option;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/tools/SimpleXLSX.php';
$APPLICATION->SetTitle('Импорт линейных размеров');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

class ProductPropertyImport
{
    private $iblockId;
    private $arFileProduct;
    private $arNeededProductArticle;
    private $arNeededProduct;
    private $arCompareProduct;
    private $arProductForUpdate;

    public function go()
    {
        $this->iblockId = Option::get('likee.exchange', 'IBLOCK_ID', 0);
        if (empty($_REQUEST['action'])) {
            $this->showStart();
        } else {
            if ($_REQUEST['action'] == 'readFile') {
                $this->readFile();
                $this->loadProduct();
                $this->compareProduct();
                $this->createTmpFile();
                $this->showFile();
            } elseif ($_REQUEST['action'] == 'startImport') {
                $this->readTmpFile();
                if (!empty($this->arProductForUpdate)) {
                    $this->updateProductProperty($_REQUEST['onePart']);
                }
            }
        }
    }

    private function showStart()
    {
        $obCFile = new CFile();
        echo <<<html
            <div>
                <p>
                    Файл импорта должен иметь формат xlsx <br><br><br>
                    Первая строка - названия столбцов (В ИМПОРТ НЕ ВКЛЮЧАЕТСЯ И ПРИ ЧТЕНИИ ИГНОРИРУЕТСЯ)<br><br>
                    Пустое значение - при импорте ничего не меняет<br><br>
                    0 - при импорте удаляет значение<br><br><br>
                    Столбцы:
                    <table border=1>
                        <tr>
                            <th>Артикул</th>
                            <th>Длина</th>
                            <th>Высота</th>
                            <th>Ширина</th>
                            <th>Описание</th>
                        </tr>
                    </table>
                    <br>
                    Названия измерений соответстуют названиям в админке<br>
                    Порядок измерений соответствует порядку в карточке товара: Длина х Высота x Ширина </b><br><br>
                    Пример заполнения файла импорта:<br>
                    <table border=1>
                        <tr>
                            <th>Артикул</th>
                            <th>Длина</th>
                            <th>Высота</th>
                            <th>Ширина</th>
                            <th>Описание</th>
                        </tr>
                        <tr>
                            <td>9206_beige</td>
                            <td>27</td>
                            <td>18</td>
                            <td>2.5</td>
                            <td>&lt;p&gt;Текст с поддержкой HTML&lt;/p&gt;</td>
                        </tr>
                        <tr>
                            <td>1234_abcd</td>
                            <td>12</td>
                            <td>34</td>
                            <td>56</td>
                            <td>Текст описания</td>
                        </tr>
                        <tr>
                            <td>A-123-00-11</td>
                            <td></td>
                            <td>18</td>
                            <td>2.5</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>12345/67</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                </p>
            </div>
            <p>Выберите файл импорта (*.xlsx):</p>
            <form name="import_settings" method="post" enctype="multipart/form-data">
                {$obCFile->InputFile('product_size_import', 0, 0)}
                <input type="hidden" name="action" value="readFile" />
                <input type="submit" name="config" value="Далее" />
            </form>
html;
    }

    private function showFile()
    {
        global $APPLICATION;
        \Bitrix\Main\UI\Extension::load("ui.progressbar");
        echo <<<html
            <div class="result">
                <div class="import-process">
                    <div id="import-progress"></div>
                </div>
            </div>
            <div class="result-count"></div>
            <input type="button" id="import-cancel" value="Отмена" onclick="canceled = true; window.location='{$APPLICATION->GetCurPage(false)}?lang=ru';" />
            <div class="preview">
                <table border=1>
                    <tr>
                        <td class="back_green">Зеленый</td>
                        <td>Свойство будет заполнено впервые или изменений в импорте нет</td>
                    </tr>
                    <tr>
                        <td class="back_white">Белый</td>
                        <td>Свойство будет очищено</td>
                    </tr>
                    <tr>
                        <td class="back_yellow">Желтый</td>
                        <td>Свойство будет перезаписано</td>
                    </tr>
                    <tr>
                        <td class="back_red">Красный</td>
                        <td>Ошибка, свойство изменено не будет</td>
                    </tr>
                </table>
                <br>
                Если навести курсор на значение, в ячейке появится старое значение (если оно существует у данного артикула в базе)
                <br>
                <h2>Предварительный просмотр:</h2>
                <table border=1>
                    <tr>
                        <th>ARTICLE</th>
                        <th>LENGTH</th>
                        <th>HEIGHT</th>
                        <th>WIDTH</th>
                        <th>DESCRIPTION</th>
                        <td>ERRORS</td>
                    </tr>
html;
        foreach ($this->arCompareProduct as $idProduct => $arProductInfo) {
            echo '<td class="back_' . (empty($arProductInfo['ERRORS']['ARTICLE']) ? 'green' : 'red') . '"><span class="new_info">' . $arProductInfo['ARTICLE'] . '</span></td>';
            echo '<td class="';
            if (empty($arProductInfo['ERRORS']['ARTICLE'])) {
                if (empty($arProductInfo['ERRORS']['LENGTH'])) {
                    if ($arProductInfo['NEW_LENGTH'] != '0') {
                        if (!empty($arProductInfo['NEW_LENGTH'])) {
                            if (!empty($arProductInfo['OLD_LENGTH']) && $arProductInfo['NEW_LENGTH'] != $arProductInfo['OLD_LENGTH']) {
                                echo 'back_yellow';
                            } else {
                                echo 'back_green';
                            }
                        } else {
                            echo 'back_green';
                        }
                    } else {
                        if (!empty($arProductInfo['OLD_LENGTH'])) {
                            echo 'back_white';
                        } else {
                            echo 'back_green';
                        }
                    }
                } else {
                    echo 'back_red';
                }
            } else {
                echo 'back_red';
            }
            echo '"><span class="new_info">' . $arProductInfo['NEW_LENGTH'] . '</span><span class="old_info">' . $arProductInfo['OLD_LENGTH'] . '</span></td>';
            echo '<td class="';
            if (empty($arProductInfo['ERRORS']['ARTICLE'])) {
                if (empty($arProductInfo['ERRORS']['HEIGHT'])) {
                    if ($arProductInfo['NEW_HEIGHT'] != '0') {
                        if (!empty($arProductInfo['NEW_HEIGHT'])) {
                            if (!empty($arProductInfo['OLD_HEIGHT']) && $arProductInfo['NEW_HEIGHT'] != $arProductInfo['OLD_HEIGHT']) {
                                echo 'back_yellow';
                            } else {
                                echo 'back_green';
                            }
                        } else {
                            echo 'back_green';
                        }
                    } else {
                        if (!empty($arProductInfo['OLD_HEIGHT'])) {
                            echo 'back_white';
                        } else {
                            echo 'back_green';
                        }
                    }
                } else {
                    echo 'back_red';
                }
            } else {
                echo 'back_red';
            }
            echo '"><span class="new_info">' . $arProductInfo['NEW_HEIGHT'] . '</span><span class="old_info">' . $arProductInfo['OLD_HEIGHT'] . '</span></td>';
            echo '<td class="';
            if (empty($arProductInfo['ERRORS']['ARTICLE'])) {
                if (empty($arProductInfo['ERRORS']['WIDTH'])) {
                    if ($arProductInfo['NEW_WIDTH'] != '0') {
                        if (!empty($arProductInfo['NEW_WIDTH'])) {
                            if (!empty($arProductInfo['OLD_WIDTH']) && $arProductInfo['NEW_WIDTH'] != $arProductInfo['OLD_WIDTH']) {
                                echo 'back_yellow';
                            } else {
                                echo 'back_green';
                            }
                        } else {
                            echo 'back_green';
                        }
                    } else {
                        if (!empty($arProductInfo['OLD_WIDTH'])) {
                            echo 'back_white';
                        } else {
                            echo 'back_green';
                        }
                    }
                } else {
                    echo 'back_red';
                }
            } else {
                echo 'back_red';
            }
            echo '"><span class="new_info">' . $arProductInfo['NEW_WIDTH'] . '</span><span class="old_info">' . $arProductInfo['OLD_WIDTH'] . '</span></td>';
            echo '<td class="';
            if (empty($arProductInfo['ERRORS']['ARTICLE'])) {
                if ($arProductInfo['NEW_DESCRIPTION'] != '0') {
                    if (!empty($arProductInfo['NEW_DESCRIPTION'])) {
                        if (!empty($arProductInfo['OLD_DESCRIPTION']) && $arProductInfo['NEW_DESCRIPTION'] != $arProductInfo['OLD_DESCRIPTION']) {
                            echo 'back_yellow';
                        } else {
                            echo 'back_green';
                        }
                    } else {
                        echo 'back_green';
                    }
                } else {
                    if (!empty($arProductInfo['OLD_DESCRIPTION'])) {
                        echo 'back_white';
                    } else {
                        echo 'back_green';
                    }
                }
            } else {
                echo 'back_red';
            }
            echo '"><span class="new_info">' . htmlspecialchars($arProductInfo['NEW_DESCRIPTION']) . '</span><span class="old_info">' . htmlspecialchars($arProductInfo['OLD_DESCRIPTION']) . '</span></td><td>';
            foreach ($arProductInfo['ERRORS'] as $error) {
                echo $error . '<br>';
            }
            echo '</td></tr>';
        }
        $total = count($this->arProductForUpdate);
        $onePart = 1;
        if ($total > 10) {
            while ($total / $onePart > 10) {
                $onePart = $onePart + 1;
            }
        }
        echo <<<html
                </table>
            </div>
            <br><br><br>
            <input type="submit" id="start_import" value="Начать импорт"/>
            <script>
                var progressBar = new BX.UI.ProgressBar({
                    color: BX.UI.ProgressBar.Color.PRIMARY,
                    value: 0,
                    maxValue: $total,
                    statusType: BX.UI.ProgressBar.Status.COUNTER,
                    textBefore: "Обновлено товаров",
                    fill: true,
                    column: true
                });
        
                $(function(){
                    let progress = $('#import-progress');
                    let itemsCount = $total;
                
                    if(itemsCount) {
                        progress.append(progressBar.getContainer());
                    }
                    
                    let button = $('#start_import');
                
                    button.on('click', function (e) {
                        $('.preview').remove();
                        $('#import-cancel').css('display', 'block');
                        $('.result').css('display', 'block');
                        e.preventDefault();
                        button.remove();
                        processChunk();
                    });
                
                    var processChunk = function () {
                        BX.ajax.post($(location).attr('href'), 'action=startImport&onePart=$onePart', function(response) {
                           progressBar.update(Number($(response).find('.processed').val()));
                           if($(response).find('.done').val() !== 'Y'){
                               $('.result-count').html('<h2>'+$(response).find(".count").html()+'</h2>');
                               processChunk();
                           } else {
                               $('#import-cancel').remove();
                               $('.result-count').html('<h2>'+$(response).find(".count").html()+'<br>Импорт завершен</h2>');
                           } 
                        });
                    }
                });
            </script>
html;
    }

    private function readFile()
    {
        $this->log('Чтение файла импорта');
        $arFile = $_FILES['product_size_import'];
        if (!empty($arFile['name'])) {
            if (strtolower(substr($arFile["name"], -5)) != ".xlsx") {
                $this->log('Ошибка! Неправильный формат файла');
            } else {
                $xlsx = SimpleXLSX::parse($arFile['tmp_name']);
                $arRow = $xlsx->rows();
                foreach ($arRow as $row) {
                    if (empty($arResult['HEADER'])) {
                        $arResult['HEADER'] = $row;
                    } else {
                        $arResult['DATA'][] = $row;
                    }
                }
                if (!empty($arResult['DATA'])) {
                    $arResult['ITEMS'] = [];
                    foreach ($arResult['DATA'] as $n => $adInfo) {
                        foreach ($adInfo as $i => $str) {
                            if (empty(mb_detect_encoding($str, array("ANSII", "UTF-8"), true))) {
                                $adInfo[$i] = iconv('cp1251', 'utf-8', $str);
                            }
                        }
                        $item = [
                            'ARTICLE' => $adInfo[0],
                            'LENGTH' => $adInfo[1],
                            'HEIGHT' => $adInfo[2],
                            'WIDTH' => $adInfo[3],
                            'DESCRIPTION' => $adInfo[4],
                        ];
                        $this->arNeededProductArticle[] = $adInfo[0];
                        $arResult['ITEMS'][] = $item;
                    }
                }
            }
        } else {
            $this->log('Выберите файл с заказами');
        }
        $this->log('Чтение файла завершено');
        $this->arFileProduct = $arResult;
    }

    private function loadProduct()
    {
        $this->log('Загрузка товаров');
        $arSelect = [
            'IBLOCK_ID',
            'ID',
            'PROPERTY_ARTICLE',
            'DETAIL_TEXT',
            'PROPERTY_HEIGHT',
            'PROPERTY_WIDTH',
            'PROPERTY_LENGTH',
        ];

        $rsElements = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $this->iblockId,
            ],
            false,
            false,
            $arSelect
        );

        while ($arElement = $rsElements->fetch()) {
            if (in_array($arElement['PROPERTY_ARTICLE_VALUE'], $this->arNeededProductArticle, true)) {
                $this->arNeededProduct[$arElement['PROPERTY_ARTICLE_VALUE']] = $arElement;
            }
        }

        $this->log('Загружено ' . count($this->arNeededProduct) . ' товаров');
    }

    private function compareProduct()
    {
        foreach ($this->arFileProduct['ITEMS'] as $fileProduct) {
            $this->arCompareProduct[$fileProduct['ARTICLE']] = [];
            if (isset($this->arNeededProduct[$fileProduct['ARTICLE']])) {
                $this->arCompareProduct[$fileProduct['ARTICLE']] = [
                    'ARTICLE' => $fileProduct['ARTICLE'],
                    'OLD_HEIGHT' => $this->arNeededProduct[$fileProduct['ARTICLE']]['PROPERTY_HEIGHT_VALUE'],
                    'OLD_WIDTH' => $this->arNeededProduct[$fileProduct['ARTICLE']]['PROPERTY_WIDTH_VALUE'],
                    'OLD_LENGTH' => $this->arNeededProduct[$fileProduct['ARTICLE']]['PROPERTY_LENGTH_VALUE'],
                    'OLD_DESCRIPTION' => $this->arNeededProduct[$fileProduct['ARTICLE']]['DETAIL_TEXT'],
                    'NEW_HEIGHT' => $fileProduct['HEIGHT'],
                    'NEW_WIDTH' => $fileProduct['WIDTH'],
                    'NEW_LENGTH' => $fileProduct['LENGTH'],
                    'NEW_DESCRIPTION' => $fileProduct['DESCRIPTION'],
                ];

                if (!empty($fileProduct['HEIGHT'])) {
                    if (!ctype_digit(str_replace(['.', ','], '', $fileProduct['HEIGHT']))) {
                        $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['HEIGHT'] = 'HEIGHT содержит не только цифры;';
                    }
                }
                if (!empty($fileProduct['WIDTH'])) {
                    if (!ctype_digit(str_replace(['.', ','], '', $fileProduct['WIDTH']))) {
                        $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['WIDTH'] = 'WIDTH содержит не только цифры;';
                    }
                }
                if (!empty($fileProduct['LENGTH'])) {
                    if (!ctype_digit(str_replace(['.', ','], '', $fileProduct['LENGTH']))) {
                        $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['LENGTH'] = 'LENGHT содержит не только цифры;';
                    }
                }

                $this->arProductForUpdate[$this->arNeededProduct[$fileProduct['ARTICLE']]['ID']] = [
                    'HEIGHT' => $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['HEIGHT'] ? '' : $fileProduct['HEIGHT'],
                    'WIDTH' => $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['WIDTH'] ? '' : $fileProduct['WIDTH'],
                    'LENGTH' => $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['LENGTH'] ? '' : $fileProduct['LENGTH'],
                    'DETAIL_TEXT' => $this->arCompareProduct[$fileProduct['ARTICLE']]['ERRORS']['DESCRIPTION'] ? '' : $fileProduct['DESCRIPTION'],
                ];
            } else {
                $this->arCompareProduct[$fileProduct['ARTICLE']] = [
                    'ERRORS' => ['ARTICLE' => 'Артикул не найден в каталоге;'],
                    'ARTICLE' => $fileProduct['ARTICLE'],
                    'NEW_HEIGHT' => $fileProduct['HEIGHT'],
                    'NEW_WIDTH' => $fileProduct['WIDTH'],
                    'NEW_LENGTH' => $fileProduct['LENGTH'],
                    'NEW_DESCRIPTION' => $fileProduct['DESCRIPTION'],
                ];
            }
        }
    }

    private function createTmpFile()
    {
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/import_size_and_descriotion.tmp', serialize($this->arProductForUpdate));
    }

    private function readTmpFile()
    {
        $this->arProductForUpdate = unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/import_size_and_descriotion.tmp'));
    }

    private function updateProductProperty($onePart)
    {
        $i = 0;
        $count['PRODUCT'] = $this->arProductForUpdate['PRODUCT_COMPLETE'] ?? 0;
        $count['PROPERTY'] = $this->arProductForUpdate['PROPERTY_COMPLETE'] ?? 0;
        $obElement = new CIBlockElement();
        foreach ($this->arProductForUpdate as $productId => $productProperty) {
            foreach ($productProperty as $property => $value) {
                if ($property == 'DETAIL_TEXT') {
                    if (empty($value)) {
                        if ($value === 0) {
                            $obElement->Update($productId, ['DETAIL_TEXT' => '']);
                            $count['PROPERTY']++;
                        }
                    } else {
                        $obElement->Update($productId, ['DETAIL_TEXT' => $value, 'DETAIL_TEXT_TYPE' => 'html']);
                        $count['PROPERTY']++;
                    }
                } else {
                    if (empty($value)) {
                        if ($value === 0) {
                            $obElement->SetPropertyValues($productId, $this->iblockId, '', $property);
                            $count['PROPERTY']++;
                        }
                    } else {
                        $obElement->SetPropertyValues($productId, $this->iblockId, $value, $property);
                        $count['PROPERTY']++;
                    }
                }
            }
            $count['PRODUCT']++;
            $i++;
            unset($this->arProductForUpdate[$productId]);
            if ($i == $onePart) {
                $this->arProductForUpdate['PRODUCT_COMPLETE'] = $count['PRODUCT'];
                $this->arProductForUpdate['PROPERTY_COMPLETE'] = $count['PROPERTY'];
                $this->createTmpFile();
                break;
            }
        }
        echo '<input class="processed" value="' . $count['PRODUCT'] . '"/>';
        echo '<input class="done" value="' . (count($this->arProductForUpdate) == 2 ? 'Y' : 'N') . '"/>';
        echo '<div class="count">Обновлено ' . $count['PROPERTY'] . ' свойств у ' . $count['PRODUCT'] . ' товаров</div>';
        if (count($this->arProductForUpdate) == 2) {
            $this->log('Обновлено ' . $count['PROPERTY'] . ' свойств у ' . $count['PRODUCT'] . ' товаров');
            unlink($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/import_size_and_descriotion.tmp');
        }
    }

    private function log($message)
    {
        orgasm_logger($message, date('Y.m.d') . '.log', '/local/logs/importProductProperty/');
    }
}

$import = new ProductPropertyImport();
$import->go();
?>
    <style>
        .new_info {
            display: block;
            font-size: 15px;
            width: 100%;
        }

        .new_info:hover + .old_info {
            display: block;
        }

        .old_info {
            display: none;
            text-align: right;
            font-size: 12px;
            opacity: 0.5;
        }

        .back_green {
            background: green;
        }

        .back_red {
            background: red;
        }

        .back_yellow {
            background: yellow;
        }

        .back_white {
            background: white;
        }
        .result, #import-cancel{
            display: none;
        }
    </style>
<?
require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");