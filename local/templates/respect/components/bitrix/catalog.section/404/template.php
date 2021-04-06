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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="container">
        <div class="column-8 pre-1">
            <h3>Вас может заинтересовать</h3>
        </div>
    </div>
    <div class="container container--no-padding">
        <div class="column-8 pre-1">
            <div class="container js-products-slider">
                <? foreach ($arResult['ITEMS'] as $arItem): ?>
                    <?
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'));
                    ?>
                    <div class="column-25 gallery-25 gallery-phone-50">
                        <div class="products-item products-item--square">
                            <div id="<?= $this->GetEditAreaId($arItem['ID']); ?>" class="products-item__content">
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

            <div class="container show-more">
                <div class="column-6 pre-2">
                    <a href="/catalog/" class="button button--xl button--transparent button--block">
                        Показать все
                    </a>
                </div>
            </div>
        </div>
    </div>
<? endif; ?>