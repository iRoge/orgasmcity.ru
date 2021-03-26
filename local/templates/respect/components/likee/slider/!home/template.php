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
/** @var LikeeSliderComponent $component */
$this->setFrameMode(true);
?>
<? if (!empty($arResult['ITEMS'])): ?>
    <div class="slider">
        <div id="main-slider<?= $arParams['CUSTOM_NUMBER'] ?>" class="slides<? !empty($arResult['ITEMS'][0]['VIDEO']) and print ' slides--with-video' ?>"<? !empty($arResult['SLICK']) and print " data-slick='".json_encode($arResult['SLICK'])."'"; ?>>
            <? foreach ($arResult['ITEMS'] as $arItem): ?>
                <a href="<?=$arItem['PROPS']['VIDEO_GAG_LINK']['VALUE']?>" id="msi-<?= $arItem['ID'] ?>" class="slides-item slider-one main-top col-xs-12" style="background-image: url('<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>')">
                    <div class="main">
                        <div class="col-sm-6">
                            <?/*div class="in-main-top col-xs-12">
                                <img src="<?=$arItem['DETAIL_PICTURE']['SRC']?>" />
                                <?if($arItem['PROPS']['TEXT_TOP']['VALUE']):?>
                                    <span><?= $arItem['PROPS']['TEXT_TOP']['VALUE'] ?></span>
                                <?endif;?>
                                <h2><?= $arItem['NAME'] ?></h2>
                                <p>
                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                </p>
                                <?if($arItem['PROPS']['VIDEO_GAG_LINK']['VALUE'] && $arItem['PROPS']['LINK_TEXT']['VALUE']):?>
                                    <a href="<?=$arItem['PROPS']['VIDEO_GAG_LINK']['VALUE']?>"><?= $arItem['PROPERTIES']['LINK_TEXT']['VALUE'] ?></a>
                                <?endif;?>
                            </div*/?>
                        </div>
                        <div class="col-sm-5 col-sm-offset-1 shoes-top">
                            <?if($arItem['PRODUCT_IMG']['SRC']):?>
                                <img src="<?= $arItem['PRODUCT_IMG']['SRC']?>"/>
                            <?endif;?>
                        </div>
                        <div class="col-sm-12">
                            <div class="in-main-top col-xs-12">
                                <h2><?= $arItem['NAME'] ?></h2>
                                <?if($arItem['PROPS']['VIDEO_GAG_LINK']['VALUE'] && $arItem['PROPS']['LINK_TEXT']['VALUE']):?>
                                    <a href="<?=$arItem['PROPS']['VIDEO_GAG_LINK']['VALUE']?>"><?= $arItem['PROPERTIES']['LINK_TEXT']['VALUE'] ?></a>
                                <?endif;?>
                            </div>
                        </div>
                    </div>
                </a>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>