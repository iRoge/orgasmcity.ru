<?
// подключим все необходимые файлы:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Тестовый каталог');
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

if ($_GET['location_code']) {
    setcookie("LOCATION_CODE", $_GET['location_code'], time() + 2592000, "/");
    $_SESSION["BRANCH"]["LOCATION_CODE"] = $_GET['location_code'];
    $_COOKIE["LOCATION_CODE"] = $_GET['location_code'];
}
$location_code = $_COOKIE["LOCATION_CODE"];
// пересоздаем экземпляр, чтобы изменения сразу вступили в силу
unset($LOCATION);
$LOCATION = new \Qsoft\Branches();

$section_code_path = $_GET['section_code_path'] ?: "/dlya_zhenshchin/obuv/tufli_";
preg_match("/\/([^\/]+?)$/", $section_code_path, $section_code);
$section_code = $section_code[1];

$delivery = $_GET['delivery'] == "on" ? true : false;
$reservation = $_GET['reservation'] == "on" ? true : false;
if ($delivery || $reservation) {
    if ($delivery && $reservation) {
        $type = false;
    } elseif ($delivery) {
        $type = 1;
    } else {
        $type = 2;
    }
} else {
    $delivery = $reservation = true;
    $type = false;
}

\Bitrix\Main\Loader::includeModule("iblock");
\Bitrix\Main\Loader::includeModule("sale");

$arLocations = \Bitrix\Sale\Location\LocationTable::GetList(array(
    "select" => array(
        "ID",
        "CODE",
        "NAME_RU" => "NAME.NAME",
    ),
    "filter" => array(
        "TYPE_ID" => array(5),
        "NAME.LANGUAGE_ID" => "ru",
    ),
    "order" => array(
        "NAME.NAME" => "ASC",
    )
))->FetchAll();

$res = CIBlockSection::GetList(
    array(
        "SORT" => "ASC",
    ),
    array(
        "IBLOCK_ID" => IBLOCK_CATALOG,
        "CODE" => $section_code,
        "ACTIVE" => "Y",
    ),
    false,
    array(
        "ID",
        "SECTION_PAGE_URL",
        "LEFT_MARGIN",
        "RIGHT_MARGIN",
    ),
    false
);
$arSection = array();
while ($arItem = $res->GetNext()) {
    if ($section_code_path == $arItem["SECTION_PAGE_URL"]) {
        $arSection = $arItem;
        break;
    }
}
$res = CIBlockSection::GetList(
    array(
        "SORT" => "ASC",
    ),
    array(
        "IBLOCK_ID" => IBLOCK_CATALOG,
        ">LEFT_MARGIN" => $arSection["LEFT_MARGIN"],
        "<RIGHT_MARGIN" => $arSection["RIGHT_MARGIN"],
    ),
    array(
        "ID",
    ),
    false
);
$arSectionIds = array();
while ($arItem = $res->Fetch()) {
    $arSectionIds[] = $arItem["ID"];
}?>
<div id="qsoft_test_catalog">
<form method="GET" name="test_branch" action="<?= $APPLICATION->GetCurPage() ?>?lang=<?= LANGUAGE_ID ?>"">
Выбранное местоположение: <select name="location_code">
<? foreach ($arLocations as $value) : ?>
    <option value="<?= $value["CODE"] ?>" <?= ($value["CODE"] == $location_code) ? "selected" : "" ?>><?= $value["NAME_RU"]?></option>
