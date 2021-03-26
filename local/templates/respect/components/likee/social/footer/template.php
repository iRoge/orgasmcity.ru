<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeSocialComponent $component */
$this->setFrameMode(true);
$icons = array(
    array("FACEBOOK_LINK", "fb.png"),
    array("INSTAGRAM_LINK", "insta.png"),
    array("VK_LINK", "vk.png"),
    array("TELEGRAM_LINK", "telegram.png"),
    array("PINTEREST_LINK", "pinterest.png"),
    array("YOUTUBE_LINK", "youtube.png"),
);
$i = 0;?>
<div class="mobile_soc_icon">
<? foreach ($icons as $value) : ?>
    <? if (!empty($arResult[$value[0]])) : ?>
        <? if ($i % 3 == 0 && $i != 0) : ?>
            </div>
            <div class="mobile_soc_icon">
        <? endif ?>
        <a target="_blank" href="<?= $arResult[$value[0]]; ?>"><img src="<?= SITE_TEMPLATE_PATH; ?>/img/<?= $value[1] ?>" /></a>
        <? $i++; ?>
    <? endif; ?>
<? endforeach; ?>
</div>
