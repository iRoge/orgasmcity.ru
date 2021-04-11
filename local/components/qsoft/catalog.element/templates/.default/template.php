<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs('/local/templates/respect/lib/jquery.zoom.min.js');
global $LOCATION;
global $APPLICATION;
?>
<script type="text/javascript">
    (window["rrApiOnReady"] = window["rrApiOnReady"] || []).push(function() {
        try{ rrApi.groupView([<?=implode(',', array_keys($arResult['RESTS']['ALL']))?>],{"stockId": "<?=$GLOBALS['USER_SHOWCASE']?>"}); } catch(e) {}
    })

    var propsTooltip = <?= CUtil::PhpToJSObject($arResult['AR_PROPS_TOOLTIP']); ?>;
</script>
<div class="col-xs-12 carto">
    <div class="main">
        <? if (!empty($arResult)) : ?>
            <script>
                BX.message({'RESERVED_STORES_LIST': '<?= json_encode($arResult['SHOPS']) ?>'});
            </script>
            <? if ($arResult['IS_PREORDER'] == false && ((empty($arResult['RESTS']['DELIVERY']) && empty($arResult['RESTS']['RESERVATION'])) || empty($arResult['PRICE_PRODUCT']))) : ?>
            <div class="product-page__na"><?= Loc::getMessage("OUT_STOCK_IN_LOCATION") ?></div>
            <? endif; ?>
            <?
            $propCat = $arResult['PROPS_GTM']['RHODEPRODUCT']['VALUE'];
            $propCat .= $arResult['PROPS_GTM']['VID']['VALUE'] ? '/' . $arResult['PROPS_GTM']['VID']['VALUE'] : '';
            $propCat .= $arResult['PROPS_GTM']['TYPEPRODUCT']['VALUE'] ? '/' . $arResult['PROPS_GTM']['TYPEPRODUCT']['VALUE'] : '';
            $propCat .= $arResult['PROPS_GTM']['SUBTYPEPRODUCT']['VALUE'] ? '/' . $arResult['PROPS_GTM']['SUBTYPEPRODUCT']['VALUE'] : '';
            ?>
            <div class="product-page product-main-div"
                 data-prod-id="<?= $arResult['ID'] ?>"
                 data-prod-articul="<?= $arResult['ARTICLE'] ?>"
                 data-prod-name="<?= $arResult['NAME'] . ($arResult['ARTICLE'] ? ' | ' . $arResult['ARTICLE'] : '') ?>"
                 data-prod-brand="<?= $arResult['PROPS_GTM']['BRAND']['VALUE'] ?>"
                 data-prod-top-material="<?= $arResult['PROPS_GTM']['UPPERMATERIAL']['VALUE'] ?>"
                 data-prod-lining-material="<?= $arResult['PROPS_GTM']['LININGMATERIAL']['VALUE'] ?>"
                 data-prod-season="<?= $arResult['PROPS_GTM']['SEASON']['VALUE'] ?>"
                 data-prod-variant="<?= $arResult['PROPS_GTM']['COLORSFILTER']['VALUE'] ?>"
                 data-prod-collection="<?= $arResult['PROPS_GTM']['COLLECTION']['VALUE'] ?>"
                 data-prod-category="<?= $propCat ?>"
                 data-prod-price="<?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'], 0, '', ''); ?>"
                 data-prod-list="Каталог Страница 1"
            >
                <div class="col-sm-6 slider-pro-container col-image">
                    <div id="example5" class="slider-pro">
                        <div class="sp-slides">
                            <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-slide jq-zoom">
                                    <img class="sp-image sp-image_hide sp-image-test"
                                         src=""
                                         data-src="<?= $arPhoto['SRC_ORIGINAL']; ?>"
                                         data-small="<?= Functions::checkMobileDevice() ? $arPhoto['SRC_MEDIUM'] : $arPhoto['SRC_ORIGINAL']; ?>"
                                         alt="<?= $arPhoto['ALT']; ?>"
                                         style="height: 600px;"/>
                                </div>
                            <? endforeach; ?>
                        </div>
                        <div class="sp-thumbnails">
                            <? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto) : ?>
                                <div class="sp-thumbnail">
                                    <div class="sp-thumbnail-image-container">
                                        <img class="sp-thumbnail-image sp-image_hide" src="<?= $arPhoto['THUMB']; ?>"
                                             alt="<?= $arPhoto['ALT']; ?>"/>
                                    </div>
                                </div>
                            <? endforeach; ?>
                        </div>
                    </div>
                    <? if ($arResult['DETAIL_TEXT'] || $arResult['YOUTUBE_LINK']) :?>
                        <div class="hidden-xs detail-element-text">
                            <? if (!empty($arResult['YOUTUBE_LINK'])) :?>
                                <div style="width: 100%;padding-bottom: 56.25%;position: relative;">
                                    <div style="position: absolute; left: 0; right: 0;top: 0;bottom: 0;">
                                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= $arResult['YOUTUBE_LINK'];?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                            <? endif; ?>
                            <?=$arResult['DETAIL_TEXT']?>
                        </div>
                    <? endif; ?>
                </div>

                <div class="col-sm-6 col-xs-12 right-cartochka__container">
                    <div class="right-cartochka">
                        <div class="right-cartochka__top-block">
                            <? if (!empty($arResult['ARTICLE'])) : ?>
                                <p class="grey-cart"><?= Loc::getMessage("ARTICLE_PREFIX") ?><?= $arResult['ARTICLE'] ?></p>
                            <? endif ?>
                            <button type="button" class="heart__btn <?= !empty($arResult['FAVORITES']) ? 'active' : '' ?>" data-id="<?= $arResult['ID'] ?>">
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
                        <h1 class="h1-cart"><?= $arResult["NAME"] ?></h1>
                        <? if (!empty($arResult['PRICE_PRODUCT'])) : ?>
                            <div class="right-cartochka__inner-wrap">
                                <p class="price<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red" ? " price--discount" : "" ?>
                            <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Yellow" ? " price--discount-yellow" : "" ?>
                                   <?= empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) ? " price--short" : "" ?>">
                                    <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'], 0, "", " ") ?></b>
                                    <?= Loc::getMessage("RUB") ?>
                                </p>
                                <? if (!empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) &&
                                    $arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'] < $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) : ?>
                                    <p class="percents">-<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['PERCENT'] ?>%</p>
                                    <p class="old-price">
                                        <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE'], 0, "", " ") ?></b>
                                        <?= Loc::getMessage("RUB") ?>
                                    </p>
                                <? endif ?>
                                <p class="grey-under bonus-text">
                                    <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == 'Red' || $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == 'Yellow' ? Loc::getMessage("NO_BONUS") : Loc::getMessage("BONUS") ?>
                                    <br/><?= Loc::getMessage("DIFFERENT_PRICES") ?>
                                    <?= $LOCATION->checkIfLocationIsDonorTarget($LOCATION->code) ? ('<br>*' . Bitrix\Main\Config\Option::get("respect", "product_cart_donors_text")) : '' ?>
                                </p>

                            </div>
                        <? endif ?>
                        <? if ((!empty($arResult['RESTS']['DELIVERY']) || !empty($arResult['RESTS']['RESERVATION']) || $arResult['PROPERTIES']['PREORDER']['VALUE'] == 'Y') && !empty($arResult['PRICE_PRODUCT'])) : ?>
                            <? if (!$arResult['SINGLE_SIZE']) : ?>
                                <h3 class="after-hr-cart"><?= Loc::getMessage("SIZE") ?></h3>
                            <? endif; ?>
                            <form method="post" name="name" style="width: 100%;" class="form-after-cart js-action-form">
                                <input type="hidden" name="action" value="ADD2BASKET">
                                <? if (!$arResult['SINGLE_SIZE']) : ?>
                                    <div style="display: block; width: 100%;" class="js-size-selector base-sizes">
                                        <? foreach ($arResult['RESTS']['ALL'] as $offerId => $data) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="id" id="offer-<?= $offerId ?>"
                                                       class="radio1 js-choose-size js-offer js-offer-<?= $offerId ?>"
                                                       data-is-local="<?=$arResult['RESTS']['DELIVERY'][$offerId]['IS_LOCAL']?>"
                                                       value="<?= $offerId ?>"/>
                                                <label class="<?= $arResult['IS_PREORDER'] ? 'preorder_sizes_input' : '' ?>"
                                                        for="offer-<?= $offerId ?>"
                                                       data-is-local="<?=$arResult['RESTS']['DELIVERY'][$offerId]['IS_LOCAL']?>"
                                                       data-offer-id="<?= $offerId ?>"><?= $data['SIZE'] ?></label>
                                            </div>
                                        <? endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <div style="display: none; width: 100%;" class="js-size-selector delivery-sizes">
                                        <input type="hidden" id="del-popup-type" value="">
                                        <? foreach ($arResult['RESTS']['DELIVERY'] as $offerId => $data) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="size-del" id="del-offer-<?= $offerId ?>"
                                                       class="radio1"
                                                       data-is-local="<?=$data['IS_LOCAL']?>"
                                                       value="<?= $offerId ?>"/>
                                                <label class="delivery-sizes-input" for="offer-<?= $offerId ?>"
                                                       data-is-local="<?=$data['IS_LOCAL']?>"
                                                       data-offer-id="<?= $offerId ?>"><?= $data['SIZE'] ?></label>
                                            </div>
                                        <? endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <div style="display: none; width: 100%;" class="js-size-selector reservation-sizes">
                                        <? foreach ($arResult['RESTS']['RESERVATION'] as $offerId => $data) : ?>
                                            <div class="top-minus">
                                                <input type="radio" name="size-res" id="res-offer-<?= $offerId ?>"
                                                       class="radio1"
                                                       value="<?= $offerId ?>"/>
                                                <label class="reservation-sizes-input" for="offer-<?= $offerId ?>"
                                                       data-offer-id="<?= $offerId ?>"><?= $data['SIZE'] ?></label>
                                            </div>
                                        <? endforeach; ?>
                                        <div style="clear: both"></div>
                                    </div>
                                    <div class="buttons-wrapper">
                                        <?php if ($arResult["ONLINE_TRY_ON"]) : ?>
                                            <input id="fittin_widget_button"
                                                   class="button-bordered button-bordered--transparent button-bordered--fitting <?= $USER->IsAuthorized() ? 'authorized' : 'non-authorized'; ?>"
                                                   type="button" value="Примерить онлайн">
                                            <div id="fittin_widget_dialog"></div>
                                        <?php endif; ?>
                                        <div class="sizes-popup-area">
                                            <a class="sizes-popup" href="#"><?= Loc::getMessage("SIZES_INFO") ?></a>
                                            <div class="sizes-popup-block" style="display:none;">
                                                <div class="tab-size-block">
                                                    <?= $arResult['SECTION_SIZES_TAB']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? endif; ?>
                                <div id="wrap" class="btns-wrap">
                                    <? if ($arResult['IS_PREORDER'] == false) { ?>
                                    <div id="js-toggle-delivery-ok"
                                         class="catalog-element-btn-container <?= empty($arResult['RESTS']['DELIVERY']) ? 'js-button-hide' : '' ?>">
                                        <?php if ($arResult['SHOW_ONE_CLICK']) :?>
                                        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
                                               data-is-local="<?= $arResult['SINGLE_SIZE'] ? $arResult['RESTS']['DELIVERY'][$arResult['SINGLE_SIZE']]['IS_LOCAL'] : "" ?>"
                                               id="one-click-btn"
                                               class="js-one-click cartochka-blue blue-btn"
                                               type="button"
                                               value="Купить в 1 клик"/>
                                        <?php endif; ?>
                                        <? if (in_array(\Functions::getEnvKey('SELLERS_GROUP_ID'), $USER->GetUserGroupArray())) { ?>
                                            <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
                                                   data-is-local="<?= $arResult['SINGLE_SIZE'] ? $arResult['RESTS']['DELIVERY'][$arResult['SINGLE_SIZE']]['IS_LOCAL'] : "" ?>"
                                                   id="seller_buy-btn"
                                                   class="js-cart-btn cartochka-orange yellow-btn js-cart-redirect"
                                                   style="width: <?=$arResult['SHOW_ONE_CLICK'] ? '49%' : '100%!important; margin-left: 0!important;'?>"
                                                   type="button"
                                                   value="Найти покупателя"/>
                                        <? } else { ?>
                                            <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
                                                   data-is-local="<?= $arResult['SINGLE_SIZE'] ? $arResult['RESTS']['DELIVERY'][$arResult['SINGLE_SIZE']]['IS_LOCAL'] : "" ?>"
                                                   id="buy-btn"
                                                   class="js-cart-btn cartochka-orange yellow-btn js-cart-redirect"
                                                   style="width: <?=$arResult['SHOW_ONE_CLICK'] ? '49%' : '100%!important; margin-left: 0!important;'?>"
                                                   type="button"
                                                   value="Добавить в корзину"/>
                                        <? } ?>
                                    </div>
                                    <div id="js-toggle-delivery-error"
                                         class="catalog-element-btn-container <?= !empty($arResult['RESTS']['DELIVERY']) ? 'js-button-hide' : '' ?>">
                                        <input class="cartochka-transparent cartochka-transparent--decoration"
                                               type="button"
                                               value="Недоступно для доставки"
                                               disabled/>
                                    </div>
                                    <div id="js-toggle-reserve-ok"
                                         class="catalog-element-btn-container <?= empty($arResult['RESTS']['RESERVATION']) ? 'js-button-hide' : '' ?>">
                                        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
                                               data-is-local=""
                                               id="reserved-btn"
                                               class="js-reserved-btn cartochka-border cartochka-transparent"
                                               type="button"
                                               value="Забрать в магазине"/>
                                    </div>
                                    <div id="js-toggle-reserve-error"
                                         class="catalog-element-btn-container <?= !empty($arResult['RESTS']['RESERVATION']) ? 'js-button-hide' : '' ?>">
                                        <input class="cartochka-transparent cartochka-transparent--decoration"
                                               type="button"
                                               value="Доступно только в интернет-магазине"
                                               disabled/>
                                    </div>
                                    <? } else { ?>
                                    <div id="js-toggle-preorder"
                                         class="catalog-element-btn-container">
                                        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
                                               data-is-local="<?= $arResult['SINGLE_SIZE'] ? $arResult['RESTS']['ALL'][$arResult['SINGLE_SIZE']]['IS_LOCAL'] : "" ?>"
                                               id="preorder-btn"
                                               class="js-cart-btn cartochka-orange yellow-btn js-cart-redirect"
                                               style="width: <?=$arResult['SHOW_ONE_CLICK'] ? '49%' : '100%!important; margin-left: 0!important;'?>"
                                               type="button"
                                               value="Сообщить о поступлении"/>
                                    </div>
                                    <? } ?>
                                </div>
                            </form>
                        <? endif; ?>
                        <? $APPLICATION->IncludeComponent(
                            "qsoft:infopage",
                            "advantagesInCatalogElement",
                            array(
                                    "IBLOCK_CODE" => 'advantagesInCatalogElement',
                                    "CACHE_TYPE" => "A",
                                    "CACHE_TIME" => "86400"
                                ),
                            false
                        ); ?>
                        <? if (!empty($arResult['COLORS'])) : ?>
                            <div clas="other-size">
                                <h3><?= Loc::getMessage("OTHERS_COLORS") ?></h3>
                                <div class="other-color">
                                    <? foreach ($arResult['COLORS'] as $arColor) : ?>
                                        <a href="<?= $arColor['DETAIL_PAGE_URL']; ?>" class="a-others">
                                            <div style="">
                                                <img src="<?= $arColor['FILE']; ?>" alt="<?= $arColor['NAME']; ?>"/>
                                            </div>
                                        </a>
                                    <? endforeach; ?>
                                </div>
                            </div>
                        <? endif; ?>
                    </div>
                    <? if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                        <div class="col-sm-12 hidden-xs" style="margin-right: 20px;margin-top: 50px">
                            <? if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                                <div class="p3">
                                    <div class="l3">Размер</div>
                                    <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                                </div>
                            <? endif;?>
                            <? $arTypesNotIgnore4Props = ['ЦБ0012949', 'ЦБ0013283'];
                            $ignore4Props = !in_array($arResult['PROPERTIES']['TYPEPRODUCT']['VALUE'], $arTypesNotIgnore4Props);
                            $ar4Props = ['MATERIALGOLENISHCHE', 'MATERIALSOUZKA', 'PODKLADGOLENISHCHE', 'PODKLADSOUZKA'];
                            foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                                <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                    <? if ($ignore4Props && in_array($key, $ar4Props)) :
                                        continue;
                                    endif; ?>
                                    <div class="p3 for-relative">
                                        <div class="l3"><?= $arProperty['NAME']; ?></div>
                                        <? if ($key == 'BRAND') { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip' : '' ?>"><a href='<?= $arResult['BRAND_PAGE'] ?>'><?= $arProperty['VALUE']; ?></a></div>
                                        <? } else { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip' : '' ?>"><?= $arProperty['VALUE']; ?></div>
                                        <? } ?>
                                        <? if (!empty($arProperty['TOOLTIP'])) : ?>
                                            <div class="props-tooltip"><?= $arProperty['TOOLTIP'] ?></div>
                                        <? endif; ?>
                                    </div>
                                <? endif; ?>
                            <? endforeach; ?>
                            <div class="opisanie-after"><?= Loc::getMessage("DESCRIPTION_HEADER") ?></div>
                        </div>
                    <? endif; ?>
                </div>
                <? if (!empty($arResult['DISPLAY_PROPERTIES'])) : ?>
                    <div class="hidden-lg hidden-md hidden-sm col-xs-12 info--"
                         style="margin-left: 20px;margin-top: 50px">
                        <? if (!empty($arResult['SIZES_PROPERTIES'])) :?>
                            <div class="p3">
                                <div class="l3">Размер</div>
                                <div class="r3"><?=implode(' x ', $arResult['SIZES_PROPERTIES']) . ' см'?></div>
                            </div>
                        <? endif;?>
                        <? $arTypesNotIgnore4Props = ['ЦБ0012949', 'ЦБ0013283'];
                        $ignore4Props = !in_array($arResult['PROPERTIES']['TYPEPRODUCT']['VALUE'], $arTypesNotIgnore4Props);
                        $ar4Props = ['MATERIALGOLENISHCHE', 'MATERIALSOUZKA', 'PODKLADGOLENISHCHE', 'PODKLADSOUZKA'];
                        foreach ($arResult['DISPLAY_PROPERTIES'] as $key => $arProperty) : ?>
                            <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])) : ?>
                                <? if ($ignore4Props && in_array($key, $ar4Props)) :
                                    continue;
                                endif; ?>
                                <div class="p3">
                                    <div class="l3"><?= $arProperty['NAME']; ?></div>
                                    <? if ($key == 'BRAND') { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip-mob' : '' ?>"
                                            <?= $arProperty['TOOLTIP'] ? ' data-tooltipname="' . $arProperty['CODE'] . '"' : '' ?>
                                        >
                                            <a href='<?= $arResult['BRAND_PAGE'] ?>'><?= $arProperty['VALUE']; ?></a>
                                        </div>
                                    <? } else { ?>
                                        <div class="r3 <?= $arProperty['TOOLTIP'] ? 'have-tooltip-mob' : '' ?>"
                                            <?= $arProperty['TOOLTIP'] ? ' data-tooltipname="' . $arProperty['CODE'] . '"' : '' ?>
                                        >
                                            <?= $arProperty['VALUE']; ?>
                                        </div>
                                    <? } ?>
                                </div>
                            <? endif; ?>
                        <? endforeach; ?>
                        <? if ($arResult['DETAIL_TEXT'] || $arResult['YOUTUBE_LINK']) :?>
                            <div class="detail-element-text">
                                <? if (!empty($arResult['YOUTUBE_LINK'])) :?>
                                <div style="width: 100%;padding-bottom: 56.25%;position: relative;">
                                    <div style="position: absolute; left: 0; right: 0;top: 0;bottom: 0;">
                                        <iframe width="100%" height="100%" src="https://www.youtube.com/embed/<?= $arResult['YOUTUBE_LINK'];?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <? endif; ?>
                                <?=$arResult['DETAIL_TEXT']?>
                            </div>
                        <? endif; ?>
                    </div>
                <? endif; ?>
            </div>

            <div class="hidden-divs" style="display: none">
                <form id="one-click-form"
                      class="product-page product b-element-one-click one-click-form js-one-click-form one-click-content"
                      action="/cart/" method="post">
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="action" value="1click">
                    <input type="hidden" name="PRODUCTS[]" value="">
                    <input type="hidden" name="PROPS[IS_LOCAL]" value="Y">
                    <div id="after-cart-in-err"></div>
                    <div class="container container--quick-order">
                        <div class="column-5 column-md-2">
                            <? if (!empty($arResult['PRICE_PRODUCT'][$arResult['ID']]) && $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == 'White' && !$USER->IsAuthorized()) :?>
                                <div style="margin-top: 25px; font-size: 1.6rem;" class="product-page__na"><?='Заказ будет оформлен без учета индивидуальной скидки по программе лояльности RESPECT| БОНУС. Стоимость заказа составит <b>' . $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE'] . ' руб</b>. <span style="color: #337ab7" class="ent span-1click-auth" onclick="$(\'.popup\').toggle();$(\'body\').removeClass(\'with--popup\')">Авторизуйтесь или зарегистрируйтесь</span>, чтобы получить скидку согласно вашего бонусного статуса.';?></div>
                            <? endif; ?>
                            <div class="form">
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="one_click_fio"
                                           value="<?=$arResult['USER']['NAME'] ? $arResult['USER']['NAME'] . ' ' . $arResult['USER']['LAST_NAME'] : ''?>"
                                           type="text" name="PROPS[FIO]" placeholder="<?=$arResult['USER']['NAME'] ? $arResult['USER']['NAME'] . ' ' . $arResult['USER']['LAST_NAME'] : 'ФИО'?>">
                                </div>
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="one_click_phone"
                                           data-phone="<?=$arResult['USER']['PERSONAL_PHONE'];?>"
                                           type="text" name="PROPS[PHONE]" placeholder="*Телефон" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order">
                        <div class="column-10">
                            <hr/>
                        </div>
                    </div>
                    <div class="container container--quick-order">
                        <div class="column-4 pre-3 column-md-2">
                            <button id="button-one-click"
                                    class="buttonFastBuy"><?= Loc::getMessage("MAKE_ORDER") ?></button>
                            <div class="buttonFastBuy-loader">
                                <div class="one-click-preloader-div">
                                    <button class="one-click-preloader"><?= Loc::getMessage("WAIT") ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order product__footer">
                        <? $APPLICATION->IncludeComponent('qsoft:subscribe.manager', 'popUp', ['SOURCE' => '1click']); ?>
                        <div id="one_click_checkbox_policy_error"></div>
                        <div id="one_click_checkbox_policy" class="col-xs-12">
                            <input type="checkbox" id="one_click_checkbox_policy_checked"
                                   name="one_click_checkbox_policy"
                                   class="checkbox3" checked/>
                            <label for="one_click_checkbox_policy_checked"
                                   class="checkbox--_"><?= Loc::getMessage('AGREEMENT') ?></label>
                        </div>
                    </div>
                </form>

                <div class="product-preorder-success js-choose-size">
                    <h2><?= Loc::getMessage("CHOOSE_SIZE") ?></h2>
                    <form method="post" name="name" class="form-after-cart js-action-form-popup-size">
                        <input type="hidden" name="action" value="">
                        <div class="js-size-popup">
                        </div>
                    </form>
                </div>

                <form id="preorder-form"
                      class="product-page product b-element-one-click one-click-form js-one-click-form one-click-content"
                      action="#" method="">
                    <div class="preorder-head">ОСТАВЬТЕ E-MAIL,<br>МЫ ПРИШЛЕМ ВАМ УВЕДОМЛЕНИЕ О ПОСТУПЛЕНИИ ТОВАРА</div>
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="action" value="preorder">
                    <div class="container container--quick-order">
                        <div class="column-5 column-md-2">
                            <div class="form form-preorder">
                                <div class="input-group input-group--phone">
                                    <input style="margin-top: 10px"
                                           class="preorder_email"
                                           data-phone="<?= $arResult['USER']['EMAIL'] ?? $_SESSION['PREORDER_EMAIL'];?>"
                                           type="text" name="PROPS[EMAIL]" placeholder="*Электронная почта" required value="<?= $arResult['USER']['EMAIL'] ?? $_SESSION['PREORDER_EMAIL'];?>">
                                </div>
                                <div class="error-email error-preorder"></div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order">
                        <div class="column-10">
                            <hr/>
                        </div>
                    </div>
                    <div class="container container--quick-order">
                        <div class="column-4 pre-3 column-md-2">
                            <button id="button-preorder"
                                    class="buttonFastBuy"><?= Loc::getMessage("MAKE_PREORDER") ?></button>
                            <div class="buttonFastBuy-loader">
                                <div class="one-click-preloader-div">
                                    <button class="one-click-preloader"><?= Loc::getMessage("WAIT") ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container container--quick-order product__footer">
                        <div id="one_click_checkbox_policy_error"></div>
                        <div id="one_click_checkbox_policy" class="col-xs-12">
                            <input type="checkbox" id="one_click_checkbox_policy_checked"
                                   name="one_click_checkbox_policy"
                                   class="checkbox3" checked/>
                            <label for="one_click_checkbox_policy_checked"
                                   class="checkbox--_"><?= Loc::getMessage('AGREEMENT') ?></label>
                            <div class="error-policy error-preorder"></div>
                        </div>
                    </div>
                </form>

                <form id="reserve-form" class="product-page product b-element-one-click js-reserv" action="/cart/"
                      method="post">
                    <?= bitrix_sessid_post(); ?>
                    <input type="hidden" name="action" value="reserv">
                    <input type="hidden" name="DELIVERY_STORE_ID" value="">
                    <div class="product-preorder">
                        <header>
                            <div class="product-preorder__title"><?= Loc::getMessage("RESERVATION") ?></div>
                        </header>
                        <main>
                            <?if ($arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == 'White' && !$USER->IsAuthorized()) :?>
                                <div class="product-page__na product-page__na--reserve">
                                    <?=Loc::getMessage('WARNING', ['#PRICE#' => $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']])?>
                                </div>
                            <? endif; ?>
                            <div class="row product-preorder__container">
                                <aside class="col-md-6 col-lg-4 product-preorder__aside-info column-33 column-md-2">
                                    <div class="product-preorder__short-info">
                                        <? if (!empty($arResult['NAME'])) : ?>
                                            <div class="product-preorder__name">
                                                <?= $arResult['NAME']; ?>
                                            </div>
                                        <? endif; ?>
                                        <? if (!empty($arResult['ARTICLE'])) : ?>
                                            <div class="product-preorder__sku">
                                                <?= Loc::getMessage('ARTICLE_PREFIX') ?><?= $arResult['ARTICLE'] ?>
                                            </div>
                                        <? endif; ?>
                                    </div>
                                    <div class="product-preorder__info">
                                        <div class="product-preorder__price">
                                            <b class="product-preorder__main-price<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red" ? " product-preorder__main-price--discount" : "" ?>
                                                <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Yellow" ? " product-preorder__main-price--discount-yellow" : "" ?>">
                                                <?= number_format(
                                                    $arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'],
                                                    0,
                                                    "",
                                                    " "
                                                ) ?><?= Loc::getMessage('RUB') ?>
                                            </b>
                                            <? if (!empty($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) &&
                                                $arResult['PRICE_PRODUCT'][$arResult['ID']]['PRICE'] < $arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE']) : ?>
                                                <div class="product-preorder__discount-percent">
                                                    -<?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['PERCENT'] ?>%
                                                </div>
                                                <div class="product-preorder__old-price">
                                                    <b><?= number_format($arResult['PRICE_PRODUCT'][$arResult['ID']]['OLD_PRICE'], 0, "", " ") ?></b><?= Loc::getMessage('RUB') ?>
                                                </div>
                                            <? endif ?>
                                        </div>
                                    </div>
                                    <? if (!empty($arResult['PRICE_PRODUCT'])) : ?>
                                        <div class="product-preorder__messages">
                                            <div class="product-preorder__cost-segment-desc">
                                                <?= $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Red" || $arResult['PRICE_PRODUCT'][$arResult['ID']]['SEGMENT'] == "Yellow" ? '*скидка по бонусной программе будет рассчитана автоматически' : Loc::getMessage('BONUS') ?>
                                            </div>
                                            <div class="product-preorder__cost-segment-desc">
                                                <?= Loc::getMessage('DIFFERENT_PRICES') ?>
                                            </div>
                                        </div>
                                    <? endif ?>
                                    <? if (!$arResult['SINGLE_SIZE']) : ?>
                                        <div class="product-preorder__size">
                                            <label><?= Loc::getMessage("SIZES") ?></label>
                                            <div class="size-selector size-selector--wrap js-size-selector">
                                                <? foreach ($arResult['RESTS']['RESERVATION'] as $offerId => $data) : ?>
                                                    <div class="top-minus">
                                                        <input type="radio"
                                                               name="PRODUCTS[]"
                                                               id="reserve-offer-<?= $offerId ?>"
                                                               class="radio1 js-offer-res"
                                                               value="<?= $offerId ?>"
                                                        />
                                                        <label class="reservation-popup-sizes-input"
                                                               for="reserve-offer-<?= $offerId ?>"
                                                               data-offer-id="<?= $offerId ?>"><?= $data['SIZE'] ?></label>
                                                    </div>
                                                <? endforeach; ?>
                                                <div class="alert alert--danger js-offer-error" style="display: none;">
                                                    <div class="alert-content noshop-block">
                                                        <i class="icon icon-exclamation-circle"></i>
                                                        <?= Loc::getMessage('CHOOSE_SIZE') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <? else : ?>
                                        <input type="radio"
                                               name="PRODUCTS[]"
                                               id="reserve-offer-<?= $arResult['SINGLE_SIZE'] ?>"
                                               class="radio1 js-offer-res single-size-input"
                                               value="<?= $arResult['SINGLE_SIZE'] ?>"
                                               checked/>
                                    <? endif ?>
                                </aside>
                                <article class="col-md-6 col-lg-8 column-md-2 product-preorder__article column-66">
                                    <div class="tabs tabs--shop js-tabs">
                                        <a data-target="#list"
                                           class="tabs-item active"><?= Loc::getMessage("LIST") ?></a>
                                        <a data-target="#map" class="tabs-item "><?= Loc::getMessage("MAP") ?></a>
                                    </div>
                                    <input type="text" name="store_name" class="search-mag d-inline col-xs-12" placeholder="Поиск по названию, адресу, метро" value="">
                                    <div class="clearfix"></div>
                                    <br>
                                    <div class="tabs-targets">
                                        <div id="list" class="active" data-init="list">
                                            <div class="preorder-list" id="reserved-shop-list">
                                            </div>
                                        </div>
                                        <div id="map" data-init="map">
                                            <div class="shop-map--square js-shop-list-map shop-map" id="reserved-map"
                                                 data-lat="<?= $arResult['LOCATION']['LAT'] ?>"
                                                 data-lon="<?= $arResult['LOCATION']['LON'] ?>">
                                            </div>
                                        </div>
                                        <div id="subway" class="subway-map" data-init="metro">
                                            <div class="preloader">
                                                <div class="bounce1"></div>
                                                <div class="bounce2"></div>
                                                <div class="bounce3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                                <aside class="col-md-6 col-lg-4 product-preorder__aside-form">
                                    <div class="product-preorder__form">
                                        <div id="after-cart-in-err"></div>
                                        <input name="PROPS[FIO]" data-fio="<?= $arResult['USER']['FIO'] ?>" class="fio"
                                               placeholder="*ФИО" type="text" required>
                                        <input name="PROPS[PHONE]"
                                               data-phone="<?= $arResult['USER']['PERSONAL_PHONE'] ?>"
                                               class="reservation_phone" placeholder="*Телефон" type="text" required>
                                        <? if ($_COOKIE['seller_id'] && !in_array(\Functions::getEnvKey('SELLERS_GROUP_ID'), $USER->GetUserGroupArray())) { ?>
                                        <input name="USER_DESCRIPTION"
                                               class="reservation_comment" placeholder="Комментарий к заказу" type="text">
                                        <? } ?>
                                        <div class="alert alert--danger js-store-selected phone--only"
                                             style="display: none;">
                                            <div class="alert-content">
                                                <?= Loc::getMessage('SELECTED_STORE') ?> «<span
                                                        class="js-store-selected-value"></span>»
                                            </div>
                                        </div>
                                        <? if (in_array(\Functions::getEnvKey('SELLERS_GROUP_ID'), $USER->GetUserGroupArray())) { ?>
                                            <button id="seller_btn-reserv"
                                                    class="js-preorder-submit cartochka-transparent js-preorder-submit--reservation">Найти покупателя</button>
                                        <? } else { ?>
                                            <button form="reserve-form"
                                                    class="js-preorder-submit cartochka-transparent js-preorder-submit--reservation"><?= Loc::getMessage("RESERVE") ?></button>
                                        <? } ?>
                                        <div class="buttonReservation-loader">
                                            <div class="one-click-preloader-div">
                                                <button class="reservation-preloader"><?= Loc::getMessage('WAIT') ?></button>
                                            </div>
                                        </div>
                                           <? $APPLICATION->IncludeComponent('qsoft:subscribe.manager', 'popUp', ['SOURCE' => 'reserv']); ?>
                                        <div id="reservation_checkbox_policy" class="col-xs-12">
                                            <input type="checkbox" id="reservation_checkbox_policy_checked"
                                                   name="reservation_checkbox_policy" class="checkbox3" checked/>
                                            <label for="reservation_checkbox_policy_checked"
                                                   class="checkbox--_"><?= Loc::getMessage('AGREEMENT') ?></label>
                                        </div>
                                    </div>
                                </aside>
                            </div>
                        </main>
                    </div>
                    <div class="js-success-cont" style="display: none;">
                        <article class="popup__content">
                            <div class="product-preorder-success">
                                <header><?= Loc::getMessage("THANKS") ?></header>
                                <article>
                                    <div class="product-preorder-success__title"><?= Loc::getMessage("PRODUCT_RESERVED") ?></div>
                                    <div class="product-preorder-success__subtitle"><?= Loc::getMessage("RESERVE_NUMBER") ?></div>
                                    <div class="product-preorder-success__number"></div>
                                </article>
                                <footer>
                                    <button class="js-popup-close button button--xxl button--primary button--outline"><?= Loc::getMessage("OK") ?></button>
                                </footer>
                            </div>
                        </article>
                    </div>
                </form>
            </div>
            <!-- RetailRocket -->
            <div class="js-retail-rocket-recommendation" data-retailrocket-markup-block="5ebcfd7197a52821e059f039" data-product-id="<?=implode(',', array_keys($arResult['RESTS']['ALL']))?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>"></div>
            <div class="js-retail-rocket-recommendation2" data-retailrocket-markup-block="5ebcfd7a97a5250230f3bfb4" data-product-id="<?=implode(',', array_keys($arResult['RESTS']['ALL']))?>" data-stock-id="<?=$GLOBALS['USER_SHOWCASE']?>"></div>
            <!-- End RetailRocket -->
            <? // фикс для безразмерной номенклатуры?>
            <? if ($arResult['SINGLE_SIZE']) :
                if ($arResult["BASKET_OFFERS"][$arResult['SINGLE_SIZE']]) : ?>
            <script>$("#buy-btn").val("В корзине")</script>
                <? endif;
            endif ?>
            <script>
                inBasket = JSON.parse('<?= json_encode($arResult["BASKET_OFFERS"] ?? array(), JSON_UNESCAPED_UNICODE) ?>') || [];
            </script>
        <? else : ?>
            <div class="container">
                <div class="column-8 pre-1">
                    <div class="alert alert-danger"><?= Loc::getMessage("ELEMENT_NOT_FOUND") ?></div>
                </div>
            </div>
        <? endif; ?>
    </div>
</div>