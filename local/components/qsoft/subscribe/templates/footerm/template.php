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
        <form id="subscribe-form_mobile" novalidate method="post">
            <?= bitrix_sessid_post() ?>
            <input type="hidden" name="action" value="subscribe">
            <input type="hidden" name="subscribe" value="Y">
            <input type="email" class="js-footer-email" name="EMAIL"
                   placeholder="Введите ваш e-mail" required autocomplete="off">
            <input type="submit" value="Подписаться">
            <input class="checkbox22 js-footer-agreement" id="agreement_mobile" type="checkbox" name="agreement" required checked/>
            <label for="agreement_mobile">
                Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a>.
            </label>
            <ul class="subscribe-errors"></ul>
        </form>
    <? endif; ?>
</div>
