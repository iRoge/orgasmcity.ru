<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
?>

<div class="container">
    <div class="column-8 column-center column-phone-2">
        <div class="heading"><?= $arResult['NAME']; ?></div>
        <?= $arResult['DETAIL_TEXT']; ?>
        <? if (!empty($arResult['PHOTOS'])): ?>
            <div class="page-media">
                <? foreach ($arResult['PHOTOS'] as $arPhoto): ?>
                    <?if($arPhoto['LINK']):?>
                        <a href='<?=$arPhoto['LINK'];?>' <?if($arPhoto['TARGET']):?> target="<?=$arPhoto['TARGET'];?>" <?endif;?> >
                            <img src="<?= $arPhoto['SRC']; ?>" alt="<?= $arPhoto['ALT']; ?>">
                        </a>
                    <?else:?>
                        <img src="<?= $arPhoto['SRC']; ?>" alt="<?= $arPhoto['ALT']; ?>">
                    <?endif;?>
                <? endforeach; ?>
            </div>
        <? endif; ?>
    </div>
</div>

<div class="container show-more show-more--large">
    <? if (!empty($arResult['NEXT'])): ?>
        <div class="column-6 pre-2 text--center column-phone-2">
            <div class="button-group">
                <a href="<?= $arResult['LIST_PAGE_URL']; ?>" class="button button--primary">К списку новостей</a>
                <a href="<?= $arResult['NEXT']; ?>" class="button button--outline button--thin">
                    Следующая новость
                </a>
            </div>
        </div>
    <? else: ?>
        <div class="column-4 pre-3 text--center column-phone-2">
            <div class="button-group">
                <a href="<?= $arResult['LIST_PAGE_URL']; ?>" class="button button--primary">К списку новостей</a>
            </div>
        </div>
    <? endif; ?>
</div>