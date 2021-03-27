<?php

use Bitrix\Main\Loader;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
global $APPLICATION;

$defaultCityId = 129;
$currentCityId = empty($_POST['city']) ? $defaultCityId : intval($_POST['city']);

Loader::includeModule("iblock");
Loader::includeModule("catalog");
Loader::includeModule("likee.location");

ini_set('memory_limit', '768M');

$arAllLocations = \Likee\Location\Location::all();
$arOnlineStores = \Likee\Site\Helpers\Catalog::getOnlineStores();
$arStaticStores = [];
$arNotDefaultStores = ($currentCityId == $defaultCityId) ? [] : false;

foreach ($arAllLocations as $locationId => $arLocation) {
    $arStores = array_column($arLocation['STORES'], 'ID');
    $arStaticStores = array_unique(array_merge($arStores, $arStaticStores));

    if ($locationId != $defaultCityId && is_array($arNotDefaultStores)) {
        $arNotDefaultStores = array_unique(array_merge($arStores, $arNotDefaultStores));
    }
}
$arStaticStores = array_diff($arStaticStores, $arOnlineStores);

$arStores = [];

// наличие на складе
$arStores[0]['title'] = 'в ИМ';
$arStores[0]['include'] = $arOnlineStores;
$arStores[0]['exclude'] = [];

// наличие в выбранном городе
$arStores[1]['title'] = 'в салонах г.' . $arAllLocations[$currentCityId]['CITY_NAME'];
$arStores[1]['include'] = [];
$arStores[1]['exclude'] = [];
foreach ($arAllLocations[$currentCityId]['STORES'] as $store) {
    $arStores[1]['include'][] = $store['ID'];
}

// начилие только в ИМ исключая города
$arStores[2]['title'] = 'только заказ в ИМ';
$arStores[2]['include'] = $arOnlineStores;
$arStores[2]['exclude'] = array_diff($arStores[1]['include'], $arOnlineStores); //$arStaticStores;

// в других городах если смотрим Москву
if ($arNotDefaultStores) {
    $arStores[3]['title'] = 'в других городах';
    $arStores[3]['include'] = $arNotDefaultStores;
    $arStores[3]['exclude'] = [];
}

// формирование отчета
$arReport = [];

$arReporrVariants = [
    'OBUV' => [
        0 => 'Обувь',
        1 => [593, 587]
    ]
];

$arReportDisplay = [
    'ACTIVE' => [
        'title' => 'по статусу',
        'labels' => [
            0 => 'Вручную снят с показа',
            1 => 'В продаже'
        ]
    ],
    'DETAIL_PICTURE' => [
        'title' => 'по наличию изображения',
        'labels' => [
            0 => 'Без изображения',
            1 => 'Содержит изображение'
        ]
    ],
    'PROPERTY_COLLECTION' => [
        'title' => 'по коллекции',
        'labels' => []
    ],
    'PROPERTY_RHODEPRODUCT' => [
        'title' => 'по роду',
        'labels' => []
    ]
];

$arTempItems = [];
$arTempOffers = [];
$arTempProductsByStore = [];

// данные по товарам
$rsItems = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => IBLOCK_CATALOG,
    ],
    false,
    false,
    ['IBLOCK_ID', 'ID', 'ACTIVE', 'IBLOCK_SECTION_ID', 'DETAIL_PICTURE', 'PROPERTY_ARTICLE', 'PROPERTY_COLLECTION', 'PROPERTY_RHODEPRODUCT']
);
while ($arItem = $rsItems->Fetch(false, false)) {
    $arTempItems[$arItem['ID']] = $arItem;
}
unset($rsItems, $arItem);

// наличие на складах
$rsStore = CCatalogStoreProduct::GetList(
    [],
    [],
    false,
    false,
    ['STORE_ID', 'PRODUCT_ID', 'AMOUNT']
);
while ($arStore = $rsStore->Fetch(false, false)) {
    $arTempProductsByStore[$arStore['PRODUCT_ID']][$arStore['STORE_ID']] = $arStore['AMOUNT'];
}
unset($rsStore, $arStore);

