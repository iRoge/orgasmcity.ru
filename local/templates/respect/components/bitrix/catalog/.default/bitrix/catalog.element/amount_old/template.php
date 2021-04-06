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

<? if ($arParams['JSON'] == 'N'): ?>
    <div class="product-shop-list">
        <header>
            <div class="product-shop-list__header">
                <? if (!empty($arResult['PICTURE'])): ?>
                    <div class="product__media">
                        <img src="<?= $arResult['PICTURE']['SRC']; ?>" alt="<?= $arResult['PICTURE']['ALT']; ?>">
                    </div>
                <? endif; ?>

                <div class="product__information">
                    <div class="product__title"><?= $arResult['NAME']; ?></div>
                    <? if (!empty($arResult['ARTICLE'])): ?>
                        <div class="product__sku">Арт: <?= $arResult['ARTICLE']; ?></div>
                    <? endif; ?>
                </div>
            </div>

            <? if (!empty($arResult['SIZES'])): ?>
                <label for="size">
                    <span>Размер</span>
                    <select id="size" name="size" class="selectize">
                        <? foreach ($arResult['SIZES'] as $arSize): ?>
                            <option value="<?= $arSize['OFFER_ID']; ?>"><?= $arSize['VALUE']; ?></option>
                        <? endforeach; ?>
                    </select>
                </label>
            <? else: ?>
                <div>Товара нет в наличии</div>
            <? endif; ?>
        </header>

        <? if (!empty($arResult['STORES'])): ?>

            <section class="shop-selector phone--only">
                <label for="shop">
                    <span>Магазин</span>
                    <select id="shop" name="shop" class="selectize">
                        <? foreach ($arResult['STORES'] as $i => $iStore): ?>
                            <option value="<?= $i; ?>" selected="selected"><?= $arResult['STORES_NAME'][$iStore]; ?></option>
                        <? endforeach; ?>
                    </select>
                </label>
            </section>
        <? endif; ?>

        <article>
            <ul class="shop-list phone--hidden"></ul>
            <div class="shop-list-map">
                <div class="shop-list-map__container js-shop-list-map shop-map"
                     data-lat="<?= $arResult['LOCATION']['LAT'] ?>"
                     data-lon="<?= $arResult['LOCATION']['LON'] ?>"></div>
            </div>
        </article>

        <!--<footer class="phone--only">
            <div class="input-group">
                <input type="search" placeholder="Поиск по адресу, индексу или названию магазина">
                <button class="button button--third"><i class="icon icon-search"></i></button>
            </div>
            <div class="input-group">
                <button class="button button--primary js-popup-close">Назад</button>
            </div>
        </footer>-->
    </div>
<? endif; ?>