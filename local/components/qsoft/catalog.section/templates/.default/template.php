<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
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

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;
global $USER;
$curPage = $APPLICATION->GetCurPage(false);
$freeDeliveryMinSum = Option::get("respect", "free_delivery_min_summ", 4000);
if (!$arResult['IS_AJAX']) {
    Asset::getInstance()->addString('<link rel="canonical" href="https://' . SITE_SERVER_NAME . $curPage . '">');
    Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/lib/jquery.ellipsis.min.js', true);
//    Asset::getInstance()->addCss('/local/components/qsoft/catalog.section/templates/.default/libs/jquery.scrollbar.css');
//    Asset::getInstance()->addJs('/local/components/qsoft/catalog.section/templates/.default/libs/jquery.scrollbar.min.js');
//    Asset::getInstance()->addJs('/local/components/qsoft/catalog.section/templates/.default/libs/lazysizes.min.js');
?>
<script>
    var currentHost = "<?=$arResult['CURRENT_HOST']?>";
</script>
<div class="col-xs-12 catalog-wrapper">
    <div class="main main--banner">
        <?php $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "",
            array(
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "0",
            )
        ); ?>
        <h1 class="zagolovok">
            <?php
            $APPLICATION->SetTitle($arResult['TITLE']);
            $APPLICATION->ShowTitle(false);
            ?>
        </h1>
        <?php
        if ($arResult['SHOW_CATALOGS_LINE']) {
            $filter = [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'ACTIVE' => 'Y',
                'SECTION_ID' => $arResult['SECTION_ID'],
            ];
            $APPLICATION->IncludeComponent(
                'orgasmcity:catalogs.line',
                'default',
                [
                    'MAX_COUNT' => 24,
                    'FILTERS' => $filter,
                    'ICONS_TYPE' => 'COLORED'
                ]
            );
        }

        if ($arResult['TIMER_DATE'] !== null) {
            $APPLICATION->IncludeComponent(
                'orgasmcity:timer',
                'catalog',
                [
                    'DATE_TO' => $arResult['TIMER_DATE'],
                ]
            );
        }
        ?>
        <!-- catalog -->
        <div class="catalog">
            <!-- banner -->
            <?php if ($arResult['BANNER']['SINGLE'] || $arResult['BANNER']['MOBILE']) : ?>
                <?php if ($arResult['BANNER']['SINGLE']) : ?>
                    <div class="catalog__banner clearfix">
                        <div class="col-xs-12">
                            <div class="banner">
                                <img src="<?= $arResult['BANNER']['SINGLE'] ?>" class="banner__img" alt="">
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="cards__banner stock-banner stock-banner--external">
                        <div class="stock-banner__wrapper">
                            <img class="stock-banner__img" src="<?= $arResult['BANNER']['MOBILE'] ?>" alt="">
                            <?php foreach ($arResult['BANNER']['MOBILE_LINKS'] as $arItem) : ?>
                                <a class="stock-banner__link" href="<?= $arItem['LINK'] ?>"
                                   style="<?= $arItem['STYLE'] ?>"></a>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php endif ?>
            <?php endif ?>
            <!-- banner -->
            <!-- main -->
            <div class="catalog__main">
                <!-- settings -->
                <div class="catalog__settings clearfix">
                    <div class="catalog__settings-col catalog__settings-col--left col-xs-3">
                        <!-- filter-toggle -->
                        <span class="catalog__filter-toggle catalog__filter-toggle--desktop filter-toggle js-filter-toggle">
                            <span class="filter-toggle__icon">
                                <img src="/local/templates/respect/img/list2.png"
                                     class="filter-toggle__icon-pic filter-toggle__icon-pic--hide" alt="">
                                <img src="/local/templates/respect/img/z-hide2.png"
                                     class="filter-toggle__icon-pic filter-toggle__icon-pic--show" alt="">
                            </span>
                            <span class="filter-toggle__text filter-toggle__text--hide">Скрыть фильтр</span>
                            <span class="filter-toggle__text filter-toggle__text--show">Раскрыть фильтр</span>
                        </span>
                        <!-- /filter-toggle -->
                        <!-- filter-toggle -->
                        <span class="catalog__filter-toggle catalog__filter-toggle--device filter-toggle js-filter-toggle-mobile">
                            <span class="filter-toggle__icon">
                                <img src="/local/templates/respect/img/svg/filter.svg"
                                     class="filter-toggle__icon-pic filter-toggle__icon-pic--hide" alt="">
                            </span>
                            <span class="filter-toggle__text filter-toggle__text--mobile">Фильтр</span>
                        </span>
                        <!-- /filter-toggle -->
                    </div>
                    <div class="lds-ring lds-ring--settings" style="visibility: hidden">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <?php if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                    <div class="catalog__settings-col catalog__settings-col--right col-xs-9">
                        <div class="catalog__settings-line">
                            <!-- sort -->
                            <div class="catalog__sort catalog__sort--desktop sort">
                                <span class="sort__title">Сортировка:</span>
                                <ul class="sort__items">
                                    <?php foreach ($arResult['SORT_ARRAY'] as $key => $value) : ?>
                                        <li class="sort__item" data-sort="<?= $key ?>">
                                            <span class="sort__text <?= $arResult['SELECTED_SORT'] === $key ? 'sort__text--active' : '' ?>"><?= $value ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <!-- /sort -->
                            <!-- view -->
                            <div class="catalog__view view js-view">
                                <span class="view__item catalog-sort catalog-sort--mobile-icon">
                                    <span class="view__item-icon view__item-icon--device">
                                        <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/img/svg/sort-arrows.svg')) {
                                            include $_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/img/svg/sort-arrows.svg';
                                        } ?>
                                    </span>
                                </span>
                                <span class="view__item view__item--small js-view-item <?= $arResult['USER_SETTINGS']['GRID'] == 'small' ? 'view__item--active' : '' ?>"
                                      data-view-type="small" title="Изменить вид">
                                    <i class="view__item-icon view__item-icon--desktop icon icon-small-tiles"></i>
                                    <i class="view__item-icon view__item-icon--device icon icon-big-tiles"></i>
                                </span>
                                <span class="view__item view__item--big js-view-item <?= $arResult['USER_SETTINGS']['GRID'] == 'big' ? 'view__item--active' : '' ?>"
                                      data-view-type="big" title="Изменить вид">
                                    <i class="view__item-icon view__item-icon--desktop icon icon-big-tiles"></i>
                                    <i class="view__item-icon view__item-icon--device view__item-icon--square"></i>
                                </span>
                            </div>
                            <!-- /view -->
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                <div class="catalog__sort catalog__sort--mobile">
                    <select class="catalog__sort-select js-change-sort">
                        <?php foreach ($arResult['SORT_ARRAY'] as $key => $value) : ?>
                            <option value="<?= $key ?>" <?= $arResult['SELECTED_SORT'] === $key ? 'selected' : '' ?>><?= $value ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <!-- /settings -->
