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
<?php if (!$arResult['SUCCESS'] && !empty($arResult['ERRORS'])) { ?>
    <div class="feeback-errors-wrapper">
        <?php foreach ($arResult['ERRORS'] as $error) {?>
            <p class="text-danger">
                <?=$error?>
            </p>
        <?php }?>
    </div>
<?php } ?>
<?php if ($arResult['SUCCESS']) { ?>
    <div class="feeback-success-wrapper">
        <p class="text-success">
            Ваш отзыв успешно отправлен и <?=!$arResult['HAS_ORDER'] ? 'будет размещен после проверки модератором, т.к. вы не подтвердили совершенный заказ' : 'размещен'?>
        </p>
    </div>
<?php } else { ?>
    <form method="post" class="feedback-form" action="/feedback/" enctype="multipart/form-data">
        <input class="feedback-form-element" name="NAME" type="text" placeholder="Ваше имя*" value="<?=isset($_POST['NAME']) ? $_POST['NAME'] : ''?>">
        <select name="GENDER" class="feedback-form-element feedback-form-select">
            <option value="">Ваш пол*</option>
            <option value="Женщина" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 1 ? 'selected' : ''?>>Женщина</option>
            <option value="Мужчина" <?=isset($_POST['SCORE']) && $_POST['SCORE'] == 2 ? 'selected' : ''?>>Мужчина</option>
        </select>
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
            <label for="haveOrderInput" class="haveOrder-input-label">Я совершал заказ (если вы укажете номер заказа с почтой, то ваш отзыв автоматически попадет на главную страницу. Так же вы можете по желанию прикрепить фото товара)</label>
            <div class="haveOrder-closed">
                <input class="feedback-form-element" name="ORDER_ID" type="text" placeholder="Номер вашего заказа*" value="<?=isset($_POST['ORDER_ID']) ? $_POST['ORDER_ID'] : ''?>">
                <input class="feedback-form-element" name="ORDER_EMAIL" type="text" placeholder="Email, указанный в заказе*" value="<?=isset($_POST['ORDER_EMAIL']) ? $_POST['ORDER_EMAIL'] : ''?>">
                <input class="feedback-form-element" name="FILE" type="file" title="По желанию вставьте фото товара" value="<?=isset($_POST['FILE']) ? $_POST['FILE'] : ''?>">
            </div>
        </div>
        <input class="feedback-form-submit" type="submit" name="SUBMIT" value="Отправить отзыв">
    </form>
<?php } ?>

