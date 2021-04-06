<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="products-grid products-grid--gitter b-recommended">
        <h4>Вам может понравиться</h4>
        <div class="container product-page js-products-slider">
            <? foreach ($arResult['ITEMS'] as $arItem): ?>

                <div class="column-5 column-md-1 column-xs-2">
                    <div class="products-item products-item--square">
                        <div class="products-item__content">

                            <? if (!empty($arItem['MORE_PICTURES'])): ?>
                                <div class="products-item__colors">
                                    <? $i = 0; ?>
                                    <? foreach ($arItem['MORE_PICTURES'] as $arPicture): ?>
                                        <a<? if (!$i++): ?> class="selected"<? endif; ?> style="background-image: url('<?= $arPicture['THUMB']; ?>');"></a>
                                    <? endforeach; ?>
                                </div>
                            <? endif; ?>

                            <? if (!empty($arItem['MORE_PICTURES'])): ?>
                                <? $i = 0; ?>
                                <? foreach ($arItem['MORE_PICTURES'] as $sColor => $arPicture): ?>
                                    <a class="products-item__image<? if ($i++): ?> hidden<? endif; ?>"
                                       href="<?= $arItem['DETAIL_PAGE_URL']; ?>"
                                       style="background-image: url('<?= $arPicture['FULL']; ?>')"></a>
                                <? endforeach; ?>
                            <? elseif (!empty($arItem['PICTURE'])): ?>
                                <a class="products-item__image"
                                   href="<?= $arItem['DETAIL_PAGE_URL']; ?>"
                                   style="background-image: url('<?= $arItem['PICTURE']['SRC']; ?>')"></a>
                            <? endif; ?>

                            <div class="products-item__information">
                                <div class="container products-item__title">
                                    <div class="column-10"><b><?= $arItem['NAME']; ?></b></div>
                                </div>
                                <div class="container">
                                    <div class="column-5">
                                        <div class="products-item__cost"><?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></div>
                                    </div>
                                    <div class="column-5">
                                        <div class="products-item__buttons shortcuts">
                                            <a class="shortcut js-add-to-favorites" title="Добавить в избранное" href="#" data-id="<?= $arItem['ID'] ?>"><i class="icon icon-heart"></i></a>

                                            <? if (!empty($arItem['CAN_BUY'])): ?>
                                                <a data-id="<?= $arItem['ID'] ?>" title="Купить с доставкой" class="shortcut js-add-to-basket" href="<?= $arItem['DETAIL_PAGE_URL']; ?>"><i class="icon icon-cart"></i></a>
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
<? endif; ?>