<? endforeach ?>
</select><br>
Выбранный раздел каталога: <input type="text" name="section_code_path" value="<?=$section_code_path?>"><br>
Доставка: <input type="checkbox" name="delivery" <?= ($delivery) ? "checked" : "" ?>><br>
Резерв: <input type="checkbox" name="reservation" <?= ($reservation) ? "checked" : "" ?>><br>
<input type="submit" value="Обновить">
</form><br>
Показана информация для города: <b><?= $LOCATION->getName() ?></b><br>
<br>
<?
$obCache = new CPHPCache;
$cache_dir = "/catalog/tp/";
if ($obCache->InitCache(86400, "tp_cache".$section_code_path, $cache_dir)) {
    $vars = $obCache->GetVars();
    $arProducts = $vars["PRODUCTS"];
    $arOffers = $vars["OFFERS"];
} elseif ($obCache->StartDataCache()) {
    $CACHE_MANAGER->StartTagCache($cache_dir);
    $CACHE_MANAGER->RegisterTag("iblock_id_".IBLOCK_CATALOG);
    $CACHE_MANAGER->RegisterTag("iblock_id_".IBLOCK_OFFERS);
    $res = CIBlockElement::GetList(
        array(
            "SORT" => "ASC"
        ),
        array(
            "IBLOCK_ID" => IBLOCK_CATALOG,
            "IBLOCK_SECTION_ID" => $arSectionIds,
            "ACTIVE" => "Y",
        ),
        false,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "NAME",
            "DETAIL_PICTURE",
        )
    );
    $arProducts = array();
    while ($arItem = $res->GetNext()) {
        $arProducts[$arItem["ID"]] = $arItem;
    }
    $res = CIBlockElement::GetList(
        array(
            "SORT" => "ASC"
        ),
        array(
            "IBLOCK_ID" => IBLOCK_OFFERS,
            "ACTIVE" => "Y",
        ),
        false,
        false,
        array(
            "ID",
            "IBLOCK_ID",
            "PROPERTY_CML2_LINK",
            "PROPERTY_SIZE",
        )
    );
    $arOffers = array();
    while ($arItem = $res->Fetch()) {
        if (!$arProducts[$arItem["PROPERTY_CML2_LINK_VALUE"]]) {
            continue;
        }
        $arOffers[$arItem["ID"]] = $arItem;
    }
    $CACHE_MANAGER->EndTagCache();
    $obCache->EndDataCache(array(
        "PRODUCTS" => $arProducts,
        "OFFERS" => $arOffers,
    ));
}
echo "Всего активных товаров в выбранном разделе каталога: ".count($arProducts)."<br>";
echo "Всего активных торговых предожений у этих товаров: ".count($arOffers)."<br>";
$arStorages = $LOCATION->getStorages($type);
$arStoragesNames = $LOCATION->getStoragesName($type);
$arRests = $LOCATION->getRests(array_keys($arOffers), $type);
echo "Торговых предожений с остатками большими чем резерв в текущем местоположении учитывая доставку и резерв: ".count($arRests)."<br>";
$arResult = array();
foreach ($arOffers as $offerId => $value) {
    if (!$arRests[$offerId]) {
        continue;
    }
    $pid = $value["PROPERTY_CML2_LINK_VALUE"];
    if (!$arResult[$pid]) {
        $arResult[$pid] = $arProducts[$pid];
    }
    $arResult[$pid]["SIZES"][] = $value["PROPERTY_SIZE_VALUE"];
}
echo "Товаров с торговыми предложениями, полученными ранее: ".count($arResult)."<br><br>";
$arPrices = $LOCATION->getProductsPrices(array_keys($arResult));
$arPricesFlags = $LOCATION->priceFlags;
echo "Товары с абсолютной ценой: ".$arPricesFlags[0]."<br>
      Товары с филиальной ценой: ".$arPricesFlags[1]."<br>
      Товары со страховочной ценой: ".$arPricesFlags[2]."<br>
      Товары БЕЗ ЦЕНЫ: ".$arPricesFlags[3]."<br>";
?>
<style>
#qsoft_test_catalog table {
    border-collapse: collapse;
}
#qsoft_test_catalog td, th {
    padding: 0px 5px;
    border: 1px solid #000;
}
</style>
<br>
<? $branchesName = $LOCATION->getBranchName(true); ?>
Филиал: <?= $branchesName["fil"] ?><br>
Безусловная цена: <?= $branchesName["abs"] ?><br>
Страховочная цена: <?= $branchesName["def"] ?><br>
<br>
Склады (<?= count($arStorages) ?> шт):
<table>
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Доставка</th>
        <th>Резервирование</th>
    </tr>
<? foreach ($arStorages as $storageId => $value) : ?>
    <tr>
        <td><?= $storageId ?></td>
        <td><?= $arStoragesNames[$storageId] ?></td>
        <td><?= $value[0] == 1 ? "Да" : "Нет" ?></td>
        <td><?= $value[1] == 1 ? "Да" : "Нет" ?></td>
    </tr>
<? endforeach ?>
</table>
<br><br>
<table style="width:100%">
    <tr>
        <th>№</th>
        <th>Название</th>
        <th>Картинка</th>
        <th>Размеры</th>
        <th>Цена (по сегменту)</th>
    </tr>
    <? foreach ($arResult as $value) : ?>
    <tr>
        <td><?= ++$i ?></td>
        <td><?= $value["NAME"] ?></td>
        <td><img style="width:100px;height:100px" src="<?= CFile::GetPath($value["DETAIL_PICTURE"]) ?>"></td>
        <td><?= implode(", ", $value["SIZES"])?></td>
        <td>
            <? if ($arPrices[$value["ID"]]) : ?>
                <?= $arPrices[$value["ID"]]["PRICE"]." <s>".$arPrices[$value["ID"]]["OLD_PRICE"]."</s> (".$arPrices[$value["ID"]]["SEGMENT"].")" ?>
            <? else : ?>
                (NO)
            <? endif ?>
        </td>
    </tr>
    <? endforeach ?>
</table>
</div>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
