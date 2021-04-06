<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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
?>

<div class="container">
    <div class="column-10">
        <? if (!empty($arResult['COLORS'])): ?>
            <div class="product-module">
                <div class="product-types-slider">
                    <ul class="js-slider">
                        <? foreach ($arResult['COLORS'] as $arColor): ?>
                            <li>
                                <a href="<?= $arColor['DETAIL_PAGE_URL']; ?>" class="" style="<?= $sStyle; ?>">
                                    <img src="<?= $arColor['FILE']; ?>" alt="<?= $arColor['NAME']; ?>">
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <? endif; ?>
        <? if (0): ?>
            <div class="product-module product-module--small">
                <div class="product-module__title">Доставка</div>
                <div class="product-module__content">
                    <i>
                        <? if ($arParams['CITY_CODE'] == '0000073738')://Москва ?>
                            Мы доставим эту пару бесплатно по Москве
                        <? elseif ($arResult['DELIVERY_PRICE'] !== false): ?>
                            <? if ($arResult['DELIVERY_PRICE'] > 0): ?>
                                Доставка в ваш город от
                                <?= CCurrencyLang::CurrencyFormat($arResult['DELIVERY_PRICE'], $arResult['MIN_PRICE']['CURRENCY']); ?>
                            <? else: ?>
                                Мы доставим эту пару бесплатно в ваш город
                            <? endif; ?>
                        <? endif; ?>
                    </i>
                </div>
            </div>
        <? endif; ?>
    </div>
</div>

<div class="container">
    <div class="column-10"></div>
</div>