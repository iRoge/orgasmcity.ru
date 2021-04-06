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

$arFirstBlockItems = array_slice($arResult['ITEMS'], 0, 4);
$arBigItem = reset(array_slice($arResult['ITEMS'], 4, 1));
$arLastBlockItems = array_slice($arResult['ITEMS'], 5);
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
    <? if (!empty($arFirstBlockItems)): ?>
        <div class="container container--no-padding">
            <div class="column-8 pre-1">
                <div class="container products-grid">
                    <? if ($arBigItem): ?>
                        <div class="column-5 column-md-2">
                            <? foreach (array_chunk($arFirstBlockItems, 2) as $arItems): ?>
                                <div class="container">
                                    <? foreach ($arItems as $arItem): ?>
                                        <div class="column-5 column-md-1 column-xs-1">
                                            <div class="products-item products-item--square">
                                                <? include 'element.php'; ?>
                                            </div>
                                        </div>
                                    <? endforeach; ?>
                                </div>
                            <? endforeach; ?>
                        </div>

                        <div class="column-5 column-md-2">
                            <div class="products-item products-item--square products-item--2x">
                                <? $arItem = $arBigItem; ?>
                                <? include 'element.php'; ?>
                            </div>
                        </div>
                    <? else: ?>
                        <div class="column-10">
                            <div class="container">
                                <? foreach ($arFirstBlockItems as $arItem): ?>
                                    <div class="column-25 column-md-1 column-xs-1">
                                        <div class="products-item products-item--square">
                                            <? include 'element.php'; ?>
                                        </div>
                                    </div>
                                <? endforeach; ?>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
        </div>
    <? endif; ?>
    <? if ($arResult['BANNER']): ?>
        <div class="container phone--hidden">
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
    <? endif ?>
    <? if ($arLastBlockItems): ?>
        <div class="container container--no-padding">
            <div class="column-8 pre-1">
                <?
                $iCount = empty($arResult['ACTION']) ? 4 : 3; //кол-во товаров в первом ряду
                $arItems1 = array_slice($arLastBlockItems, 0, $iCount);
                $arItems2 = array_slice($arLastBlockItems, $iCount);
                ?>
                <div class="container products-grid">
                    <? foreach ($arItems1 as $arItem): ?>
                        <div class="column-25 column-md-1 column-xs-1">
                            <div class="products-item products-item--square">
                                <? include 'element.php'; ?>
                            </div>
                        </div>
                    <? endforeach; ?>

                    <? if (!empty($arResult['ACTION'])): ?>
                        <div class="column-25 column-md-1 column-xs-1">
                            <? if (!empty($arResult['ACTION']['PROPERTY_LINK_VALUE'])): ?>
                            <a href="<?= $arResult['ACTION']['PROPERTY_LINK_VALUE']; ?>">
                                <? endif; ?>
                                <img src="<?= $arResult['ACTION']['PREVIEW_PICTURE']['SRC'] ?>"
                                     alt="<?= $arResult['ACTION']['PREVIEW_PICTURE']['ALT'] ?>">

                                <? if (!empty($arResult['ACTION']['PROPERTY_LINK_VALUE'])): ?>
                            </a>
                        <? endif; ?>
                        </div>
                    <? endif; ?>
                </div>

                <? foreach (array_chunk($arItems2, 4) as $arItems): ?>
                    <div class="container products-grid js-products-slider">
                        <? foreach ($arItems as $arItem): ?>
                            <div class="column-25 column-md-1 column-xs-1">
                                <div class="products-item products-item--square">
                                    <? include 'element.php'; ?>
                                </div>
                            </div>
                        <? endforeach; ?>
                    </div>
                <? endforeach; ?>
            </div>
        </div>
    <? endif; ?>
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