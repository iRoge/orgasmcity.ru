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
<? if (!empty($arResult['ITEMS'])): ?>
    <div class="articles-block">
        <? foreach ($arResult['ITEMS'] as $id => $arItem): ?>
            <div class="articles-each">
                <div class="name">
                    <h2>
                        <a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a>
                    </h2>
                </div>
                <div class="text"><?=$arItem['PREVIEW_TEXT']?></div>
                <div class="more">
                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>">читать далее</a>
                </div>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>