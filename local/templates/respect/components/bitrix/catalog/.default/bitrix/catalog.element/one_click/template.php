<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use \Bitrix\Main\UserTable;
?>
<form id="one-click-form" class="product-page product b-element-one-click one-click-form js-one-click-form one-click-content" action="/cart/" method="post">
    <?= bitrix_sessid_post(); ?>
    <input type="hidden" name="action" value="1click">
    <input type="hidden" name="PRODUCTS[]" value="">
    <div id="after-cart-in-err"></div>
    <div class="container container--quick-order">
        <div class="column-5 column-md-2">
            <div class="form">
                <div class="input-group input-group--phone">
                    <? if (CUser::IsAuthorized()) {
                        $arUser = UserTable::getList(array(
                            "select" => array(
                                "PERSONAL_PHONE",
                            ),
                            "filter" => array(
                                "ID" => $USER->GetID(),
                            ),
                            "limit" => 1,
                        ))->Fetch();
                    } ?>
                    <input class="one_click_phone" data-phone="<?= $arUser['PERSONAL_PHONE']; ?>" type="text" name="PROPS[PHONE]" placeholder="*Телефон" required>
                </div>
            </div>
        </div>
    </div>
    <div class="container container--quick-order">
        <div class="column-10">
            <hr/>
        </div>
    </div>
    <div class="container container--quick-order">
        <div class="column-4 pre-3 column-md-2">
            <button id="button-one-click" class="buttonFastBuy">
                Отправить заказ
            </button>
            <div class="buttonFastBuy-loader">
                <div class="one-click-preloader-div">
                    <button class="one-click-preloader">ОЖИДАЙТЕ</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container container--quick-order product__footer">
        <div id="one_click_checkbox_policy_error"></div>
        <div id="one_click_checkbox_policy" class="col-xs-12">
            <input type="checkbox" id="one_click_checkbox_policy_checked" name="one_click_checkbox_policy" class="checkbox3" checked/>
            <label for="one_click_checkbox_policy_checked" class="checkbox--_">Я соглашаюсь на обработку моих персональных данных и ознакомлен(а) с <a href="<?= OFFER_FILENAME ?>">политикой конфиденциальности</a>.</label>
        </div>
    </div>
</form>
<script type="text/javascript">
$(document).ready(function() {
    var presetDataPhone = $("input.one_click_phone").data('phone');
    $("input.one_click_phone").val(presetDataPhone);
    $("input.one_click_phone").mask("+7 (999) 999-99-99", {autoclear: false});
    $("input.one_click_phone").click(function(){
        if ($("input.one_click_phone").val() == presetDataPhone) {
            $("input.one_click_phone").mask("");
            $("input.one_click_phone").val('');
            $("input.one_click_phone").mask("+7 (999) 999-99-99", {autoclear: false});
            $("input.one_click_phone").val('+7 (___) ___-__-__');
        }
        if ($("input.one_click_phone").val() == "+7 (___) ___-__-__") {
            $(this)[0].selectionStart = 4;
            $(this)[0].selectionEnd = 4;
        }
    });
    $("input.one_click_phone").mouseover(function () {
        $("input.one_click_phone").attr('placeholder', '+7 (___) ___-__-__');
    });
    $("input.one_click_phone").mouseout(function () {
        $("input.one_click_phone").attr('placeholder', '*Телефон');
    });
    $('#one-click-form').on('submit', function(e) {
        e.preventDefault();
        var cou_err = 0;
        var text_html = "";
        if ($("input.one_click_phone").val().trim() == "") {
            cou_err++;
            text_html += '<p>Необходимо заполнить поле *Телефон</p>';
            $("input.one_click_phone").addClass("red_border");
        } else {
            var inputPhoneValue = $("input.one_click_phone").val().replace(/\D+/g, '');
            if (inputPhoneValue.length - 1 < 10) {
                text_html += "<p>Неверно заполнено поле *Телефон</p>";
                $("input.one_click_phone").addClass("red_border");
                cou_err++;
            } else {
                $("input.one_click_phone").removeClass("red_border");
            }
        }
        if (!($("#one_click_checkbox_policy_checked").prop('checked'))) {
            cou_err++;
            text_html += "<p>Необходимо согласие с политикой конфиденциальности</p>";
        }
        $("#after-cart-in-err").html(text_html);
        if (cou_err > 0) {
            return false;
        }
        $("#one-click-form").addClass("loader-one-click-form");
        $(".input-group--phone").addClass("loader-one-click-element");
        $("#button-one-click").hide();
        $(".buttonFastBuy-loader").show();
        $.ajax({
            type: "POST",
            url: "/cart/",
            data: $(this).serialize(),
            dataType: "json",
            success: function(data) {
                if (data.status == "ok") {
                    window.location = "/order-success/?orderId="+data.text;
                    return false;
                }
                $("#one-click-form").removeClass("loader-one-click-form");
                $(".input-group--phone").removeClass("loader-one-click-element");
                $("#button-one-click").show();
                $(".buttonFastBuy-loader").hide();
                $("#after-cart-in-err").html(data.text.join("<br>"));
            },
            error: function(jqXHR, exception) {
                $("#one-click-form").removeClass("loader-one-click-form");
                $(".input-group--phone").removeClass("loader-one-click-element");
                $("#button-one-click").show();
                $(".buttonFastBuy-loader").hide();
            },
        });
        return false;
    });
});
</script>
