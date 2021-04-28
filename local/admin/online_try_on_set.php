<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin() && !in_array(12, $USER->GetUserGroupArray())) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Установка свойства «Онлайн примерочная»');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

if ($_POST['clear']) {
    unset($_POST);
}

if ($_POST['check']) {
    $arInputVendorCodes = array_filter(
        array_map('trim', explode(',', $_POST['vendor_code'])),
        function ($vendorCode) {
            return !empty($vendorCode);
        }
    );
    
    if (!empty($arInputVendorCodes) && CModule::IncludeModule('iblock') && defined('IBLOCK_CATALOG')) {
        $dbProducts = CIBlockElement::GetList(
            false,
            ['IBLOCK_ID' => IBLOCK_CATALOG, 'PROPERTY_ARTICLE' => $arInputVendorCodes],
            false,
            false,
            ['ID', 'PROPERTY_ARTICLE', 'PROPERTY_ONLINE_TRY_ON']
        );

        $arUpdateVendorCodes = [];
        $arOnlineTryOnProducts = [];
        while ($arProduct = $dbProducts->fetch()) {
            if ($arProduct['PROPERTY_ONLINE_TRY_ON_VALUE']) {
                $arOnlineTryOnProducts[$arProduct['ID']] = $arProduct['PROPERTY_ARTICLE_VALUE'];
            } else {
                $arUpdateVendorCodes[$arProduct['ID']] = $arProduct['PROPERTY_ARTICLE_VALUE'];
            }
        }

        $arFoundVendorCodes = array_merge($arUpdateVendorCodes, $arOnlineTryOnProducts);
        if (!empty($arFoundVendorCodes)) {
            $arMissingVendorCodes = array_diff(
                array_map('mb_strtoupper', $arInputVendorCodes),
                array_map('mb_strtoupper', $arFoundVendorCodes)
            );
            $isChecked = true;
        } else {
            $arMessage = [
                'MESSAGE' => 'Введенные артикулы не найдены.',
                'TYPE' => 'ERROR',
            ];
        }
    } else {
        $arMessage = [
            'MESSAGE' => empty($arInputVendorCodes) ? 'Введите артикулы в поле ниже.' : 'Произошла внутренняя ошибка.',
            'TYPE' => 'ERROR',
        ];
    }
}

if ($_POST['set'] && $_POST['product_id']) {
    $arProductID = array_map('trim', explode(',', $_POST['product_id']));
    $arUpdateVendorCodes = array_map('trim', explode(',', $_POST['vendor_code']));

    if (!empty($arProductID) && CModule::IncludeModule('iblock') && defined('IBLOCK_CATALOG')) {
        // TODO: Изменить ID значения свойства «Онлайн примерочная» при переносе
        $onlineTryOnPropertyValueID = 3243;
        
        foreach ($arProductID as $productID) {
            CIBlockElement::SetPropertyValuesEx(
                $productID,
                IBLOCK_CATALOG,
                ['ONLINE_TRY_ON' => $onlineTryOnPropertyValueID]
            );
        }
        $arMessage = [
            'MESSAGE' => 'Артикулы успешно обновлены.',
            'TYPE' => 'OK',
        ];
    } else {
        $arMessage = [
            'MESSAGE' => empty($arProductID) ? 'Не найдены ID товаров.' : 'Произошла внутренняя ошибка.',
            'TYPE' => 'ERROR',
        ];
    }
}
?>

<?php if (!empty($arMessage)) : ?>
    <?= CAdminMessage::ShowMessage($arMessage); ?>
<?php endif; ?>

<form method="post">
    <table style="width: 500px">
        <tr>
            <td colspan="2">
                <textarea name="vendor_code" placeholder="Введите артикулы товаров через запятую" rows="5" style="width: 100%;<?= $isChecked ? ' color: green; font-weight: bold" readonly' : '"' ?>><?= implode(', ', $arUpdateVendorCodes); ?></textarea>
            </td>

            <?php if ($isChecked) : ?>
                <input type="hidden" name="product_id" value="<?= implode(', ', array_keys($arUpdateVendorCodes)); ?>">
            <?php endif; ?>
        </tr>

        <tr>
            <td colspan="2">
                <?php if (!empty($arUpdateVendorCodes)) : ?>
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message" style="width: 500px; padding: 2px 2px;">
                            <p style="text-align: center;">
                                У артикулов, выведенных выше, будет установлено свойство "Онлайн&#160;примерочная"
                            </p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($arMissingVendorCodes)) : ?>
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message" style="width: 500px; padding: 2px 2px;">
                            <p style="text-align: center;">
                                <b><span style="color: red">Внимание!</span> Следующие артикулы не найдены:</b>
                            </p>
                            <ul>
                                <?php foreach ($arMissingVendorCodes as $vendorCode) : ?>
                                    <li><?= $vendorCode; ?></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($isChecked && !empty($arOnlineTryOnProducts)) : ?>
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message" style="width: 500px; padding: 2px 2px;">
                            <p style="text-align: center;">
                                У следующих артикулов уже установлено свойство "Онлайн&#160;примерочная"
                                (они&#160;не&#160;будут&#160;обновлены):
                            </p>
                            <ul>
                                <?php foreach ($arOnlineTryOnProducts as $vendorCode) : ?>
                                    <li><?= $vendorCode; ?></li>
                                <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <?php if ($isChecked) : ?>
                <td style="width: 50%;"> 
                    <input type="submit" name="set" value="Установить" style="width: 100%;" class="adm-btn-save">
                </td>
                <td style="width: 50%;">
                    <input type="submit" name="clear" value="Очистить форму" style="width: 100%;">   
                </td>
            <?php else : ?>
                <td colspan="2"> 
                    <input type="submit" name="check" value="Проверить" style="width: 50%;">   
                </td>
            <?php endif; ?>
        </tr>
    </table>
</form>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
