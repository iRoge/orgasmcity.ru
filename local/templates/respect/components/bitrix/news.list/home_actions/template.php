<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
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
/** @global CDatabase $DB */

$this->setFrameMode(true);
?>

<? if (!empty($arResult['SLIDES'])) : ?>
    <? $iSlidesSize = sizeof($arResult['SLIDES']); ?>
    <? $i = 1; ?>
    <div class="categories col-xs-12 news-main-mob">
        <div class="main">
            <? foreach ($arResult['SLIDES'] as $arSlideItems) : ?>
                <? foreach ($arSlideItems as $iKey => $arItem) :
                    $dataProps = 'data-rblock-id="' . $arItem['ID'] .'" '; // id баннера
                    $dataProps .= 'data-rblock-name="' . $arParams['BANNER_TYPE'] . '" ';  // Тип баннера
                    //$dataProps .= 'data-prod-brand="Respect" ';  // Бренд баннера
                    $dataProps .= 'data-prod-creative="' . $arItem['NAME'] . ' | ' . $arItem['ACTIVE_FROM'] . '" ';  // Название и начало активности баннера
                    $dataProps .= 'data-prod-position="' . $i . '" ';  // Номер баннера
                    ?>
                    <? $sLink = $arItem['PROPERTIES']['LINK']['VALUE']; ?>
                    <? if ($arItem['PROPERTIES']['ACTIVE_MULTIPLY_LINKS']['VALUE'] == 'Y') : ?>
                            <div class="cat-one col-sm-4 banner_item" <?= $dataProps?>>
                                <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>"/>
                        <? foreach ($arItem['BANNER']['MULTIPLY_LINKS'] as $arLink) : ?>
                            <a class="stock-banner__link" href="<?= $arLink['LINK'] ?>"
                               style="<?= $arLink['STYLE'] ?>"></a>
                        <? endforeach ?>
                            </div>
                    <? elseif ($sLink) : ?>
                        <a data-title="<?= $arItem['NAME']; ?>" href="<?= $sLink ?>"
                           class="slides-item banner_item <?= $i === $iSlidesSize ? 'last' : ''; ?>" <?= $dataProps?>>
                            <div class="cat-one col-sm-4">
                                <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>"/>
                                <? /*p class="cat-name"><?= $arItem['NAME']; ?></p*/ ?>
                            </div>
                        </a>
                    <? else : ?>
                        <div class="cat-one col-sm-4 banner_item <?= $i === $iSlidesSize ? 'last' : ''; ?>" <?= $dataProps?>>
                            <img src="<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>"/>
                            <? /*p class="cat-name"><?= $arItem['NAME']; ?></p*/ ?>
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
                <? $i++; ?>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>
