<?php
foreach ($arResult['ITEMS'] as $item) {
    $imgIds[] = $item['PROPERTIES']['IMG']['VALUE'];
}

$dbResImgs = CFile::GetList([], ['@ID' => $imgIds]);
$filePath = '/' . COption::GetOptionString('main', 'upload_dir') . '/';
while ($img = $dbResImgs->Fetch()) {
    $arResult['IMG_SOURCES'][$img['ID']] =  $filePath . $img['SUBDIR'] . '/' . $img['FILE_NAME'];
}
