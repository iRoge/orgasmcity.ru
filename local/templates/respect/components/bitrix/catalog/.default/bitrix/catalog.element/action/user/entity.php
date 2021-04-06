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
?>

<? if (!empty($arResult['COLORS'])): ?>
    <div class="product-module">
        <div class="product-types-slider">
            <ul class="js-slider">
                <? foreach ($arResult['COLORS'] as $arColor): ?>
                    <li>
                        <a href="<?= $arColor['DETAIL_PAGE_URL']; ?>">
                            <img src="<?= $arColor['FILE']; ?>" alt="<?= $arColor['NAME']; ?>">
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
    </div>
<? endif; ?>

<? /*
<div class="container">
    <div class="column-10">
        <div class="product-module">
            <div class="product-module__content">
                <small class="text--muted"><u>Подитог: <span class="total"></span> с НДС</u></small>
            </div>
        </div>
    </div>
</div>
*/ ?>

<? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
    <div class="container">
        <div class="column-10"></div>
        <a data-target="#description-tooltip-content" class="button button--outline button--l button--block js-tooltip tooltipstered">Описание</a>
        <div id="description-tooltip-content" class="description-tooltip">
            <dl class="dl--inline">
                <? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty): ?>
                    <? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])): ?>
                        <dt><?= $arProperty['NAME']; ?></dt>
                        <dd><?= $arProperty['VALUE']; ?></dd>
                    <? endif; ?>
                <? endforeach; ?>
            </dl>
        </div>
    </div>
<? endif; ?>