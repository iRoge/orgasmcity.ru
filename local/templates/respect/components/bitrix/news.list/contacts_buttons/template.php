<?php

use Bitrix\Main\Localization\Loc;

$waPhone = COption::GetOptionString('respect', 'whatsapp_phone');
$waProlog = COption::GetOptionString('respect', 'whatsapp_text');
?>
<script>
    //TODO вероятно надо будет удалить или заменить и вызов ниже по коду
    function onlinechat() {
        jivo_api.open({start: 'jcont'});
    }
    function feedback() {
        $('.right-block-top').find('.mail2').click();
        $('html, body').animate({scrollTop: 0},500);
    }

    //TODO вероятно надо будет удалить или заменить и вызов ниже по коду
    function call() {
        $('.mango-false-button').click();
    }
</script>

<section class="advantages-section-wrapper">
    <div class="grid">
        <?
        foreach ($arResult['ITEMS'] as $item) : ?>
            <a  <? if ($item['PROPERTIES']['BUTTON_NAME']['VALUE'] == 'whatsapp') : ?>
                    href="<?= $item['PROPERTIES']['BUTTON_NAME']['VALUE'] == 'whatsapp' ? 'https://wa.me/' . $waPhone . '?text=' . $waProlog : '' ?>"
                <? endif; ?>
                <? if ($item['PROPERTIES']['BUTTON_NAME']['VALUE'] == 'online_chat') : ?>
                    onclick="onlinechat()" href="javascript:void(0);"
                <? endif; ?>
                <? if ($item['PROPERTIES']['BUTTON_NAME']['VALUE'] == 'feedback_form') : ?>
                    onclick="feedback()" href="javascript:void(0);"
                <? endif; ?>
                <? if ($item['PROPERTIES']['BUTTON_NAME']['VALUE'] == 'call') : ?>
                    onclick="call()" href="javascript:void(0);"
                <? endif; ?>>
                <figure class="wrapper padded-container">
                    <img class="img centered" src="<?= $arResult['IMG_SOURCES'][$item['PROPERTIES']['IMG']['VALUE']] ?>"
                         alt="">
                    <figcaption class="advantages-text"><?= $item['DETAIL_TEXT'] ?></figcaption>
                </figure>
            </a>
        <? endforeach; ?>
    </div>
</section>
