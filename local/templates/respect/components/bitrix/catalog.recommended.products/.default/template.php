<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])): ?>
    <? foreach (array_chunk($arResult['ITEMS'], $arParams['LINE_ELEMENT_COUNT']) as $arItems): ?>
        <div class="container product-page">
            <div class="column-8 pre-1">
                <div class="products-grid products-grid--gitter">
                    <div class="container">
                        <? foreach ($arItems as $arItem): ?>
                            <div class="column-25 column-md-2">
                                <div class="products-item products-item--square">
                                    <div class="products-item__content">
                                        <? if (!empty($arItem['PICTURE']) && is_array($arItem['PICTURE'])): ?>
                                            <a href="<?= $arItem['DETAIL_PAGE_URL']; ?>"
                                               style="background-image: url(<?= $arItem['PICTURE']['SRC']; ?>)"
                                               class="products-item__image"></a>
                                        <? endif; ?>

                                        <div class="products-item__information">
                                            <div class="container products-item__title">
                                                <div class="column-10"><b><?= $arItem['NAME'] ?></b></div>
                                            </div>
                                            <div class="container">
                                                <div class="column-5">
                                                    <div class="products-item__cost">
                                                        <b><?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></b>
                                                    </div>
                                                </div>
                                                <div class="column-5">
                                                    <div class="products-item__buttons shortcuts">
                                                        <a class="shortcut js-add-to-favorites" title="Добавить в избранное" href="#" data-id="<?= $arItem['ID'] ?>"><i class="icon icon-heart"></i></a>
                                                        <? if (!empty($arItem['CAN_BUY'])): ?>
                                                            <a data-id="<?= $arItem['ID'] ?>" title="Купить с доставкой" class="shortcut js-add-to-basket" href="<?= $arItem['ADD_URL']; ?>"><i class="icon icon-cart"></i></a>
                                                        <? endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <? endforeach; ?>
<? endif; ?>