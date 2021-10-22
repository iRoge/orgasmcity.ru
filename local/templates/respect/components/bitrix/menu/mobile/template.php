<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
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

<?php if (!empty($arResult)) { ?>
    <div class="blue-menu-div">
        <span class="cls-blue-menu" style="display: inline-block;"></span>
        <?php foreach ($arResult as $arItem) { ?>
            <?php
            $sClassLi = 'menu-ul-li';
            if ($arItem['PARAMS']['HIGHLIGHT'] == 'Y') {
                $sClassLi .= ' navigation-highlight';
            }

            if ($arItem['IS_PARENT']) {
                $sClassLi .= ' js-has-children';
            }

            if ($arItem["PARAMS"]["CLASS"]) {
                $sClassLi .= ' ' . $arItem["PARAMS"]["CLASS"];
            }

            $sClassA = 'menu-ul-li-a';

            if ($arItem['PARAMS']['BUTTON'] == 'Y') {
                $sClassA .= ' sale-a';
                $sClassLi .= ' sale';
            }

            $levelId = str_replace('/', '-', trim($arItem['LINK'], '/'));
            ?>
            <?php if ($arItem['IS_PARENT']) { ?>
                <div class="blue-menu-div-div">
                <span class="more-span">
                    <?= $arItem['TEXT'] ?>
                </span>
                    <div class="blue-menu-div-hidden">
                        <?php foreach ($arItem['ITEMS'] as $i => $arItem2Level) { ?>
                            <?php if ($arItem2Level['IS_PARENT']) { ?>
                                <a href="<?= $arItem2Level['LINK'] ?>">
                                    <span style="opacity: 0.7; padding-top: 10px">
                                        <?= $arItem2Level['TEXT'] ?>
                                    </span>
                                </a>
                                <?php //foreach (array_chunk($arItem2Level['ITEMS'], ceil(count($arItem2Level['ITEMS']) / 2)) as $arItem3LevelChunks): ?>
                                <?php foreach ($arItem2Level['ITEMS'] as $arItem3LevelChunks) { ?>
                                    <?php // foreach ($arItem3LevelChunks as $arItem3Level) { ?>
                                    <a class="blue-menu-div-element" href="<?= $arItem3LevelChunks['LINK'] ?>">
                                        <div class="mobile-menu-img-wrapper">
                                            <?php if (isset($arItem3LevelChunks["PARAMS"]["IMG_PATH"])) { ?>
                                                <img class="lazy-img-menu" style="max-width: 100%; height: 100%"
                                                     data-src="<?= $arItem3LevelChunks["PARAMS"]["IMG_PATH"] ?>"
                                                     alt="<?= $arItem3LevelChunks['TEXT'] ?>">
                                            <?php } ?>
                                        </div>
                                        <?= $arItem3LevelChunks['TEXT']; ?>
                                    </a>

                                    <?php // } ?>
                                <?php } ?>
                            <?php } else { ?>
                                <a class="blue-menu-div-element" href="<?= $arItem2Level['LINK'] ?>">
                                    <div class="mobile-menu-img-wrapper">
                                        <?php if (isset($arItem2Level["PARAMS"]["IMG_PATH"])) { ?>
                                            <img class="lazy-img-menu" style="max-width: 100%; height: 100%"
                                                 data-src="<?= $arItem2Level["PARAMS"]["IMG_PATH"] ?>"
                                                 alt="<?= $arItem2Level['TEXT'] ?>">
                                        <?php } ?>
                                    </div>
                                    <span><?= $arItem2Level['TEXT']; ?></span>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <a id="<?= $levelId ?>" class="blue-menu-div-div" href="<?= $arItem['LINK'] ?>">
                    <span>
                        <?= $arItem['TEXT'] ?>
                    </span>
                </a>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
