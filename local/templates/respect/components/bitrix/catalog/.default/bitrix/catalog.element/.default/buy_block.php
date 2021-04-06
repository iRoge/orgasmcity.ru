<div id="wrap" class="btns-wrap">
    <div id="js-toggle-delivery-ok" class="catalog-element-btn-container <?= empty($arResult['RESTS']['DELIVERY']) ? 'js-button-hide' : '' ?>">
        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
               id="one-click-btn"
               class="js-one-click cartochka-blue blue-btn"
               type="button"
               value="Купить в 1 клик"/>
        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
               id="buy-btn"
               class="js-cart-btn cartochka-orange yellow-btn js-cart-redirect"
               type="button"
               value="Добавить в корзину"/>
    </div>
    <div id="js-toggle-delivery-error" class="catalog-element-btn-container <?= !empty($arResult['RESTS']['DELIVERY']) ? 'js-button-hide' : '' ?>">
        <input class="cartochka-transparent cartochka-transparent--decoration"
               type="button"
               value="Недоступно для доставки"
               disabled/>
    </div>
    <div id="js-toggle-reserve-ok" class="catalog-element-btn-container <?= empty($arResult['RESTS']['RESERVATION']) ? 'js-button-hide' : '' ?>">
        <input data-offer-id="<?= $arResult['SINGLE_SIZE'] ? $arResult['SINGLE_SIZE'] : "" ?>"
               id="reserved-btn"
               class="js-reserved-btn cartochka-border cartochka-transparent"
               type="button"
               value="Забрать в магазине"/>
    </div>
    <div id="js-toggle-reserve-error" class="catalog-element-btn-container <?= !empty($arResult['RESTS']['RESERVATION']) ? 'js-button-hide' : '' ?>">
        <input class="cartochka-transparent cartochka-transparent--decoration"
               type="button"
               value="Доступно только в интернет-магазине"
               disabled/>
    </div>
</div>