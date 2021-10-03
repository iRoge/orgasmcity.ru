<?php
define('HIDE_TITLE', true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Статус заказа. Город Оргазма");
$APPLICATION->SetTitle("Статус заказа");
$APPLICATION->SetPageProperty("keywords", DEFAULT_KEYWORDS);
$APPLICATION->SetPageProperty("description", "Узнать статус заказа в Городе Оргазма");

?>
<script>
    $(document).ready(function () {
        $('.order-info__submit').on('click', function ()  {
            //Получаем номер заказа и номер телефона из формы
            var parent_section = $(this).parent();
            var order_number = parent_section.find($('.order-info__input[name=order_number]')).val();
            var order_phone = parent_section.find($('.order-info__input[name=order_phone]')).val();
            var captcha_word = parent_section.find($('.static_input[name=captcha_word]')).val();
            var captcha_code = parent_section.find($('.static_input[name=captcha_code]')).val();

            //Создаем массив с ошибками
            var arerror="";
            if(!order_phone){
                arerror = arerror+'Заполните поле "Номер телефона"';
            }
            if(!order_number){
                arerror = arerror+'<br />Заполните поле "Номер заказа"';
            }
            if(!captcha_word){
                arerror = arerror+'<br />Заполните поле "Текст с картинки"';
            }

            //Проверяю заполнили ли поля в форме и отправляем ajax запрос в файл
            if(order_phone && order_number && captcha_word){
                $.post("/local/templates/respect/ajax/order_status.php",
                    {
                        order_number: order_number,
                        order_phone: order_phone,
                        captcha_code: captcha_code,
                        captcha_word: captcha_word,
                    },
                    //выводим результат запроса
                    function(data){
                        parent_section.find($(".order-info__result")).html(data);
                        //Если заказ не найден, то для следующей попытки выводим поле с капчей
                        if(data.search('не найден')!=-1 || data.search('картинки')!=-1 || data.search('не соответствует')!=-1){
                            parent_section.find($('.order-info__captcha')).show();
                            $.getJSON('/local/templates/respect/ajax/reload_captcha.php', function(data) {
                                parent_section.find($('.order-info_captcha-img')).attr('src','/bitrix/tools/captcha.php?captcha_sid='+data);
                                parent_section.find($('.static_input[name=captcha_code]')).val(data);
                            });
                            return false;
                        }
                    }
                );
            }else{
                //выводим массив с ошибками
                parent_section.find($(".order-info__result")).html(arerror);
            }

        });
    });
</script>
<style>
    .order-info {
        float: left;
        width: 100%;
    }
</style>
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