// данные по предложениям
$rsItems = CIBlockElement::GetList(
    [],
    [
        'IBLOCK_ID' => IBLOCK_OFFERS,
    ],
    false,
    false,
    ['IBLOCK_ID', 'ID', 'PROPERTY_CML2_LINK.ID']
);
while ($arOffer = $rsItems->Fetch(false, false)) {
    if (empty($arOffer['PROPERTY_CML2_LINK_ID']) ||
        empty($arTempItems[$arOffer['PROPERTY_CML2_LINK_ID']]) ||
        empty($arTempProductsByStore[$arOffer['ID']])) {
        continue;
    }

    $elementId = $arOffer['PROPERTY_CML2_LINK_ID'];
    $sReportVariant = 'OBUV';

    $arTempOffers[$elementId][] = $arOffer['ID'];

    foreach ($arTempProductsByStore[$arOffer['ID']] as $storeId => $storeAmount) {
        $arReport['TOTAL'][$storeId]['ITEMS'][$elementId] = 1;
        $arReport['TOTAL'][$storeId]['OFFERS'][$arOffer['ID']] = $storeAmount;

        foreach ($arReportDisplay as $propKey => $propData) {
            if (0 === strpos($propKey, 'PROPERTY_')) {
                $elementPropKey = $propKey . '_VALUE';
                if (empty($arTempItems[$elementId][$elementPropKey])) {
                    continue;
                }

                $propKeyValue = $arTempItems[$elementId][$elementPropKey];
            } else {
                $propKeyValue = 0;
                if ('Y' == $arTempItems[$elementId][$propKey]) {
                    $propKeyValue = 1;
                } elseif (!empty($arTempItems[$elementId]['DETAIL_PICTURE'])) {
                    $propKeyValue = 1;
                }
            }

            $arReport[$sReportVariant][$propKey][$propKeyValue][$storeId]['OFFERS'][$arOffer['ID']] = $storeAmount;
            $arReport[$sReportVariant][$propKey][$propKeyValue][$storeId]['ITEMS'][$elementId] = 1;
        }
    }
}
unset($rsItems, $arItem);

if (!empty($_POST['hash']) && !empty($_POST['type'])) :
    $list = [];
    $elementsIds = [];

    if (isset($arReport[$_POST['hash']])) {
        $list = &$arReport[$_POST['hash']];
    } else {
        list($prop_1, $prop_2, $prop_3) = explode(':', $_POST['hash']);
        $list = &$arReport[$prop_1][$prop_2][$prop_3];
    }

    $storeListKey = intval($_POST['type']);
    if (!empty($arStores[$storeListKey])) {
        foreach ($arStores[$storeListKey]['include'] as $storeId) {
            if (!empty($list[$storeId]['ITEMS'])) {
                $elementsIds = array_merge($elementsIds, array_keys($list[$storeId]['ITEMS']));
            }
        }
        foreach ($arStores[$storeListKey]['exclude'] as $storeId) {
            if (!empty($list[$storeId]['ITEMS'])) {
                $elementsIds = array_diff($elementsIds, array_keys($list[$storeId]['ITEMS']));
            }
        }
    } else {
        $elementsIds[] = 0;
    }

    $elementsIds = array_unique($elementsIds);
    ?>
    <table class="adm-list-table">
        <tdead>
            <tr class="adm-list-table-header">
                <td class="adm-list-table-cell align-left">№</td>
                <td class="adm-list-table-cell align-center">Изображение</td>
                <td class="adm-list-table-cell align-center">Артикул</td>
                <td class="adm-list-table-cell align-right">Цена</td>
            </tr>
        </tdead>
        <tbody>
        <?
        $counter = 1;

        $rsItems = CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'ID' => $elementsIds
            ],
            false,
            false,
            ['IBLOCK_ID', 'ID', 'DETAIL_PICTURE', 'PROPERTY_ARTICLE']
        );
        while ($arItem = $rsItems->Fetch(false, false)) :
            $arPrice = false;
            if (!empty($arTempOffers[$arItem['ID']])) {
                $arPrice = CCatalogProduct::GetOptimalPrice($arTempOffers[$arItem['ID']][0], 1, array(2), 'N');
            }
            ?>
            <tr class="adm-list-table-row">
                <td class="adm-list-table-cell"><?= $counter++; ?></td>
                <td class="adm-list-table-cell"><?
                if (empty($arItem['DETAIL_PICTURE'])) {
                    echo '<span class="no-img">нет</div>';
                } else {
                    $img = CFile::GetPath($arItem['DETAIL_PICTURE']);
                    echo '<img src="' . $img . '" class="mini-img" alt="" />';
                }
                ?></td>
                <td class="adm-list-table-cell"><?= $arItem['PROPERTY_ARTICLE_VALUE'] ?></td>
                <td class="adm-list-table-cell align-right"><?= ($arPrice ? $arPrice['RESULT_PRICE']['BASE_PRICE'] : '&nbsp;') ?></td>
            </tr>
            <?
        endwhile;
        unset($rsItems, $arItem);
        ?>
        </tbody>
    </table>
    <?
    exit;
