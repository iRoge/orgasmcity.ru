<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
?>


<div class="social-icons">

    <? if (!empty($arResult['FACEBOOK_LINK'])): ?>
        <a target="_blank" href="<?= $arResult['FACEBOOK_LINK']; ?>"><i class="icon icon-facebook"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['INSTAGRAM_LINK'])): ?>
        <a target="_blank" href="<?= $arResult['INSTAGRAM_LINK']; ?>"><i class="icon icon-instagram"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['VK_LINK'])): ?>
        <a target="_blank" href="<?= $arResult['VK_LINK']; ?>"><i class="icon icon-vk"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['TELEGRAM_LINK'])): ?>
        <a target="_blank" href="<?= $arResult['TELEGRAM_LINK']; ?>"><i class="icon icon-telegram tele-foot"></i></a>
    <? endif; ?>


</div>