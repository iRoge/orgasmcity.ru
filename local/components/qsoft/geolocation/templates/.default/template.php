<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;
$this->addExternalJS("/local/templates/respect/js/select2/select2.min.js");
$this->addExternalJS("/local/templates/respect/js/select2/select2.ru.min.js");
$this->addExternalCss("/local/templates/respect/js/select2/select2.min.css");
?>
<div class="col-sm-5 col-xs-8 from">
    <div class="tooltip-window" style="display: <?= $LOCATION->isStranger ? 'flex' : 'none' ?>">
        <p class="tooltip-window__text">Информация о доставке будет отображаться для региона</p>
        <div class="tooltip-window__city"><?= $arResult['LOCATION_NAME'] ?></div>
        <div class="tooltip-window__controls">
            <span class="tooltip-window__button tooltip-window__button--ok">Да, всё верно</span>
            <span class="tooltip-window__button tooltip-window__button-js tooltip-window__button--no">Выбрать другой</span>
        </div>
    </div>
    <span class="user-region">Ваш регион доставки: </span>
    <img class="location-icon" src="<?= SITE_TEMPLATE_PATH ?>/img/placeholder.png"/>
    <span class="current-locality"><?= $arResult['LOCATION_NAME'] ?></span>
</div>

<?php $this->SetViewTarget('geolocation_popup'); ?>
<div class="geoposition">
    <div class="geoposition__close cls-mail-div" data-action="close" title="Закрыть"></div>
    <form class="geoposition__form" method="post">
        <span class="geoposition__heading">Ваш регион доставки</span>
        <select id="geo_location_search" name="geo_location_search" placeholder="Населенный пункт" style="width:100%"></select>
        <input type="hidden" id="geo_location_code" name="geo_location_code">
        <div class="geoposition__wrapper">
            <div class="geoposition__set-city-auto">
                <span class="geoposition__link">Определить автоматически</span>
            </div>
            <button class="geoposition__button geoposition__button--ok">Запомнить выбор</button>
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
        <div class="country_container">
            <hr>
            <ul class="country_list">
                <li>
                    <a href="https://respect-shoes.by/?utm_source=respect_ru&utm_medium=referral&utm_campaign=okno_gorodov">Беларусь</a>
                </li>
                <li>
                    <a href="https://respect-shoes.kz/?utm_source=respect_ru&utm_medium=referral&utm_campaign=okno_gorodov">Казахстан</a>
                </li>
                <li>
                    <a href="https://respect-shoes.com.ua/?utm_source=respect_ru&utm_medium=referral&utm_campaign=okno_gorodov">Украина</a>
                </li>
            </ul>
        </div>
    </form>
</div>
<?php $this->EndViewTarget(); ?>
