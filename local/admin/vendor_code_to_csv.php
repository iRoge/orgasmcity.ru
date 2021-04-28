<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin() && !in_array(12, $USER->GetUserGroupArray())) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Преобразовать артикулы в CSV-файл');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

use Qsoft\Helpers\IBlockHelper;

define('BASE_DIRECTORY', '/upload/vendor_code_to_csv/');
define('FULL_PATH', $_SERVER['DOCUMENT_ROOT'] . BASE_DIRECTORY);

if ($_POST['RESET']) {
    unset($_POST);
}

if ($_POST['VENDOR_CODE']) {
    $arVendorCodes = array_unique(array_map('trim', explode(',', $_POST['VENDOR_CODE'])));

    if (!empty($arVendorCodes)) {
        $outputFileName = date('Y.m.d-H.i.s') . '.csv';
    }
}

if ($outputFileName) {
    $errorMessage = '';

    if (CModule::IncludeModule('iblock')) {
        $arElementIds = IBlockHelper::getElementIds(IBLOCK_CATALOG, [
            'ACTIVE' => 'Y',
            'PROPERTY_ARTICLE' => $arVendorCodes,
        ]);
        $arItemProperties = IBlockHelper::getPropertyArray(
            IBLOCK_CATALOG,
            $arElementIds,
            [
                'MORE_PHOTO',
                'SEASON',
                'RHODEPRODUCT',
                'VID',
                'TYPEPRODUCT',
                'SUBTYPEPRODUCT'
            ]
        );

        $dbProducts = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'PROPERTY_ARTICLE' => $arVendorCodes
            ],
            false,
            false,
            [
                'ID',
                'CODE',
                'NAME',
                'DETAIL_PICTURE',
                'PREVIEW_PICTURE',
                'PROPERTY_ARTICLE',
                'ACTIVE'
            ]
        );

        $arProducts = [];
        $arInactiveVendorCodes = [];
        while ($arProduct = $dbProducts->Fetch()) {
            $arProducts[$arProduct['PROPERTY_ARTICLE_VALUE']] = [
                'ID' => $arProduct['ID'],
                'VENDOR_CODE' => $arProduct['PROPERTY_ARTICLE_VALUE'],
                'NAME' => $arProduct['NAME'],
                'PHOTO_ID' => $arProduct['DETAIL_PICTURE'] ?? ($arProduct['PREVIEW_PICTURE'] ?? $arItemProperties[$arProduct['ID']]['MORE_PHOTO']['VALUE'][0]),
                'PRODUCT_CARD_URL' => 'https://' . $_SERVER['SERVER_NAME'] . '/' . $arProduct['CODE'],
                'SEASON' => $arItemProperties[$arProduct['ID']]['SEASON']['VALUE'],
                'GENDER' => $arItemProperties[$arProduct['ID']]['RHODEPRODUCT']['VALUE'],
                'VID' => $arItemProperties[$arProduct['ID']]['VID']['VALUE'],
                'TYPEPRODUCT' => $arItemProperties[$arProduct['ID']]['TYPEPRODUCT']['VALUE'],
                'SUBTYPEPRODUCT' => $arItemProperties[$arProduct['ID']]['SUBTYPEPRODUCT']['VALUE'],
            ];

            if ($arProduct['ACTIVE'] != 'Y') {
                $arInactiveVendorCodes[] = $arProduct['PROPERTY_ARTICLE_VALUE'];
            }
        }

        $photoID = implode(',', array_column($arProducts, 'PHOTO_ID'));
        if ($photoID) {
            $dbPhotos = CFile::GetList(
                [],
                ['@ID' => $photoID]
            );

            while ($arPhoto = $dbPhotos->Fetch()) {
                $photoPath = 'https://' . $_SERVER['SERVER_NAME'] . '/upload/' . $arPhoto['SUBDIR'] . '/' . $arPhoto['FILE_NAME'];

                foreach ($arProducts as $vendorCode => $arProduct) {
                    if ($arProduct['PHOTO_ID'] == $arPhoto['ID']) {
                        $arProducts[$vendorCode]['PHOTO_SRC'] = $photoPath;
                    }
                }
            }
        }

        if (!empty($arProducts)) {
            if ($handle = fopen(FULL_PATH . $outputFileName, 'w')) {
                fputcsv(
                    $handle,
                    [
                        'ID',
                        'Артикул',
                        'Название',
                        'Путь к изображению',
                        'Ссылка на карточку товара',
                        'Род изделия',
                        'Сезон',
                        'Вид номенклатуры',
                        'Тип изделия',
                        'Вид изделия'
                    ]
                );

                foreach ($arProducts as $arProduct) {
                    fputcsv(
                        $handle,
                        [
                            $arProduct['ID'],
                            $arProduct['VENDOR_CODE'],
                            $arProduct['NAME'],
                            $arProduct['PHOTO_SRC'] ?? 'Изображение отсутствует',
                            $arProduct['PRODUCT_CARD_URL'],
                            $arProduct['GENDER'],
                            $arProduct['SEASON'],
                            $arProduct['VID'],
                            $arProduct['TYPEPRODUCT'],
                            $arProduct['SUBTYPEPRODUCT']
                        ]
                    );
                }

                fclose($handle);
            } else {
                $errorMessage = 'Не удалось создать файл.';
                unset($outputFileName);
            }

            $resultVendorCodes = implode(', ', array_keys($arProducts));

            $inputVendorCodes = array_map('strtoupper', $arVendorCodes);
            $foundVendorCodes = array_map('strtoupper', array_keys($arProducts));
            $arMissingVendorCodes = array_unique(array_diff($inputVendorCodes, $foundVendorCodes));
        } else {
            $errorMessage = 'Введены несуществующие артикулы.';
            unset($outputFileName);
        }
    } else {
        $errorMessage = 'Произошла внутренняя ошибка.';
        unset($outputFileName);
    }
}
?>

