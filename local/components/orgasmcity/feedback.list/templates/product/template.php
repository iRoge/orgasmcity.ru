<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeSliderComponent $component */

?>
<div class="left-block-title">
    Отзывы о товаре:
</div>
<div class="product-feedback-wrapper">
    <?php if (!empty($arResult['ITEMS'])) { ?>
        <?php foreach ($arResult['ITEMS'] as $item) { ?>
            <div class="product-feedback-item-wrapper">
                <img class="hidden-xs hidden-sm" height="100%" src="<?=$item['PROPERTY_GENDER_VALUE'] == 'Женщина' ? SITE_TEMPLATE_PATH . '/img/avatars/g' . ($item['ID'] % 11) . '.png' : SITE_TEMPLATE_PATH . '/img/avatars/m' . ($item['ID'] % 11) . '.png'?>" alt="Аватар">
                <div class="product-feedback-item-text-wrapper">
                    <div class="product-feedback-item-title-wrapper">
                        <div class="product-feedback-item-title">@<?=$item['NAME']?></div>
                        <div class="product-feedback-item-score-wrapper" title="Оценка <?=$item['PROPERTY_SCORE_VALUE']?> из 5">
                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <div class="product-feedback-item-heart-wrapper">
                                    <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path style="fill: <?=$item['PROPERTY_SCORE_VALUE'] >= $i ? '#D18C8C' : '#E4E4E4'?>" d="M0.0595703 4.97532C0.0595703 -0.373625 7.1161 -2.356 9.28738 3.83611C11.4587 -2.356 18.5152 -0.373625 18.5152 4.97532C18.5152 10.7869 9.28738 18.1553 9.28738 18.1553C9.28738 18.1553 0.0595703 10.7869 0.0595703 4.97532Z" fill="#E4E4E4"/>
                                    </svg>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <span class="feedback-date"><?=$item['DATE_CREATE']?></span>
                    <div class="product-feedback-item-detail-text"><?=$item['DETAIL_TEXT']?></div>
                </div>
            </div>
        <?php }?>
    <?php } else { ?>
        <span class="product-feedback-no-items">Пока что никто не оставил отзыв о товаре, но вы можете быть первым ;)</span>
    <?php } ?>
</div>
<div class="feedback-errors-wrapper" style="display: none">
    <p class="text-danger js-error-message">
    </p>
</div>
<div class="feedback-success-wrapper" style="display: none">
    <p class="text-success js-success-message">
    </p>
</div>
<div class="feedback-form-wrapper">
    <div class="left-block-title">
        Оставить отзыв:
    </div>
    <form method="post" class="feedback-form" action="" enctype="multipart/form-data">
        <input hidden name="PRODUCT_ID" type="text" value="<?=$arResult['PRODUCT_ID']?>">
        <input class="feedback-form-element" name="NAME" type="text" placeholder="Ваше имя*" value="" maxlength="15">
        <select name="GENDER" class="feedback-form-element feedback-form-select">
            <option value="">Ваш пол*</option>
            <option value="Женщина">Женщина</option>
            <option value="Мужчина">Мужчина</option>
        </select>
        <select name="SCORE" class="feedback-form-element feedback-form-select">
            <option value="" selected>Оценка товара*</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        <textarea class="feedback-form-element" name="FEEDBACK_TEXT" maxlength="300" style="resize: none; height: 100px" placeholder="Текст отзыва (максимум 300 символов)*"></textarea>
        <div class="haveOrder-wrapper">
            <input type="checkbox" name="HAS_ORDER" value="1" class="haveOrder-input" id="haveOrderInput">
            <label for="haveOrderInput" class="haveOrder-input-label">Я совершал заказ (если вы укажете номер заказа с почтой, то ваш отзыв автоматически будет отображен)</label>
            <div class="haveOrder-closed">
                <input class="feedback-form-element" name="ORDER_ID" type="text" placeholder="Номер вашего заказа*" value="">
                <input class="feedback-form-element" name="ORDER_EMAIL" type="text" placeholder="Email, указанный в заказе*" value="">
            </div>
        </div>
        <div class="submit-feedback-wrapper">
            <input class="feedback-form-submit" type="submit" name="SUBMIT" value="Отправить отзыв">
        </div>
    </form>
</div>