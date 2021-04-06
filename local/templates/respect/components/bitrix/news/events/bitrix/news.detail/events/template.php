<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$APPLICATION->SetTitle($arResult['NAME']);
?>
<?if (date('d.m.Y H:i:s', time()) >= $arResult['DATE_ACTIVE_TO']
    && $arResult['PROPERTIES']['NEWS_OR_ACTION']['VALUE_XML_ID'] == 'actions') :?>
    <div class="container">
        <div class="column-8 column-center" style="text-align: center;">
            <div class="action-content">
                <p>Акция завершена</p>
            </div>
        </div>
    </div>
<?endif;?>
<? if ($arParams['DISPLAY_PICTURE'] != 'N'
    && is_array($arResult['DETAIL_PICTURE'])
    && $arResult['PROPERTIES']['PICTURE_LOCATION']['VALUE_XML_ID'] == 'OVER') : ?>
    <div class="container container--no-padding">
        <div class="column-10">
            <? if ($arResult["DETAIL_PICTURE"]['LINK']) : ?>
                <a href="<?= $arResult['PROPERTIES']['ACTIVE_PHOTO_LINK']['VALUE'] ? $arResult['PROPERTIES']['PHOTO_LINK']['VALUE'] : $arResult["DETAIL_PICTURE"]['LINK']; ?>" <? if ($arResult["DETAIL_PICTURE"]['TARGET']) :
                    ?> target="<?= $arResult["DETAIL_PICTURE"]['TARGET']; ?>" <?
                         endif; ?>>
                    <img width="100%" src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" alt="<?= $arResult["DETAIL_PICTURE"]["ALT"]; ?>">
                </a>
            <? else : ?>
                <img width="100%" src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" alt="<?= $arResult["DETAIL_PICTURE"]["ALT"]; ?>">
            <? endif; ?>
        </div>
    </div>
    <div class="spacer--2"></div>
<? endif; ?>

<? if (!empty($arResult['DETAIL_TEXT']) || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['PROPERTIES']['TEXT'])) : ?>
    <div class="container">
        <div class="column-8 column-center">
            <div class="action-content">
                <? if (!empty($arResult['DETAIL_TEXT'])) : ?>
                    <?= $arResult['DETAIL_TEXT']; ?>
                <? else : ?>
                    <?= $arResult['PREVIEW_TEXT']; ?>
                <? endif ?>
            </div>
        </div>
    </div>
<? endif ?>

<? if ($arParams['DISPLAY_PICTURE'] != 'N' && is_array($arResult['DETAIL_PICTURE']) && $arResult['PROPERTIES']['PICTURE_LOCATION']['VALUE_XML_ID'] == 'UNDER') : ?>
    <div class="container container--no-padding">
        <div class="column-10">
            <? if ($arResult["DETAIL_PICTURE"]['LINK']) : ?>
                <a href="<?= $arResult["DETAIL_PICTURE"]['LINK']; ?>" <?=$arResult["DETAIL_PICTURE"]['TARGET'] ? 'target="' . $arResult["DETAIL_PICTURE"]['TARGET'] . '"' : '' ?>>
                    <img width="100%" src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" alt="<?= $arResult["DETAIL_PICTURE"]["ALT"]; ?>">
                </a>
            <? else : ?>
                <img width="100%" src="<?= $arResult["DETAIL_PICTURE"]["SRC"] ?>" alt="<?= $arResult["DETAIL_PICTURE"]["ALT"]; ?>">
            <? endif; ?>
        </div>
    </div>
    <div class="spacer--2"></div>
<? endif; ?>

<div class="container">
    <div class="column-8 column-center" style="width: 100%; text-align: center; margin: 30px;">
        <a href="/<?= $arResult['PROPERTIES']['NEWS_OR_ACTION']['VALUE_XML_ID']; ?>/"
           class="btn-grey">
            <?= getMessage('EVENTS_RETURN_' . $arResult['PROPERTIES']['NEWS_OR_ACTION']['VALUE_XML_ID']);?>
        </a>
    </div>
</div>