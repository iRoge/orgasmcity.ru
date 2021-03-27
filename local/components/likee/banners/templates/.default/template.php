<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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
/** @var LikeeBannersComponent $component */
$this->setFrameMode(true);
?>

<div class="products-slider in-view">
    <div class="container container--no-padding products-grid">
        <? if ($arResult['BANNERS_LEFT']) : ?>
            <? $arBanner = reset($arResult['BANNERS_LEFT']); ?>
            <? if ($arBanner['PROPS']['VERTICAL']['VALUE']) : ?>
                <div class="column-3 column-md-2">
                    <div class="products-item products-item--vertical">
                        <? if (!empty($arBanner['PROPS']['LINK']['VALUE'])): ?>
                            <a class="products-item__content" href="<?= $arBanner['PROPS']['LINK']['VALUE']; ?>">
                                <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                     alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                            </a>
                        <? else: ?>
                            <div class="products-item__content">
                                <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                     alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? else : ?>
                <div class="column-3 column-md-2">
                    <? foreach ($arResult['BANNERS_LEFT'] as $arBanner): ?>
                        <div class="products-item products-item--square">
                            <? if (!empty($arBanner['PROPS']['LINK']['VALUE'])): ?>
                                <a class="products-item__content" href="<?= $arBanner['PROPS']['LINK']['VALUE']; ?>">
                                    <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                </a>
                            <? else: ?>
                                <div class="products-item__content">
                                    <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                </div>
                            <? endif; ?>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endif; ?>
        <? endif; ?>

        <? if (!empty($arResult['BANNERS_SLIDER'])) : ?>
            <div class="column-4 pos-r column-md-2">
                <div id="vertical-slider" class="slides slick--no-inline">
                    <? foreach ($arResult['BANNERS_SLIDER'] as $arSliderBanner): ?>
                        <div class="slides-item">
                            <div class="products-item products-item--vertical">
                                <? if (!empty($arSliderBanner['PROPS']['LINK']['VALUE'])): ?>
                                    <a class="products-item__background" href="<?= $arSliderBanner['PROPS']['LINK']['VALUE']; ?>">
                                        <img src="<?= $arSliderBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                             alt="<?= $arSliderBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                    </a>
                                <? else: ?>
                                    <div class="products-item__background">
                                        <img src="<?= $arSliderBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                             alt="<?= $arSliderBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                    </div>
                                <? endif; ?>
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>

                <div class="pagination--overlay">
                    <a id="vertical-slider-prev" class="products-slider-arrow float-left">
                        <i class="icon icon-arrow-left"></i>
                    </a>
                    <a id="vertical-slider-next" class="products-slider-arrow float-right">
                        <i class="icon icon-arrow-right"></i>
                    </a>
                    <div id="vertical-slider-pagination" class="pagination"></div>
                </div>
            </div>
        <? endif; ?>

        <? if ($arResult['BANNERS_RIGHT']) : ?>
            <? $arBanner = reset($arResult['BANNERS_RIGHT']); ?>
            <? if ($arBanner['PROPS']['VERTICAL']['VALUE']) : ?>
                <div class="column-3 column-md-2">
                    <div class="products-item products-item--vertical">
                        <? if (!empty($arBanner['PROPS']['LINK']['VALUE'])): ?>
                            <a class="products-item__content" href="<?= $arBanner['PROPS']['LINK']['VALUE']; ?>">
                                <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                     alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                            </a>
                        <? else: ?>
                            <div class="products-item__content">
                                <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                     alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? else : ?>
                <div class="column-3 column-md-2">
                    <? foreach ($arResult['BANNERS_RIGHT'] as $arBanner): ?>
                        <div class="products-item products-item--square">
                            <? if (!empty($arBanner['PROPS']['LINK']['VALUE'])): ?>
                                <a class="products-item__content" href="<?= $arBanner['PROPS']['LINK']['VALUE']; ?>">
                                    <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                </a>
                            <? else: ?>
                                <div class="products-item__content">
                                    <img src="<?= $arBanner['PREVIEW_PICTURE']['SRC']; ?>"
                                         alt="<?= $arBanner['PREVIEW_PICTURE']['ALT']; ?>">
                                </div>
                            <? endif; ?>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endif; ?>
        <? endif; ?>

    </div>
</div>