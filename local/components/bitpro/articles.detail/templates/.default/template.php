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
$this->setFrameMode(true);
?>
<?if($arResult['ITEM']['ID']):?>
    <div class="article-text">
        <?if($arResult['ITEM']['SEO']['ELEMENT_PAGE_TITLE']):?>
            <h1><?=$arResult['ITEM']['SEO']['ELEMENT_PAGE_TITLE']?></h1>
        <?else:?>
            <h1><?=$arResult['ITEM']['NAME']?></h1>
        <?endif;?>
        <?=$arResult['ITEM']['DETAIL_TEXT']?>
    </div>
    <?/*<div class="tender-button">
        <a class="button button--primary js-tender-form" href="#" data-tender-name="<?=$arResult['ITEM']['NAME']?>" data-tender-id="<?=$arResult['ITEM']['ID']?>">Оформить заявку</a>
    </div>*/?>
<?endif;?>
