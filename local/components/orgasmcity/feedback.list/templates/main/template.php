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
<?php if (!empty($arResult['ITEMS'])) {?>
    <div style="float: left; width: 100%; margin: 25px 0">
        <h2 class="default-header">Последние отзывы клиентов</h2>
        <div id="feedback-list" class="main">
            <?php foreach ($arResult['ITEMS'] as $item) { ?>
                <div class="feedback-card">
                    <div class="feedback-card-wrapper">
                        <div class="feedback-card-title-wrapper">
                            <span class="feedback-card-title">
                                <?=$item['NAME']?>
                            </span>
                            <div class="feedback-item-score-wrapper" title="Оценка <?=$item['PROPERTY_SCORE_VALUE']?> из 5">
                                <?php for ($i = 1; $i <= 5; $i++) { ?>
                                    <div class="feedback-item-heart-wrapper">
                                        <svg viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path style="fill: <?=$item['PROPERTY_SCORE_VALUE'] >= $i ? '#D18C8C' : '#E4E4E4'?>" d="M0.0595703 4.97532C0.0595703 -0.373625 7.1161 -2.356 9.28738 3.83611C11.4587 -2.356 18.5152 -0.373625 18.5152 4.97532C18.5152 10.7869 9.28738 18.1553 9.28738 18.1553C9.28738 18.1553 0.0595703 10.7869 0.0595703 4.97532Z" fill="#E4E4E4"/>
                                        </svg>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <span class="feedback-card-date">
                            <?=$item['DATE_CREATE']?>
                        </span>
                        <span class="feedback-card-text">
                            <?=$item['DETAIL_TEXT']?>
                        </span>
                    </div>
                </div>
            <?php }?>
        </div>
        <div class="feedback-button-wrapper">
            <a href="/feedback/" class="feedback-button">Оставить отзыв</a>
        </div>
    </div>
<?php }?>