endif;


$APPLICATION->SetTitle('Отчет по товарам');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

\CUtil::InitJSCore(array("jquery"));


foreach ($arReportDisplay as $propKey => $propData) {
    if (0 !== strpos($propKey, 'PROPERTY_')) {
        continue;
    }

    $propName = substr($propKey, 9);
    $sPropCode = ucfirst(strtolower($propName));
    $obEntity = \Likee\Site\Helpers\HL::getEntityClassByHLName($sPropCode);

    if (!empty($obEntity) && is_object($obEntity)) {
        $sClass = $obEntity->getDataClass();
        $rsPropValues = $sClass::getList();

        while ($arPropValue = $rsPropValues->fetch()) {
            $arReportDisplay[$propKey]['labels'][$arPropValue['UF_XML_ID']] = $arPropValue['UF_NAME'];
        }
    }
}


function likeeReportCount($arData, $arStoresInclude, $arStoresExclude = [], $sType = 'ITEMS')
{
    if (empty($arData) || empty($arStoresInclude)) {
        return 0;
    }

    $total = [];
    foreach ($arStoresInclude as $storeId) {
        if (!empty($arData[$storeId][$sType])) {
            foreach ($arData[$storeId][$sType] as $itemId => $sum) {
                if ('ITEMS' == $sType) {
                    $total[$itemId] = $sum;
                } else {
                    $total[$itemId] += $sum;
                }
            }
        }
    }
    foreach ($arStoresExclude as $storeId) {
        if (!empty($arData[$storeId][$sType])) {
            $total = array_diff_key($total, $arData[$storeId][$sType]);
        }
    }
    return array_sum($total);
}

$reportTitleColspan = 1 + (count($arStores) * 2);

