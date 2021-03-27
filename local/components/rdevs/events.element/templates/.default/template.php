<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
/** @var array $arParams */
global $APPLICATION;
?>

<h1 class="zagolovok zagolovok--catalog">
    <? $APPLICATION->ShowTitle(false); ?>
</h1>
<div class="col-xs-12 padding-o">
    <div class="main">
        <div class="event-content">
            <? if ($arResult['EVENT']['PROPERTY_PICTURE_POSITION_VALUE'] == 'UP') : ?>
                <? if (!empty($arResult['EVENT']['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                    <a href="<?= $arResult['EVENT']['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                        <img src="<?= $arResult['EVENT']['DETAIL_PICTURE']; ?>" alt="">
                    </a>
                <? else : ?>
                    <img src="<?= $arResult['EVENT']['DETAIL_PICTURE']; ?>" alt="">
                <? endif; ?>
            <? endif ?>
            <? if ($arResult['EVENT']['DATE_END']) : ?>
                <span style="color: red">Акция завершена<br></span>
            <? endif; ?>

            <p><?= $arResult['EVENT']['DETAIL_TEXT']; ?></p>
            <? if ($arResult['EVENT']['PROPERTY_PICTURE_POSITION_VALUE'] == 'DOWN') : ?>
                <? if (!empty($arResult['EVENT']['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                    <a href="<?= $arResult['EVENT']['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                        <img src="<?= $arResult['EVENT']['DETAIL_PICTURE']; ?>" alt="">
                    </a>
                <? else : ?>
                    <img src="<?= $arResult['EVENT']['DETAIL_PICTURE']; ?>" alt="">
                <? endif; ?>
            <? endif; ?>
            <? if ($arResult['CONTEST']['ENABLE'] && !$arResult['EVENT']['DATE_END']) : ?>
                <div class="container">
                    <div class="column-8 column-center text-center">
                        <a class="blue-btn events-link-btn js-popup-insta-contest" <?= $arResult['CONTEST']['STYLE_BTN'] ?>><?= $arResult['CONTEST']['TEXT_BTN'] ?></a>
                    </div>
                </div>

                <? if ($arResult['CONTEST']['SHOW_RULES']) : ?>
                <div class="container">
                    <div class="column-8 column-center text-center">
                        <a href="#insta_contest_rules" class="js-popup-open insta_contest_rules">
                            Полные правила конкурса
                        </a>
                    </div>
                </div>

                <div class="container">
                    <div class="column-8 column-center">
                        <div id="insta_contest_rules" class="hidden">
                            <div class="contest-popup-container">
                                <?= $arResult['CONTEST']['TEXT_RULES']; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <? endif; ?>


                <script>
                    $(document).ready(function () {
                        function initFileField($wrapper) {
                            $('.likee-file-upload', $wrapper).each(function () {
                                let _this = this;

                                $('.button', this).on('click', function () {
                                    $('input[type="file"]', _this).focus().trigger('click');
                                });

                                $('input[type="file"]', this).on('change', function (e) {
                                    let fileName = e.target.value.split('\\').pop();
                                    if (fileName) {
                                        $('.js-file-upload-info', _this).html('Файл : ' + fileName);
                                    }
                                });
                            });
                        }

                        function reinitPopupWrapper() {
                            window.currentPage.init($('.popup'));
                            initFileField($('.popup'));
                        }

                        $('.js-popup-insta-contest').on('click', function (e) {
                            e.preventDefault();

                            $.get('/local/ajax/insta_contest_popup_form.php?action_id=<?= $arResult['EVENT']['ID'] ?>&btn_color=<?= $arResult['CONTEST']['COLOR_BTN'] ?>&btn_text_color=<?= $arResult['CONTEST']['COLOR_TEXT_BTN'] ?>', function (response) {
                                Popup.show($(response), {
                                    title: '',
                                    className: '',
                                    onShow: function (popup) {
                                        initFileField($('.popup'));
                                        BX.addCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                                    },
                                    onClose: function (popup) {
                                        BX.removeCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                                    }
                                });
                            });
                        });
                    });
                </script>
            <? endif; ?>
            <a href="<?= $arParams['DEFAULT_SECTION']['LINK']; ?>" class="blue-btn events-link-btn">ВОЗВРАТ К СПИСКУ</a>
        </div>
    </div>
</div>
