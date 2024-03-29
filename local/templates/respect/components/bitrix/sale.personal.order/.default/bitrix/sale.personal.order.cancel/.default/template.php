<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

//это сообщение приходит из ядра модуля sale, подругому не поменять
if (!empty($arResult["ERROR_MESSAGE"]) && $arResult["ERROR_MESSAGE"] == 'Ошибка отмены заказа.  У заказа есть активные оплаты.') {
    $arResult["ERROR_MESSAGE"] = 'Невозможно отменить заказ. У заказа есть активные оплаты, либо учтенные бонусы. Свяжитесь с менеджером для отмены.';
}
?>


<div class="container">
    <div class="column-6 pre-1 column-md-2 padding-right">
        <a href="<?= $arResult["URL_TO_LIST"] ?>"><?= GetMessage("SALE_RECORDS_LIST") ?></a>

        <div class="bx_my_order_cancel">
            <? if (strlen($arResult["ERROR_MESSAGE"]) <= 0): ?>
                <form method="post" action="<?= POST_FORM_ACTION_URI ?>">

                    <input type="hidden" name="CANCEL" value="Y">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>">

                    <?= GetMessage("SALE_CANCEL_ORDER1") ?>

                    <a href="<?= $arResult["URL_TO_DETAIL"] ?>"><?= GetMessage("SALE_CANCEL_ORDER2") ?>
                        #<?= $arResult["ACCOUNT_NUMBER"] ?></a>?
                    <b><?= GetMessage("SALE_CANCEL_ORDER3") ?></b><br/><br/>
                    <?= GetMessage("SALE_CANCEL_ORDER4") ?>:<br/>

                    <textarea name="REASON_CANCELED"></textarea><br/><br/>
                    <input class="button button--primary" type="submit" name="action" value="<?= GetMessage("SALE_CANCEL_ORDER_BTN") ?>">

                </form>
            <? else: ?>
                <br>
                <?= ShowError($arResult["ERROR_MESSAGE"]); ?>
            <? endif; ?>
        </div>
    </div>
</div>

<div class="spacer--3"></div>