<form method="post">
    <table style="width: 500px">
        <tr>
            <td colspan="2">
                <textarea name="VENDOR_CODE" placeholder="Введите артикулы товаров через запятую" rows="5" style="width: 100%"<?= isset($outputFileName) ? ' disabled' : '' ?>><?= $resultVendorCodes; ?></textarea>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <?php if ($errorMessage) : ?>
                    <p style="color: red; width: 100%"><?= $errorMessage; ?></p>
                <?php endif; ?>

                <?php if (isset($arMissingVendorCodes) && !empty($arMissingVendorCodes)) : ?>
                    <?php $ending = count($arMissingVendorCodes) > 1 ? 'ы' : ''; ?>
                    <p style="color: red; width: 100%">
                        Артикул<?= $ending; ?> <?= implode(', ', $arMissingVendorCodes); ?> не найден<?= $ending; ?>.
                    </p>
                    <p style="width: 100%">
                        Доступные для скачивания артикулы выведены выше.
                    </p>
                <?php endif; ?>

                <?php if (isset($arMissingVendorCodes) && empty($arMissingVendorCodes)) : ?>
                    <p style="color: green; width: 100%">
                        Все артикулы успешно обработаны.
                    </p>
                <?php endif; ?>

                <?php if (!empty($arInactiveVendorCodes)) : ?>
                    <?php $ending = count($arInactiveVendorCodes) > 1 ? ['ы', 'ны'] : ['', 'ен']; ?>
                    <p style="width: 100%"> 
                        Обратите внимание на то, что артикул<?= $ending[0]; ?> <?= implode(', ', $arInactiveVendorCodes); ?> <b>неактив<?= $ending[1]; ?></b>.
                    </p>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <?php if (isset($outputFileName)) : ?>
                <td>
                    <a href="<?= BASE_DIRECTORY . $outputFileName; ?>">
                        <input type="button" value="Скачать" style="width: 100%">
                    </a>
                </td>
                <td>
                    <input type="submit" name="RESET" value="Сбросить" style="width: 100%">
                </td>
            <?php else : ?>
                <td colspan="2">
                    <input type="submit" value="Сгенерировать файл" style="width: 50%">
                </td>
            <?php endif; ?>
        </tr>
    </table>
</form>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
