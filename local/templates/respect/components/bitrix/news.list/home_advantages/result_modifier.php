<?php
foreach ($arResult['ITEMS'] as $item) {
    $imgIds[] = $item['PROPERTIES']['IMG']['VALUE'];
}

$dbResImgs = CFile::GetList([], ['@ID' => $imgIds]);
$filePath = '/' . COption::GetOptionString('main', 'upload_dir') . '/';
while ($img = $dbResImgs->Fetch()) {
    $arResult['IMG_SOURCES'][$img['ID']] =  $filePath . $img['SUBDIR'] . '/' . $img['FILE_NAME'];
}

if ($DEVICE->isMobile() || $DEVICE->isTablet()) {
    $itemsCount = count($arResult['ITEMS']);
    if ($itemsCount % 3 != 0) {
        $arResult['ITEMS'] = array_slice($arResult['ITEMS'], 0, $itemsCount - ($itemsCount % 3));
    }
}
