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
<?php if (!empty($arResult['ITEMS'])) {?>
    <div class="banners-list-main main">
        <?php foreach ($arResult['ITEMS'] as $item) {?>
            <?php if ($item['CODE']) { ?>
                <div class="col-lg-3 col-md-3 banners-list-element">
                    <a href="<?=$item['CODE']?>">
                        <img width="100%" src="<?=$item['PREVIEW_PICTURE_SRC']?>" alt="<?=$item['NAME']?>">
                    </a>
                </div>
            <?php } else { ?>
                <div class="col-lg-3 col-md-3 banners-list-element">
                    <img width="100%" src="<?=$item['PREVIEW_PICTURE_SRC']?>" alt="<?=$item['NAME']?>">
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php }?>