<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

<? if (!empty($arResult)): ?>
    <nav class="nav nav--horizontal">
        <div class="container">
            <div class="column-8 column-center">
                <ul>
                    <? foreach ($arResult as $arItem): ?>
                        <li <? if ($arItem['SELECTED']): ?>class="active"<? endif; ?>>
                            <a href="<?= $arItem['LINK'] ?>"><?= $arItem['TEXT'] ?></a>
                        </li>
                    <? endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
<? endif; ?>