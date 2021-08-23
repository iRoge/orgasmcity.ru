<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<div class="js-subscribe-new">
    <? if (!empty($arResult['MESSAGE'])) : ?>
    <div class="subscribe-wrapper">
        <div class="main">
            <div class="col-lg-6 col-md-9 col-sm-9 subscribe-left-wrapper">
                <img class="subscribe-first-img" src="<?=SITE_TEMPLATE_PATH?>/img/subscribe1.webp" alt="Подписка">
            </div>
            <div class="col-lg-4 col-md-3 col-sm-3 subscribe-center-wrapper">
                <div class="subscribe-message">
                    <i class="icon icon-check"></i>
                    <b><?= $arResult['MESSAGE']; ?></b>
                </div>
            </div>
            <div class="col-lg-2 hidden-xs hidden-md hidden-sm subscribe-right-wrapper">
                <img class="subscribe-second-img" src="<?=SITE_TEMPLATE_PATH?>/img/subscribe2.webp" alt="Подписка 2">
            </div>
        </div>
    </div>
    <? else : ?>
    <div class="subscribe-wrapper">
        <div class="main">
            <div class="col-lg-6 col-md-9 col-sm-9 subscribe-left-wrapper">
                <img class="subscribe-first-img" src="<?=SITE_TEMPLATE_PATH?>/img/subscribe1.webp" alt="Подписка">
            </div>
            <div class="col-lg-4 col-md-3 col-sm-3 subscribe-center-wrapper">
                <form id="subscribe-form" novalidate method="post">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="action" value="subscribe">
                    <input type="hidden" name="subscribe" value="Y">
                    <input type="email" class="js-footer-email" name="EMAIL"
                           placeholder="Ваша электронная почта" required autocomplete="off">
                    <input type="submit" value="Подписаться">
                    <ul class="subscribe-errors"></ul>
                </form>
            </div>
            <div class="col-lg-2 hidden-xs hidden-md hidden-sm subscribe-right-wrapper">
                <img class="subscribe-second-img" src="<?=SITE_TEMPLATE_PATH?>/img/subscribe2.webp" alt="Подписка 2">
            </div>
        </div>
    </div>
    <? endif; ?>
</div>
