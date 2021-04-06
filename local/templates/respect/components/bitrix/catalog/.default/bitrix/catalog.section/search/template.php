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

$arResult['ITEMS'] = array_chunk($arResult['ITEMS'], 4);
?>

<? if (!empty($arResult['ITEMS'])): ?>
    <div class="container container--no-padding">
        <div class="column-8 pre-1">
            <? foreach ($arResult['ITEMS'] as $arItems): ?>
                <div class="container js-products-slider">
                    <? foreach ($arItems as $arItem): ?>
                        <div class="column-25 column-md-1 column-xs-2">
                            <div class="products-item products-item--square">
                                <? include 'element.php'; ?>
                            </div>
                        </div>
                    <? endforeach; ?>
                </div>
            <? endforeach; ?>
        </div>
    </div>

    <?= $arResult['NAV_STRING']; ?>
<? else: ?>
    <div class="page-massage">
        <? ShowError('Товары не найдены'); ?>
    </div>
<? endif; ?>