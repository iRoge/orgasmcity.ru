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
<form method="post" class="feedback-form" action="/feedback/" enctype="multipart/form-data">
    <input class="feedback-form-element" name="NAME" type="text" placeholder="Ваше имя*" value="<?=isset($_POST['NAME']) ? $_POST['NAME'] : ''?>">
    <select name="SCORE" class="feedback-form-element feedback-form-select">
        <option value="">Оценка нашего магазина*</option>
        <option value="1" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 1 ? 'selected' : ''?>>1</option>
        <option value="2" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 2 ? 'selected' : ''?>>2</option>
        <option value="3" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 3 ? 'selected' : ''?>>3</option>
        <option value="4" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 4 ? 'selected' : ''?>>4</option>
        <option value="5" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 5 ? 'selected' : ''?>>5</option>
    </select>
    <textarea class="feedback-form-element" name="FEEDBACK_TEXT" style="resize: none; height: 100px" placeholder="Текст отзыва*"><?=isset($_POST['FEEDBACK_TEXT']) ? $_POST['FEEDBACK_TEXT'] : ''?></textarea>
    <div class="haveOrder-wrapper">
        <input type="checkbox" name="HAS_ORDER" value="1" class="haveOrder-input" id="haveOrderInput" <?=isset($_POST['HAS_ORDER']) && $_POST['HAS_ORDER'] == 1 ? 'checked' : ''?>>
        <label for="haveOrderInput" class="haveOrder-input-label">Я совершал заказ (если вы укажете номер заказа с телефоном, то ваш отзыв автоматически попадет на главную страницу. Так же вы можете по желанию прикрепить фото товара)</label>
        <div class="haveOrder-closed">
            <input class="feedback-form-element" name="ORDER_ID" type="text" placeholder="Номер вашего заказа*" <?=isset($_POST['ORDER_ID']) ? $_POST['ORDER_ID'] : ''?>>
            <input class="feedback-form-element" name="ORDER_PHONE" type="text" placeholder="Номер телефона, указанного в заказе*" <?=isset($_POST['ORDER_PHONE']) ? $_POST['ORDER_PHONE'] : ''?>>
            <input class="feedback-form-element" name="FILE" type="file" title="По желанию вставьте фото товара" value="<?=isset($_POST['FILE']) ? $_POST['FILE'] : ''?>">
        </div>
    </div>
    <input class="feedback-form-submit" type="submit" name="SUBMIT" value="Отправить отзыв">
</form>
