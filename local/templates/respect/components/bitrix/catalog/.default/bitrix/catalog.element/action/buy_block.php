<?php
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
/** @global CDatabase $DB */

$bCanBuyLeastOne = false;
$bCanReservedLeastOne = false;
?>

<? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
    <?
    $iOfferID = intval($arSize['OFFER_ID']);
    $bCanBuy = $iOfferID > 0 && $arSize['CATALOG_AVAILABLE'] == 'Y';

    if (!$bCanBuy) continue;

    $bCanBuy = \Likee\Site\Helpers\Catalog::productCanBuy($iOfferID, $arSize['STORES']);
    $bCanReserved = empty($arResult['RESERVE_NOT_ALLOWED']) && \Likee\Site\Helpers\Catalog::productCanBeReserved($iOfferID, $arSize['STORES']);

    if ($bCanBuy)
        $bCanBuyLeastOne = true;

    if ($bCanReserved)
        $bCanReservedLeastOne = true;
    ?>

    <? if ($arResult['MIN_PRICE']['VALUE']): ?>
        <div class="container js-size-btn-box hidden" data-offer-id="<?= $iOfferID; ?>">

            <? if ($bCanBuy ) : ?>
            <div class="column-10 product-page__add-to-cart size-button-list">
				<label class="btn-desc btn-desc-cart  left-label" for="one-click-btn-<?= $arResult['ID'] ?>">Без регистрации</label>
				<input data-offer-id="<?= $iOfferID; ?>"
						id="one-click-btn-<?= $arResult['ID'] ?>"
                       class="half-button half-left js-one-click button button--primary button--outline button--block button--xxl"
						<?= !$bCanBuy ? ' disabled' : ''; ?>
                       type="submit"
                       value="Купить в 1 клик"
				/>				
                <input data-id="<?= $arResult['ID'] ?>"
                       id="buy-btn-<?= $arResult['ID'] ?>"
                       class="half-button half-right js-cart-btn button button--primary button--outline button--block button--xxl"
						<?= !$bCanBuy ? ' disabled' : ''; ?>
                       type="submit"
                       value="В корзину"
				/>   
            </div>
            <? endif; ?>

            <div class="column-10 product-page__add-to-cart">
                <label class="btn-desc btn-desc-reserved" for="reserved-btn-<?= $arResult['ID'] ?>">
                    Для самовывоза из розничного магазина
                </label>
                <input data-id="<?= $arResult['ID'] ?>"
                       id="reserved-btn-<?= $arResult['ID'] ?>"
                       class="js-reserved-btn button button--primary button--outline button--block button--xxl"
                    <?= !$bCanReserved ? ' disabled' : ''; ?>
                       type="button" value="Забрать в магазине">
            </div>

            <? if ($bCanBuy): ?>
              <? if (defined('BUY_IN_CREDIT') && BUY_IN_CREDIT === true): ?>
                <div class="column-10 product-page__links">                    
                  <a href="#" class="js-credit">Купить в кредит</a>
                </div>
              <? endif; ?>
            <? endif; ?>
        </div>
    <? endif; ?>
<? endforeach; ?>

<? if ($arResult['MIN_PRICE']['VALUE']): ?>
    <div class="container js-size-btn-box">
        <? if ($bCanBuyLeastOne ) : ?>
        <div class="column-10 product-page__add-to-cart size-button-list">
			<label class="btn-desc btn-desc-cart left-label " for="one-click-btn-<?= $arResult['ID'] ?>">Без регистрации</label>
			<input data-id="<?= $arResult['ID'] ?>"
				id="one-click-btn-<?= $arResult['ID'] ?>"
				class="half-button half-left js-cart-btn button button--primary button--outline button--block button--xxl"
				<?= !$bCanBuyLeastOne ? ' disabled' : ''; ?>
				type="submit"
				value="Купить в 1 клик"
			/>			
            <input data-id="<?= $arResult['ID'] ?>"
                   id="buy_btn"
                   class="half-button half-right js-cart-btn button button--primary button--outline button--block button--xxl"
                <?= !$bCanBuyLeastOne ? ' disabled' : ''; ?>
                   type="submit" value="В корзину">
        </div>
        <? endif; ?>

        <div class="column-10 product-page__add-to-cart">
            <label class="btn-desc btn-desc-reserved" for="reserved_btn">
                Для самовывоза из розничного магазина
            </label>
            <input data-id="<?= $arResult['ID'] ?>"
                   id="reserved_btn"
                   class="js-reserved-btn button button--primary button--outline button--block button--xxl"
                <?= !$bCanReservedLeastOne ? ' disabled' : ''; ?>
                   type="button" value="Забрать в магазине">
        </div>
    </div>
<? endif; ?>