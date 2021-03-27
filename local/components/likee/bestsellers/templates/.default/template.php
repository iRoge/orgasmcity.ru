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
/** @var LikeeBestsellersComponent $component */

$this->setFrameMode(true);

/*$arProducts1 = array_slice($arResult['PRODUCTS'], 0, 2);
$arProducts2 = array_slice($arResult['PRODUCTS'], 2, 1);
$arProducts3 = array_slice($arResult['PRODUCTS'], 5);*/

$arProducts1 = array_slice($arResult['PRODUCTS'], 0, 2);
$arProducts2 = array_slice($arResult['PRODUCTS'], 2, 1);
$arProducts3 = array_slice($arResult['PRODUCTS'], 3, 2);
?>

<div class="bestsellers in-view">
    <h2 class="h2--square">Бестселлеры</h2>
    <div class="phone--hidden">
        <div class="bestsellers__grid">
            <? if ($arProducts1): ?>
                <div class="container container--no-padding products-grid">
                    <? foreach ($arProducts1 as $iKey => $arItem): ?>
                        <div class="column-2<?= $iKey == 0 ? ' pre-2' : ''; ?>">
                            <? include 'element.php'; ?>
                        </div>
                        <? if ($iKey == 0): ?>
                            <div class="column-2" style="min-height: 1px"></div>
                        <? endif; ?>
                    <? endforeach; ?>
                </div>
            <? endif; ?>

            <? if ($arProducts2): ?>
                <div class="container products-grid">
                    <? foreach ($arProducts2 as $iKey => $arItem): ?>
                        <div class="column-4<?= $iKey == 0 ? ' pre-2' : ''; ?>">

                            <div class="container">
                                <div class="column-5 pre-5">
                                    <? include 'element.php'; ?>
                                </div>
                            </div>


                            <?/*<div class="container">
                                <? if ($iKey == 0 && !empty($arResult['ACTIONS']['SMALL'])): ?>
                                    <? $arItem = $arResult['ACTIONS']['SMALL']; ?>
                                    <div class="column-5">
                                        <? include 'element.php'; ?>
                                    </div>
                                <? endif; ?>

                                <? if (!empty($arParams['INSATGRAM_LINK'])): ?>
                                    <div class="column-5">
                                        <a class="products-item products-item--square" href="<?= $arParams['INSATGRAM_LINK']; ?>" target="_blank">
                                            <div class="products-item__content">
                                                <img src="<?= SITE_TEMPLATE_PATH; ?>/images/products-grid/instagram.png">
                                            </div>
                                        </a>
                                    </div>
                                <? endif; ?>
                            </div>*/?>
                        </div>
                    <? endforeach; ?>

                    <? if (!empty($arResult['ACTIONS']['BIG'])): ?>
                        <div class="column-4">
                            <? $arItem = $arResult['ACTIONS']['BIG']; ?>
                            <? include 'element.php'; ?>
                        </div>
                    <? endif; ?>
                </div>
            <? endif; ?>

            <? if ($arProducts1): ?>
                <div class="container container--no-padding products-grid">
                    <? foreach ($arProducts3 as $iKey => $arItem): ?>
                        <div class="column-2<?= $iKey == 0 ? ' pre-2' : ''; ?>">
                            <? include 'element.php'; ?>
                        </div>
                        <? if ($iKey == 0): ?>
                            <div class="column-2" style="min-height: 1px"></div>
                        <? endif; ?>
                    <? endforeach; ?>
                </div>
            <? endif; ?>
            <?/* if ($arProducts3): ?>
                <div class="container products-grid">
                    <? foreach ($arProducts3 as $iKey => $arItem): ?>
                        <div class="column-2<?= $iKey > 0 ? ' pre-2' : ''; ?>">
                            <? include 'element.php'; ?>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endif; */?>
        </div>

        <? if (!empty($arParams['CATALOG_LINK'])): ?>
            <div class="container show-more">
                <div class="column-6 pre-2">
                    <a href="<?= $arParams['CATALOG_LINK']; ?>" class="button button--xxl button--transparent">
                        Смотреть все
                    </a>
                </div>
            </div>
        <? endif; ?>
    </div>

    <div class="products-grid phone--only container container--no-padding">
        <div class="container js-products-slider">
            <? foreach ($arResult['PRODUCTS'] as $arItem): ?>
                <div class="column-25 column-md-1 column-xs-2">
                    <div class="products-item products-item--square">
                        <div class="products-item__content">
                            <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>"
                               style="background-image: url('<?= $arItem['PREVIEW_PICTURE']['SRC']; ?>')"
                               class="products-item__image"></a>

                            <!--<div class="products-item__information">
                                <div class="container products-item__title">
                                    <div class="column-10"><b><? /*= $arItem['NAME']; */ ?></b></div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>