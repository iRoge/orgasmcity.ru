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
if (!empty($arResult)) {?>
    <div class="mobile_menu_top">
    <div class="sex-block col-xs-12">
        <? $cou=0;
        foreach ($arResult as $arItem) { ?>
            <?$cou++;
            if ($arItem['TEXT'] == '?????') {?>
                <div class="sex-btn sex-btn-left col-xs-12" >
                    <span class="sex-span" data-name="<?= $arItem['TEXT'] ?>"><?= $arItem['TEXT'] ?></span>
                </div>
            <?} elseif ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1) {?>
            <div class="sex-btn <?=($cou % 2 == 1)? "sex-btn-left":""?> col-xs-6" >
                <span class="sex-span" data-name="<?= $arItem['TEXT'] ?>"><?= $arItem['TEXT'] ?></span>
            </div>
            <?}?>
        <? } ?>
    </div>
    <? foreach ($arResult as $arItem) { ?>
        <?if ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1) {?>
            <div class="sex-list col-sm-12" data-name="<?= $arItem['TEXT'] ?>">
                <div class="topmenu">
                    <?if ($arItem['IS_PARENT']) {?>
                        <?if ($arItem['DEPTH_LEVEL'] != 1) { ?>
                            <div>
                                <span class="submenu-item submenu-item-main arrow arrow-down"><?=$arItem['TEXT']?></span>
                            </div>
                            <div class="sub-submenu">
                        <?}?>
                            <? foreach ($arItem['ITEMS'] as $i => $arItem2Level) { ?>
                                <? if ($arItem2Level['IS_PARENT']) {  ?>
                                <div class="second-level-menu-wrapper">
                                    <span class="submenu-level2-item submenu-item-main arrow arrow-down">
                                        <?=$arItem2Level['TEXT']?>
                                    </span>
                                    <div class="sub-submenu">
                                        <div style="margin-bottom: 30px">
                                            <a href="<?=$arItem2Level['LINK']?>">
                                                <?=GetMessage("SHOW_ALL_ITEMS")?>
                                            </a>
                                         </div>
                                        <div class="sub-submenu-wrapper">
                                            <? foreach ($arItem2Level['ITEMS'] as $arItem3Level) { ?>
                                                <? if ($arItem3Level['IS_PARENT']) { ?>
                                                <div>
                                                    <span class="submenu-item arrow arrow-down"> <?=$arItem3Level['TEXT']?></span>
                                                    <div class="sub-submenu">
                                                        <div class="submenu-wrapper">
                                                           <a href="<?=$arItem3Level['LINK']?>">
                                                               <?=GetMessage("SHOW_ALL_ITEMS")?>
                                                            </a>
                                                        </div>
                                                        <? foreach ($arItem3Level['ITEMS'] as $arItem4LevelChunks) { ?>
                                                        <div class="submenu-wrapper">
                                                            <a href="<?=$arItem4LevelChunks['LINK']?>">
                                                                <div class="submenu-img-wrapper">
                                                                    <img src="<?=$arItem4LevelChunks['PARAMS']['IMG_PATH']?>" alt="<?=$arItem4LevelChunks['TEXT']?>">
                                                                </div>
                                                                <span class="submenu-item submenu-item-main">
                                                                    <?=$arItem4LevelChunks['TEXT']?>
                                                                </span>
                                                            </a>
                                                        </div>
                                                        <? } ?>
                                                    </div>
                                                </div>
                                                <? } else { ?>
                                                <div class="submenu-wrapper">
                                                    <a href="<?=$arItem3Level['LINK']?>">
                                                        <div class="submenu-img-wrapper">
                                                            <img src="<?=$arItem3Level['PARAMS']['IMG_PATH']?>" alt="<?=$arItem3Level['TEXT']?>">
                                                        </div>
                                                        <span class="submenu-item submenu-item-main">
                                                            <?=$arItem3Level['TEXT']?>
                                                        </span>
                                                    </a>
                                                </div>
                                                <? } ?>
                                            <? } ?>
                                        </div>
                                    </div>
                                </div>
                                <? } else { ?>
                                        <div class="submenu-wrapper">
                                            <a href="<?=$arItem2Level['LINK']?>">
                                                <div class="submenu-img-wrapper">
                                                    <img src="<?=$arItem2Level['PARAMS']['IMG_PATH']?>" alt="<?=$arItem2Level['TEXT']?>">
                                                </div>
                                                <span class="submenu-item submenu-item-main arrow">
                                                    <?=$arItem2Level['TEXT']?>
                                                </span>
                                            </a>
                                        </div>
                                <? } ?>
                            <? } ?>
                        <?if ($arItem['DEPTH_LEVEL']!= 1) { ?>
                            </div>
                        <?}?>
                    <? } else {?>
                        <? if ($arItem['DEPTH_LEVEL']!= 1) { ?>
                        <div class="submenu-wrapper">
                            <a href="<?=$arItem['LINK']?>">
                                <span class="submenu-item submenu-item-main arrow arrow-down">
                                    <?=$arItem['TEXT']?>
                                </span>
                            </a>
                        </div>
                        <? }?>
                    <? }?>
                </div>
            </div>
        <?}?>
    <? } ?>
</div>    
<? } ?>


