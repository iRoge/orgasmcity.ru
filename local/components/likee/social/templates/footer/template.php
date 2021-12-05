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
        <a title="Facebook" target="_blank" href="<?= $arResult['FACEBOOK_LINK']; ?>"><i class="icon icon-facebook"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['INSTAGRAM_LINK'])): ?>
        <a title="Инстаграм" target="_blank" href="<?= $arResult['INSTAGRAM_LINK']; ?>"><i class="icon icon-instagram"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['VK_LINK'])): ?>
        <a title="Вконтакте" target="_blank" href="<?= $arResult['VK_LINK']; ?>"><i class="icon icon-vk"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['TELEGRAM_LINK'])): ?>
        <a title="Telegram" target="_blank" href="<?= $arResult['TELEGRAM_LINK']; ?>"><i class="icon icon-telegram tele-foot"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['FACEBOOK_LINK'])): ?>
        <a title="Telegram" target="_blank" href="<?= $arResult['FACEBOOK_LINK']; ?>"><i class="icon icon-telegram tele-foot"></i></a>
    <? endif; ?>

    <? if (!empty($arResult['TWITTER_LINK'])): ?>
        <a title="Telegram" target="_blank" href="<?= $arResult['TWITTER_LINK']; ?>"><i class="icon icon-telegram tele-foot"></i></a>
    <? endif; ?>
</div>