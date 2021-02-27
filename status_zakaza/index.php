<?
define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Статус заказа");
$APPLICATION->SetTitle("Статус заказа");
?>

<div class="order-info">
    <div class="order-info__fix">
        <div class="order-info__title">Для того, чтобы узнать статус заказа<br /> введите номер заказа и ваш номер телефона</div>
        <div class="order-info__row">
            <input class="order-info__col order-info__input" type="text" name="order_number" placeholder="Введите номер заказа">
            <div class="input-group--phone">
                <input class="phone order-info__col order-info__input" type="text" name="order_phone" placeholder="Введите номер телефона">
            </div>
        </div>

        <div class="order-info__row order-info__captcha">
            <?
            include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/captcha.php");
            $cpt = new CCaptcha();
            $captchaPass = COption::GetOptionString("main", "captcha_password", "");
            if (strlen($captchaPass) <= 0)
            {
                $captchaPass = randString(10);
                COption::SetOptionString("main", "captcha_password", $captchaPass);
            }
            $cpt->SetCodeCrypt($captchaPass);
            ?>
            <input class="static_input" type="hidden" name="captcha_code" value="<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">

            <img class="order-info__col order-info_captcha-img" src="/bitrix/tools/captcha.php?captcha_code=<?= htmlspecialchars($cpt->GetCodeCrypt()) ?>">
            <input class="static_input order-info__col order-info__input inputtext" placeholder="Введите текст с картинки" type="text" size="10" name="captcha_word">
        </div>
        <div class="button button--primary button--outline button--xl button--block order-info__submit">Узнать статус заказа</div>

        <div class="order-info__result"></div>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
