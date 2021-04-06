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
    <div class="search-filter">
        <div class="container">
            <div class="column-8 pre-1">
                <nav class="navigation">
                    <button class="navigation-toggler phone--only"></button>
                    <ul class="navigation-row">
                        <li class="navigation-highlight"><a>Фильтр по категориям</a></li>
                        <? foreach ($arResult as $arItem): ?>
                            <? if (!$arItem['IS_PARENT']) continue; //показываем только разделы каталога ?>
                            <li>
                                <a href="<?= $arItem['LINK'] ?>"><?= $arItem['TEXT'] ?></a>

                                <? if ($arItem['IS_PARENT']): ?>
                                    <nav class="navigation-submenu">
                                        <div class="nav__container">
                                            <ul class="navigation-submenu__list">
                                                <? foreach ($arItem['ITEMS'] as $arItem2Level): ?>
                                                    <li<? if (count($arItem2Level['ITEMS']) > 8): ?> class="multicolumn"<? endif; ?>>
                                                        <nav class="navigation-column">
                                                            <h4><?= $arItem2Level['TEXT']; ?></h4>
                                                            <? if ($arItem2Level['IS_PARENT']): ?>
                                                                <ul>
                                                                    <? foreach ($arItem2Level['ITEMS'] as $arItem3Level): ?>
                                                                        <li>
                                                                            <a href="<?= $arItem3Level['LINK']; ?>"><?= $arItem3Level['TEXT']; ?></a>
                                                                        </li>
                                                                    <? endforeach; ?>
                                                                </ul>
                                                            <? endif; ?>
                                                        </nav>
                                                    </li>
                                                <? endforeach; ?>
                                            </ul>
                                        </div>
                                    </nav>
                                <? endif; ?>
                            </li>
                        <? endforeach; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
<? endif; ?>