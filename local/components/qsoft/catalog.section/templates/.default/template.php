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
$curPage = $APPLICATION->GetCurPage(false);
if (!$arResult['IS_AJAX']) :
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
        <!-- catalog -->
        <div class="catalog">
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
                                <? if (!empty($arResult['SAME_SECTIONS']) && $arResult['IS_AJAX']) :?>
                                    <div class="in-left-catalog subsections-block">
                                        <div class="name-h3 active-name-h3">
                                            <h3>
                                                Похожие разделы
                                            </h3>
                                            <svg class="minus" style="display: inline; position: absolute">
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#minus"></use>
                                            </svg>
                                            <svg class="plus" style="display: none;">
                                                <use xlink:href="/local/templates/respect/icons/icons-sprite.svg#plus"></use>
                                            </svg>
                                        </div>
                                        <div class="in-in-left scrollbar-inner max-height-400" data-filter="type" style="display: block;">
                                            <ul class=" filter__main-list" id="gender-list">
                                                <div class="filter__vid-elem">
                                                    <? foreach ($arResult['SAME_SECTIONS'] as $section) :?>
                                                        <li>
                                                            <a class="name-h3" href="<?=$section['SECTION_PAGE_URL']?>" style="display: block; width: 100%; font-weight: bold; font-family: 'firalight'; font-size: 20px; color: #4e4e4e;">
                                                                <?=$section['NAME']?>
                                                            </a>
                                                        </li>
                                                    <? endforeach; ?>
                                                </div>
                                            </ul>
                                        </div>
                                        <div style="clear: both;"></div>
                                    </div>
                                <? endif; ?>
                                <input id="set_filter" type="hidden" name="set_filter" value="Y">
                                <? foreach ($arResult['FILTER_KEYS'] as $filterKey) : ?>
                                    <? if (in_array($filterKey, ['PRICE', 'DIAMETER', 'LENGTH']) && !empty($arResult['FILTER']['MAX_' . $filterKey]) && !empty($arResult['FILTER']['MIN_' . $filterKey])) : ?>
                                        <div class="in-left-catalog in-left-catalog--price<?= $arResult['FILTER']['CHECKED'][$filterKey] ? ' in-left-catalog--checked' : '' ?>">
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
                                            <div class="in-in-left"<?=$arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:block"' : '' ?>>
                                                <div class="from">
                                                    <span>От</span>
                                                    <input id="min_<?=strtolower($filterKey)?>" class="js-number-filter" type="text" name="min_<?=strtolower($filterKey)?>"
                                                           value="<?= $arResult['FILTER']['CHECKED']['MIN_' . $filterKey] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MIN_' . $filterKey] ?>">
                                                </div>
                                                <div class="to">
                                                    <span>До</span>
                                                    <input id="max_<?=strtolower($filterKey)?>" class="js-number-filter" type="text" name="max_<?=strtolower($filterKey)?>"
                                                           value="<?= $arResult['FILTER']['CHECKED']['MAX_' . $filterKey] ?>"
                                                           autocomplete="off" autofocus="" spellcheck="false"
                                                           oninput="smartFilter.changedPriceFilter(this);return false;"
                                                           placeholder="<?= $arResult['FILTER']['MAX_' . $filterKey] ?>">
                                                </div>
                                                <div style="clear: both"></div>
                                            </div>
                                            <div style="clear: both;"></div>
                                        </div>
                                        <? continue; ?>
                                    <? endif; ?>
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
                                        <div class="in-in-left scrollbar-inner"<?=$arResult['FILTER']['CHECKED'][$filterKey] ? ' style="display:block"' : '' ?>
                                             data-filter-name="<?= $jsKey ?>">
                                            <?if ($filterKey === 'COLORS') :?>
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
                                                        <label for="color_<?= $xml_id ?>" class="label-for-color <?= $color['DISABLED'] ? 'mydisabled' : '' ?>">
                                                            <? if ($color['VALUE']['IMG_SRC']) : ?>
                                                            <img class="inner-color" width="22" height="22" src="<?=$color['VALUE']['IMG_SRC']; ?>" alt="">
                                                            <? endif; ?>
                                                            <?=$color['VALUE']['UF_NAME']; ?>
                                                        </label>
                                                    </div>
                                                <? endforeach; ?>
                                            <? else :?>
                                                <? foreach ($value as $key => $item) : ?>
                                                <input id="<?=$jsKey ?>_<?=$key ?>"
                                                       class="checkbox_size"
                                                       type="checkbox"
                                                       name="<?=$jsKey ?>"
                                                       value="<?=$key ?>"
                                                    <? if (!empty($item['CHECKED'])) : ?>
                                                        checked
                                                    <? endif; ?>
                                                    <? if (!empty($item['DISABLED'])) : ?>
                                                        disabled
                                                    <? endif; ?>
                                                       onchange="smartFilter.click(this)">
                                                <label for="<?=$jsKey ?>_<?=$key ?>" <?= !empty($item['DISABLED']) ? 'class="mydisabled"' : '' ?>><?=$item['VALUE'] ?></label>
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
                                    <? while ($arItem = $arResult['ITEMS']->Fetch()) : ?>
                                        <!-- card -->
                                        <div class="cards__item">
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
<!--                                                        <style>-->
<!--                                                            .fig {-->
<!--                                                                display: block; /* Блочный элемент (для старых браузеров) */-->
<!--                                                                text-align: center; /* Выравнивание по центру */-->
<!--                                                                font-style: italic; /* Курсивное начертание */-->
<!--                                                                margin-top: 0; /* Отступ сверху */-->
<!--                                                                margin-bottom: 5px; /* Отступ снизу */-->
<!--                                                                color: #666; /* Цвет подрисуночной подписи */-->
<!--                                                            }-->
<!--                                                        </style>-->
<!--                                                        <figure class="fig">-->
                                                            <img
                                                                <? if ($arResult['USER_SETTINGS']['GRID'] == 'big') : ?>
                                                                    src="<?= $arItem['DETAIL_PICTURE_BIG'] ;?>"
                                                                    data-src-small="<?= $arItem['DETAIL_PICTURE'] ?>"
                                                                <? else : ?>
                                                                    src="<?= $arItem['DETAIL_PICTURE'] ;?>"
                                                                    data-src-big="<?= $arItem['DETAIL_PICTURE_BIG'] ?>"
                                                                <? endif; ?>
                                                                class="card__img-pic pic-active pic-one"
                                                                alt="<?= $arItem['NAME'] ?>">
<!--                                                        </figure>-->
                                                    </div>
                                                    <div class="card__info">
                                                        <div class="card__meta">
                                                            <div class="card__prices">
                                                                <div class="card__prices-top">
                                                                <span class="card__price <?= $arItem['PRICE'] < $arItem['OLD_PRICE'] ? " card__price--discount" : "" ?>">
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
                                                <button title="Добавить в избранное" type="button" class="heart__btn<?=isset($arResult['FAVORITES'][$arItem['ID']]) ? ' active' : '' ?>" data-id="<?= $arItem['ID'] ?>">
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
</div>

