<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if (!empty($arResult['MESSAGE'])) : ?>
    <p><?= $arResult['MESSAGE']; ?></p>
<?else :?>
    <p>Вы можете подписаться на рассылку заполнив форму подписки внизу сайта.</p>
<? endif; ?>
