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

$iBonusesMinus = 0;
foreach ($arResult['PAYMENT'] as $arPayment) {
    if ($arPayment['PAY_SYSTEM']['CODE'] == 'SAILPLAY' && $arPayment['PAID'] == 'Y') {
        $iBonusesMinus = intval($arPayment['SUM']);
    }
}


$iBonusesPlus = 0;
?>

<div class="container">
    <div class="column-6 pre-1 column-md-2 padding-right">
        <h5>
            <div class="heading__subtitle float-right text--primary">
                <? if ($arResult['CANCELED'] == 'Y') : ?>
                    Отменен
                <? else : ?>
                    <?= $arResult['STATUS']['NAME']; ?>
                <? endif; ?>
            </div>
            ЗАКАЗ №<?= $arResult['ID']; ?>
            <small>интернет-магазин</small>
        </h5>
        <div class="product-table">
            <? ob_start(); ?>
            <header>
                <div class="product-table__product"></div>
                <div class="product-table__info">
                    <div class="product-table__color">Цвет</div>
                    <div class="product-table__size">Размер/<span class="text--primary">Количество</span></div>
                    <div class="product-table__cost">Стоимость, <span class="text--muted">руб.</span></div>
                </div>
                <div class="product-table__total">Итого, <span class="text--muted">руб.</span></div>
            </header>
            <?= \Likee\Site\Helper::minHTML(ob_get_clean()); ?>

            <? foreach ($arResult['BASKET'] as $arProduct) : ?>
                <section class="product-table-item">

                    <div class="product-table__product">
                        <div class="products-item products-item--square">
                            <div class="products-item__content">
                                <a href="<?= $arProduct['DETAIL_PAGE_URL']; ?>" style="background-image: url(<?= $arProduct['PICTURE']['SRC']; ?>)" class="products-item__image"></a>
                                <div class="products-item__information">
                                    <div class="container products-item__title">
                                        <div class="column-10">
                                            <b><?= $arProduct['NAME'] ?></b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="product-table__info">
                        <div class="product-table-row">
                            <div class="product-table__color">
                                <div class="color-selector colors-selector--x">
                                    <? if (!empty($arProduct['COLOR']['FILE']) && is_array($arProduct['COLOR']['FILE'])) : ?>
                                        <a style="background-image: url('<?= $arProduct['COLOR']['FILE']['SRC']; ?>')"></a>
                                    <? else : ?>
                                        <a style="background-color: <?= $arProduct['COLOR']['VALUE']; ?>"></a>
                                    <? endif; ?>
                                </div>

                                <? if (!empty($arProduct['ARTICLE'])) : ?>
                                    <a data-sku="<?= $arProduct['ARTICLE']; ?>" class="js-sku sku">Артикул</a>
                                <? endif; ?>
                            </div>

                            <div class="product-table__size">
                                <div class="size-selector size-selector--wrap center">
                                    <a class="selected">
                                        <div class="size-selector__count"><?= $arProduct['QUANTITY']; ?></div>
                                        <? foreach ($arProduct['PROPS'] as $arProp) {
                                            if ($arProp['CODE'] == 'SIZE') {
                                                echo $arProp['VALUE'];
                                                break;
                                            }
                                        } ?>
                                    </a>
                                </div>
                            </div>
                            <div class="product-table__cost"><?= number_format($arProduct['PRICE'], 0, ',', ' '); ?></div>
                        </div>
                    </div>

                    <div class="product-table__total">
                        <div class="cart-total__right"><?= number_format($arProduct['PRICE'] * $arProduct['QUANTITY'], 0, ',', ' '); ?></div>
                    </div>
                </section>
            <? endforeach; ?>
        </div>

        <? if (!empty($arResult['SHIPMENT'])) : ?>
            <div class="order-block order-block--white">
                <header>СПОСОБ ДОСТАВКИ</header>
                <? foreach ($arResult['SHIPMENT'] as $arDelivery) : ?>
                    <?
                    if ($arDelivery['LOGOTIP']) {
                        $arDelivery['DELIVERY']['SRC_LOGOTIP'] = \Likee\Site\Helper::getResizePath($arDelivery['LOGOTIP'], 80, 40);
                    }
                    ?>
                    <div>
                        <table>
                            <tr>
                                <td>
                                    <? if (!empty($arDelivery['DELIVERY']['SRC_LOGOTIP'])) : ?>
                                        <img src="<?= $arDelivery['DELIVERY']['SRC_LOGOTIP']; ?>"
                                             alt="<?= $arDelivery['DELIVERY_NAME']; ?>"
                                             width="100">
                                    <? endif; ?>
                                    <b><?= $arDelivery['DELIVERY_NAME']; ?></b>
                                </td>
                                <td class="order-block__cost"><?= $arDelivery['PRICE_DELIVERY_FORMATED']; ?></td>
                            </tr>
                        </table>
                    </div>
                <? endforeach; ?>
            </div>
        <? endif; ?>

        <table class="order-footer">
            <tr>
                <td><b>Сумма заказа:</b></td>
                <td class="text-right"><?= CCurrencyLang::CurrencyFormat(floatval($arResult['PRICE']), $arResult['CURRENCY']) ?></td>
            </tr>
            <? if ($iBonusesMinus > 0) : ?>
                <tr>
                    <td><b>Списано бонусов:</b></td>
                    <td class="text-right"><span class="text--primary">-<?= $iBonusesMinus; ?></span></td>
                </tr>
                <tr>
                    <td><b>Сумма к оплате:</b></td>
                    <td class="text-right"><?= CCurrencyLang::CurrencyFormat(floatval($arResult['PRICE'] - $iBonusesMinus), $arResult['CURRENCY']) ?></td>
                </tr>
            <? endif; ?>
            <? if ($iBonusesPlus > 0) : ?>
                <tr>
                    <td><b>Начислено бонусов:</b></td>
                    <td class="text-right"><span class="text--third">+<?= $iBonusesPlus; ?></span></td>
                </tr>
            <? endif; ?>
        </table>

        <div class="order-block order-block--white">
            <div>
                <dl class="dl">
                    <dt>Контактное лицо</dt>
                    <dd>
                        <b><?= $arResult['ORDER_PROPS']['FIO']['VALUE']; ?></b>
                        <br>
                        <?= $arResult['ORDER_PROPS']['PHONE']['VALUE']; ?>
                        <? if (!empty($arResult['ORDER_PROPS']['EMAIL']['VALUE'])) : ?>
                            <br>
                            <? $sPersonalMail = $arResult['ORDER_PROPS']['EMAIL']['VALUE']; ?>
                            <a href="mailto:<?= $sPersonalMail ?>" target="_blank">
                                <?= $sPersonalMail ?>
                            </a>
                        <? endif; ?>
                    </dd>
                    <dt>Адрес доставки</dt>
                    <dd>
                        <? if (!empty($arResult['STORES'])) : ?>
                            <?= implode('<br>', array_column($arResult['STORES'], 'ADDRESS')); ?>
                        <? else : ?>
                            <?
                            $arAddress = [];
                            foreach ($arResult['ORDER_PROPS'] as $arProp) {
                                if (!empty($arProp['VALUE']) && in_array($arProp['CODE'], ['STREET', 'HOUSE', 'FLAT', 'STRUCTURE', 'HOUSING', 'PORCH', 'FLOOR', 'NUMBER', 'INTERCOM'])) {
                                    $arAddress[] = $arProp['NAME'] . ': ' . $arProp['VALUE'];
                                }
                            }
                            ?>
                            <?= $arResult['ORDER_PROPS']['CITY']['VALUE']; ?>
                            <br>
                            <?= implode(', ', $arAddress); ?>
                        <? endif; ?>
                    </dd>

                    <? foreach ($arResult['PAYMENT'] as $arPaySystem) : ?>
                        <? if ($arPaySystem['PAY_SYSTEM']['CODE'] == 'SAILPLAY') {
                            continue;
                        } ?>

                        <dt>Способ оплаты</dt>
                        <dd><b><?= $arPaySystem['PAY_SYSTEM']['NAME']; ?></b></dd>
                    <? endforeach; ?>
                </dl>
            </div>
        </div>
    </div>

    <div class="column-2 column-md-2">
        <div class="block--black">
            <? foreach ($arResult['PAYMENT'] as $arPayment) : ?>
                <? if ($arPayment['PAY_SYSTEM']['CODE'] == 'SAILPLAY') {
                    continue;
                } ?>

                <? if ($arPayment['PAID'] == 'Y') : ?>
                    <div class="text--center text--primary" style="font-size: 1.35rem;">Оплачено</div>
                <? elseif ($arPayment['PAY_SYSTEM']['IS_CASH'] === 'Y') : ?>
                    Оплата наличными
                <? elseif ($arPayment['PAY_SYSTEM']['PSA_NEW_WINDOW'] === 'Y') : ?>
                    <a class="button button--primary button--outline button--block"
                       <? if ($arPayment['PAY_SYSTEM']['PSA_NEW_WINDOW'] == 'Y') :
                            ?>target="_blank"<?
                       endif; ?>
                       href="<?= htmlspecialcharsbx($arPayment['PAY_SYSTEM']['PSA_ACTION_FILE']); ?>">
                        ОПЛАТИТЬ
                    </a>
                <? elseif (!empty($arPayment['BUFFERED_OUTPUT'])) : ?>
                    <?= $arPayment['BUFFERED_OUTPUT']; ?>
                <? endif; ?>
                <? if (!empty($arPayment['PAY_SYSTEM']['DESCRIPTION'])) : ?>
                    <p>
                        <small><?= $arPayment['PAY_SYSTEM']['DESCRIPTION']; ?></small>
                    </p>
                <? endif; ?>
            <? endforeach; ?>
        </div>
        <a href="<?= $arParams['PATH_TO_LIST']; ?>" class="button button--third button--block">
            Назад к списку заказов
        </a>

        <? if ($arResult['STATUS']['ID'] == 'F') : ?>
            <a href="/refund/" target="_blank" class="button button--third button--block">
                Возврат товара
            </a>
        <? elseif ($arResult['CANCELED'] != 'Y' && $arResult['CAN_CANCEL'] == 'Y') : ?>
            <a href="<?= $arResult['URL_TO_CANCEL']; ?>" class="button button--third button--block">
                Отменить заказ
            </a>
        <? endif; ?>
    </div>
</div>