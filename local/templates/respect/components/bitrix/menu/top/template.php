<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
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

<? if (!empty($arResult)) : ?>
<ul class="menu-ul">
    <? foreach ($arResult as $arItem) : ?>
        <?
        $sClassLi = 'menu-ul-li';
        if ($arItem['PARAMS']['HIGHLIGHT'] == 'Y') {
            $sClassLi .= ' navigation-highlight';
        }
        if ($arItem['IS_PARENT']) {
            $sClassLi .= ' js-has-children';
        }
        if ($arItem["PARAMS"]["CLASS"]) {
            $sClassLi .= ' '.$arItem["PARAMS"]["CLASS"];
        }
        $sClassA = 'menu-ul-li-a';
        if ($arItem['PARAMS']['BUTTON'] == 'Y') {
            $sClassA .= ' sale-a';
            $sClassLi .= ' sale';
        }
        $levelId = str_replace('/', '-', trim($arItem['LINK'], '/'));
        ?>
        <li class="<?= $sClassLi; ?>" id="<?=$levelId ?>">
            <a class="<?= $sClassA; ?> js-top-menu-item" href="<?= $arItem['LINK'] ?>"
            style="
            <? if ($arItem['PARAMS']['PROPS']['TEXT_COLOR']) : ?>
                color:<?=$arItem['PARAMS']['PROPS']['TEXT_COLOR']?>;
            <?endif;?>"><?= $arItem['TEXT'] ?></a>
        </li>
        <? if ($arItem['IS_PARENT']) : ?>
            <div class="hide-menu" id="div-<?=$levelId ?>">
                <div class="hide-fake-area">
                    <div class="main">
                        <? foreach (array_chunk($arItem['ITEMS'], 4) as $chunk) : ?>
                            <?
                                $class = array(3, 12);
//                            if (count($arItem2Level['ITEMS']) > $arItem['MAX']) {
//                                $class = array(4, 6);
//                            } elseif ($arItem2Level["MAX_L"] <= 15) {
//                                $class = array(2, 12);
//                            }
                            ?>
                            <div class="row-menu-wrapper">
                                <? foreach ($chunk as $i => $arItem2Level) : ?>
                                    <div class="col-md-<?=$class[0]?> left-hide-menu">
                                        <? if ($arItem2Level['LINK']) : ?>
                                            <div class="col-md-12 zagolovok-hide-menu">
                                                <p><a href="<?=$arItem2Level['LINK']?>"><?= $arItem2Level['TEXT']; ?></a></p>
                                            </div>
                                        <? else : ?>
                                            <div class="col-md-12 zagolovok-hide-menu">
                                                <p><?= $arItem2Level['TEXT']; ?></p>
                                            </div>
                                        <? endif; ?>
                                        <? if ($arItem2Level['IS_PARENT']) : ?>
                                            <? foreach (array_chunk($arItem2Level['ITEMS'], 25) as $arItem3LevelChunks) : ?>
                                                <div class="col-md-<?=$class[1]?> lvl3-items-block">
                                                <? foreach ($arItem3LevelChunks as $arItem3Level) : ?>
                                                    <a href="<?= $arItem3Level['LINK']; ?>" <?= ($arItem3Level["PARAMS"]["CLASS"] ? ' class="'.$arItem3Level["PARAMS"]["CLASS"].'"' : '') ?>>
                                                        <span><?= $arItem3Level['TEXT'] ?></span>
                                                    </a>
                                                <? endforeach; ?>
                                                </div>
                                            <? endforeach; ?>
                                        <? endif; ?>
                                    </div>
                                <? endforeach; ?>
                            </div>
                        <? endforeach; ?>
                        <div style="clear: both"></div>
                    </div>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</ul>
<? endif; ?>