<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="container">
    <div class="column-3 column-center">
        <form class="form--subscribe" data-user-email="<?= $USER->GetEmail() ?>" method="post">
            <?=bitrix_sessid_post()?>
            <legend class="legend-padding">Подпишитесь на рассылку Города Оргазма, чтобы быть в курсе всех акций и новостей</legend>
            <fieldset>
                <div class="input-group input-group--manage">
                    <input type="checkbox" name="subscribe" value="Y" class="checkbox3" id="email" <?=$arResult['SUBSCRIBED'] ? 'checked' : ''?>>
                    <label for="email">Я хочу получать рассылку e-mail</label>
                </div>
            </fieldset>
            <fieldset class="with-padding btn-save-mailing">
                <input type="submit" name="Save" class="button button--primary button--outline button--block button--xxl btn-style-blue" value="Сохранить изменения">
            </fieldset>
        </form>
    </div>
</div>