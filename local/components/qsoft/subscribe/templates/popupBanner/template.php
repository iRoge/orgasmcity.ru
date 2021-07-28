<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

global $USER;
?>
<script>
    let isAuth = <?=$USER->IsAuthorized() ? '1' : '0'?>;
</script>
<div class="js-popup-banner">
    <img src="/img/surprise.png" alt="Surprise" width="100%" height="100%">
    <div class="mailsender" style="z-index: 1001">
        <div class="js-subscribe-new">
            <? if (!empty($arResult['MESSAGE'])) : ?>
                <div class="subscribe-message">
                    <i class="icon icon-check"></i>
                    <b><?= $arResult['MESSAGE']; ?></b>
                </div>
            <? else : ?>
                <form id="subscribe-form_mobile" novalidate method="post">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="action" value="subscribeSurprise">
                    <input type="hidden" name="subscribe" value="Y">
                    <input type="email" class="js-footer-email" name="EMAIL"
                           placeholder="Введите ваш e-mail" required autocomplete="off">
                    <input type="submit" value="Получить подарок">
                    <ul class="subscribe-errors"></ul>
                </form>
            <? endif; ?>
        </div>
    </div>
</div>
