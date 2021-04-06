<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

global $APPLICATION;
global $USER;

if (isset($arResult['ENABLE_SUBSCRIBE']) && true == $arResult['ENABLE_SUBSCRIBE']) {
    $APPLICATION->SetPageProperty('HIDE_SUBSCRIBE', 'Y');
    $APPLICATION->IncludeComponent('likee:subscribe', 'actions', [
        'ACTION_NAME' => $arResult['NAME']
    ], false);
}

$contestViewEnabled = (isset($arResult['ENABLE_CONTEST']) && true == $arResult['ENABLE_CONTEST']);
$contestInActiveStage = false;

$isContestTypeInstagram = (isset($arResult['CONTEST_TYPE']) && $arResult['CONTEST_TYPE'] == 'Y');

if ($contestViewEnabled && $USER->IsAuthorized() && !$isContestTypeInstagram && $arResult['PROPERTIES']['NEWS_OR_ACTION']['VALUE_XML_ID'] == 'actions') {
    $contestInActiveStage = $APPLICATION->IncludeComponent('likee:contest', '', [
        'ACTION_NAME' => $arResult['NAME']
    ], false);
}


$btnColorStyle = 'style="';
$btnColorStyle .= !empty($arResult['CONTEST_BTN_COLOR']) ? ('background-color: #' . $arResult['CONTEST_BTN_COLOR'] . ';') : '';
$btnColorStyle .= !empty($arResult['CONTEST_BTN_TEXT_COLOR']) ? ('color: #' . $arResult['CONTEST_BTN_TEXT_COLOR'] . ';') : '';
$btnColorStyle .= '"';

\Likee\Site\Helper::addBodyClass('page--action hide-title');
?>

<? if ($contestViewEnabled && !$isContestTypeInstagram) : ?>
    <!--noindex-->
    <div class="js-contest-start"></div>

    <? if (!$USER->IsAuthorized()) : ?>
        <div class="spacer--3"></div>
        <div class="container">
            <div class="column-8 column-center text--center">
                <a href="#" class="button button--primary button--xl button--bigger js-popup-login">Приступить</a>
            </div>
        </div>
        <? \Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/app.popup-login.js'); ?>
    <? elseif (!empty($arResult['CONTEST_END'])) : ?>
        <div class="container container--contest-end<?= ($contestInActiveStage ? '' : ' hidden') ?>">
            <div class="column-8 column-center">
                <div class="action-content">
                    <?= $arResult['CONTEST_END']; ?>
                </div>
            </div>
        </div>
    <? endif ?>

    <? if (!empty($arResult['CONTEST_RULES'])) : ?>
        <div class="container">
            <div class="column-8 column-center">
                <div id="contest-rules" class="<?= ($contestInActiveStage ? 'hidden' : '') ?>">
                    <?= $arResult['CONTEST_RULES']; ?>
                </div>
            </div>
        </div>
    <? endif ?>
    <!--/noindex-->
<? elseif ($contestViewEnabled && $isContestTypeInstagram) : ?>
    <!--noindex-->
    <div class="container">
        <div class="column-8 column-center text-center">
            <a class="btn-enroll button-primary button-xl button-bigger js-popup-insta-contest" <?= $btnColorStyle ?>><?= $arResult['CONTEST_BTN_ENROLL_TEXT'] ?></a>
        </div>
    </div>

    <? if ($arResult['CONTEST_RULES_SHOW']) : ?>
        <div class="container">
            <div class="column-8 column-center text-center">
                <a href="#insta_contest_rules" class="js-popup-open insta_contest_rules">
                    Правила проведения конкурса
                </a>
            </div>
        </div>

        <div class="container">
            <div class="column-8 column-center">
                <div id="insta_contest_rules" class="hidden">
                    <div class="contest-popup-container">
                        <?= $arResult['CONTEST_RULES']; ?>
                    </div>
                </div>
            </div>
        </div>
    <? endif; ?>
    <!--/noindex-->
    <script>
        $(document).ready(function() {

            function initFileField($wrapper) {
                $('.likee-file-upload', $wrapper).each(function() {
                    var _this = this;

                    $('.button', this).on('click', function() {
                        $('input[type="file"]', _this).focus().trigger('click');
                    });

                    $('input[type="file"]', this).on('change', function(e) {
                        var fileName = e.target.value.split('\\').pop();

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

            $('.js-popup-insta-contest').on('click', function(e) {
                e.preventDefault();

                $.get('/local/ajax/insta_contest_popup_form.php?action_id=<?= $arResult['ID'] ?>&btn_color=<?= $arResult['CONTEST_BTN_COLOR'] ?>&btn_text_color=<?= $arResult['CONTEST_BTN_TEXT_COLOR'] ?>', function(response) {
                    Popup.show($(response), {
                        title: '',
                        className: '',
                        onShow: function(popup) {
                            initFileField($('.popup'));
                            BX.addCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                        },
                        onClose: function(popup) {
                            BX.removeCustomEvent('onAjaxSuccess', reinitPopupWrapper);
                        }
                    });
                });
            });

        });
    </script>
<? endif ?>