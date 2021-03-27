<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
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

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Page\AssetLocation;

if (!$arResult['IS_AJAX']) :
    Asset::getInstance()->addString('<script type="text/javascript">
    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
        try { rrApi.categoryView(' . $arResult['SECTION_ID'] . '); } catch(e) {}
    })
</script>', false, AssetLocation::AFTER_JS);

    $curPage = $APPLICATION->GetCurPage(false);
    Asset::getInstance()->addString('<link rel="canonical" href="https://' . SITE_SERVER_NAME . $curPage . '">');

//    Asset::getInstance()->addCss('/local/components/qsoft/catalog.section/templates/.default/libs/jquery.scrollbar.css');
//    Asset::getInstance()->addJs('/local/components/qsoft/catalog.section/templates/.default/libs/jquery.scrollbar.min.js');
   // Asset::getInstance()->addJs('/local/components/qsoft/catalog.section/templates/.default/libs/lazysizes.min.js');
    ?>
<script>
    var currentHost = "<?=$arResult['CURRENT_HOST']?>";
</script>
<div class="col-xs-12">
    <div class="main main--banner">
        <? $APPLICATION->IncludeComponent(
            "bitrix:breadcrumb",
            "",
            array(
                "PATH" => "",
                "SITE_ID" => "s1",
                "START_FROM" => "0",
            )
        ); ?>
        <h1 class="zagolovok">
            <?
            $APPLICATION->SetTitle($arResult['TITLE']);
            $APPLICATION->ShowTitle(false);
            ?>
        </h1>

        <? if (!empty($arResult['TAGS'])) : ?>
            <div class="tags ">
                <button class="tags-arrow tags-arrow--next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="9" viewBox="0 0 22 40"><path fill="#FFF" d="M2.098-.035L.009 2.064l17.789 17.883L-.048 37.886l2.089 2.099 19.935-20.038z"/></svg>
                </button>

                <div class="tags-wrapper swiper-container">

                    <div class="catalog-section-tags swiper-wrapper">
                        <? foreach ($arResult['TAGS'] as $arTag) : ?>
                            <a class="tag_btn button--tag swiper-slide"
                               href="<?= $arTag['DETAIL_PAGE_URL'] ?>"><?= $arTag['NAME'] ?></a>
                        <? endforeach ?>
                    </div>
                    <div class="swiper-scrollbar"></div>
                </div>

                <button class="tags-arrow tags-arrow--prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="20" viewBox="0 0 22 40"><path fill="#FFF" d="M19.83-.035l2.089 2.099L4.13 19.947l17.847 17.939-2.09 2.099L-.048 19.947z"/></svg>
                </button>
            </div>
        <? endif ?>

        <!-- catalog -->
        <div class="catalog">
            <? if (!empty($arResult['SUBSECTIONS'])) : ?>
                <div class="catalog__subsections clearfix">
                    <div class="col-xs-12">
                        <?
                        $arCurSection['ID'] = $arResult['SUBSECTIONS']['ID'];
                        require_once $_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/components/bitrix/catalog/.default/subsections.php';
                        ?>
                    </div>
                </div>
            <? endif ?>
            <!-- banner -->
            <? if ($arResult['BANNER']['SINGLE'] || $arResult['BANNER']['MOBILE']) : ?>
                <? if ($arResult['BANNER']['SINGLE']) : ?>
                    <div class="catalog__banner clearfix">
                        <div class="col-xs-12">
                            <div class="banner">
                                <img src="<?= $arResult['BANNER']['SINGLE'] ?>" class="banner__img" alt="">
                            </div>
                        </div>
                    </div>
                <? else : ?>
                    <div class="cards__banner stock-banner stock-banner--external">
                        <div class="stock-banner__wrapper">
                            <img class="stock-banner__img" src="<?= $arResult['BANNER']['MOBILE'] ?>" alt="">
                            <? foreach ($arResult['BANNER']['MOBILE_LINKS'] as $arItem) : ?>
                                <a class="stock-banner__link" href="<?= $arItem['LINK'] ?>"
                                   style="<?= $arItem['STYLE'] ?>"></a>
                            <? endforeach ?>
                        </div>
                    </div>
                <? endif ?>
            <? endif ?>
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
                    <? if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                    <div class="catalog__settings-col catalog__settings-col--right col-xs-9">
                        <div class="catalog__settings-line">
                            <!-- sort -->
                            <div class="catalog__sort catalog__sort--desktop sort">
                                <span class="sort__title">Сортировка:</span>
                                <ul class="sort__items">
                                    <? foreach ($arResult['SORT_ARRAY'] as $key => $value) : ?>
                                        <li class="sort__item" data-sort="<?= $key ?>">
                                            <span class="sort__text <?= $arResult['SELECTED_SORT'] === $key ? 'sort__text--active' : '' ?>"><?= $value ?></span>
                                        </li>
                                    <? endforeach; ?>
                                </ul>
                            </div>
                            <!-- /sort -->
                            <!-- view -->
                            <div class="catalog__view view js-view">
                                <span class="view__item catalog-change-image <?= $arResult['USER_SETTINGS']['VIEW'] == 'true' ? 'active' : '' ?>">
                                    <span class="view__item-icon view__item-icon--all">
                                        <? if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/img/svg/boot-arrows.svg')) {
                                            include $_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/img/svg/boot-arrows.svg';
                                        } ?>
                                    </span>
                                </span>
                                <span class="view__item catalog-sort catalog-sort--mobile-icon">
                                    <span class="view__item-icon view__item-icon--device">
                                        <? if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/local/templates/respect/img/svg/sort-arrows.svg')) {
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
                    <? endif; ?>
                </div>
                <? if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                <div class="catalog__sort catalog__sort--mobile">
                    <select class="catalog__sort-select js-change-sort">
                        <? foreach ($arResult['SORT_ARRAY'] as $key => $value) : ?>
                            <option value="<?= $key ?>" <?= $arResult['SELECTED_SORT'] === $key ? 'selected' : '' ?>><?= $value ?></option>
                        <? endforeach; ?>
                    </select>
                </div>
                <? endif; ?>
                <!-- /settings -->
