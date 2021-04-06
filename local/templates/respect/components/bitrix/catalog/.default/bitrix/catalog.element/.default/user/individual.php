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

<?/*<div class="container">
    <div class="column-10">
        <? if (!empty($arResult['COLORS'])): ?>
            <div class="product-module">
                <div class="product-module__title">Цвета</div>

                <div class="product-module__content">
                    <div class="color-selector colors-selector--x js-colors-box">
                        <? foreach ($arResult['COLORS'] as $arColor): ?>
                            <?
                            $sStyle = 'background-color: ' . $arColor['COLOR'];
                            $sClass = $arColor['CURRENT'] == 'Y' ? ' selected' : '';
                            if (!empty($arColor['FILE'])) {
                                $sStyle = "background-image: url('" . $arColor['FILE'] . "')";
                                $sClass .= ' has-img';
                            }
                            ?>
                            <a class="js-btn-color<?= $sClass; ?>"
                               href="<?= $arColor['DETAIL_PAGE_URL']; ?>"
                               style="<?= $sStyle; ?>"></a>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        <? endif; ?>
	</div>
</div>*/?>

<div class="container">
    <div class="column-10">
        <div class="button-group">
            <? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
                <a class="button button--outline js-tooltip tooltipstered"
                   data-target="#description-tooltip-content">Описание</a>
            <? endif; ?>
            <? if ($arResult['AVAILABILITY_IN_SHOPS'] == 'Y' && $arResult['AVAILABILITY_IN_REGION'] == 'Y'): ?>
                <a class="button button--primary js-shop-list-custom">Наличие в магазинах</a>
            <? endif; ?>
        </div>

        <? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
            <div id="description-tooltip-content" class="description-tooltip">
                <dl class="dl--inline">
                    <? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty): ?>
                        <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])): ?>
                            <dt><?= $arProperty['NAME']; ?></dt>
                            <dd><?= $arProperty['VALUE']; ?></dd>
                        <? endif; ?>
                    <? endforeach; ?>
                </dl>
            </div>
        <? endif; ?>
    </div>
</div>

<div class="container">
    <div class="column-10">
        <? /*if ($arResult['PHOTOS_COUNT'] > 1): ?>
            <div class="product-module">
                <div class="product-images">
                    <ul class="js-slider">
                        <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto): ?>
                            <li>
                                <a href="#product-image-<?= $iKey; ?>" class="product-image">
                                    <img src="<?= $arPhoto['SRC']; ?>" alt="<?= $arPhoto['ALT']; ?>">
                                </a>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </div>
            </div>
        <? endif; */ ?>
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