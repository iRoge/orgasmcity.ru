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
<div class="main">
    <?php if (!$arResult['SUCCESS'] && !empty($arResult['ERRORS'])) { ?>
        <div class="feedback-errors-wrapper">
            <?php foreach ($arResult['ERRORS'] as $error) {?>
                <p class="text-danger">
                    <?=$error?>
                </p>
            <?php }?>
        </div>
    <?php } ?>
    <?php if ($arResult['SUCCESS']) { ?>
        <div class="feedback-success-wrapper">
            <p class="text-success">
                Ваш отзыв успешно отправлен и <?=!$arResult['HAS_ORDER'] ? 'будет размещен после проверки модератором' : 'размещен'?>
            </p>
        </div>
    <?php } else { ?>
        <form method="post" class="feedback-form" action="/feedback/" enctype="multipart/form-data">
            <input class="feedback-form-element" maxlength="15" name="NAME" type="text" placeholder="Ваше имя*" value="<?=$_POST['NAME'] ?? '' ?>">
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
            <textarea class="feedback-form-element" name="FEEDBACK_TEXT" maxlength="300" style="resize: none; height: 100px" placeholder="Текст отзыва (максимум 300 символов)*"><?=$_POST['FEEDBACK_TEXT'] ?? '' ?></textarea>
            <div class="haveOrder-wrapper">
                <input type="checkbox" name="HAS_ORDER" value="1" class="haveOrder-input" id="haveOrderInput" <?=isset($_POST['HAS_ORDER']) && $_POST['HAS_ORDER'] == 1 ? 'checked' : ''?>>
                <label for="haveOrderInput" class="haveOrder-input-label">Я совершал заказ (если вы укажете номер заказа с почтой, то ваш отзыв автоматически попадет на главную страницу. Так же вы можете по желанию прикрепить фото товара)</label>
                <div class="haveOrder-closed">
                    <input class="feedback-form-element" name="ORDER_ID" type="text" placeholder="Номер вашего заказа*" value="<?=$_POST['ORDER_ID'] ?? '' ?>">
                    <input class="feedback-form-element" name="ORDER_EMAIL" type="text" placeholder="Email, указанный в заказе*" value="<?=$_POST['ORDER_EMAIL'] ?? '' ?>">
                    <input class="feedback-form-element" name="FILE" type="file" title="По желанию вставьте фото товара" value="<?=$_POST['FILE'] ?? '' ?>">
                </div>
            </div>
            <input class="feedback-form-submit" type="submit" name="SUBMIT" value="Отправить отзыв">
        </form>
    <?php } ?>
    <?php if (!empty($arResult['ITEMS'])) { ?>
        <h2 class="default-header">Последние отзывы</h2>
        <div class="all-feedbacks-wrapper">
            <?php foreach ($arResult['ITEMS'] as $item) { ?>
                <div class="feedback-block-wrapper product-feedback-item-title-wrapper">
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
                    <span class="feedback-date"><?=$item['DATE_CREATE']?></span>
                    <div class="product-feedback-item-detail-text"><?=$item['DETAIL_TEXT']?></div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>


