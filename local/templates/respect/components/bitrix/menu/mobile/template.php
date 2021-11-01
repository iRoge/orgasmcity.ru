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
        <span class="cls-blue-menu" style="display: flex;">
            <svg width="65%" height="65%" viewBox="0 0 22 22" fill="white" xmlns="http://www.w3.org/2000/svg">
                <line x1="1.93934" y1="20.4462" x2="20.4461" y2="1.93948" stroke="white" stroke-width="3"/>
                <line x1="2.06066" y1="1.93934" x2="20.5674" y2="20.4461" stroke="white" stroke-width="3"/>
            </svg>
        </span>
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
                    <span><?= $arItem['TEXT'] ?></span>
                    <svg class="more-arrow-down" width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20.6628 11.0189L10.7753 1.08008L0.887695 11.0189" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <svg class="more-arrow-up" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="body_1" width="23" height="12">
                        <defs>
                            <clipPath id="1">
                            <path id=""  clip-rule="evenodd" transform="matrix(1 0 0 1 0 0)"  d="M16.5 0L16.5 9L16.5 9L0 9L0 9L0 0L0 0L16.5 0z" />    </clipPath>
                        </defs>
                        <g transform="matrix(1.3333 0 0 1.3333 0 0)">
                            <g clip-path="url(#1)">
                            <path id="" transform="matrix(1 0 0 -1 0 9)"  d="M1.0029001 8.264175L1.0029001 8.264175L1.0029001 8.264175L8.418525 0.81006L8.418525 0.81006L15.8342285 8.264175" stroke="#000000" stroke-width="1" stroke-linecap="square" fill="none" />
                            </g>
                        </g>
                    </svg>
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
