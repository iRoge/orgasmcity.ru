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

<div class="social-block phone--hidden in-view">
    <div class="container">
        <? if (!empty($arResult['FACEBOOK_LINK'])): ?>
            <div class="column-15 pre-2">
                <a target="_blank" href="<?= $arResult['FACEBOOK_LINK']; ?>"><i class="icon icon-facebook"></i><span>Facebook</span></a>
            </div>
        <? endif; ?>
        <? if (!empty($arResult['VK_LINK'])): ?>
            <div class="column-15">
                <a target="_blank" href="<?= $arResult['VK_LINK']; ?>"><i class="icon icon-vk"></i><span>Vkontakte</span></a>
            </div>
        <? endif; ?>

        <? if (!empty($arResult['INSTAGRAM_LINK'])): ?>
            <div class="column-15">
                <a target="_blank" href="<?= $arResult['INSTAGRAM_LINK']; ?>"><i class="icon icon-instagram"></i><span>Instagram</span></a>
            </div>
        <? endif; ?>
        <? if (!empty($arResult['TELEGRAM_LINK'])): ?>
            <div class="column-15">
                <a target="_blank" href="<?= $arResult['TELEGRAM_LINK']; ?>"><i class="icon icon-telegram line"></i><span>Telegram</span></a>
            </div>
        <? endif; ?>
        <? if (!empty($arResult['FACEBOOK_LINK'])): ?>
            <div class="column-15">
                <a target="_blank" href="<?= $arResult['FACEBOOK_LINK']; ?>"><i class="icon icon-telegram line"></i><span>Telegram</span></a>
            </div>
        <? endif; ?>
        <? if (!empty($arResult['TWITTER_LINK'])): ?>
            <div class="column-15">
                <a target="_blank" href="<?= $arResult['TWITTER_LINK']; ?>"><i class="icon icon-telegram line"></i><span>Telegram</span></a>
            </div>
        <? endif; ?>
    </div>
</div>
