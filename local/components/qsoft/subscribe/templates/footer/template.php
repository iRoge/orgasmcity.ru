<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<div class="js-subscribe-new">
    <? if (!empty($arResult['MESSAGE'])) : ?>
        <div class="subscribe-message">
            <i class="icon icon-check"></i>
            <b><?= $arResult['MESSAGE']; ?></b>
        </div>
    <? else : ?>
    <div class="subscribe-wrapper">
        <div class="main">
            <form class="col-lg-3" id="subscribe-form" novalidate method="post">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="action" value="subscribe">
                <input type="hidden" name="subscribe" value="Y">
                <input type="email" class="js-footer-email" name="EMAIL"
                       placeholder="Введите ваш e-mail" required autocomplete="off">
                <input type="submit" value="Подписаться">
                <ul class="subscribe-errors"></ul>
            </form>
<!--                <img class="col-lg-3 subscribe-second-img" src="--><?//=SITE_TEMPLATE_PATH?><!--/img/subscribe2.webp" alt="Подписка">-->
        </div>
    </div>
    <? endif; ?>
</div>
