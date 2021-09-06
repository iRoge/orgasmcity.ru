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

<? if (!empty($arResult)) { ?>
<ul class="menu-ul">
    <? foreach ($arResult as $arItem) { ?>
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
        if ($arItem['PARAMS']['PROPS']['IS_SPECIAL'] !== 'Y') {
        ?>
        <li class="<?= $sClassLi; ?>" id="<?=$levelId ?>">
            <a class="<?= $sClassA; ?> js-top-menu-item" href="<?= $arItem['LINK'] ?>"
                <?=isset($arItem['PARAMS']['PROPS']['TEXT_COLOR']) ? 'style="color: ' . $arItem['PARAMS']['PROPS']['TEXT_COLOR'] . '"' : ''?>
            ><?= $arItem['TEXT'] ?>
                <div class="menu-arrow"></div>
            </a>

        </li>
        <?php } else { ?>
        <li class="<?=$sClassLi;?> special-menu-li" id="<?=$levelId?>">
            <a class="<?=$sClassA;?> js-top-menu-item" href="<?=$arItem['LINK']?>"
               <?=isset($arItem['PARAMS']['PROPS']['TEXT_COLOR']) ? 'style="color: ' . $arItem['PARAMS']['PROPS']['TEXT_COLOR'] . '"' : ''?>
            >
                <div class="menu-text-wrapper">
                    <svg style="width: 16px; height: 21px" width="16" height="21" viewBox="0 0 16 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.21906 21C5.09721 21.0001 4.97681 20.9745 4.86633 20.9251C4.75584 20.8756 4.65796 20.8034 4.57954 20.7137C4.50113 20.6239 4.44409 20.5187 4.41244 20.4054C4.38079 20.2922 4.37529 20.1736 4.39633 20.0581V20.0533L5.67407 13.3005H0.727202C0.589892 13.3005 0.455391 13.2631 0.339256 13.1926C0.223122 13.1221 0.130099 13.0214 0.0709464 12.9021C0.011794 12.7828 -0.0110708 12.6499 0.00499692 12.5187C0.0210646 12.3874 0.0754082 12.2632 0.161742 12.1605L10.1241 0.307659C10.2375 0.169122 10.3949 0.0701442 10.5723 0.02586C10.7497 -0.0184242 10.9372 -0.00557698 11.1063 0.0624372C11.2754 0.130451 11.4167 0.249889 11.5085 0.402485C11.6004 0.555081 11.6378 0.732437 11.615 0.90743C11.615 0.920555 11.6114 0.933241 11.6091 0.946365L10.3268 7.70091H15.2728C15.4101 7.70092 15.5446 7.73834 15.6607 7.80885C15.7769 7.87936 15.8699 7.98006 15.9291 8.09932C15.9882 8.21858 16.0111 8.35152 15.995 8.48277C15.9789 8.61401 15.9246 8.73819 15.8383 8.84095L5.87453 20.6938C5.79624 20.789 5.69664 20.866 5.58315 20.919C5.46967 20.972 5.34521 20.9997 5.21906 21Z" fill="white"/>
                    </svg>
                    <span style="padding-left: 10px"><?=$arItem['TEXT']?></span>
                </div>
                <div class="menu-arrow"></div>
            </a>
        </li>
        <?php }
            if ($arItem['IS_PARENT']) { ?>
            <div class="hide-menu" id="div-<?=$levelId ?>">
                <div class="hide-fake-area">
                    <div class="main">
                        <? foreach (array_chunk($arItem['ITEMS'], 4) as $chunk) { ?>
                            <?
                                $class = array(3, 12);
//                            if (count($arItem2Level['ITEMS']) > $arItem['MAX']) {
//                                $class = array(4, 6);
//                            } elseif ($arItem2Level["MAX_L"] <= 15) {
//                                $class = array(2, 12);
//                            }
                            ?>
                            <div class="row-menu-wrapper">
                                <? foreach ($chunk as $i => $arItem2Level) { ?>
                                    <div class="col-md-<?=$class[0]?> left-hide-menu">
                                        <? if ($arItem2Level['LINK']) { ?>
                                            <div class="col-md-12 zagolovok-hide-menu">
                                                <p>
                                                    <a href="<?=$arItem2Level['LINK']?>">
                                                        <span class="hide-menu-icons" style="background-image:url(<?=$arItem2Level["PARAMS"]["IMG_PATH"]?>)"></span>
                                                        <?= $arItem2Level['TEXT']; ?>
                                                    </a>
                                                </p>
                                            </div>
                                        <? } else { ?>
                                            <div class="col-md-12 zagolovok-hide-menu">
                                                <span class="hide-menu-icons" style="background-image:url(<?=$arItem2Level["PARAMS"]["IMG_PATH"]?>)"></span>
                                                <p><?= $arItem2Level['TEXT']; ?></p>
                                            </div>
                                        <? } ?>
                                        <? if ($arItem2Level['IS_PARENT']) { ?>
                                            <? foreach (array_chunk($arItem2Level['ITEMS'], 25) as $arItem3LevelChunks) { ?>
                                                <div class="col-md-<?=$class[1]?> lvl3-items-block">
                                                <? foreach ($arItem3LevelChunks as $arItem3Level) { ?>
                                                    <a href="<?= $arItem3Level['LINK']; ?>" class="<?=($arItem3Level["PARAMS"]["CLASS"] ? ' '.$arItem3Level["PARAMS"]["CLASS"] : '') ?>">
                                                        <span class="hide-menu-icons" style="background-image:url(<?=$arItem3Level["PARAMS"]["IMG_PATH"]?>)"></span>
                                                        <span><?= $arItem3Level['TEXT'] ?></span>
                                                    </a>
                                                <? } ?>
                                                </div>
                                            <? } ?>
                                        <? } ?>
                                    </div>
                                <? } ?>
                            </div>
                        <? } ?>
                        <div style="clear: both"></div>
                    </div>
                </div>
            </div>
        <? } ?>
    <? } ?>
</ul>
<? } ?>