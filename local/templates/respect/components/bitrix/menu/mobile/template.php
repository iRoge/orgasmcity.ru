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
<div class="blue-menu-div">
    <span class="cls-blue-menu" style="display: inline-block;"></span>
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
            $sClassLi .= ' ' . $arItem["PARAMS"]["CLASS"];
        }

        $sClassA = 'menu-ul-li-a';

        if ($arItem['PARAMS']['BUTTON'] == 'Y') {
            $sClassA .= ' sale-a';
            $sClassLi .= ' sale';
        }

        $levelId = str_replace('/', '-', trim($arItem['LINK'], '/'));
        ?>
        <? if ($arItem['IS_PARENT']) : ?>
            <div class="blue-menu-div-div">
                <a href="<?= $arItem['LINK'] ?>" class="more-span"><span
                style="
                <? if ($arItem['PARAMS']['PROPS']['UF_TEXT_COLOR']['PROPERTY_COLOR_VALUE']) :?>
                       color:#<?=$arItem['PARAMS']['PROPS']['UF_TEXT_COLOR']['PROPERTY_COLOR_VALUE']?>;
                <? endif;?>
                <? if ($arItem['PARAMS']['PROPS']['UF_BG_COLOR']['PROPERTY_COLOR_VALUE']) : ?>
                       background-color:#<?=$arItem['PARAMS']['PROPS']['UF_BG_COLOR']['PROPERTY_COLOR_VALUE']?>;
                <? endif;?>"><?= $arItem['TEXT'] ?></span></a>
                <ul>
                    <? foreach ($arItem['ITEMS'] as $i => $arItem2Level) : ?>
                        <? if ($arItem2Level['IS_PARENT']) : ?>
                            <?//pre($arItem2Level['ITEMS']);?>
                            <? //foreach (array_chunk($arItem2Level['ITEMS'], ceil(count($arItem2Level['ITEMS']) / 2)) as $arItem3LevelChunks): ?>
                            <? foreach ($arItem2Level['ITEMS'] as $arItem3LevelChunks) : ?>
                                <?// foreach ($arItem3LevelChunks as $arItem3Level): ?>
                                <?
                                $imageCode=basename($arItem3LevelChunks['LINK']);

                                $imagePath = $_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/img/'.$imageCode.'.png';
                                if (empty($imageCode) || !file_exists($imagePath)) {
                                    $imageCode = 'botinki';
                                }
                                ?>
                                                <li>
                                <a href="<?= $arItem3LevelChunks['LINK'] ?>">
                                  <?= $arItem3LevelChunks['TEXT']; ?>
                                  <img src="<?= SITE_TEMPLATE_PATH; ?>/img/<?= $imageCode; ?>.png"/>
                                </a>
                                </li>
                                <?// endforeach; ?>
                            <? endforeach; ?>
                        <?else :?>
                            <a href="<?= $arItem2Level['LINK'] ?>">
                                <li><?= $arItem2Level['TEXT']; ?></li>
                            </a>
                        <? endif; ?>
                    <? endforeach; ?>
                </ul>
            </div>
        <?else :?>
            <div class="blue-menu-div-div" id="<?= $levelId ?>">
                <a href="<?= $arItem['LINK'] ?>"><span><?= $arItem['TEXT'] ?></span></a>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</div>
<? endif; ?>
