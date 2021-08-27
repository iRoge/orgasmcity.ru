<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
/** @var array $arParams */
global $APPLICATION;
?>

<h1 class="zagolovok zagolovok--catalog">
    <? $APPLICATION->ShowTitle(false); ?>
</h1>
<div class="col-xs-12 padding-o">
    <div class="main">
        <div class="event-content">
            <? if ($arResult['POST']['PROPERTY_PICTURE_POSITION_VALUE'] == 'UP') : ?>
                <? if (!empty($arResult['POST']['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                    <a href="<?= $arResult['POST']['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                        <img src="<?= $arResult['POST']['DETAIL_PICTURE']; ?>" alt="">
                    </a>
                <? else : ?>
                    <img src="<?= $arResult['POST']['DETAIL_PICTURE']; ?>" alt="">
                <? endif; ?>
            <? endif ?>
            <? if ($arResult['POST']['DATE_END']) : ?>
                <span style="color: red">Акция завершена<br></span>
            <? endif; ?>

            <p><?= $arResult['POST']['DETAIL_TEXT']; ?></p>
            <? if ($arResult['POST']['PROPERTY_PICTURE_POSITION_VALUE'] == 'DOWN') : ?>
                <? if (!empty($arResult['POST']['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                    <a href="<?= $arResult['POST']['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                        <img src="<?= $arResult['POST']['DETAIL_PICTURE']; ?>" alt="">
                    </a>
                <? else : ?>
                    <img src="<?= $arResult['POST']['DETAIL_PICTURE']; ?>" alt="">
                <? endif; ?>
            <? endif; ?>
            <a href="<?= $arParams['DEFAULT_SECTION']['LINK']; ?>" class="blue-btn events-link-btn">ВОЗВРАТ К СПИСКУ</a>
        </div>
    </div>
</div>
