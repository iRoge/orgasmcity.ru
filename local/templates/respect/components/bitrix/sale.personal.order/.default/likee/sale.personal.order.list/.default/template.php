<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

if (!$USER->IsAuthorized()) {
    return;
}
?>
<? if (empty($arResult['ORDERS'])) : ?>
    <div class="container column-center text--center" style="padding: 25px 0 45px; font-size: 1.35rem;">
        <? ShowError('К сожалению, ваш список заказов пуст.'); ?>
    </div>
<? else : ?>
<div class="sort col-xs-12">
    <div class="col-md-8 sort-in" style="padding-right: 30px">
        <p class="num-zkz">Заказ</p>
        <p class="date-zkz">Статус</p>
        <p class="stat-zkz">Стоимость доставки</p>
        <p class="sum-zkz">Сумма заказа</p>
    </div>
</div>

<div class="col-md-8 col-xs-12 orders-wrapper lk-wrapper">
    <? foreach ($arResult['ORDERS'] as $arOrder) : ?>
        <?
        $sStatusName = $arOrder['ORDER']['STATUS_ID'];
        $iBonusesPlus = 0;
        $iBonusesMinus = 0;
        foreach ($arOrder['PAYMENT'] as $arPayment) {
            if ($arPayment['PAY_SYSTEM_ID'] == 12) {
                $iBonusesMinus = intval($arPayment['SUM']);
            }
        }
        ?>
        <div class="col-xs-12 one-zkz">
            <div class="order-info-grid opn">
                <p class="num-zkz"><span class="in-mobile">Заказ </span>№ <?= $arOrder['ORDER']['ID'] ?></p>
                <p class="date-zkz"><span class="in-mobile">Дата: </span><?= $arOrder['ORDER']['DATE_INSERT']->format($arParams['ACTIVE_DATE_FORMAT']) ?></p>
                <p class="delivery-zkz"><span class="in-mobile">Доставка: </span>
                    <?= number_format($arOrder['ORDER']['PRICE_DELIVERY'], 0, ',', ' '); ?> р.
                </p>
                <p class="stat-zkz excellent-one-zkz"><span class="in-mobile">Статус: </span>
                    <? if ($arOrder['ORDER']['CANCELED'] == 'Y') : ?>
                        Отменен
                    <? else : ?>
                        <?=(!empty($arResult['INFO']['STATUS'][$sStatusName]['DESCRIPTION']))? $arResult['INFO']['STATUS'][$sStatusName]['DESCRIPTION'] : $arResult['INFO']['STATUS'][$sStatusName]['NAME']?>
                    <? endif; ?>
                </p>
                <p class="sum-zkz price-one-zkz"><span class="in-mobile">Сумма: </span>
                    <?= number_format(floatval($arOrder['ORDER']['PRICE'] - $iBonusesMinus), 0, ',', ' '); ?> р.
                    <? if ($arOrder['ORDER']['PAYED'] == 'N' && in_array($arResult['INFO']['PAY_SYSTEM'][$arOrder['ORDER']['PAY_SYSTEM_ID']]['CODE'], ONLINE_PAYMENT_CODES)) : ?>
                        <button type="button" class="pay-lk-button" data-order-id="<?=$arOrder['ORDER']['ID']?>">Оплатить</button>
                    <? endif; ?>
                </p>
            </div>
            <div class="order-basket-items">
                <? foreach ($arOrder['BASKET_ITEMS'] as $arProduct) : ?>
                <a href="/<?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['CODE'] ?>/">
                    <div class="in-one-zkz">
                        <div class="date-bns image-box">
                            <img
                                    class="image"
                                    src="<?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['PREVIEW_PICTURE'] ?>"
                                    alt="<?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['NAME'] ?>"
                            >
                        </div>
                        <p class="num-bns"><?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['ARTICLE'] ?: '-'; ?></p>
                        <p class="zach-bns"><?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['NAME'] ?></p>
                        <p class="spis-bns"><?= $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['PROPERTY_SIZE_VALUE'] ?
                                $arResult['PRODUCTS'][$arProduct['PRODUCT_ID']]['PROPERTY_SIZE_VALUE'] . ' размер' : ''?></p>
                        <p class="stat-bns"><?= number_format($arProduct['PRICE'] * $arProduct['QUANTITY'], 0, ',', ' '); ?> ₽</p>
                    </div>
                </a>
                <? endforeach; ?>
            </div>
        </div>
    <? endforeach; ?>
    <div class="col-xs-12 padding-o" style="padding-right: 30px">
        <?= $arResult['NAV_STRING']; ?>
    </div>
</div>
<? endif; ?>
