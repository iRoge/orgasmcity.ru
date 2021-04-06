<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
?>

<div id="subscribe" class="subscribe">
    <div class="container">
        <div class="column-4 pre-3 column-md-2">
            <form id="subscribe-form" novalidate action="<?= $arResult['FORM_ACTION']; ?>" method="post">
                <?= bitrix_sessid_post() ?>
                <input type="hidden" name="sender_subscription" value="add">

                <? //подписываем пользовател на все подряд ?>
                <? foreach ($arResult['RUBRICS'] as $arRubric): ?>
                    <input type="hidden" name="SENDER_SUBSCRIBE_RUB_ID[]" value="<?= $arRubric['ID']; ?>">
                <? endforeach; ?>

                <div class="input-group">
                    <input type="email" name="SENDER_SUBSCRIBE_EMAIL" value="<?= $arResult['EMAIL']; ?>"
                           placeholder="Подпишитесь на рассылку" required autocomplete="off">
                    <button type="submit">OK</button>
                </div>

                <div class="input-group">
                    <label for="agreement" class="checkbox">
                        <input id="agreement" type="checkbox" name="agreement" required><span>Согласен предоставить данные</span>
                    </label>
                </div>
            </form>
            <div id="subscribe-message" class="subscribe-message">
                <i class="icon icon-check"></i>
                <b>Поздравляем, вы успешно подписаны на новости!</b>
            </div>
        </div>
    </div>
</div>