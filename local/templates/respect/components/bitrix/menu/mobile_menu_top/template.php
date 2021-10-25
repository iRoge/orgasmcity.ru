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
IncludeTemplateLangFile(__FILE__);
if (!empty($arResult)) { ?>
    <div class="mobile_menu_top">
        <div class="sex-block col-xs-12">
            <? foreach ($arResult as $arItem) { ?>
                <? if ($arItem['TEXT'] == '?????') { ?>
                    <div class="sex-btn col-xs-12">
                        <? if (isset($arItem['PARAMS']['IMG_PATH'])) { ?>
                            <img width="20" height="20" src="<?= $arItem['PARAMS']['IMG_PATH'] ?>"
                                 alt="<?= $arItem['TEXT'] ?>">
                        <? } ?>
                        <span
                                class="sex-span"
                                data-name="<?= $arItem['TEXT'] ?>"
                                onclick="$('.sex-list[data-name=\'<?= $arItem['TEXT'] ?>\']').find('img.lazy-img-menu').lazyLoadXT({forceLoad: true, visibleOnly: false, throttle: 0})"
                        >
                        <?= $arItem['TEXT'] ?>
                    </span>
                    </div>
                <? } elseif ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1) { ?>
                    <div class="sex-btn">
                        <? if (isset($arItem['PARAMS']['IMG_PATH'])) { ?>
                            <div class="mob-catalog-img-wrapper">
                                <img width="20" height="20" src="<?= $arItem['PARAMS']['IMG_PATH'] ?>"
                                     alt="<?= $arItem['TEXT'] ?>">
                            </div>
                        <? } ?>
                        <span
                                class="sex-span"
                                data-name="<?= $arItem['TEXT'] ?>"
                                onclick="$('.sex-list[data-name=\'<?= $arItem['TEXT'] ?>\']').find('img.lazy-img-menu').lazyLoadXT({forceLoad: 1, visibleOnly: 0, throttle: 0})"
                        >
                            <?= $arItem['TEXT'] ?>
                        </span>
                    </div>
                <? } ?>
            <? } ?>
        </div>
        <? foreach ($arResult as $arItem) { ?>
            <? if ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1) { ?>
                <div class="sex-list col-sm-12" data-name="<?= $arItem['TEXT'] ?>">
                    <div class="topmenu">
                        <? if ($arItem['IS_PARENT']) { ?>
                            <? if ($arItem['DEPTH_LEVEL'] != 1) { ?>
                                <div>
                                    <span class="submenu-item submenu-item-main arrow-down-hidden">
                                        <span><?= $arItem['TEXT'] ?></span>
                                        <svg class="submenu-arrow-down" width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M20.6628 11.0189L10.7753 1.08008L0.887695 11.0189" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <svg class="submenu-arrow-up" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="body_1" width="23" height="12">
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
                                </div>
                                <div class="sub-submenu">
                            <? } ?>
                            <? foreach ($arItem['ITEMS'] as $i => $arItem2Level) { ?>
                                <? if ($arItem2Level['IS_PARENT']) { ?>
                                    <div class="second-level-menu-wrapper">
                                        <span
                                                class="submenu-level2-item submenu-item-main"
                                                onclick="$(this).parent().find('img.lazy-img-menu').lazyLoadXT({forceLoad: 1, visibleOnly: 0, throttle: 0})"
                                        >
                                            <span><?=$arItem2Level['TEXT']?></span>
                                            <svg class="submenu-arrow-down" width="22" height="12" viewBox="0 0 22 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.6628 11.0189L10.7753 1.08008L0.887695 11.0189" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                            <svg class="submenu-arrow-up" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="body_1" width="23" height="12">
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
                                        <div class="sub-submenu">
                                            <div style="margin-bottom: 10px">
                                                <a href="<?= $arItem2Level['LINK'] ?>">
                                                    <?= GetMessage("SHOW_ALL_ITEMS") ?>
                                                </a>
                                            </div>
                                            <div class="sub-submenu-wrapper">
                                                <? foreach ($arItem2Level['ITEMS'] as $arItem3Level) { ?>
                                                    <? if ($arItem3Level['IS_PARENT']) { ?>
                                                        <span class="submenu-item arrow arrow-down"> <?= $arItem3Level['TEXT'] ?></span>
                                                        <div class="sub-submenu">
                                                            <a class="submenu-wrapper" href="<?= $arItem3Level['LINK'] ?>">
                                                                <?= GetMessage("SHOW_ALL_ITEMS") ?>
                                                            </a>
                                                            <? foreach ($arItem3Level['ITEMS'] as $arItem4LevelChunks) { ?>

                                                                    <a class="submenu-wrapper" href="<?= $arItem4LevelChunks['LINK'] ?>">
                                                                        <div class="submenu-img-wrapper">
                                                                            <img class="lazy-img-menu"
                                                                                 data-src="<?= $arItem4LevelChunks['PARAMS']['IMG_PATH_WEBP'] ?>"
                                                                                 alt="<?= $arItem4LevelChunks['TEXT'] ?>">
                                                                        </div>
                                                                        <span class="submenu-item submenu-item-main">
                                                                            <?= $arItem4LevelChunks['TEXT'] ?>
                                                                        </span>
                                                                    </a>

                                                            <? } ?>
                                                        </div>
                                                    <? } else { ?>

                                                            <a class="submenu-wrapper" href="<?= $arItem3Level['LINK'] ?>">
                                                                <div class="submenu-img-wrapper">
                                                                    <img class="lazy-img-menu"
                                                                         data-src="<?= $arItem3Level['PARAMS']['IMG_PATH_WEBP'] ?>"
                                                                         alt="<?= $arItem3Level['TEXT'] ?>">
                                                                </div>
                                                                <span class="submenu-item submenu-item-main">
                                                                    <?= $arItem3Level['TEXT'] ?>
                                                                </span>
                                                            </a>
                                                    <? } ?>
                                                <? } ?>
                                            </div>
                                        </div>
                                    </div>
                                <? } else { ?>
                                    <a class="submenu-wrapper" href="<?= $arItem2Level['LINK'] ?>">
                                        <div class="submenu-img-wrapper">
                                            <img class="lazy-img-menu"
                                                 data-src="<?= $arItem2Level['PARAMS']['IMG_PATH_WEBP'] ?>"
                                                 alt="<?= $arItem2Level['TEXT'] ?>">
                                        </div>
                                        <span class="submenu-item submenu-item-main arrow">
                                                <?= $arItem2Level['TEXT'] ?>
                                        </span>
                                    </a>
                                <? } ?>
                            <? } ?>
                            <? if ($arItem['DEPTH_LEVEL'] != 1) { ?>
                                </div>
                            <? } ?>
                        <? } else { ?>
                            <? if ($arItem['DEPTH_LEVEL'] != 1) { ?>
                                <a class="submenu-wrapper" href="<?= $arItem['LINK'] ?>">
                                    <span class="submenu-item submenu-item-main arrow arrow-down">
                                        <?= $arItem['TEXT'] ?>
                                    </span>
                                </a>
                            <? } ?>
                        <? } ?>
                    </div>
                </div>
            <? } ?>
        <? } ?>
    </div>
<? } ?>


