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

<div class="bonus-block-wrapper">
    <div class="bonus-block">
        <span class="bonus-block-text">
            Общая сумма оплаченных заказов
        </span>
        <span class="bonus-block-sum-text">
             = <?=$arResult['USER_ORDER_SUM']?> ₽
        </span>
    </div>
    <div class="bonus-block">
        <span class="bonus-block-text">
            Ваша постоянная клубная скидка
        </span>
        <span class="bonus-block-discount-text">
            <?=$arResult['USER_BONUS']?>%
        </span>
    </div>
</div>

<div class="bonus-how-work-block-wrapper">
    <div class="bonus-block-how-work">
        Как работает наша бонусная программа?
    </div>
</div>

<div class="bonus-block-text-info-wrapper">
    <div class="bonus-block-text-info">
        Вы имеете возможность снизить цену благодаря нашей бонусной программе. <br>
        Регистрируйтесь на сайте и получайте постоянную скидку за осуществление суммарных покупок на определенную сумму:<br>
        5000 рублей = 1% постоянная скидка<br>
        10000 рублей = 3% постоянная скидка<br>
        20000 рублей = 6% постоянная скидка<br>
        30000 рублей = 10% постоянная скидка<br>
        Вашу текущую общую сумму покупок и размер скидки вы можете посмотреть в личном кабинете.<br>
        Все скидки автоматически суммируются и показываются в каталоге.<br>
    </div>
</div>
