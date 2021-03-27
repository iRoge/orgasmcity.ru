<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arActions = $component->getActionsDescription();
?>
<div class="in-after-lk">
    <div class="balance-wrap">
        <div class="balance">
            <div class="col-xs-12">
                <h3>ВАШ СТАТУС</h3>
                <p class="cur-balance"><?= $arResult['USER_STATUS']; ?></p>
            </div>
            <div class="col-xs-12">
                <h3>ТЕКУЩИЙ БАЛАНС</h3>
                <p class="cur-balance"><?= $arResult['USER_BONUSES']; ?></p><p class="cur-balance-after">бонус<?=$arResult['USER_BONUSES_ENDING']?></p>
            </div>
            <hr class="hr-balance" />
            <p class="col-xs-12 bon-rub">1 бонус = 1 рубль</p>
        </div>

        <div class="balance balance-update">
            <div class="col-xs-12">
                <h3>Для актуализации баланса бонусного счета нажмите</h3>
                <p class="update-btn"><a href = "javascript:void(0);" class="js-btn-history-update">Обновить данные</a></p>
                <div class="purchase-show-block">
                    <input type="checkbox" class="purchase-show-checkbox" style="display: inline-block; margin-right: 10px;" id="purchase-show">
                    <label for="purchase-show" class="purchase-show-text">Показать списания и начисления только за покупки</label>
                </div>
            </div>
        </div>
    </div>
    <div class="sort sort--lk">
        <div class="left-bns sort-in">
            <p class="date-bns">Дата</p>
            <p class="zach-bns">Изменение</p>
            <p class="stat-bns">Содержание</p>
        </div>

    <? if (!empty($arResult['ITEMS'])) : ?>
        <div class="left-bns">
            <? foreach ($arResult['ITEMS'] as $iKey => $arItem) : ?>
                <div class="one-zkz<?= strpos($arItem['ACTION'], 'PURCHASE') === false ? ' bonus-check' : ''?>">
                    <p class="date-bns"><?= $arItem['DATE']; ?> <?= $arItem['DATE2']; ?></p>
                    <p class="zach-bns">
                        <? if (isset($arItem['DELTA']['DEBITED'])) : ?>
                            <span class="zach-bns minus-bonus">-<?= $arItem['DELTA']['DEBITED']; ?></span></br>
                        <? endif; ?>
                        <span class="zach-bns <?=$arItem['DELTA']['SIGN'] === 'plus' ? 'plus-bonus' : 'minus-bonus'?>">
                            <?= $arItem['DELTA']['VALUE']; ?>   
                        </span>
                    </p>
                    <p class="stat-bns">
                        <? if (array_key_exists($arItem['ACTION'], $arActions)) : ?>
                            <?= $arActions[$arItem['ACTION']]; ?>
                        <? else : ?>
                            <?= $arItem['NAME']; ?>
                        <? endif; ?>
                    </p>
                </div>
            <?endforeach;?>
        </div>
    <?else :?>
    <div class="empty-balance">
        У вас нет бонусов
        <a href = "javascript:void(0);" class="js-btn-history-update">Обновить</a>
    </div>
    <?endif;?>
    <div id="update-error" class="update-error"></div>
    </div>

</div>
