<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;
?>
<div class="col-sm-5 col-xs-9 from">
    <div class="tooltip-window" style="display: <?= $LOCATION->isStranger ? 'flex' : 'none' ?>">
        <p class="tooltip-window__text">Информация о доставке будет отображаться для региона</p>
        <div class="tooltip-window__city"><?= $arResult['LOCATION_NAME'] ?></div>
        <div class="tooltip-window__controls">
            <span class="tooltip-window__button tooltip-window__button--ok">Да, всё верно</span>
            <span class="tooltip-window__button tooltip-window__button-js tooltip-window__button--no">Выбрать другой</span>
        </div>
    </div>
    <span class="user-region">Ваш регион <br> для доставки: </span>
    <svg version="1.0" xmlns="http://www.w3.org/2000/svg"
         width="31px" height="31px" viewBox="0 0 31.000000 31.000000"
         preserveAspectRatio="xMidYMid meet">

        <g transform="translate(0.000000,31.000000) scale(0.100000,-0.100000)"
           fill="#000000" stroke="none">
            <path d="M114 271 c-17 -10 -34 -30 -38 -44 -7 -29 14 -93 50 -155 20 -33 29
-41 38 -32 22 18 70 133 70 168 0 62 -65 97 -120 63z m70 -2 c54 -25 52 -87
-6 -187 l-22 -40 -19 30 c-10 17 -27 53 -38 81 -23 57 -15 96 24 115 29 14 32
14 61 1z"/>
            <path d="M130 226 c-27 -33 18 -72 48 -42 13 13 14 20 6 34 -15 23 -38 26 -54
8z m45 -22 c0 -10 -8 -20 -18 -22 -22 -4 -35 27 -16 39 20 12 34 5 34 -17z"/>
        </g>
    </svg>
    <span class="current-locality"><?= $arResult['LOCATION_NAME'] ?></span>
</div>

<?php $this->SetViewTarget('geolocation_popup'); ?>
<div class="geoposition">
    <div class="geoposition__close cls-mail-div" data-action="close" title="Закрыть">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <line x1="1.93934" y1="20.4462" x2="20.4461" y2="1.93942" stroke="black" stroke-width="3"/>
            <line x1="2.06066" y1="1.93934" x2="20.5674" y2="20.4461" stroke="black" stroke-width="3"/>
        </svg>
    </div>
    <form class="geoposition__form" method="post">
        <span class="geoposition__heading">Укажите ваш регион доставки</span>
        <select id="geo_location_search" name="geo_location_search" placeholder="Населенный пункт"
                style="width:100%"></select>
        <input type="hidden" id="geo_location_code" name="geo_location_code">
        <div class="geoposition__wrapper">
            <button class="geoposition__button geoposition__set-city-auto">Попробовать Автоопределение</button>
            <button class="geoposition__button geoposition__button--ok">Запомнить выбор региона</button>
        </div>
        <?php if (!empty($arResult['POPULAR_LOCALITIES'])) : ?>
            <div class="geoposition__default-cities">Популярные города</div>
            <div class="geoposition__lists-wrapper">
                <?php foreach ($arResult['POPULAR_LOCALITIES'] as $arColumn) : ?>
                    <ul class="geoposition__list">
                        <?php foreach ($arColumn as $arLocality) : ?>
                            <li class="geoposition__city" id="<?= $arLocality['location_code'] ?>">
                                <?= $arLocality['name'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </form>
</div>
<?php $this->EndViewTarget(); ?>
