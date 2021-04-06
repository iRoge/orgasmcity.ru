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

if (empty($arParams['COLOR']))
    $arParams['COLOR'] = key($arResult['MATRIX']);

$productPageUrl = '/'.$arResult['CODE'];
?>

<script>

    $(function () {
        window.page_url = '<?= $productPageUrl; ?>';
        window.application.addUrl({
            'shopList': '<?= $productPageUrl; ?>?action=get_amount_json',
            'shopListPage': '<?= $productPageUrl; ?>?action=get_amount',
            'product': '<?= $productPageUrl; ?>?action=get_one_click'
        });
    });

    BX.message({
        'CATALOG_ELEMENT_TEMPLATE_PATH': '<?= $templateFolder; ?>',
        'IS_PARTNER': '<?= \Likee\Site\User::isPartner() ? 'Y' : 'N'; ?>',
        'ONE_CLICK_URL': '<?= $productPageUrl; ?>?action=get_one_click'
    });
</script>

<? if (!empty($arResult)): ?>
    <div class="container container--no-padding">
        <div class="column-8 pre-1">
            <section class="product-page">

                <? if ('N' == $arResult['AVAILABILITY_IN_REGION']) : ?>
                <div class="product-page__na">Данный артикул недоступен для заказа в вашем городе.</div>
                <? endif; ?>

                <div class="container">
                    <div class="column-5 product-page__left phone--hidden">
                    <? foreach ($arResult['LABELS'] as $sClass => $arLabel) : ?>
                        <a class="products-item__label products-item__label--full-product products-item__label--<?= $sClass ?>" 
                                href="<?= $arLabel['PAGE_URL'] ?>" 
                                title="<?= $arLabel['NAME'] ?>" 
                                style="background-image: url('<?= $arLabel['SRC']; ?>')"></a>
                    <? endforeach; ?>
                        <section class="product-gallery">
                            <div class="product-gallery__slider">
                                <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto): ?>
                                    <a id="product-image-<?= $iKey; ?>" href="<?= $arPhoto['SRC']; ?>"
                                       class="product-image">
                                        <span><img src="<?= $arPhoto['THUMB']; ?>" alt="<?= $arPhoto['ALT']; ?>"></span>
                                    </a>
                                <? endforeach; ?>
                            </div>
                        </section>
                    </div>

                    <form method="POST" class="column-5 column-md-2 product-page__right js-action-form">
                        <input type="hidden" name="action" value="ADD2BASKET">

                        
                        <? if (! empty($arParams['GROUP_SECTION']['ACTION']['IMAGE'])) : ?>
                            <div class="ga-block">
                                <div class="ga-block__img">
                                    <a href="/catalog/<?=$arParams['GROUP_SECTION']['CODE']?>/">
                                        <img src="<?= $arParams['GROUP_SECTION']['ACTION']['IMAGE']; ?>" alt="<?= $arParams['GROUP_SECTION']['ACTION']['NAME']; ?>" />
                                    </a>
                                </div>
                            </div>
                        <? endif; ?>

                        <div class="product-page__information">
                            <h1>
                                <?= $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']; ?>

                                <? if ($arResult['CATALOG_AVAILABLE'] == 'Y'): ?>
                                    <a class="product-page__wishlist js-add-to-favorites js-favorites-filled"
                                       title="Добавить в фавориты"
                                       href="/catalog/favorites/<?= $arResult['ID'] ?>/"
                                       data-id="<?= $arResult['ID'] ?>">
                                        <i class="icon icon-heart"></i>
                                    </a>
                                <? endif; ?>
                            </h1>

                            <? if (!empty($arResult['ARTICLE'])): ?>
                                <div class="product-page__sku">
                                    Арт. <?= $arResult['ARTICLE']; ?>
                                </div>
                            <? endif; ?>

                            <? if (!empty($arResult['PREVIEW_TEXT'])): ?>
                                <div class="product-page__description">
                                    <?= $arResult['PREVIEW_TEXT']; ?>
                                </div>
                            <? endif; ?>

                            <div class="product-page__container">
                                <div class="container cost-container">
                                    <?
                                    $bDiscount = $arResult['MIN_PRICE']['VALUE'] > $arResult['MIN_PRICE']['DISCOUNT_VALUE'];
                                    $bShowBonusInfo = $bDiscount || 'red' == $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'];
                                    ?>
                                    <div class="column-5 product__cost<?= $bDiscount ? ' discount' : ''; ?>">
                                        <? if ($bDiscount): ?>
                                            <span class="product__cost-segment--<?= $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] ?>"><?= $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></span>
                                            <div class="product__old-cost">
                                                <?= $arResult['MIN_PRICE']['PRINT_VALUE']; ?>
                                                <? !empty($arResult['MIN_PRICE']['DISCOUNT_PCT']) and print '<span class="product__old-pct">'.$arResult['MIN_PRICE']['DISCOUNT_PCT'].'</span>'; ?>
                                            </div>
                                        <? else: ?>
                                            <span class="product__cost-segment--<?= $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] ?>"><?= $arResult['MIN_PRICE']['PRINT_VALUE']; ?></span>
                                        <? endif; ?>
                                        <? if ($bShowBonusInfo) : ?>
                                            <div class="product__cost-segment-desc">* <?= 'red' == $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] ? 'бонусная программа не действует' : 'по условиям бонусной программы' ?></div>
                                        <? endif; ?>
                                        <div class="product__cost-segment-desc<? if ($bShowBonusInfo): ?> product__cost-segment-desc--s-line<? endif; ?>">* цены на сайте могут отличаться от цен в магазинах</div>
                                    </div>

                                    <? if ($arResult['BONUS'] > 0): ?>
                                        <div class="column-5 widget__bonus">
                                            <div class="widget__bonus-count">+<?= $arResult['BONUS']; ?></div>
                                            <div class="widget__bonus-text">бонусов</div>
                                        </div>
                                    <? endif; ?>
                                </div>

                                <? if (!empty($arResult['PHOTOS'])): ?>
                                    <div class="container phone--only">
                                        <section class="product-gallery">
                                            <div class="product-gallery__slider">
                                                <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto): ?>
                                                    <a id="product-image-<?= $iKey; ?>" href="<?= $arPhoto['SRC']; ?>"
                                                       class="product-image">
                                                        <span><img src="<?= $arPhoto['THUMB']; ?>" alt="<?= $arPhoto['ALT']; ?>"></span>
                                                    </a>
                                                <? endforeach; ?>
                                            </div>
                                        </section>
                                    </div>
                                <? endif; ?>

                                <div class="container<? if ($bShowBonusInfo): ?> md-up-margin-top-35<? endif; ?>">
                                    <div class="column-10">

                                        <? if ($arResult['CATALOG_AVAILABLE'] == 'Y' && !empty($arResult['SIZES'])): ?>
                                            <? if ($arResult['NO_SIZES']): ?>
                                                <? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
                                                    <?
                                                        $iOfferID = intval($arSize['OFFER_ID']);
                                                        $bCanBuy = $arSize['CAN_BUY'];
                                                        $bCanReserved = $arSize['CAN_RESERVED'];

                                                        if ($bCanBuy || $bCanReserved): ?>
                                                            <input type="hidden" name="id" class="js-offer js-offer-<?= $iOfferID; ?>" value="<?= $iOfferID; ?>" />
                                                        <? endif; ?>
                                                <? endforeach; ?>
                                            <? elseif (\Likee\Site\User::isPartner()): ?>
                                                <div class="product-module">
                                                    <div class="product-module__title">Выберите размер и количество
                                                    </div>
                                                    <div class="product-module__content">
                                                        <div class="size-selector size-selector--wrap js-size-selector">
                                                            <? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
                                                                <?
                                                                $iOfferID = intval($arSize['OFFER_ID']);
                                                                $bCanBuy = $arSize['CAN_BUY'];
                                                                $bCanReserved = $arSize['CAN_RESERVED'];

                                                                $sClass = '';
                                                                if (!$bCanBuy && !$bCanReserved)
                                                                    $sClass = 'missed';
                                                                ?>
                                                                <? if ($bCanBuy || $bCanReserved): ?>
                                                                    <a class="<?= $sClass; ?>"
                                                                        data-offer-id="<?= $iOfferID; ?>"><?= $sSize; ?></a>
                                                                    <input type="hidden" name="id[]"
                                                                        value="<?= $arSize['OFFER_ID']; ?>"
                                                                        class="js-offer js-offer-<?= $arSize['OFFER_ID']; ?> <?= $sClass; ?>"
                                                                        disabled>
                                                                <? endif; ?>
                                                            <? endforeach; ?>
                                                        </div>
                                                        <? /* <a class="product-module__info">Таблица размеров</a> */ ?>
                                                        <div class="alert alert--danger js-offer-error"
                                                             style="display: none;">
                                                            <div class="alert-content">
                                                                <i class="icon icon-exclamation-circle"></i>
                                                                Выберите размер для продолжения заказа
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <? else: ?>
                                                <div class="product-module">
                                                    <div class="product-module__title phone--only">Размеры</div>
                                                    <div class="product-module__content">
                                                        <div class="size-selector size-selector--wrap js-size-selector">
                                                            <? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
                                                                <?
                                                                $iOfferID = intval($arSize['OFFER_ID']);
                                                                $bCanBuy = $arSize['CAN_BUY'];
                                                                $bCanReserved = $arSize['CAN_RESERVED'];

                                                                $sClass = '';
                                                                if (!$bCanBuy && !$bCanReserved)
                                                                    $sClass = 'missed';
                                                                ?>
                                                                <? if ($bCanBuy || $bCanReserved): ?>
                                                                    <a class="<?= $sClass; ?>"
                                                                        data-offer-id="<?= $iOfferID; ?>"><?= $sSize; ?></a>
                                                                    <input type="hidden" name="id"
                                                                           class="js-offer js-offer-<?= $iOfferID; ?>"
                                                                           value="<?= $iOfferID; ?>" disabled>
                                                                <? endif; ?>
                                                            <? endforeach; ?>
                                                        </div>
                                                        <? /* <a class="product-module__info">Таблица размеров</a> */ ?>
                                                        <div class="alert alert--danger js-offer-error"
                                                             style="display: none;">
                                                            <div class="alert-content">
                                                                <i class="icon icon-exclamation-circle"></i>
                                                                Выберите размер для продолжения заказа
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="product-page__container">
                                <? if ($arResult['CATALOG_AVAILABLE'] == 'Y'): ?>
                                    <? if (\Likee\Site\User::isPartner()): ?>
                                        <div class="container">
                                            <div class="column-10">
                                                <input class="button button--primary button--outline button--xxl button--block js-buy-button"
                                                       type="submit" value="Добавить в корзину">
                                            </div>
                                        </div>
                                        <br>
                                    <? elseif ('Y' == $arResult['AVAILABILITY_IN_REGION']): ?>
                                        <?if(isset($arResult['SECTION_SIZES_TAB'])):?>
                                            <div class="sizes-popup-area">
                                                <a class="sizes-popup" href="#">Руководство по размерам</a>
                                                <div class="sizes-popup-block" style="display:none;">
                                                    <div class="tab-size-block">
                                                        <?=$arResult['SECTION_SIZES_TAB'];?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?endif;?>
                                        <? include 'buy_block.php'; ?>
                                    <? endif; ?>
                                <? endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
<? else: ?>
    <div class="container">
        <div class="column-8 pre-1">
            <div class="alert alert-danger">Элемент не найден!</div>
        </div>
    </div>
<? endif; ?>
