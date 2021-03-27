<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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
/** @var LikeeRefundComponent $component */
$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])) : ?>
    <? foreach ($arResult['ITEMS'] as $arItem) : ?>
        <?
        $bOpen = !empty($arItem['PROPERTY_OPEN_VALUE']);
        ?>
        <p class="<? $bOpen and print 'active-'; ?>blue">
            <?= $arItem['NAME'] ?>
            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/arr-up.png" class="arr-up"/>
            <img src="<?= SITE_TEMPLATE_PATH; ?>/img/arr-dwn.png" class="arr-down"/>
        </p>
        <div style="<? $bOpen and print 'display:block;'; ?>margin-top:0" class="after-blue"><?= $arItem['PREVIEW_TEXT'] ?></div>
    <? endforeach; ?>
<? endif; ?>