?>
    <div class="adm-detail-content-wrap">
        <div class="adm-detail-content" id="main">
            <div class="adm-detail-title"><?= FormatDate('d F Y, D', time()) ?></div>
            <div class="adm-detail-content-item-block">
                <form id="likee-report-form" method="POST"
                      action="likee_report.php?lang=<?= LANGUAGE_ID ?><?= $returnUrl ?>" enctype="multipart/form-data"
                      name="editform">
                    <select id="city-id" name="city">
                        <? foreach ($arAllLocations as $arCity) : ?>
                            <? $bSelected = $currentCityId == $arCity['ID']; ?>
                            <option value="<?= $arCity['ID'] ?>"<? if ($bSelected) :
                                ?> selected<?
                                           endif; ?>><?= $arCity['CITY_NAME']; ?></option>
                        <? endforeach; ?>
                    </select>
                </form>
                <p>&nbsp;</p>

                <table class="adm-list-table">
                    <tdead>
                        <tr class="adm-list-table-header">
                            <td class="adm-list-table-cell align-center" width="30%">
                                <div class="adm-list-table-cell-inner">Параметры</div>
                            </td>
                            <? foreach ($arStores as $arStoreData) : ?>
                                <td class="adm-list-table-cell align-center">
                                    <div class="adm-list-table-cell-inner">Всего арт. <?= $arStoreData['title'] ?></div>
                                </td>
                                <td class="adm-list-table-cell align-center">
                                    <div class="adm-list-table-cell-inner">Всего
                                        шт./пар <?= $arStoreData['title'] ?></div>
                                </td>
                            <? endforeach; ?>
                        </tr>
                    </tdead>

                    <tbody>
                    <tr class="adm-list-table-header report-header">
                        <td class="adm-list-table-cell" colspan="<?= $reportTitleColspan ?>">
                            <div class="adm-list-table-cell-inner">Всего в показе</div>
                        </td>
                    </tr>
                    <tr class="adm-list-table-row" data-hash="TOTAL">
                        <td class="adm-list-table-cell align-right">Всего</td>
                        <? foreach ($arStores as $storeListKey => $arStoreData) : ?>
                            <td class="adm-list-table-cell align-right" data-type="<?= $storeListKey ?>_i">
                                <?= likeeReportCount($arReport['TOTAL'], $arStoreData['include'], $arStoreData['exclude'], 'ITEMS') ?>
                            </td>
                            <td class="adm-list-table-cell align-right">
                                <?= likeeReportCount($arReport['TOTAL'], $arStoreData['include'], $arStoreData['exclude'], 'OFFERS') ?>
                            </td>
                        <? endforeach; ?>
                    </tr>

                    <?
                    foreach ($arReporrVariants as $sReportVariant => $sReportVariantOpts) :
                        if (empty($arReport[$sReportVariant])) {
                            continue;
                        }

                        foreach ($arReportDisplay as $propKey => $propData) :
                            if (empty($arReport[$sReportVariant][$propKey])) {
                                continue;
                            }
                            ?>
                            <tr class="adm-list-table-header report-header">
                                <td class="adm-list-table-cell" colspan="<?= $reportTitleColspan ?>">
                                    <div class="adm-list-table-cell-inner"><?= $sReportVariantOpts[0] . ' ' . $propData['title'] ?></div>
                                </td>
                            </tr>
                            <? foreach ($propData['labels'] as $reportKey => $reportLabel) :
                                $sReportHash = $sReportVariant . ':' . $propKey . ':' . $reportKey;

                                $arPropReport = [];
                                foreach ($arStores as $storeListKey => $arStoreData) {
                                    $arPropReport[$storeListKey . '_i'] = likeeReportCount($arReport[$sReportVariant][$propKey][$reportKey], $arStoreData['include'], $arStoreData['exclude'], 'ITEMS');
                                    $arPropReport[$storeListKey . '_o'] = likeeReportCount($arReport[$sReportVariant][$propKey][$reportKey], $arStoreData['include'], $arStoreData['exclude'], 'OFFERS');
                                }

                                if (0 == array_sum($arPropReport)) {
                                    continue;
                                }
                                ?>
                            <tr class="adm-list-table-row" data-hash="<?= $sReportHash ?>">
                                <td class="adm-list-table-cell align-right"><?= $reportLabel ?></td>
                                <? foreach ($arPropReport as $sPropReportKey => $iPropReportValue) : ?>
                                    <td class="adm-list-table-cell align-right" data-type="<?= $sPropReportKey ?>">
                                        <?= $iPropReportValue ?>
                                    </td>
                                <? endforeach; ?>
                            </tr>
                            <? endforeach; ?>
                        <? endforeach; ?>
                    <? endforeach; ?>
                </table>
            </div>
        </div>
        <div class="adm-detail-content-btns-wrap">
            <div class="adm-detail-content-btns adm-detail-content-btns-empty"></div>
        </div>
    </div>

    <script>
        var currentCity = <?= $currentCityId ?>;
        BX.ready(function () {
            BX.bind(BX('city-id'), 'change', function () {
                BX('likee-report-form').submit();
            });

            $('td[data-type$="_i"]').on('click', function (e) {
                e.preventDefault();

                var $row = $(this).closest('tr');

                var hashKey = $row.data('hash');
                var typeKey = $(this).attr('data-type');
                var title = $row.find('td:first-child').text();

                d(title, hashKey, typeKey);
            });
        });

        function d(title, hashKey, typeKey) {
            BX.showWait();
            $.post('/bitrix/admin/likee_report.php', {
                'city': currentCity,
                'hash': hashKey,
                'type': typeKey
            }, function (data) {
                var Dialog = new BX.CDialog({
                    title: 'Артикула товаров',
                    head: title,
                    content: data,
                    icon: 'head-block',
                    resizable: true,
                    draggable: true,
                    height: '500',
                    width: '400',
                    buttons: [BX.CDialog.btnClose]
                });

                BX.closeWait();
                Dialog.Show();
            });
        }
    </script>
    <style type="text/css">
        td[data-type$='_i'] {
            color: #1c53a2;
            cursor: pointer;
        }

        td[data-type$='_i']:hover {
            text-decoration: underline;
        }

        .mini-img {
            max-height: 50px;
            width: auto;
        }
    </style>
<?
// завершение страницы
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>