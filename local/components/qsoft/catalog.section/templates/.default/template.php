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
                            <div class="cls-blue-menu cls-blue-menu2 js-filter-mobile-close">
                                <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
                                     width="20px" height="20px" viewBox="0 0 20.000000 20.000000"
                                     preserveAspectRatio="xMidYMid meet">

                                    <g transform="translate(0.000000,20.000000) scale(0.100000,-0.100000)"
                                       fill="#000000" stroke="none">
                                    </g>
                                </svg>
                            </div>
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
                                                        <svg height="100%" width="100%" style="max-width: 100%;margin-top: 5px" viewBox="0 0 62 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="62" height="31" rx="15.5" fill="url(#paint0_linear)"/>
                                                            <path d="M13.261 9.61414C13.6677 9.72747 13.981 9.93081 14.201 10.2241C14.4277 10.5175 14.541 10.8841 14.541 11.3241C14.541 11.9308 14.331 12.4041 13.911 12.7441C13.4977 13.0775 12.9177 13.2441 12.171 13.2441H9.62102C9.46102 13.2441 9.33435 13.2008 9.24102 13.1141C9.15435 13.0208 9.11102 12.8975 9.11102 12.7441V6.69414C9.11102 6.54081 9.15435 6.42081 9.24102 6.33414C9.33435 6.24081 9.46102 6.19414 9.62102 6.19414H12.061C12.7877 6.19414 13.3543 6.35747 13.761 6.68414C14.1743 7.00414 14.381 7.45414 14.381 8.03414C14.381 8.40747 14.281 8.73081 14.081 9.00414C13.8877 9.27747 13.6143 9.48081 13.261 9.61414ZM10.121 9.24414H11.901C12.8877 9.24414 13.381 8.87081 13.381 8.12414C13.381 7.75081 13.2577 7.47414 13.011 7.29414C12.771 7.11414 12.401 7.02414 11.901 7.02414H10.121V9.24414ZM12.061 12.4141C12.5743 12.4141 12.951 12.3208 13.191 12.1341C13.431 11.9475 13.551 11.6575 13.551 11.2641C13.551 10.8641 13.4277 10.5675 13.181 10.3741C12.941 10.1741 12.5677 10.0741 12.061 10.0741H10.121V12.4141H12.061ZM16.3984 13.2441C16.2384 13.2441 16.1117 13.2008 16.0184 13.1141C15.9317 13.0208 15.8884 12.8975 15.8884 12.7441V6.69414C15.8884 6.54081 15.9317 6.42081 16.0184 6.33414C16.1117 6.24081 16.2384 6.19414 16.3984 6.19414H20.0784C20.2317 6.19414 20.3484 6.23081 20.4284 6.30414C20.515 6.37747 20.5584 6.48081 20.5584 6.61414C20.5584 6.74747 20.515 6.85081 20.4284 6.92414C20.3484 6.99081 20.2317 7.02414 20.0784 7.02414H16.8984V9.24414H19.8784C20.0317 9.24414 20.1484 9.28081 20.2284 9.35414C20.315 9.42747 20.3584 9.53081 20.3584 9.66414C20.3584 9.79747 20.315 9.90081 20.2284 9.97414C20.1484 10.0475 20.0317 10.0841 19.8784 10.0841H16.8984V12.4141H20.0784C20.3984 12.4141 20.5584 12.5508 20.5584 12.8241C20.5584 12.9575 20.515 13.0608 20.4284 13.1341C20.3484 13.2075 20.2317 13.2441 20.0784 13.2441H16.3984ZM23.9703 13.3241C23.4636 13.3241 22.9803 13.2541 22.5203 13.1141C22.067 12.9741 21.7036 12.7808 21.4303 12.5341C21.3036 12.4275 21.2403 12.2908 21.2403 12.1241C21.2403 12.0041 21.2736 11.9041 21.3403 11.8241C21.407 11.7375 21.487 11.6941 21.5803 11.6941C21.6736 11.6941 21.787 11.7375 21.9203 11.8241C22.5136 12.2641 23.1903 12.4841 23.9503 12.4841C24.4836 12.4841 24.8936 12.3841 25.1803 12.1841C25.467 11.9841 25.6103 11.6975 25.6103 11.3241C25.6103 11.0308 25.477 10.8108 25.2103 10.6641C24.9436 10.5175 24.517 10.3775 23.9303 10.2441C23.3703 10.1241 22.9103 9.98747 22.5503 9.83414C22.1903 9.68081 21.907 9.47414 21.7003 9.21414C21.5003 8.94747 21.4003 8.61081 21.4003 8.20414C21.4003 7.79747 21.5103 7.43747 21.7303 7.12414C21.957 6.80414 22.2703 6.55747 22.6703 6.38414C23.0703 6.20414 23.527 6.11414 24.0403 6.11414C24.5136 6.11414 24.9503 6.18081 25.3503 6.31414C25.7503 6.44747 26.0903 6.64414 26.3703 6.90414C26.5036 7.01747 26.5703 7.15414 26.5703 7.31414C26.5703 7.42747 26.5336 7.52747 26.4603 7.61414C26.3936 7.70081 26.3136 7.74414 26.2203 7.74414C26.1336 7.74414 26.0236 7.70081 25.8903 7.61414C25.5836 7.38081 25.2936 7.21414 25.0203 7.11414C24.7536 7.00747 24.4303 6.95414 24.0503 6.95414C23.537 6.95414 23.137 7.06081 22.8503 7.27414C22.5636 7.48081 22.4203 7.77081 22.4203 8.14414C22.4203 8.46414 22.547 8.70747 22.8003 8.87414C23.0536 9.03414 23.4603 9.18081 24.0203 9.31414C24.607 9.44747 25.0803 9.58747 25.4403 9.73414C25.807 9.87414 26.097 10.0708 26.3103 10.3241C26.5303 10.5708 26.6403 10.8908 26.6403 11.2841C26.6403 11.6841 26.527 12.0408 26.3003 12.3541C26.0803 12.6608 25.767 12.9008 25.3603 13.0741C24.9603 13.2408 24.497 13.3241 23.9703 13.3241ZM29.9634 13.2941C29.81 13.2941 29.6834 13.2475 29.5834 13.1541C29.49 13.0541 29.4434 12.9275 29.4434 12.7741V7.05414H27.4434C27.1234 7.05414 26.9634 6.91081 26.9634 6.62414C26.9634 6.48414 27.0034 6.37747 27.0834 6.30414C27.17 6.23081 27.29 6.19414 27.4434 6.19414H32.4834C32.6367 6.19414 32.7534 6.23081 32.8334 6.30414C32.92 6.37747 32.9634 6.48414 32.9634 6.62414C32.9634 6.91081 32.8034 7.05414 32.4834 7.05414H30.4834V12.7741C30.4834 12.9275 30.4367 13.0541 30.3434 13.1541C30.25 13.2475 30.1234 13.2941 29.9634 13.2941Z" fill="white"/>
                                                            <path d="M11.441 24.1381C10.9343 24.1381 10.451 24.0681 9.99102 23.9281C9.53768 23.7881 9.17435 23.5948 8.90102 23.3481C8.77435 23.2414 8.71102 23.1048 8.71102 22.9381C8.71102 22.8181 8.74435 22.7181 8.81102 22.6381C8.87768 22.5514 8.95768 22.5081 9.05102 22.5081C9.14435 22.5081 9.25768 22.5514 9.39102 22.6381C9.98435 23.0781 10.661 23.2981 11.421 23.2981C11.9543 23.2981 12.3643 23.1981 12.651 22.9981C12.9377 22.7981 13.081 22.5114 13.081 22.1381C13.081 21.8448 12.9477 21.6248 12.681 21.4781C12.4143 21.3314 11.9877 21.1914 11.401 21.0581C10.841 20.9381 10.381 20.8014 10.021 20.6481C9.66102 20.4948 9.37768 20.2881 9.17102 20.0281C8.97102 19.7614 8.87102 19.4248 8.87102 19.0181C8.87102 18.6114 8.98102 18.2514 9.20102 17.9381C9.42768 17.6181 9.74102 17.3714 10.141 17.1981C10.541 17.0181 10.9977 16.9281 11.511 16.9281C11.9843 16.9281 12.421 16.9948 12.821 17.1281C13.221 17.2614 13.561 17.4581 13.841 17.7181C13.9743 17.8314 14.041 17.9681 14.041 18.1281C14.041 18.2414 14.0043 18.3414 13.931 18.4281C13.8643 18.5148 13.7843 18.5581 13.691 18.5581C13.6043 18.5581 13.4943 18.5148 13.361 18.4281C13.0543 18.1948 12.7643 18.0281 12.491 17.9281C12.2243 17.8214 11.901 17.7681 11.521 17.7681C11.0077 17.7681 10.6077 17.8748 10.321 18.0881C10.0343 18.2948 9.89102 18.5848 9.89102 18.9581C9.89102 19.2781 10.0177 19.5214 10.271 19.6881C10.5243 19.8481 10.931 19.9948 11.491 20.1281C12.0777 20.2614 12.551 20.4014 12.911 20.5481C13.2777 20.6881 13.5677 20.8848 13.781 21.1381C14.001 21.3848 14.111 21.7048 14.111 22.0981C14.111 22.4981 13.9977 22.8548 13.771 23.1681C13.551 23.4748 13.2377 23.7148 12.831 23.8881C12.431 24.0548 11.9677 24.1381 11.441 24.1381ZM15.8905 24.0581C15.7305 24.0581 15.6039 24.0148 15.5105 23.9281C15.4239 23.8348 15.3805 23.7114 15.3805 23.5581V17.5081C15.3805 17.3548 15.4239 17.2348 15.5105 17.1481C15.6039 17.0548 15.7305 17.0081 15.8905 17.0081H19.5705C19.7239 17.0081 19.8405 17.0448 19.9205 17.1181C20.0072 17.1914 20.0505 17.2948 20.0505 17.4281C20.0505 17.5614 20.0072 17.6648 19.9205 17.7381C19.8405 17.8048 19.7239 17.8381 19.5705 17.8381H16.3905V20.0581H19.3705C19.5239 20.0581 19.6405 20.0948 19.7205 20.1681C19.8072 20.2414 19.8505 20.3448 19.8505 20.4781C19.8505 20.6114 19.8072 20.7148 19.7205 20.7881C19.6405 20.8614 19.5239 20.8981 19.3705 20.8981H16.3905V23.2281H19.5705C19.8905 23.2281 20.0505 23.3648 20.0505 23.6381C20.0505 23.7714 20.0072 23.8748 19.9205 23.9481C19.8405 24.0214 19.7239 24.0581 19.5705 24.0581H15.8905ZM21.7302 24.0581C21.5768 24.0581 21.4535 24.0148 21.3602 23.9281C21.2735 23.8414 21.2302 23.7248 21.2302 23.5781V17.4781C21.2302 17.3248 21.2768 17.2014 21.3702 17.1081C21.4635 17.0081 21.5868 16.9581 21.7402 16.9581C21.9002 16.9581 22.0268 17.0048 22.1202 17.0981C22.2135 17.1914 22.2602 17.3181 22.2602 17.4781V23.1981H25.3002C25.4535 23.1981 25.5702 23.2348 25.6502 23.3081C25.7368 23.3814 25.7802 23.4881 25.7802 23.6281C25.7802 23.7681 25.7368 23.8748 25.6502 23.9481C25.5702 24.0214 25.4535 24.0581 25.3002 24.0581H21.7302ZM27.1989 24.0581C27.0456 24.0581 26.9222 24.0148 26.8289 23.9281C26.7422 23.8414 26.6989 23.7248 26.6989 23.5781V17.4781C26.6989 17.3248 26.7456 17.2014 26.8389 17.1081C26.9322 17.0081 27.0556 16.9581 27.2089 16.9581C27.3689 16.9581 27.4956 17.0048 27.5889 17.0981C27.6822 17.1914 27.7289 17.3181 27.7289 17.4781V23.1981H30.7689C30.9222 23.1981 31.0389 23.2348 31.1189 23.3081C31.2056 23.3814 31.2489 23.4881 31.2489 23.6281C31.2489 23.7681 31.2056 23.8748 31.1189 23.9481C31.0389 24.0214 30.9222 24.0581 30.7689 24.0581H27.1989ZM32.6777 24.0581C32.5177 24.0581 32.391 24.0148 32.2977 23.9281C32.211 23.8348 32.1677 23.7114 32.1677 23.5581V17.5081C32.1677 17.3548 32.211 17.2348 32.2977 17.1481C32.391 17.0548 32.5177 17.0081 32.6777 17.0081H36.3577C36.511 17.0081 36.6277 17.0448 36.7077 17.1181C36.7943 17.1914 36.8377 17.2948 36.8377 17.4281C36.8377 17.5614 36.7943 17.6648 36.7077 17.7381C36.6277 17.8048 36.511 17.8381 36.3577 17.8381H33.1777V20.0581H36.1577C36.311 20.0581 36.4277 20.0948 36.5077 20.1681C36.5943 20.2414 36.6377 20.3448 36.6377 20.4781C36.6377 20.6114 36.5943 20.7148 36.5077 20.7881C36.4277 20.8614 36.311 20.8981 36.1577 20.8981H33.1777V23.2281H36.3577C36.6777 23.2281 36.8377 23.3648 36.8377 23.6381C36.8377 23.7714 36.7943 23.8748 36.7077 23.9481C36.6277 24.0214 36.511 24.0581 36.3577 24.0581H32.6777ZM43.5173 23.4181C43.5773 23.5048 43.6073 23.5981 43.6073 23.6981C43.6073 23.8181 43.5573 23.9214 43.4573 24.0081C43.3639 24.0881 43.2539 24.1281 43.1273 24.1281C42.9406 24.1281 42.7906 24.0448 42.6773 23.8781L41.3373 21.7981C41.1839 21.5581 41.0206 21.3914 40.8473 21.2981C40.6739 21.1981 40.4473 21.1481 40.1673 21.1481H39.0373V23.5881C39.0373 23.7481 38.9906 23.8748 38.8973 23.9681C38.8106 24.0614 38.6873 24.1081 38.5273 24.1081C38.3739 24.1081 38.2506 24.0614 38.1573 23.9681C38.0639 23.8748 38.0173 23.7481 38.0173 23.5881V17.5081C38.0173 17.3548 38.0606 17.2348 38.1473 17.1481C38.2406 17.0548 38.3673 17.0081 38.5273 17.0081H41.0173C41.7973 17.0081 42.3906 17.1814 42.7973 17.5281C43.2106 17.8748 43.4173 18.3814 43.4173 19.0481C43.4173 19.6014 43.2606 20.0514 42.9473 20.3981C42.6339 20.7381 42.1873 20.9548 41.6073 21.0481C41.7806 21.0948 41.9373 21.1781 42.0773 21.2981C42.2173 21.4181 42.3539 21.5848 42.4873 21.7981L43.5173 23.4181ZM40.8973 20.3181C41.4173 20.3181 41.8006 20.2181 42.0473 20.0181C42.3006 19.8114 42.4273 19.4948 42.4273 19.0681C42.4273 18.6414 42.3039 18.3314 42.0573 18.1381C41.8106 17.9381 41.4239 17.8381 40.8973 17.8381H39.0273V20.3181H40.8973Z" fill="white"/>
                                                            <path d="M43.9302 8.56916H40.1311L38.9651 5L37.7991 8.56916H34L37.1075 10.7653L35.8973 14.3344L38.9651 12.1225L42.0329 14.3344L40.8204 10.7653L43.9302 8.56916Z" fill="white"/>
                                                            <defs>
                                                                <linearGradient id="paint0_linear" x1="0" y1="15.5" x2="62" y2="15.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#FF0844"/>
                                                                    <stop offset="1" stop-color="#FFB199"/>
                                                                </linearGradient>
                                                            </defs>
                                                        </svg>
                                                    <?php } ?>
                                                    <?php if ($arItem['PROPERTY_NEW_VALUE']) { ?>
                                                        <svg height="100%" width="100%" style="max-width: 100%;margin-top: 5px" viewBox="0 0 62 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect width="62" height="31" rx="15.5" fill="url(#paint1_linear)"/>
                                                            <path d="M13.9413 6.14414C14.088 6.14414 14.2046 6.19414 14.2913 6.29414C14.3846 6.38747 14.4313 6.51081 14.4313 6.66414V12.7741C14.4313 12.9275 14.3846 13.0541 14.2913 13.1541C14.198 13.2475 14.078 13.2941 13.9313 13.2941C13.758 13.2941 13.6246 13.2275 13.5313 13.0941L9.61129 7.92414V12.7741C9.61129 12.9275 9.56462 13.0541 9.47129 13.1541C9.38462 13.2475 9.26796 13.2941 9.12129 13.2941C8.97462 13.2941 8.85796 13.2475 8.77129 13.1541C8.68462 13.0608 8.64129 12.9341 8.64129 12.7741V6.66414C8.64129 6.51081 8.68796 6.38747 8.78129 6.29414C8.87462 6.19414 8.99462 6.14414 9.14129 6.14414C9.31462 6.14414 9.44796 6.21081 9.54129 6.34414L13.4613 11.5241V6.66414C13.4613 6.51081 13.5046 6.38747 13.5913 6.29414C13.678 6.19414 13.7946 6.14414 13.9413 6.14414ZM16.5829 13.2441C16.4229 13.2441 16.2963 13.2008 16.2029 13.1141C16.1163 13.0208 16.0729 12.8975 16.0729 12.7441V6.69414C16.0729 6.54081 16.1163 6.42081 16.2029 6.33414C16.2963 6.24081 16.4229 6.19414 16.5829 6.19414H20.2629C20.4163 6.19414 20.5329 6.23081 20.6129 6.30414C20.6996 6.37747 20.7429 6.48081 20.7429 6.61414C20.7429 6.74747 20.6996 6.85081 20.6129 6.92414C20.5329 6.99081 20.4163 7.02414 20.2629 7.02414H17.0829V9.24414H20.0629C20.2163 9.24414 20.3329 9.28081 20.4129 9.35414C20.4996 9.42747 20.5429 9.53081 20.5429 9.66414C20.5429 9.79747 20.4996 9.90081 20.4129 9.97414C20.3329 10.0475 20.2163 10.0841 20.0629 10.0841H17.0829V12.4141H20.2629C20.5829 12.4141 20.7429 12.5508 20.7429 12.8241C20.7429 12.9575 20.6996 13.0608 20.6129 13.1341C20.5329 13.2075 20.4163 13.2441 20.2629 13.2441H16.5829ZM30.8332 6.45414C30.8732 6.34747 30.9332 6.26747 31.0132 6.21414C31.0932 6.16081 31.1832 6.13414 31.2832 6.13414C31.4099 6.13414 31.5199 6.17747 31.6132 6.26414C31.7132 6.34414 31.7632 6.45081 31.7632 6.58414C31.7632 6.65747 31.7499 6.72414 31.7232 6.78414L29.5132 12.9641C29.4666 13.0775 29.3932 13.1641 29.2932 13.2241C29.1932 13.2841 29.0832 13.3141 28.9632 13.3141C28.8499 13.3141 28.7432 13.2841 28.6432 13.2241C28.5432 13.1641 28.4732 13.0775 28.4332 12.9641L26.6132 7.80414L24.7532 12.9641C24.7132 13.0775 24.6432 13.1641 24.5432 13.2241C24.4432 13.2841 24.3366 13.3141 24.2232 13.3141C24.1032 13.3141 23.9932 13.2841 23.8932 13.2241C23.7999 13.1641 23.7299 13.0775 23.6832 12.9641L21.4732 6.78414C21.4466 6.72414 21.4332 6.65747 21.4332 6.58414C21.4332 6.45081 21.4832 6.34414 21.5832 6.26414C21.6832 6.17747 21.8032 6.13414 21.9432 6.13414C22.0432 6.13414 22.1332 6.16081 22.2132 6.21414C22.2999 6.26747 22.3632 6.34747 22.4032 6.45414L24.2532 11.7741L26.1332 6.50414C26.1732 6.39081 26.2366 6.30414 26.3232 6.24414C26.4166 6.18414 26.5166 6.15414 26.6232 6.15414C26.7299 6.15414 26.8266 6.18747 26.9132 6.25414C26.9999 6.31414 27.0666 6.40081 27.1132 6.51414L28.9532 11.8241L30.8332 6.45414Z" fill="white"/>
                                                            <path d="M10.9032 24.1081C10.7499 24.1081 10.6266 24.0614 10.5332 23.9681C10.4399 23.8748 10.3932 23.7481 10.3932 23.5881V17.5081C10.3932 17.3548 10.4366 17.2348 10.5232 17.1481C10.6166 17.0548 10.7432 17.0081 10.9032 17.0081H13.3132C14.0599 17.0081 14.6399 17.1914 15.0532 17.5581C15.4666 17.9248 15.6732 18.4448 15.6732 19.1181C15.6732 19.7848 15.4666 20.3048 15.0532 20.6781C14.6399 21.0514 14.0599 21.2381 13.3132 21.2381H11.4232V23.5881C11.4232 23.7481 11.3766 23.8748 11.2832 23.9681C11.1966 24.0614 11.0699 24.1081 10.9032 24.1081ZM13.2032 20.4081C14.1899 20.4081 14.6832 19.9781 14.6832 19.1181C14.6832 18.2648 14.1899 17.8381 13.2032 17.8381H11.4232V20.4081H13.2032ZM22.4167 23.4181C22.4767 23.5048 22.5067 23.5981 22.5067 23.6981C22.5067 23.8181 22.4567 23.9214 22.3567 24.0081C22.2633 24.0881 22.1533 24.1281 22.0267 24.1281C21.84 24.1281 21.69 24.0448 21.5767 23.8781L20.2367 21.7981C20.0833 21.5581 19.92 21.3914 19.7467 21.2981C19.5733 21.1981 19.3467 21.1481 19.0667 21.1481H17.9367V23.5881C17.9367 23.7481 17.89 23.8748 17.7967 23.9681C17.71 24.0614 17.5867 24.1081 17.4267 24.1081C17.2733 24.1081 17.15 24.0614 17.0567 23.9681C16.9633 23.8748 16.9167 23.7481 16.9167 23.5881V17.5081C16.9167 17.3548 16.96 17.2348 17.0467 17.1481C17.14 17.0548 17.2667 17.0081 17.4267 17.0081H19.9167C20.6967 17.0081 21.29 17.1814 21.6967 17.5281C22.11 17.8748 22.3167 18.3814 22.3167 19.0481C22.3167 19.6014 22.16 20.0514 21.8467 20.3981C21.5333 20.7381 21.0867 20.9548 20.5067 21.0481C20.68 21.0948 20.8367 21.1781 20.9767 21.2981C21.1167 21.4181 21.2533 21.5848 21.3867 21.7981L22.4167 23.4181ZM19.7967 20.3181C20.3167 20.3181 20.7 20.2181 20.9467 20.0181C21.2 19.8114 21.3267 19.4948 21.3267 19.0681C21.3267 18.6414 21.2033 18.3314 20.9567 18.1381C20.71 17.9381 20.3233 17.8381 19.7967 17.8381H17.9267V20.3181H19.7967ZM26.8021 24.1381C26.1355 24.1381 25.5555 23.9914 25.0621 23.6981C24.5755 23.4048 24.1988 22.9881 23.9321 22.4481C23.6655 21.9081 23.5321 21.2714 23.5321 20.5381C23.5321 19.7981 23.6621 19.1581 23.9221 18.6181C24.1888 18.0714 24.5688 17.6548 25.0621 17.3681C25.5555 17.0748 26.1355 16.9281 26.8021 16.9281C27.4755 16.9281 28.0588 17.0748 28.5521 17.3681C29.0455 17.6548 29.4221 18.0714 29.6821 18.6181C29.9488 19.1581 30.0821 19.7948 30.0821 20.5281C30.0821 21.2614 29.9488 21.9014 29.6821 22.4481C29.4221 22.9881 29.0455 23.4048 28.5521 23.6981C28.0588 23.9914 27.4755 24.1381 26.8021 24.1381ZM26.8021 23.2981C27.5088 23.2981 28.0588 23.0581 28.4521 22.5781C28.8455 22.0981 29.0421 21.4148 29.0421 20.5281C29.0421 19.6414 28.8455 18.9614 28.4521 18.4881C28.0655 18.0081 27.5155 17.7681 26.8021 17.7681C26.1021 17.7681 25.5555 18.0081 25.1621 18.4881C24.7755 18.9614 24.5821 19.6414 24.5821 20.5281C24.5821 21.4148 24.7755 22.0981 25.1621 22.5781C25.5555 23.0581 26.1021 23.2981 26.8021 23.2981ZM31.997 24.0581C31.837 24.0581 31.7103 24.0148 31.617 23.9281C31.5303 23.8348 31.487 23.7114 31.487 23.5581V17.5081C31.487 17.3548 31.5303 17.2348 31.617 17.1481C31.7103 17.0548 31.837 17.0081 31.997 17.0081H33.957C35.097 17.0081 35.9803 17.3148 36.607 17.9281C37.2337 18.5414 37.547 19.4081 37.547 20.5281C37.547 21.6481 37.2337 22.5181 36.607 23.1381C35.9803 23.7514 35.097 24.0581 33.957 24.0581H31.997ZM33.897 23.1981C35.6303 23.1981 36.497 22.3081 36.497 20.5281C36.497 18.7548 35.6303 17.8681 33.897 17.8681H32.517V23.1981H33.897ZM41.7679 24.1381C40.8279 24.1381 40.1146 23.8948 39.6279 23.4081C39.1413 22.9214 38.8979 22.2048 38.8979 21.2581V17.4781C38.8979 17.3181 38.9446 17.1914 39.0379 17.0981C39.1313 17.0048 39.2546 16.9581 39.4079 16.9581C39.5613 16.9581 39.6846 17.0048 39.7779 17.0981C39.8713 17.1914 39.9179 17.3181 39.9179 17.4781V21.3281C39.9179 21.9748 40.0713 22.4648 40.3779 22.7981C40.6913 23.1314 41.1546 23.2981 41.7679 23.2981C42.3746 23.2981 42.8346 23.1314 43.1479 22.7981C43.4613 22.4648 43.6179 21.9748 43.6179 21.3281V17.4781C43.6179 17.3248 43.6646 17.2014 43.7579 17.1081C43.8513 17.0081 43.9746 16.9581 44.1279 16.9581C44.2813 16.9581 44.4046 17.0048 44.4979 17.0981C44.5913 17.1914 44.6379 17.3181 44.6379 17.4781V21.2581C44.6379 22.1981 44.3913 22.9148 43.8979 23.4081C43.4113 23.8948 42.7013 24.1381 41.7679 24.1381ZM49.2933 24.1381C48.62 24.1381 48.0333 23.9948 47.5333 23.7081C47.0333 23.4148 46.65 22.9981 46.3833 22.4581C46.1167 21.9114 45.9833 21.2681 45.9833 20.5281C45.9833 19.7948 46.1167 19.1581 46.3833 18.6181C46.65 18.0714 47.0333 17.6548 47.5333 17.3681C48.0333 17.0748 48.62 16.9281 49.2933 16.9281C49.7333 16.9281 50.15 16.9981 50.5433 17.1381C50.9367 17.2714 51.28 17.4681 51.5733 17.7281C51.7 17.8281 51.7633 17.9614 51.7633 18.1281C51.7633 18.2481 51.73 18.3514 51.6633 18.4381C51.5967 18.5181 51.5167 18.5581 51.4233 18.5581C51.31 18.5581 51.2 18.5148 51.0933 18.4281C50.7733 18.1881 50.48 18.0214 50.2133 17.9281C49.9533 17.8281 49.6567 17.7781 49.3233 17.7781C48.59 17.7781 48.0267 18.0148 47.6333 18.4881C47.24 18.9614 47.0433 19.6414 47.0433 20.5281C47.0433 21.4214 47.24 22.1048 47.6333 22.5781C48.0267 23.0514 48.59 23.2881 49.3233 23.2881C49.6433 23.2881 49.9333 23.2381 50.1933 23.1381C50.46 23.0381 50.76 22.8714 51.0933 22.6381C51.2267 22.5514 51.3367 22.5081 51.4233 22.5081C51.5167 22.5081 51.5967 22.5514 51.6633 22.6381C51.73 22.7181 51.7633 22.8181 51.7633 22.9381C51.7633 23.1048 51.7 23.2381 51.5733 23.3381C51.28 23.5981 50.9367 23.7981 50.5433 23.9381C50.15 24.0714 49.7333 24.1381 49.2933 24.1381ZM55.0932 24.1081C54.9399 24.1081 54.8132 24.0614 54.7132 23.9681C54.6199 23.8681 54.5732 23.7414 54.5732 23.5881V17.8681H52.5732C52.2532 17.8681 52.0932 17.7248 52.0932 17.4381C52.0932 17.2981 52.1332 17.1914 52.2132 17.1181C52.2999 17.0448 52.4199 17.0081 52.5732 17.0081H57.6132C57.7666 17.0081 57.8832 17.0448 57.9632 17.1181C58.0499 17.1914 58.0932 17.2981 58.0932 17.4381C58.0932 17.7248 57.9332 17.8681 57.6132 17.8681H55.6132V23.5881C55.6132 23.7414 55.5666 23.8681 55.4732 23.9681C55.3799 24.0614 55.2532 24.1081 55.0932 24.1081Z" fill="white"/>
                                                            <path d="M34.5725 6.84863C34.3951 6.84863 34.2329 6.99937 34.2448 7.17633L34.4086 11.6002C34.4086 11.6436 34.4259 11.6853 34.4566 11.716C34.4873 11.7468 34.529 11.764 34.5725 11.764C34.6159 11.764 34.6576 11.7468 34.6883 11.716C34.719 11.6853 34.7363 11.6436 34.7363 11.6002L34.9001 7.17633C34.912 6.99937 34.7498 6.84863 34.5725 6.84863Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M34.5728 14.0577C34.7538 14.0577 34.9005 13.911 34.9005 13.73C34.9005 13.5491 34.7538 13.4023 34.5728 13.4023C34.3918 13.4023 34.2451 13.5491 34.2451 13.73C34.2451 13.911 34.3918 14.0577 34.5728 14.0577Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <defs>
                                                                <linearGradient id="paint1_linear" x1="0" y1="15.5" x2="62" y2="15.5" gradientUnits="userSpaceOnUse">
                                                                    <stop stop-color="#6A11CB"/>
                                                                    <stop offset="1" stop-color="#2575FC"/>
                                                                </linearGradient>
                                                            </defs>
                                                        </svg>
                                                    <?php } ?>
                                                    <?php if ($arItem['DISCOUNT']) { ?>
                                                        <?php if ($arItem['DISCOUNT_DATE_TO']) { ?>
                                                            <svg height="100%" width="100%" style="max-width: 100%;margin-top: 5px" viewBox="0 0 62 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect width="62" height="30" rx="15" fill="url(#paint2_linear)"/>
                                                                <path d="M10.6744 13.2196C10.1677 13.2196 9.68441 13.1496 9.22441 13.0096C8.77108 12.8696 8.40775 12.6763 8.13441 12.4296C8.00775 12.323 7.94441 12.1863 7.94441 12.0196C7.94441 11.8996 7.97775 11.7996 8.04441 11.7196C8.11108 11.633 8.19108 11.5896 8.28441 11.5896C8.37775 11.5896 8.49108 11.633 8.62441 11.7196C9.21775 12.1596 9.89441 12.3796 10.6544 12.3796C11.1877 12.3796 11.5977 12.2796 11.8844 12.0796C12.1711 11.8796 12.3144 11.593 12.3144 11.2196C12.3144 10.9263 12.1811 10.7063 11.9144 10.5596C11.6477 10.413 11.2211 10.273 10.6344 10.1396C10.0744 10.0196 9.61441 9.88298 9.25441 9.72965C8.89441 9.57631 8.61108 9.36965 8.40441 9.10965C8.20441 8.84298 8.10441 8.50631 8.10441 8.09965C8.10441 7.69298 8.21441 7.33298 8.43441 7.01965C8.66108 6.69965 8.97441 6.45298 9.37441 6.27965C9.77441 6.09965 10.2311 6.00965 10.7444 6.00965C11.2177 6.00965 11.6544 6.07631 12.0544 6.20965C12.4544 6.34298 12.7944 6.53965 13.0744 6.79965C13.2077 6.91298 13.2744 7.04965 13.2744 7.20965C13.2744 7.32298 13.2377 7.42298 13.1644 7.50965C13.0977 7.59631 13.0177 7.63965 12.9244 7.63965C12.8377 7.63965 12.7277 7.59631 12.5944 7.50965C12.2877 7.27631 11.9977 7.10965 11.7244 7.00965C11.4577 6.90298 11.1344 6.84965 10.7544 6.84965C10.2411 6.84965 9.84108 6.95632 9.55441 7.16965C9.26775 7.37631 9.12441 7.66631 9.12441 8.03965C9.12441 8.35965 9.25108 8.60298 9.50441 8.76965C9.75775 8.92965 10.1644 9.07631 10.7244 9.20965C11.3111 9.34298 11.7844 9.48298 12.1444 9.62965C12.5111 9.76965 12.8011 9.96631 13.0144 10.2196C13.2344 10.4663 13.3444 10.7863 13.3444 11.1796C13.3444 11.5796 13.2311 11.9363 13.0044 12.2496C12.7844 12.5563 12.4711 12.7963 12.0644 12.9696C11.6644 13.1363 11.2011 13.2196 10.6744 13.2196ZM20.7851 12.5396C20.8185 12.6196 20.8351 12.6863 20.8351 12.7396C20.8351 12.8663 20.7818 12.973 20.6751 13.0596C20.5751 13.1463 20.4618 13.1896 20.3351 13.1896C20.2485 13.1896 20.1651 13.1663 20.0851 13.1196C20.0118 13.0663 19.9551 12.993 19.9151 12.8996L19.2551 11.3996H15.5951L14.9351 12.8996C14.8951 12.993 14.8351 13.0663 14.7551 13.1196C14.6751 13.1663 14.5918 13.1896 14.5051 13.1896C14.3718 13.1896 14.2518 13.1463 14.1451 13.0596C14.0451 12.973 13.9951 12.8663 13.9951 12.7396C13.9951 12.6863 14.0118 12.6196 14.0451 12.5396L16.8651 6.36965C16.9118 6.26298 16.9851 6.17965 17.0851 6.11965C17.1918 6.05965 17.3018 6.02965 17.4151 6.02965C17.5285 6.02965 17.6351 6.05965 17.7351 6.11965C17.8418 6.17965 17.9185 6.26298 17.9651 6.36965L20.7851 12.5396ZM15.9651 10.5796H18.8951L17.4251 7.26965L15.9651 10.5796ZM22.4089 13.1396C22.2555 13.1396 22.1322 13.0963 22.0389 13.0096C21.9522 12.923 21.9089 12.8063 21.9089 12.6596V6.55965C21.9089 6.40631 21.9555 6.28298 22.0489 6.18965C22.1422 6.08965 22.2655 6.03965 22.4189 6.03965C22.5789 6.03965 22.7055 6.08631 22.7989 6.17965C22.8922 6.27298 22.9389 6.39965 22.9389 6.55965V12.2796H25.9789C26.1322 12.2796 26.2489 12.3163 26.3289 12.3896C26.4155 12.463 26.4589 12.5696 26.4589 12.7096C26.4589 12.8496 26.4155 12.9563 26.3289 13.0296C26.2489 13.103 26.1322 13.1396 25.9789 13.1396H22.4089ZM27.8876 13.1396C27.7276 13.1396 27.601 13.0963 27.5076 13.0096C27.421 12.9163 27.3776 12.793 27.3776 12.6396V6.58965C27.3776 6.43631 27.421 6.31631 27.5076 6.22965C27.601 6.13631 27.7276 6.08965 27.8876 6.08965H31.5676C31.721 6.08965 31.8376 6.12631 31.9176 6.19965C32.0043 6.27298 32.0476 6.37631 32.0476 6.50965C32.0476 6.64298 32.0043 6.74631 31.9176 6.81965C31.8376 6.88631 31.721 6.91965 31.5676 6.91965H28.3876V9.13965H31.3676C31.521 9.13965 31.6376 9.17631 31.7176 9.24965C31.8043 9.32298 31.8476 9.42631 31.8476 9.55965C31.8476 9.69298 31.8043 9.79631 31.7176 9.86965C31.6376 9.94298 31.521 9.97965 31.3676 9.97965H28.3876V12.3096H31.5676C31.8876 12.3096 32.0476 12.4463 32.0476 12.7196C32.0476 12.853 32.0043 12.9563 31.9176 13.0296C31.8376 13.103 31.721 13.1396 31.5676 13.1396H27.8876Z" fill="white"/>
                                                                <path d="M9.87371 23.6045C9.72038 23.6045 9.59704 23.5612 9.50371 23.4745C9.41704 23.3878 9.37371 23.2712 9.37371 23.1245V17.0245C9.37371 16.8712 9.42038 16.7478 9.51371 16.6545C9.60704 16.5545 9.73038 16.5045 9.88371 16.5045C10.0437 16.5045 10.1704 16.5512 10.2637 16.6445C10.357 16.7378 10.4037 16.8645 10.4037 17.0245V22.7445H13.4437C13.597 22.7445 13.7137 22.7812 13.7937 22.8545C13.8804 22.9278 13.9237 23.0345 13.9237 23.1745C13.9237 23.3145 13.8804 23.4212 13.7937 23.4945C13.7137 23.5678 13.597 23.6045 13.4437 23.6045H9.87371ZM15.3525 23.6545C15.1991 23.6545 15.0758 23.6078 14.9825 23.5145C14.8891 23.4212 14.8425 23.2945 14.8425 23.1345V17.0245C14.8425 16.8712 14.8891 16.7478 14.9825 16.6545C15.0758 16.5545 15.1991 16.5045 15.3525 16.5045C15.5125 16.5045 15.6391 16.5512 15.7325 16.6445C15.8258 16.7378 15.8725 16.8645 15.8725 17.0245V23.1345C15.8725 23.2945 15.8258 23.4212 15.7325 23.5145C15.6458 23.6078 15.5191 23.6545 15.3525 23.6545ZM23.7685 16.5045C23.9151 16.5045 24.0351 16.5545 24.1285 16.6545C24.2218 16.7478 24.2685 16.8712 24.2685 17.0245V23.1645C24.2685 23.3178 24.2251 23.4378 24.1385 23.5245C24.0585 23.6112 23.9451 23.6545 23.7985 23.6545C23.6585 23.6545 23.5485 23.6112 23.4685 23.5245C23.3885 23.4378 23.3485 23.3178 23.3485 23.1645V18.5145L21.3885 22.2445C21.2618 22.4778 21.0985 22.5945 20.8985 22.5945C20.6985 22.5945 20.5351 22.4778 20.4085 22.2445L18.4385 18.5545V23.1645C18.4385 23.3178 18.3985 23.4378 18.3185 23.5245C18.2385 23.6112 18.1251 23.6545 17.9785 23.6545C17.8385 23.6545 17.7251 23.6112 17.6385 23.5245C17.5585 23.4312 17.5185 23.3112 17.5185 23.1645V17.0245C17.5185 16.8712 17.5651 16.7478 17.6585 16.6545C17.7518 16.5545 17.8718 16.5045 18.0185 16.5045C18.2251 16.5045 18.3918 16.6245 18.5185 16.8645L20.9085 21.4345L23.2785 16.8645C23.4051 16.6245 23.5685 16.5045 23.7685 16.5045ZM26.4267 23.6545C26.2733 23.6545 26.15 23.6078 26.0567 23.5145C25.9633 23.4212 25.9167 23.2945 25.9167 23.1345V17.0245C25.9167 16.8712 25.9633 16.7478 26.0567 16.6545C26.15 16.5545 26.2733 16.5045 26.4267 16.5045C26.5867 16.5045 26.7133 16.5512 26.8067 16.6445C26.9 16.7378 26.9467 16.8645 26.9467 17.0245V23.1345C26.9467 23.2945 26.9 23.4212 26.8067 23.5145C26.72 23.6078 26.5933 23.6545 26.4267 23.6545ZM30.7827 23.6545C30.6294 23.6545 30.5027 23.6078 30.4027 23.5145C30.3094 23.4145 30.2627 23.2878 30.2627 23.1345V17.4145H28.2627C27.9427 17.4145 27.7827 17.2712 27.7827 16.9845C27.7827 16.8445 27.8227 16.7378 27.9027 16.6645C27.9894 16.5912 28.1094 16.5545 28.2627 16.5545H33.3027C33.456 16.5545 33.5727 16.5912 33.6527 16.6645C33.7394 16.7378 33.7827 16.8445 33.7827 16.9845C33.7827 17.2712 33.6227 17.4145 33.3027 17.4145H31.3027V23.1345C31.3027 23.2878 31.256 23.4145 31.1627 23.5145C31.0694 23.6078 30.9427 23.6545 30.7827 23.6545ZM35.1474 23.6045C34.9874 23.6045 34.8607 23.5612 34.7674 23.4745C34.6807 23.3812 34.6374 23.2578 34.6374 23.1045V17.0545C34.6374 16.9012 34.6807 16.7812 34.7674 16.6945C34.8607 16.6012 34.9874 16.5545 35.1474 16.5545H38.8274C38.9807 16.5545 39.0974 16.5912 39.1774 16.6645C39.264 16.7378 39.3074 16.8412 39.3074 16.9745C39.3074 17.1078 39.264 17.2112 39.1774 17.2845C39.0974 17.3512 38.9807 17.3845 38.8274 17.3845H35.6474V19.6045H38.6274C38.7807 19.6045 38.8974 19.6412 38.9774 19.7145C39.064 19.7878 39.1074 19.8912 39.1074 20.0245C39.1074 20.1578 39.064 20.2612 38.9774 20.3345C38.8974 20.4078 38.7807 20.4445 38.6274 20.4445H35.6474V22.7745H38.8274C39.1474 22.7745 39.3074 22.9112 39.3074 23.1845C39.3074 23.3178 39.264 23.4212 39.1774 23.4945C39.0974 23.5678 38.9807 23.6045 38.8274 23.6045H35.1474ZM40.997 23.6045C40.837 23.6045 40.7103 23.5612 40.617 23.4745C40.5303 23.3812 40.487 23.2578 40.487 23.1045V17.0545C40.487 16.9012 40.5303 16.7812 40.617 16.6945C40.7103 16.6012 40.837 16.5545 40.997 16.5545H42.957C44.097 16.5545 44.9803 16.8612 45.607 17.4745C46.2337 18.0878 46.547 18.9545 46.547 20.0745C46.547 21.1945 46.2337 22.0645 45.607 22.6845C44.9803 23.2978 44.097 23.6045 42.957 23.6045H40.997ZM42.897 22.7445C44.6303 22.7445 45.497 21.8545 45.497 20.0745C45.497 18.3012 44.6303 17.4145 42.897 17.4145H41.517V22.7445H42.897Z" fill="white"/>
                                                                <path d="M37.2065 13.6049C37.1626 13.6049 37.1192 13.596 37.0793 13.5787C37.0395 13.5615 37.0042 13.5363 36.976 13.505C36.9477 13.4737 36.9271 13.437 36.9157 13.3975C36.9043 13.358 36.9023 13.3166 36.9099 13.2763V13.2746L37.3705 10.919H35.5873C35.5378 10.919 35.4893 10.906 35.4475 10.8814C35.4056 10.8568 35.3721 10.8216 35.3508 10.78C35.3294 10.7384 35.3212 10.6921 35.327 10.6463C35.3328 10.6005 35.3524 10.5572 35.3835 10.5213L38.9746 6.38662C39.0155 6.33829 39.0722 6.30377 39.1361 6.28832C39.2001 6.27287 39.2677 6.27735 39.3286 6.30108C39.3896 6.3248 39.4405 6.36647 39.4736 6.4197C39.5067 6.47293 39.5202 6.5348 39.512 6.59584C39.512 6.60042 39.5107 6.60485 39.5099 6.60942L39.0477 8.96566H40.8305C40.88 8.96566 40.9285 8.97872 40.9703 9.00331C41.0122 9.02791 41.0457 9.06304 41.0671 9.10464C41.0884 9.14624 41.0966 9.19262 41.0908 9.2384C41.085 9.28418 41.0655 9.3275 41.0343 9.36335L37.4428 13.4981C37.4145 13.5313 37.3786 13.5581 37.3377 13.5766C37.2968 13.5951 37.252 13.6048 37.2065 13.6049V13.6049Z" fill="white"/>
                                                                <defs>
                                                                    <linearGradient id="paint2_linear" x1="0" y1="15" x2="62" y2="15" gradientUnits="userSpaceOnUse">
                                                                        <stop stop-color="#F83600"/>
                                                                        <stop offset="1" stop-color="#F9D423"/>
                                                                    </linearGradient>
                                                                </defs>
                                                            </svg>
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
                                                    <h3 class="product-title"><?=$arItem['NAME']?></h3>
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