<?php endif; ?>
                    <? if ($arResult['IS_AJAX']) {
                        $APPLICATION->RestartBuffer();
                    } ?>
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
                                <? if ($arResult['SHOW_TYPEPRODUCT_FILTER']) :?>
                                <div class="in-left-catalog<?= $arResult['FILTER']['CHECKED']['SUBTYPEPRODUCT'] || $arResult['FILTER']['CHECKED']['IBLOCK_SECTION_ID'] ? ' in-left-catalog--checked' : '' ?>">
                                    <div class="name-h3<?= $arResult['FILTER']['CHECKED']['SUBTYPEPRODUCT'] || $arResult['FILTER']['CHECKED']['IBLOCK_SECTION_ID'] ? ' active-name-h3' : '' ?>">
                                        <a href="javascript:void(0);" class="clear-section">
                                            <svg class="clear-section__icon">
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#close"></use>
                                            </svg>
                                        </a>
                                        <h3>
                                            Тип изделия
                                        </h3>
                                        <svg class="minus" style="display: none;">
                                            <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                        </svg>
                                        <svg class="plus" style="">
                                            <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                        </svg>
                                    </div>
                                    <div class="in-in-left scrollbar-inner max-height-400" data-filter="type" style="display: none;">
                                        <ul class=" filter__main-list" id="gender-list">
                                            <div class="filter__vid-elem">

                                                <?$i = 1; foreach ($arResult['TYPEPRODUCT_FILTER']['VIDS'] as $level2_section_id => $vid) :?>
                                                <li>
                                                    <div class="name-h3">
                                                        <svg class="minus" style="left: 170px; display: none;">
                                                            <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                                        </svg>
                                                        <svg class="plus" style="left: 170px;">
                                                            <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                                        </svg>
                                                        <h3>
                                                        <?=$vid['NAME']?>
                                                        </h3>
                                                    </div>
                                                    <div class="in-in-left js-filter-box type_izd" style="display: none;">
                                                        <input
                                                                class="checkbox_size general-all"
                                                                type="checkbox"
                                                                id="toggle-<?=$level2_section_id?>"
                                                        >
                                                        <label for="toggle-<?=$level2_section_id?>">Выбрать все типы</label>
                                                        <ul class="filter__type-list">
                                                            <?foreach ($vid['TYPES'] as $key => $type) : ?>
                                                            <li class="type-item">
                                                                <input
                                                                       type="checkbox"
                                                                       id="sections-<?=$key?>"
                                                                       class="checkbox_size all-type"
                                                                       data-name="sections"
                                                                       data-id="<?=$key?>"
                                                                    <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['CHECKED'] ? 'checked' : ''?>
                                                                    <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['DISABLED'] ? 'disabled' : ''?>
                                                                >
                                                                <label
                                                                        for="sections-<?=$key?>"
                                                                    <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['DISABLED'] ? ' class="mydisabled"' : ''?>
                                                                ><?=$type['NAME']?></label>
                                                                <ul class="filter__product-list">
                                                                    <? foreach ($type['SUBTYPES'] as $key2 => $subtype) : ?>
                                                                    <li <?=count($type['SUBTYPES']) < 2 || $subtype['NAME'] === 'NONAME' ? 'hidden' : ''?>>
                                                                        <input
                                                                                type="checkbox"
                                                                                id="subtypeproduct-<?=$i?>"
                                                                                class="type checkbox_size"
                                                                                data-name="subtypeproduct"
                                                                                data-id="<?=$key2?>"
                                                                                data-section="<?=$key?>"
                                                                            <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['CHECKED'] && $arResult['FILTER']['SUBTYPEPRODUCT'][$key2]['CHECKED'] ? 'checked' : ''?>
                                                                            <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['DISABLED'] || $arResult['FILTER']['SUBTYPEPRODUCT'][$key2]['DISABLED'] ? 'disabled' : ''?>
                                                                        >
                                                                        <label for="subtypeproduct-<?=$i?>"
                                                                            <?=$arResult['FILTER']['IBLOCK_SECTION_ID'][$key]['DISABLED'] || $arResult['FILTER']['SUBTYPEPRODUCT'][$key2]['DISABLED'] ? ' class="mydisabled"' : ''?>
                                                                        ><?=$subtype['NAME']?></label>
                                                                    </li>
                                                                        <?$i++;
                                                                    endforeach; ?>
                                                                </ul>
                                                            </li>
                                                            <? endforeach; ?>
                                                        </ul>
                                                    </div>
                                                </li>
                                                <? endforeach; ?>
                                            </div>
                                        </ul>
                                    </div>
                                    <div style="clear: both;"></div>
                                </div>
                                <? endif; ?>
                                <input id="set_filter" type="hidden" name="set_filter" value="Y">
                                <? if ('stores' == $arParams['SECTION_TYPE'] && $arParams['STORE_ID']) { ?>
                                    <input id="store_id" type="hidden" name="store_id" value="<?= $arParams['STORE_ID'] ?>">
                                <? } ?>
                                <? foreach ($arResult['FILTER_KEYS'] as $filterKey) : ?>
                                    <? if ($filterKey == 'ONLINE_TRY_ON' && !empty($arResult['FILTER']['ONLINE_TRY_ON']['Y'])) : ?>
                                        <div class="in-left-catalog in-left-catalog--no-toggle">
                                            <div class="name-h3">
                                                <h3>
                                                    <input id="online_try_on"
                                                           class="checkbox_size"
                                                           type="checkbox"
                                                           name="online_try_on"
                                                           value="Y"
                                                           <?= !empty($arResult['FILTER']['ONLINE_TRY_ON']['Y']['CHECKED']) ? ' checked' : ''; ?>
                                                           <?= !empty($arResult['FILTER']['ONLINE_TRY_ON']['Y']['DISABLED']) ? ' disabled' : ''; ?>
                                                           onchange="smartFilter.click(this)">
                                                    <label for="online_try_on" <?= !empty($arResult['FILTER']['ONLINE_TRY_ON']['Y']['DISABLED']) ? ' class="mydisabled"' : ''; ?>>Примерить онлайн</label>
                                                </h3>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <? continue ?>
                                    <? endif ?>
                                    <? if ($filterKey == 'FROM_DEFAULT_LOC' && !empty($arResult['FILTER']['FROM_DEFAULT_LOC']['Y'])) : ?>
                                        <div class="in-left-catalog in-left-catalog--no-toggle <?=!empty($arResult['FILTER']['FROM_DEFAULT_LOC']['N']['CHECKED']) ? 'in-left-catalog--checked' : ''?>">
                                            <div class="name-h3 active-name-h3">
                                                <h3>
                                                    <input id="from_default_loc"
                                                           class="checkbox_size"
                                                           type="checkbox"
                                                           name="from_default_loc"
                                                           value="Y"
                                                            <?= !empty($arResult['FILTER']['FROM_DEFAULT_LOC']['N']['CHECKED']) ? ' checked' : ''; ?>
                                                            <?= !empty($arResult['FILTER']['FROM_DEFAULT_LOC']['N']['DISABLED']) ? ' disabled' : ''; ?>
                                                           onchange="smartFilter.click(this)">
                                                    <label for="from_default_loc" <?= !empty($arResult['FILTER']['FROM_DEFAULT_LOC']['N']['DISABLED']) ? ' class="mydisabled"' : ''; ?>>Убрать ассортимент московского склада</label>
                                                </h3>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <? continue ?>
                                    <? endif ?>
                                    <? if ($filterKey === 'PRICE' && !empty($arResult['FILTER']['MAX_PRICE']) && !empty($arResult['FILTER']['MIN_PRICE'])) : ?>
                                        <div class="in-left-catalog in-left-catalog--price<?= $arResult['FILTER']['CHECKED']['PRICE'] ? ' in-left-catalog--checked' : '' ?>">
                                            <div class="name-h3<?= $arResult['FILTER']['CHECKED']['PRICE'] ? ' active-name-h3' : '' ?>">
                                                <a href="javascript:void(0);" class="clear-section">
                                                    <svg class="clear-section__icon">
                                                        <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#close"></use>
                                                    </svg>
                                                </a>
                                                <h3>
                                                    Цена
                                                </h3>
                                                <svg class="minus"<?= $arResult['FILTER']['CHECKED']['PRICE'] ? ' style="display:inline"' : '' ?>>
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                                </svg>
                                                <svg class="plus"<?= $arResult['FILTER']['CHECKED']['PRICE'] ? ' style="display:none"' : '' ?>>
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                                </svg>
                                            </div>
                                            <div class="in-in-left"<?= $arResult['FILTER']['CHECKED']['PRICE'] ? ' style="display:block"' : '' ?>>
                                                <div class="from">
                                                    <span>От</span>
                                                    <input id='min_price' class="js-price-from" type="text" name='min_price'
                                                           value="<?= $arResult['FILTER']['CHECKED']['MIN_PRICE'] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MIN_PRICE'] ?>">
                                                </div>
                                                <div class="to">
                                                    <span>До</span>
                                                    <input id="max_price" class="js-price-to" type="text" name="max_price"
                                                           value="<?= $arResult['FILTER']['CHECKED']['MAX_PRICE'] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MAX_PRICE'] ?>">
                                                </div>
                                                <div style="clear: both"></div>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <? continue ?>
                                    <? endif ?>
                                    <? $value = $arResult['FILTER'][$filterKey];
                                    if (empty($value)) {
                                        continue;
                                    } ?>
                                    <? $jsKey = $arResult['JS_KEYS'][$filterKey] ?>
                                    <div class="in-left-catalog<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' in-left-catalog--checked' : '' ?>">
                                        <div class="name-h3<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' active-name-h3' : '' ?>">
                                            <a href="javascript:void(0);" class="clear-section">
                                                <svg class="clear-section__icon">
                                                    <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#close"></use>
                                                </svg>
                                            </a>
                                            <h3>
                                                <?= GetMessage($filterKey) ?>
                                            </h3>
                                            <svg class="minus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:inline"' : '' ?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                            </svg>
                                            <svg class="plus"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:none"' : '' ?>>
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                            </svg>
                                        </div>
                                        <div class="in-in-left scrollbar-inner"<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:block"' : '' ?>
                                             data-filter-name="<?= $jsKey ?>">
                                            <?if ($filterKey === 'STORES') : ?>
                                                <input id="stores" type="hidden" name="stores" value="false">
                                                <input id="f_stores_o_f" class="checkbox_size js-f-stores"
                                                       type="checkbox"
                                                       value="1"
                                                       onchange="smartFilter.click(this)" <?= $arResult['FILTER']['STORES'][1]['CHECKED'] ? 'checked' : '' ?>>
                                                <label for="f_stores_o_f">КУПИТЬ С ДОСТАВКОЙ<br><span>(дом/работа/пункт выдачи)</span></label>
                                                <input id="f_stores_r_f" class="checkbox_size js-f-stores"
                                                       type="checkbox"
                                                       onchange="smartFilter.click(this)"
                                                       value="2" <?= $arResult['FILTER']['STORES'][2]['CHECKED'] ? 'checked' : '' ?>>
                                                <label for="f_stores_r_f">ЗАБРАТЬ В МАГАЗИНЕ<br><span>(резерв в магазине)</span></label>
                                            <?elseif ($filterKey === 'COLORSFILTER') :?>
                                                <? foreach ($value as $xml_id => $color) : ?>
                                                    <div class="outer-color">
                                                        <input id="color_<?= $xml_id ?>"
                                                                class="checkbox_size"
                                                                type="checkbox"
                                                                name="color_<?= $xml_id ?>"
                                                                value="<?= $xml_id ?>"
                                                                onchange="smartFilter.click(this)"
                                                            <? if ($color['CHECKED']) : ?>
                                                                checked
                                                            <? endif; ?>
                                                            <? if ($color['DISABLED']) : ?>
                                                                disabled
                                                            <? endif; ?>
                                                        />
                                                        <? if ($color['VALUE']['UF_NAME'] == 'Белый') : ?>
                                                            <label for="color_<?= $xml_id ?>" class="label-for-color--white <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                                <span class="inner-color"></span><?= $color['VALUE']['UF_NAME']; ?>
                                                            </label>
                                                        <? elseif ($color['VALUE']['UF_NAME'] == 'Золотой') : ?>
                                                            <label for="color_<?= $xml_id ?>" class="label-for-color--gold <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                                <span class="inner-color"></span><?= $color['VALUE']['UF_NAME']; ?>
                                                            </label>
                                                        <? elseif ($color['VALUE']['UF_NAME'] == 'Серебряный') : ?>
                                                            <label for="color_<?= $xml_id ?>" class="label-for-color--silver <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                                <span class="inner-color"></span><?= $color['VALUE']['UF_NAME']; ?>
                                                            </label>
                                                        <? elseif ($color['VALUE']['UF_NAME'] == 'Цветной') : ?>
                                                            <label for="color_<?= $xml_id ?>" class="label-for-color--multicolored <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                                <span class="inner-color"></span><?= $color['VALUE']['UF_NAME']; ?>
                                                            </label>
                                                        <? else : ?>
                                                            <label for="color_<?= $xml_id ?>" class="label-for-color <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                                <span class="inner-color" style="background-color:<?= $color['VALUE']['COLOR'] ?>">
                                                                </span><?= $color['VALUE']['UF_NAME']; ?>
                                                            </label>
                                                        <? endif; ?>
                                                    </div>
                                                <? endforeach; ?>
                                            <?elseif ($filterKey === 'STORAGES_AVAILABILITY') :?>
                                                <input type="text" name="storage_name" placeholder="Найти магазин" class="storage_search">
                                                <ul class="storages-list">
                                                    <? foreach ($value as $key => $item) : ?>
                                                    <li>
                                                        <input id="<?= $jsKey ?>_<?= $key ?>"
                                                                class="checkbox_size"
                                                                type="checkbox"
                                                                name="<?= $jsKey ?>"
                                                                value="<?= $key ?>"
                                                            <? if (!empty($item['CHECKED'])) : ?>
                                                                checked
                                                            <? endif; ?>
                                                            <? if (!empty($item['DISABLED'])) : ?>
                                                                disabled
                                                            <? endif; ?>
                                                                onchange="smartFilter.click(this)">
                                                        <label for="<?= $jsKey ?>_<?= $key ?>" <?= !empty($item['DISABLED']) ? 'class="mydisabled"' : '' ?>>
                                                            <p class="storage-name"><?= $item['TITLE'] ?></p>
                                                            <p class="storage-address"><?= $item['ADDRESS'] ?></p>
                                                        </label>
                                                    </li>
                                                    <? endforeach; ?>
                                                </ul>
                                            <?else :?>
                                                <? foreach ($value as $key => $item) : ?>
                                                <input id="<?= $jsKey ?>_<?= $key ?>"
                                                       class="checkbox_size"
                                                       type="checkbox"
                                                       name="<?= $jsKey ?>"
                                                       value="<?= $key ?>"
                                                    <? if (!empty($item['CHECKED'])) : ?>
                                                        checked
                                                    <? endif; ?>
                                                    <? if (!empty($item['DISABLED'])) : ?>
                                                        disabled
                                                    <? endif; ?>
                                                       onchange="smartFilter.click(this)">
                                                <label for="<?= $jsKey ?>_<?= $key ?>" <?= !empty($item['DISABLED']) ? 'class="mydisabled"' : '' ?>><?= $item['VALUE'] ?></label>
                                                <? endforeach; ?>
                                            <?endif;?>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                <? endforeach; ?>
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
                                   value="ПРИМЕНИТЬ ВСЕ ФИЛЬТРЫ" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--reset filters__reset-btn filters__btn-text--desktop js-filter-button-reset filters__btn--disabled"
                                   value="СБРОСИТЬ ВСЕ ФИЛЬТРЫ" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--reset filters__reset-btn filters__btn-text--mobile js-filter-button-reset filters__btn--disabled"
                                   value="СБРОСИТЬ" disabled>
                            <input type="button"
                                   class="filters__btn filters__btn--submit filters__btn-text--mobile js-filter-button-submit js-filter-button-mobile filters__btn--disabled"
                                   value="ПРИМЕНИТЬ" disabled>
                        </div>
                    </div>
                    <!-- /filter -->
                    <div class="catalog__content-col catalog__content-col--main col-xs-9" style="display: flex; flex-direction: column">
                        <? if (!$_GET['set_filter']) :?>
                            <? if ($arResult['SECTION_TYPE'] === 'section' || $arResult['SECTION_TYPE'] === 'stores') : ?>
                                <!-- RetailRocket -->
                                <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfd6797a5250230f3bfb0" data-category-id="<?=is_array($arResult['SECTION_ID']) ? implode(',', $arResult['SECTION_ID']) : $arResult['SECTION_ID']?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>">
                                </div>
                                <!-- /RetailRocket -->
                            <? elseif ($arResult['SECTION_TYPE'] === 'sale') : ?>
                                <!-- RetailRocket -->
                                <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfdc797a5250230f3bfce" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>" data-category-ids="<?=is_array($arResult['SECTION_ID']) ? implode(',', $arResult['SECTION_ID']) : $arResult['SECTION_ID']?>"></div>
                                <!-- /RetailRocket -->
                            <? endif ?>
                        <? endif; ?>
                        <!-- cards -->
                        <div class="catalog__cards cards js-cards <?= $arResult['USER_SETTINGS']['GRID'] == 'big' ? 'cards--big' : '' ?>">
                            <div class="cards__box">
                                <? if ($arResult['BANNER']['DESKTOP']) : ?>
                                    <div class="cards__banner stock-banner stock-banner--internal">
                                        <div class="stock-banner__wrapper">
                                            <img class="stock-banner__img" src="<?= $arResult['BANNER']['DESKTOP'] ?>"
                                                 alt="">
                                            <? foreach ($arResult['BANNER']['DESKTOP_LINKS'] as $arItem) : ?>
                                                <a class="stock-banner__link" href="<?= $arItem['LINK'] ?>"
                                                   style="<?= $arItem['STYLE'] ?>"></a>
                                            <? endforeach ?>
                                        </div>
                                    </div>
                                <? endif ?>
                                <? if ($arResult['ITEMS']->nSelectedCount > 0) : ?>
                                    <? $counter = 0 ?>
                                    <? while ($arItem = $arResult['ITEMS']->Fetch()) : ?>
                                        <? $counter++;
                                        $arResult['CATALOG_ELEMENT_IDS'][] = sprintf('%s-%s', $arItem["ID"], $arResult['BRANCH_ID']);?>
                                        <? if (!empty($arResult['BANNER']['SINGLE']) && !$request->get('load_more') && $counter === 3) : ?>
                                            <div class="cards__item cards__item--banner card">
                                                <div class="banner">
                                                    <img src="<?= $arResult['BANNER']['SINGLE'] ?>" class="banner__img"
                                                         alt="">
                                                </div>
                                            </div>
                                        <? endif ?>
                                        <!-- card -->
                                        <?  $propCat = $arResult['PROPS']['RHODEPRODUCT'][$arItem['PROPERTY_RHODEPRODUCT_VALUE']];
                                            $propCat .= $arResult['PROPS']['VID'][$arItem['PROPERTY_VID_VALUE']] ? '/' . $arResult['PROPS']['VID'][$arItem['PROPERTY_VID_VALUE']] : '';
                                            $propCat .= $arResult['PROPS']['TYPEPRODUCT'][$arItem['PROPERTY_TYPEPRODUCT_VALUE']] ? '/' . $arResult['PROPS']['TYPEPRODUCT'][$arItem['PROPERTY_TYPEPRODUCT_VALUE']] : '';
                                            $propCat .= $arResult['PROPS']['SUBTYPEPRODUCT'][$arItem['PROPERTY_SUBTYPEPRODUCT_VALUE']] ? '/' . $arResult['PROPS']['SUBTYPEPRODUCT'][$arItem['PROPERTY_SUBTYPEPRODUCT_VALUE']] : '';
                                        ?>
                                        <div class="cards__item "
                                             data-prod-id="<?= $arItem['ID'] ?>"
                                             data-prod-articul="<?= $arItem['PROPERTY_ARTICLE_VALUE'] ?>"
                                             data-prod-name="<?= $arItem['NAME'] . ($arItem['PROPERTY_ARTICLE_VALUE'] ? ' | ' . $arItem['PROPERTY_ARTICLE_VALUE'] : '') ?>"
                                             data-prod-brand="<?= $arResult['PROPS']['BRAND'][$arItem['PROPERTY_BRAND_VALUE']] ?>"
                                             data-prod-top-material="<?= $arResult['PROPS']['UPPERMATERIAL'][$arItem['PROPERTY_UPPERMATERIAL_VALUE']] ?>"
                                             data-prod-lining-material="<?= $arResult['PROPS']['LININGMATERIAL'][$arItem['PROPERTY_LININGMATERIAL_VALUE']] ?>"
                                             data-prod-season="<?= $arResult['PROPS']['SEASON'][$arItem['PROPERTY_SEASON_VALUE']] ?>"
                                             data-prod-variant="<?= $arResult['PROPS']['COLORSFILTER'][$arItem['PROPERTY_COLORSFILTER_VALUE'][0]]['UF_NAME'] ?>"
                                             data-prod-collection="<?= $arResult['PROPS']['COLLECTION'][$arItem['PROPERTY_COLLECTION_VALUE']] ?>"
                                             data-prod-category="<?= $propCat ?>"
                                             data-prod-price="<?= number_format($arItem['PRICE'], 0, '', ''); ?>"
                                             data-prod-list="Каталог Страница <?= $arResult['CURRENT_PAGE_NOM'] ?> | <?= $arResult['TITLE'] ?? $APPLICATION->ShowTitle(false); ?>"
                                             data-prod-position="<?= $counter ?>"
                                        >
                                        <div class="card">
                                                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="card__img" target="_blank">
                                                    <div class="card__img-box">
                                                        <div class="lds-ring-container-lazyload">
                                                            <div class="lds-ring lds-ring--lazyload">
                                                                <div></div>
                                                                <div></div>
                                                                <div></div>
                                                                <div></div>
                                                            </div>
                                                        </div>
                                                        <? $hasSecondPic = !empty($arItem['PREVIEW_PICTURE']) ?>
                                                        <img
                                                            <? if ($arResult['USER_SETTINGS']['GRID'] == 'big') : ?>
                                                                src="<?= $arItem['DETAIL_PICTURE_BIG'] ;?>"
                                                                data-src-small="<?= $arItem['DETAIL_PICTURE'] ?>"
                                                            <? else : ?>
                                                                src="<?= $arItem['DETAIL_PICTURE'] ;?>"
                                                                data-src-big="<?= $arItem['DETAIL_PICTURE_BIG'] ?>"
                                                            <? endif; ?>
                                                            class="card__img-pic <?= ($arResult['USER_SETTINGS']['VIEW'] == 'true' && $hasSecondPic) ? 'pic-hide' : 'pic-active' ?> <?= !$hasSecondPic ? 'pic-one' : '' ?>"
                                                            alt="<?= $arItem['NAME'] ?>">
                                                        <? if ($hasSecondPic) : ?>
                                                            <img
                                                                <? if ($arResult['USER_SETTINGS']['GRID'] == 'big') : ?>
                                                                    src="<?= $arItem['PREVIEW_PICTURE_BIG'] ;?>"
                                                                    data-src-small="<?= $arItem['PREVIEW_PICTURE'] ?>"
                                                                <? else : ?>
                                                                    src="<?= $arItem['PREVIEW_PICTURE'] ;?>"
                                                                    data-src-big="<?= $arItem['PREVIEW_PICTURE_BIG'] ?>"
                                                                <? endif; ?>
                                                                class="card__img-pic <?= $arResult['USER_SETTINGS']['VIEW'] == 'true' ? 'pic-active' : 'pic-hide' ?>"
                                                                alt="<?= $arItem['NAME'] ?>">
                                                        <? endif ?>
                                                    </div>
                                                    <div class="card__info">
                                                        <div class="card__meta">
                                                            <div class="card__prices">
                                                                <div class="card__prices-top">
                                                                <span class="card__price<?= $arItem['SEGMENT'] == "Red" ? " card__price--discount" : "" ?>
                                                                <?= $arItem['SEGMENT'] == "Yellow" ? " card__price--discount-yellow" : "" ?>">
                                                                    <span class="card__price-num"><?= number_format($arItem['PRICE'], 0, '', ' '); ?></span> р.
                                                                </span>
                                                                    <? if (!empty($arItem['OLD_PRICE']) && $arItem['PRICE'] < $arItem['OLD_PRICE']) : ?>
                                                                        <span class="card__discount">-<?= $arItem['PERCENT'] ?>%</span>
                                                                    <? endif ?>
                                                                </div>

                                                                <? if (!empty($arItem['OLD_PRICE']) && $arItem['PRICE'] < $arItem['OLD_PRICE']) : ?>
                                                                    <span class="card__price-old" style="display:block;"><?= number_format($arItem['OLD_PRICE'], 0, '', ' '); ?> р.</span>
                                                                <? endif ?>
                                                            </div>
                                                        </div>
                                                        <span class="card__title"><?= $arItem['NAME'] ?></span>
                                                    </div>
                                                </a>
                                                <div class="card__icons">
                                                    <div class="card__props_icons">
                                                        <? if ($arItem['PROPERTY_UPPERMATERIAL_ICON']) : ?>
                                                            <img class='props_icon_img'
                                                                 src="<?= $arItem['PROPERTY_UPPERMATERIAL_ICON']['SRC'] ?>">
                                                            <? if ($arItem['PROPERTY_UPPERMATERIAL_ICON']['TOOLTIP']) : ?>
                                                                <div class="icon__tooltip">
                                                                    <?= $arItem['PROPERTY_UPPERMATERIAL_ICON']['TOOLTIP'] ?>
                                                                </div>
                                                            <? endif ?>
                                                        <? endif ?>
                                                        <? if ($arItem['PROPERTY_LININGMATERIAL_ICON']) : ?>
                                                            <img class='props_icon_img'
                                                                 src="<?= $arItem['PROPERTY_LININGMATERIAL_ICON']['SRC'] ?>">
                                                            <? if ($arItem['PROPERTY_LININGMATERIAL_ICON']['TOOLTIP']) : ?>
                                                                <div class="icon__tooltip">
                                                                    <?= $arItem['PROPERTY_LININGMATERIAL_ICON']['TOOLTIP'] ?>
                                                                </div>
                                                            <? endif ?>
                                                        <? endif ?>
                                                    </div>
                                                    <div class="card__delivery_icons">
                                                        <? if ($arItem['CAN_RESERVATION']) : ?>
                                                            <img class='props_icon_img'
                                                                 src="<?= $arItem['CAN_RESERVATION']['SRC'] ?>">
                                                            <? if ($arItem['CAN_RESERVATION']['TOOLTIP']) : ?>
                                                                <div class="icon__tooltip">
                                                                    <?= $arItem['CAN_RESERVATION']['TOOLTIP'] ?>
                                                                </div>
                                                            <? endif ?>
                                                        <? endif ?>
                                                        <? if ($arItem['CAN_DELIVERY']) : ?>
                                                            <img class='props_icon_img'
                                                                 src="<?= $arItem['CAN_DELIVERY']['SRC'] ?>">
                                                            <? if ($arItem['CAN_DELIVERY']['TOOLTIP']) : ?>
                                                                <div class="icon__tooltip">
                                                                    <?= $arItem['CAN_DELIVERY']['TOOLTIP'] ?>
                                                                </div>
                                                            <? endif ?>
                                                        <? endif ?>
                                                    </div>
                                                </div>
                                                <button type="button" class="heart__btn <?= isset($arResult['FAVORITES'][$arItem['ID']]) ? 'active' : '' ?>" data-id="<?= $arItem['ID'] ?>">
                                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 20 18" xml:space="preserve">
                                                                    <g>
                                                                        <path d="M18.4,1.8c-1-1.1-2.5-1.8-4-1.8l-3.1,1.1c-0.5,0.4-0.9,0.8-1.3,1.3c-0.4-0.5-0.8-1-1.3-1.3
                                                                               C7.8,0.4,6.7,0,5.6,0c-1.5,0-3,0.6-4,1.8C0.6,2.9,0,4.4,0,6.1C0,7.8,0.6,9.4,2,11c1.2,1.5,2.9,3,5,4.7c0.7,0.6,1.5,1.3,2.3,2
                                                                               C9.4,17.9,9.7,18,10,18s0.6-0.1,0.8-0.3c0.8-0.7,1.6-1.4,2.3-2c2-1.7,3.8-3.2,5-4.7c1.4-1.6,2-3.2,2-4.9C20,4.4,19.4,2.9,18.4,1.8
                                                                               z"/>
                                                                    </g>
                                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <!-- /card -->
                                    <? endwhile ?>
                                <? else : ?>
                                    <div class="page-massage <?= !empty($arResult['TAGS']) ? 'recomendation' : '' ?>">
                                        <? if (CSite::InDir('/catalog/favorites/')) : ?>
                                            <? ShowError('Ваш список избранного пока пуст') ?>
                                        <? elseif ($arResult['IS_AJAX']) : ?>
                                            <? ShowError('Товары не найдены, измените или сбросьте настройки фильтра') ?>
                                        <? elseif (!empty($arResult['TAGS'])) : ?>
                                            <? ShowError('Воспользуйтесь подобранными товарными категориями (вверху страницы). <br><br>А так же индивидуальными предложениями <br>(внизу страницы - блок рекомендуемых вам товаров).') ?>
                                        <? else : ?>
                                            <? ShowError('Товары не найдены') ?>
                                        <? endif ?>
                                    </div>
                                <? endif ?>
                            </div>
                        </div>
                        <? if ($arResult['SECTION_TYPE'] === 'search' && $arResult['ITEMS']->nSelectedCount > 0) :?>
                        <!-- RetailRocket -->
                        <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfd8a97a5250230f3bfb8" data-search-phrase="<?=$_GET['q']?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>">
                        </div>
                        <!-- /RetailRocket -->
                        <? endif; ?>
                        <? if ($arResult['SECTION_TYPE'] === 'favorites') :?>
                            <!-- RetailRocket -->
                            <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfdd197a5250230f3bfcf" data-favorite="<?=$arResult['FAVORITES_OFFERS_IDS']?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>">
                            </div>
                            <!-- /RetailRocket -->
                        <? endif; ?>
                        <div class="catalog__navigation">
                            <? if (!empty($arResult['NAV_STRING'])) : ?>
                                <?= $arResult['NAV_STRING'] ?>
                            <? endif ?>
                        </div>
                    </div>
                </div>
                <!-- /content -->
                <? if ($arResult['IS_AJAX']) {
                    //$APPLICATION->FinalActions();
                    exit;
                } ?>
            </div>
            <!-- /main -->
        </div>
        <!-- /catalog -->
    </div>
    <? if ($arResult['SECTION_TYPE'] === 'search' && $arResult['ITEMS']->nSelectedCount == 0) :?>
        <!-- RetailRocket -->
        <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfd9497a5250230f3bfbc" data-search-phrase="<?=$_GET['q']?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>">
        </div>
        <!-- /RetailRocket -->
    <? endif; ?>
</div>


<? if ($arResult['SECTION_ID']) : ?>
    <div class="i-flocktory" data-fl-action="track-category-view"
         data-fl-category-id="<?= $arResult['SECTION_ID'] ?>"></div>
<? endif; ?>
