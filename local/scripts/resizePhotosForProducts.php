<?php
use Bitrix\Main\FileTable;

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
while (ob_get_level()) {
    ob_end_flush();
}

$time = microtime(true);
echo 'Ресайз фотографий для товаров...' . PHP_EOL;
$smallImgHeight = 300;
$bigImgHeight = 600;

$arFilter = [
    "IBLOCK_ID" => IBLOCK_CATALOG,
    "ACTIVE" => "Y",
];
$arSelect = [
    "ID",
    "DETAIL_PICTURE",
];
$res = CIBlockElement::GetList(
    ["ID" => "ASC"],
    $arFilter,
    false,
    false,
    $arSelect
);
$arImageIds = [];
while ($arItem = $res->Fetch()) {
    if (!$arItem["DETAIL_PICTURE"]) {
        continue;
    }
    $arImageIds[] = $arItem["DETAIL_PICTURE"];
}
echo 'Всего фоток: ' . count($arImageIds) . PHP_EOL;
if (!empty($arImageIds)) {
    $res = FileTable::getList([
        "select" => [
            "ID",
            "SUBDIR",
            "FILE_NAME",
            "WIDTH",
            "HEIGHT",
            "CONTENT_TYPE",
        ],
        "filter" => [
            "ID" => $arImageIds,
        ],
    ]);
    $arImages = [];
    $arImagesBig = [];
    while ($arItem = $res->Fetch()) {
        $src = "/upload/" . $arItem["SUBDIR"] . "/" . $arItem["FILE_NAME"];
        $image = new \Bitrix\Main\File\Image($_SERVER["DOCUMENT_ROOT"] . $src);
        $k = $image->getExifData()['COMPUTED']['Width'] / $image->getExifData()['COMPUTED']['Height'];
        $smallSizes = [
            'height' => $smallImgHeight,
            'width' => $k < 1 ? $k * $smallImgHeight : $smallImgHeight,
        ];
        $bigSizes = [
            'height' => $bigImgHeight,
            'width' => $k < 1 ? $k * $bigImgHeight : $bigImgHeight,
        ];
        $resizeSrc = Functions::ResizeImageGet($arItem, $smallSizes);
        $resizeSrcBig = Functions::ResizeImageGet($arItem, $bigSizes);
        if (!exif_imagetype($_SERVER["DOCUMENT_ROOT"] . $src)) {
            continue;
        }
        $arImages[$arItem["ID"]] = $resizeSrc['src'] ?: $src;
        $arImagesBig[$arItem["ID"]] = $resizeSrcBig['src'] ?: $src;
    }
}

echo 'Выполнено за ' . (microtime(true) - $time) . ' секунд' . PHP_EOL;
