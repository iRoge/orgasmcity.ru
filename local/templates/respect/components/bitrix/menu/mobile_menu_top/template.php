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
if (!empty($arResult)) :?>
<div class="mobile_menu_top">
<div class="sex-block col-xs-12">
    <? $cou=0;
    foreach ($arResult as $arItem) : ?>
        <?$cou++;
        if ($arItem['TEXT'] == 'Одежда') {?>
            <div class="sex-btn sex-btn-left col-xs-12" >
                <span class="sex-span" data-name="<?= $arItem['TEXT'] ?>"><?= $arItem['TEXT'] ?></span>
            </div>
        <?} elseif ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1 && !isset($item['PARAMS']['PROPS'])) {?>
        <div class="sex-btn <?=($cou % 2 == 1)? "sex-btn-left":""?> col-xs-6" >
            <span class="sex-span" data-name="<?= $arItem['TEXT'] ?>"><?= $arItem['TEXT'] ?></span>
        </div>
        <?}?>
    <? endforeach; ?>
</div>

    <? foreach ($arResult as $arItem) : ?>
        <?if ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1 && !isset($item['PARAMS']['PROPS'])) {?>
    <div class="sex-list col-sm-12" data-name="<?= $arItem['TEXT'] ?>">
        <ul class="topmenu">
    
            <?if ($arItem['IS_PARENT']) {?>
                <?if ($arItem['DEPTH_LEVEL']!= 1) { ?>
                <li><span class="submenu-item submenu-item-main arrow arrow-down"><?= $arItem['TEXT'] ?></span></li>
                    <ul class="sub-submenu">
                <?}?>
                    <? foreach ($arItem['ITEMS'] as $i => $arItem2Level) : ?>
                        <? if ($arItem2Level['IS_PARENT']) : ?>
                        <li><span class="submenu-item submenu-item-main arrow arrow-down"
                            style="
                            <? if ($arItem2Level['PARAMS']['PROPS']['UF_TEXT_COLOR']['PROPERTY_COLOR_VALUE']) : ?>
                                color:#<?=$arItem2Level['PARAMS']['PROPS']['UF_TEXT_COLOR']['PROPERTY_COLOR_VALUE']?>;
                            <?endif;?>
                            <? if ($arItem2Level['PARAMS']['PROPS']['UF_BG_COLOR']['PROPERTY_COLOR_VALUE']) : ?>
                                background-color:#<?=$arItem2Level['PARAMS']['PROPS']['UF_BG_COLOR']['PROPERTY_COLOR_VALUE']?>;
                            <?endif;?>"
                            ><?= $arItem2Level['TEXT'] ?></span>
                            <ul class="sub-submenu">
                                <li>
                                    <a href="<?= $arItem2Level['LINK'] ?>">
                                        <?=GetMessage("SHOW_ALL_ITEMS")?>
                                    </a>
                                 </li>
                                <? foreach ($arItem2Level['ITEMS'] as $arItem3Level) : ?>
                                    <?
                                    
                                    if ($arItem3Level['IS_PARENT']) : ?>
                                    <li><span class="submenu-item arrow arrow-down"> <?= $arItem3Level['TEXT'] ?></span>
                                        <ul class="sub-submenu">
                                            <li>
                                               <a href="<?= $arItem3Level['LINK'] ?>">
                                                   <?=GetMessage("SHOW_ALL_ITEMS")?>
                                                </a>
                                            </li>
                                            <? foreach ($arItem3Level['ITEMS'] as $arItem4LevelChunks) : ?>
                                                <?
                                                $imageCode=basename($arItem4LevelChunks['LINK']);

                                                $imagePath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/img/'.$imageCode.'.png';
                                                if (empty($imageCode) || !file_exists($imagePath)) {
                                                    $imageCode = 'botinki';
                                                }
                                                ?>
                                            <li>
                                                <a href="<?= $arItem4LevelChunks['LINK'] ?>">
                                                    <?= $arItem4LevelChunks['TEXT'] ?>
                                                    <img src="<?= SITE_TEMPLATE_PATH ?>/img/<?= $imageCode ?>.png"/>
                                                </a>
                                            </li>
                                            <? endforeach; ?>
                                        </ul> 
                                    </li>   
                                    <?else :?>
                                        <?$imageCode=basename($arItem3Level['LINK']);
                                        $imagePath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/img/'.$imageCode.'.png';
                                        if (empty($imageCode) || !file_exists($imagePath)) {
                                            $imageCode = 'botinki';
                                        }
                                        ?>
                                    <li>
                                        <a href="<?= $arItem3Level['LINK'] ?>">
                                            <?= $arItem3Level['TEXT'] ?>
                                            <img src="<?= SITE_TEMPLATE_PATH ?>/img/<?= $imageCode ?>.png"/>
                                        </a>
                                    </li>
                                    <? endif; ?>
                                <?endforeach; ?>
                            </ul> 
                        </li>   
                        <?else :?>
                                <li class="aaa"><a href="<?= $arItem2Level['LINK'] ?>"><span class="submenu-item submenu-item-main arrow arrow-down"><?= $arItem2Level['TEXT'] ?></span></a></li>
                            
                        <? endif; ?>
                    <? endforeach; ?>
                <?if ($arItem['DEPTH_LEVEL']!= 1) { ?>
                    </ul>
                <?}?>
            <?} else {?>
                <?if ($arItem['DEPTH_LEVEL']!= 1) { ?>
                <li class="aaa"><a href="<?= $arItem['LINK'] ?>"><span class="submenu-item submenu-item-main arrow arrow-down"><?= $arItem['TEXT'] ?></span></a></li>
                <?}?>
            <?}?>
    
        </ul>
    </div>
        <?}?>
    <? endforeach; ?>
</div>    
<? endif; ?>


