<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<?php if ($arParams['POPUP_FORM'] == 'Y') { ?>
    <?php $this->__template->SetViewTarget("AUTH_HEAD_BLOCK");?>
    <?php if ($USER->IsAuthorized()) { ?>
    <a href="/personal/" class="auth-div-desk">
        <?= $USER->GetEmail(); ?>
    </a>
    <div class="auth-div-personal">
        <a href="/personal/orders/">История заказов</a><br />
        <a href="/personal/bonuses/">Бонусы</a><br />
        <a href="/personal/">Личные данные</a><br />
        <a href="/personal/subscribe/">Управление рассылкой</a><br />
        <a href="<?= $APPLICATION->GetCurPage() ?>?logout=yes" id="logout-btn">Выйти</a><br />
    </div>
    <?php } else { ?>
        <img class="header-icon" src="<?= SITE_TEMPLATE_PATH; ?>/img/svg/gate.svg"/>
        <span style="display: block" id="auth-button">Войти</span>
    <?php } ?>
    <?php $this->__template->EndViewTarget(); ?>
<?php } ?>