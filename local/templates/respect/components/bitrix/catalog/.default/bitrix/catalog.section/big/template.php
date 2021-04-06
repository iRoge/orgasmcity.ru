<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
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

<? $this->SetViewTarget('under_instagram'); ?>
<? if ($arResult['DESCRIPTION']): ?>
    <div class="container">
        <div class="column-8 column-center">
            <div class="catalog-section-description">
                <?= $arResult['DESCRIPTION']; ?>
            </div>
        </div>
    </div>
<? endif; ?>
<? $this->EndViewTarget(); ?>

<? if (!empty($arResult['ITEMS'])): ?>
    <? foreach (array_chunk($arResult['ITEMS'], 2) as $iKey => $arItems): ?>
        <div class="container container--no-padding">
            <div class="column-8 pre-1">
                <div class="container products-grid">
                    <? foreach ($arItems as $arItem): ?>
                        <?
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'));
                        ?>
                        <div class="column-5 column-phone-2 column-xs-2">
                            <div class="products-item products-item--square products-item--2x">
                                <div id="<?= $this->GetEditAreaId($arItem['ID']); ?>" class="products-item__content">
                                    <? foreach ($arItem['LABELS'] as $sClass => $arLabel) : ?>
                                        <div class="products-item__label products-item__label--<?= $sClass ?>" 
                                                title="<?= $arLabel['NAME'] ?>" 
                                                style="background-image: url('<?= $arLabel['SRC']; ?>')"></div>
                                    <? endforeach; ?>
                                    
                                    <? if (!empty($arItem['MORE_PICTURES'])): ?>
                                        <div class="products-item__colors">
                                            <? $i = 0; ?>
                                            <? foreach ($arItem['MORE_PICTURES'] as $arPicture): ?>
                                                <a<? if (!$i++): ?> class="selected"<? endif; ?>
                                                        style="background-image: url('<?= $arPicture['THUMB']; ?>');"></a>
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
                                                    <a class="shortcut js-add-to-favorites" title="Добавить в избранное"
                                                       href="#" data-id="<?= $arItem['ID'] ?>"><i
                                                                class="icon icon-heart"></i></a>
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
        </div>
        <? if (!$iKey && $arResult['BANNER']): ?>
            <div class="container container--no-padding in-view in-view--visible">
                <div class="column-10">
                    <? if ($arResult['BANNER']['PROPERTY_BUTTON_LINK_VALUE']): ?>
                    <a href="<?= $arResult['BANNER']['PROPERTY_BUTTON_LINK_VALUE'] ?>">
                        <? endif ?>
                        <div style="background-image:url(<?= $arResult['BANNER']['DETAIL_PICTURE'] ?>)"
                             class="banner banner--l">

                            <div class="banner__content">
                            </div>
                        </div>

                        <? if ($arResult['BANNER']['PROPERTY_BUTTON_LINK_VALUE']): ?>
                    </a>
                <? endif ?>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>

    <? if (!empty($arResult['NAV_STRING'])): ?>
        <?= $arResult['NAV_STRING']; ?>
    <? else: ?>
        <div class="spacer--3"></div>
    <? endif; ?>
<? else: ?>
    <div class="page-massage">
        <? if (CSite::InDir('/catalog/favorites/')): ?>
            <? ShowError('Ваш список избранного пока пуст'); ?>
        <? else: ?>
            <? ShowError('Товары не найдены'); ?>
        <? endif; ?>
    </div>
<? endif; ?>