<?php } ?>
                <?php if ($arResult['IS_AJAX']) {
                        $APPLICATION->RestartBuffer();
                    }
                    ?>
                <!-- content -->
                <div class="catalog__content clearfix">
                    <!-- filter -->
                    <div class="catalog__content-col catalog__content-col--sidebar js-filter-col filter col-xs-3">
                        <div class="filter__displayed">
                            <div class="filter-reset-container">
                                <button class="js-filter-button-reset filter-reset-btn filter__disabled-reset-btn" type="button" disabled>
                                </button>
                                <div class="filter-reset-tooltip">
                                    Сбросить все фильтры
                                </div>
                            </div>
                            <div class="load-more-btn-loader filter-btn-loader"></div>
                            <button class="js-filter-button-submit filter__status-text-btn" type="button" disabled>
                                        <span class="filter-status-area">
                                            показано
                                        </span>
                                <span class="items-count">
                                            <?=$arResult['MODELS_COUNT']?>
                                        </span>
                            </button>
                        </div>
                        <div class="lds-ring-container-first">
                            <div class="lds-ring lds-ring--button">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                        <div class="filters js-filter-wrapper filter__wrapper">
                            <input hidden disabled type="text" class="all-items-count" value="<?=$arResult['MODELS_COUNT']?>">
                            <div class="cls-blue-menu cls-blue-menu2 js-filter-mobile-close"></div>
                            <form class="form filter__form js-filter-form" name="_form"
                                  action="<?= $curPage ?>" method="get">
                                <?php if (!empty($arResult['SAME_SECTIONS']) && $arResult['IS_AJAX']) :?>
                                    <div class="in-left-catalog subsections-block">
                                        <div class="name-h3 <?=$GLOBALS['device_type'] == 'mobile' ? '' : 'active-name-h3'?>">
                                            <div class="filter-name">
                                                Похожие разделы
                                            </div>
                                            <svg class="minus" <?=$GLOBALS['device_type'] == 'mobile' ? '' : 'style="display: inline; position: absolute"'?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                            </svg>
                                            <svg class="plus" <?=$GLOBALS['device_type'] == 'mobile' ? '' : 'style="display: none"'?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                            </svg>
                                        </div>
                                        <div class="in-in-left scrollbar-inner max-height-400" data-filter="type" <?=$GLOBALS['device_type'] == 'mobile' ? 'style="display: none"' : 'style="display: flex;"'?>>
                                            <ul class="filter__main-list">
                                                <?php foreach ($arResult['SAME_SECTIONS'] as $section) :?>
                                                    <li class="filter__type-item">
                                                        <a class="name-h3" href="<?=$section['SECTION_PAGE_URL']?>" style="display: block; width: 100%; font-weight: bold; font-family: 'gilroyRegular'; font-size: 15px; color: #000000;">
                                                            <?=$section['NAME']?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                <?php endif; ?>
                                <input id="set_filter" type="hidden" name="set_filter" value="Y">
                                <?php
                                foreach ($arResult['FILTER_KEYS'] as $filterKey) : ?>
                                    <?php if (in_array($filterKey, ['PRICE', 'DIAMETER', 'LENGTH']) && !empty($arResult['FILTER']['MAX_' . $filterKey]) && !empty($arResult['FILTER']['MIN_' . $filterKey])) : ?>
                                        <div class="in-left-catalog in-left-catalog--price<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' in-left-catalog--checked' : '' ?>">
                                            <div class="name-h3<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' active-name-h3' : '' ?>">
                                                <a href="javascript:void(0);" class="clear-section">
                                                    <svg class="clear-section__icon">
                                                        <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#close"></use>
                                                    </svg>
                                                </a>
                                                <div class="filter-name">
                                                    <?= GetMessage($filterKey) ?>
                                                </div>
                                                <svg class="minus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:inline"' : '' ?>>
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                                </svg>
                                                <svg class="plus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:none"' : '' ?>>
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                                </svg>
                                            </div>
                                            <div class="in-in-left"<?=$arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:flex"' : '' ?>>
                                                <div class="from-filter">
                                                    <span>От:</span>
                                                    <input id="min_<?=strtolower($filterKey)?>" class="js-number-filter" type="number" name="min_<?=strtolower($filterKey)?>"
                                                           value="<?= $arResult['FILTER']['CHECKED']['MIN_' . $filterKey] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MIN_' . $filterKey] ?>">
                                                </div>
                                                <div class="to-filter">
                                                    <span>До:</span>
                                                    <input id="max_<?=strtolower($filterKey)?>" class="js-number-filter" type="number" name="max_<?=strtolower($filterKey)?>"
                                                           value="<?= $arResult['FILTER']['CHECKED']['MAX_' . $filterKey] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MAX_' . $filterKey] ?>">
                                                </div>
                                                <div style="clear: both"></div>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <?php continue; ?>
                                    <?php endif; ?>
                                    <?php $value = $arResult['FILTER'][$filterKey];
                                    if (empty($value)) {
                                        continue;
                                    } ?>
                                    <?php $jsKey = $arResult['JS_KEYS'][$filterKey] ?>
                                    <div class="in-left-catalog<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' in-left-catalog--checked' : '' ?>">
                                        <div class="name-h3<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' active-name-h3' : '' ?>">
                                            <a href="javascript:void(0);" class="clear-section">
                                                <svg class="clear-section__icon">
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#close"></use>
                                                </svg>
                                            </a>
                                            <div class="filter-name">
                                                <?= GetMessage($filterKey) ?>
                                            </div>
                                            <svg class="minus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:inline"' : '' ?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                            </svg>
                                            <svg class="plus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:none"' : '' ?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                            </svg>
                                        </div>
                                        <div class="in-in-left scrollbar-inner"<?=$arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:flex"' : '' ?>
                                             data-filter-name="<?= $jsKey ?>">
                                            <?php if ($filterKey === 'COLORS') :?>
                                                <?php foreach ($value as $xml_id => $color) : ?>
                                                    <div class="outer-color">
                                                        <input id="color_<?= $xml_id ?>"
                                                                class="checkbox_size"
                                                                type="checkbox"
                                                                name="color_<?= $xml_id ?>"
                                                                value="<?= $xml_id ?>"
                                                                onchange="smartFilter.click(this)"
                                                            <?php if ($color['CHECKED']) : ?>
                                                                checked
                                                            <?php endif; ?>
                                                            <?php if ($color['DISABLED']) : ?>
                                                                disabled
                                                            <?php endif; ?>
                                                        />
                                                        <label for="color_<?= $xml_id ?>" class="label-for-color <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                            <?php if ($color['VALUE']['IMG_SRC']) : ?>
                                                            <img class="inner-color" width="22" height="22" src="<?=$color['VALUE']['IMG_SRC']; ?>" alt="">
                                                            <?php endif; ?>
                                                            <?=$color['VALUE']['UF_NAME']; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else :?>
                                                <?php foreach ($value as $key => $item) : ?>

                                                    <input id="<?=$jsKey ?>_<?=sha1($key)?>"
                                                           class="checkbox_size"
                                                           type="checkbox"
                                                           name="<?=$jsKey?>"
                                                           value="<?=$key?>"
                                                        <?php if (!empty($item['CHECKED'])) : ?>
                                                            checked
                                                        <?php endif; ?>
                                                        <?php if (!empty($item['DISABLED'])) : ?>
                                                            disabled
                                                        <?php endif; ?>
                                                           onchange="smartFilter.click(this)">
                                                    <label for="<?=$jsKey ?>_<?=sha1($key)?>" <?= !empty($item['DISABLED']) ? 'class="mydisabled"' : '' ?>><?=$item['VALUE'] ?></label>

                                                <?php endforeach; ?>
                                            <?php endif;?>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="lds-ring-container" style="display: none;">
                                    <div class="lds-ring lds-ring--button">
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                        <div></div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="filters__bottom">
                            <input type="button"
                                   class="filters__btn filters__btn--submit filters__btn-text--desktop js-filter-button-submit filters__btn--disabled"
                                   value="Применить фильтр" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--reset filters__reset-btn filters__btn-text--desktop js-filter-button-reset filters__btn--disabled"
                                   value="Сбросить все фильтры" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--reset filters__reset-btn filters__btn-text--mobile js-filter-button-reset filters__btn--disabled"
                                   value="Сбросить" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--submit filters__btn-text--mobile js-filter-button-submit js-filter-button-mobile filters__btn--disabled"
                                   value="Применить" disabled>
                        </div>
                    </div>
                    <!-- /filter -->
                    <div class="catalog__content-col catalog__content-col--main col-xs-9">
                        <!-- cards -->
                        <div class="catalog__cards cards js-cards<?=$arResult['USER_SETTINGS']['GRID'] == ' big' ? ' cards--big' : '' ?>">
                            <div class="cards__box">
                                <?php if ($arResult['BANNER']['DESKTOP']) : ?>
                                    <div class="cards__banner stock-banner stock-banner--internal">
                                        <div class="stock-banner__wrapper">
                                            <img class="stock-banner__img" src="<?= $arResult['BANNER']['DESKTOP'] ?>"
                                                 alt="">
                                            <?php foreach ($arResult['BANNER']['DESKTOP_LINKS'] as $arItem) : ?>
                                                <a class="stock-banner__link" href="<?= $arItem['LINK'] ?>"
                                                   style="<?= $arItem['STYLE'] ?>"></a>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                                <?php if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                                    <?php while ($arItem = $arResult['ITEMS']->Fetch()) { ?>
                                        <div class="product-card<?=$arResult['USER_SETTINGS']['GRID'] == 'big' ? ' col-lg-4 col-md-6 col-sm-6 col-xs-12' : ' col-lg-3 col-md-4 col-sm-4 col-xs-6' ?>">
                                            <div class="product-card-wrapper">
                                                <div class="product-icons-wrap">
                                                    <!--                            <img src="" alt="">-->
                                                    <?php if ($arItem['PROPERTY_BESTSELLER_VALUE']) { ?>
                                                        <img style="max-width: 100%;margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/hitProduct.svg" alt="Sale">
                                                    <?php } ?>
                                                    <?php if ($arItem['PROPERTY_NEW_VALUE']) { ?>
                                                        <img style="max-width: 100%;margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/newProduct.svg" alt="Sale">
                                                    <?php } ?>
                                                    <?php if ($arItem['DISCOUNT']) { ?>
                                                        <?php if ($arItem['DISCOUNT_DATE_TO']) { ?>
                                                            <img style="max-width: 100%;margin-top: 5px" src="<?=SITE_TEMPLATE_PATH?>/img/svg/saleProduct.svg" alt="Sale">
                                                        <?php } ?>
                                                        <div class="sale-tooltip" title="Размер скидки"><?=-$arItem['DISCOUNT']?>%</div>
                                                    <?php } ?>
                                                </div>
                                                <button title="Добавить в избранное" type="button" class="heart__btn<?=isset($arResult['FAVORITES_PROD_IDS'][$arItem['ID']]) ? ' active' : '' ?> js-favour-heart" data-id="<?=$arItem['ID']?>">
                                                    <svg width="30" height="30" viewBox="0 0 23 22" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M0 5.86414C0 -0.440483 8.73003 -2.77704 11.4163 4.52139C14.1025 -2.77704 22.8325 -0.440483 22.8325 5.86414C22.8325 12.714 11.4163 21.3989 11.4163 21.3989C11.4163 21.3989 0 12.714 0 5.86414Z" fill="black"/>
                                                    </svg>
                                                </button>
                                                <a target="_blank" class="product-href-wrapper" href="<?=$arItem["DETAIL_PAGE_URL"]?>">
                                                    <div class="product-img-wrapper">
                                                        <img class="product-img lazy-img" data-src="<?=$arItem["DETAIL_PICTURE"]?>" alt="<?=$arItem['NAME']?>">
                                                    </div>
                                                    <span class="product-title"><?=$arItem['NAME']?></span>
                                                </a>
                                                <div class="product-card-bottom">
                                                    <div class="product-card-price-wrapper">
                                                        <?php if ($arItem['DISCOUNT']) { ?>
                                                            <span class="product-card-old-price"><?=number_format($arItem['OLD_PRICE'], 0, '', ' ');?> ₽</span>
                                                        <?php } ?>
                                                        <span class="product-card-price<?=$arItem['DISCOUNT'] ? ' price-red' : ''?>"><?=number_format($arItem['PRICE'], 0, '', ' ');?> ₽</span>
                                                    </div>
                                                    <div class="product-card-buy-btn-wrapper">
                                                        <button
                                                                data-url="<?=$arItem["DETAIL_PAGE_URL"]?>"
                                                                <?=count($arItem['ASSORTMENTS']) > 1 ? '' : 'data-id="' . reset($arItem['ASSORTMENTS'])['ID'] . '"'?>
                                                                onclick="addItemToCartOrOpenDetail(this)"
                                                                data-name="<?=$arItem['NAME']?>"
                                                                data-price="<?=$arItem['PRICE']?>"
                                                                class="product-card-buy-btn"
                                                        >
                                                            <?=count($arItem['ASSORTMENTS']) > 1 ? 'Купить' : 'В корзину'?>
                                                        </button>
                                                    </div>
                                                    <?php if ($USER->GetID() == 1 || $USER->GetID() == 15) {
                                                        $wholesaleprice = $arItem['WHOLEPRICE'];
                                                        ?>
                                                        Цена закупки <?=$wholesaleprice?> ₽
                                                        <br>
                                                        Наценка <?=(int)(($arItem['PRICE'] - $wholesaleprice)*100/$wholesaleprice)?>%
                                                    <?php }?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php else : ?>
                                    <div class="page-massage <?= !empty($arResult['TAGS']) ? 'recomendation' : '' ?>">
                                        <?php if (CSite::InDir('/catalog/favorites/')) : ?>
                                            <?php ShowError('Ваш список избранного пока пуст') ?>
                                        <?php elseif ($arResult['IS_AJAX']) : ?>
                                            <?php ShowError('Товары не найдены, измените или сбросьте настройки фильтра') ?>
                                        <?php elseif (!empty($arResult['TAGS'])) : ?>
                                            <?php ShowError('Воспользуйтесь подобранными товарными категориями (вверху страницы). <br><br>А так же индивидуальными предложениями <br>(внизу страницы - блок рекомендуемых вам товаров).') ?>
                                        <?php else : ?>
                                            <?php ShowError('Товары не найдены') ?>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                        <div class="catalog__navigation">
                            <?php if (!empty($arResult['NAV_STRING'])) : ?>
                                <?= $arResult['NAV_STRING'] ?>
                            <?php endif ?>
                        </div>
                    </div>
                </div>
                <!-- /content -->
                <?php if ($arResult['IS_AJAX']) {
                    //$APPLICATION->FinalActions();
                    exit;
                } ?>
            </div>
            <!-- /main -->
        </div>
        <!-- /catalog -->
    </div>
</div>

