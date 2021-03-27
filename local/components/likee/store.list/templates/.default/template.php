<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
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

if (strlen($arResult['ERROR_MESSAGE']) > 0) {
    ShowError($arResult['ERROR_MESSAGE']);
}
?>
<div class="col-xs-12 magaz padding-o">
    <div class="main">
        <script>
            BX.message({'RESERVED_STORES_LIST': '<?=json_encode($arResult['JSON_SHOPS'])?>'});
        </script>

        <div class="col-xs-12 shop-page-block <? $APPLICATION->ShowProperty('TITLE_CLASS', ''); ?>">
            <div class="main">
                <? $APPLICATION->IncludeComponent(
                    "bitrix:breadcrumb",
                    "",
                    array(
                        "PATH" => "",
                        "SITE_ID" => "s1",
                        "START_FROM" => "0"
                    )
                ); ?>
                <h1 class="zagolovok zagolovok--catalog"><? $APPLICATION->ShowTitle(false); ?> 
                    <? $APPLICATION->IncludeComponent(
                        'qsoft:geolocation',
                        'shops',
                        array(
                            'CACHE_TYPE' => 'A',
                            'CACHE_TIME' => 31536000,
                        )
                    ); ?>
                </h1>
            </div>
        </div>
        <div class="korpus col-xs-12">
            <div class="!tabs tabs--center tabs--shop js-tabs">
                <?php
                $displayMetro = $arParams['LOCATION']['REGION'] === 'Москва и область';
                $size = $displayMetro ? '4' : '6';
                ?>
                <a data-target="#list" data-block="#wrap" class="tabs-item active col-xs-<?= $size ?>">Список</a>
                <a data-target="#map" data-block="#map" class="tabs-item col-xs-<?= $size ?>">Карта</a>
                <?php if ($displayMetro) { ?>
                    <a data-target="#metro" data-block="#metro" class="tabs-item col-xs-<?= $size ?>">Метро</a>
                <?php } ?>
            </div>
            <div class="!col-xs-12">
                <div class="top-tab col-xs-12">
                    <form id="shop-filter-list" class="form--search js-shop-search">
                        <? if (!empty($arResult['STATIONS'])) : ?>
                            <div class="col-sm-6 col-xs-12">
                                <select name="metro_id" class="col-xs-12">
                                    <? if (!empty($arResult['STATIONS'])) : ?>
                                        <option value="0">Выберите станцию метро</option>
                                        <? foreach ($arResult['STATIONS'] as $arStation) :?>
                                            <option value="<?= $arStation; ?>"
                                                <?= $arParams['METRO_ID'] === $arStation ? 'selected' : ''?>>
                                                <?= $arStation; ?>
                                            </option>
                                        <? endforeach; ?>
                                    <? else : ?>
                                        <option value="0">Станции метро отсутствуют</option>
                                    <? endif; ?>
                                </select>
                            </div>
                        <? endif; ?>
                        <div class="col-sm-<?= empty($arResult['STATIONS']) ? '12' : '6' ?> col-xs-12">
                            <input type="text" name="store_name" class="col-xs-12" placeholder="Поиск по названию, адресу, метро"
                                   value="<?= $arParams['STORE_NAME']; ?>"/>
                        </div>
                    </form>
                </div>
                <div id="wrap">
                    <? include 'stores.php' ?>
                    <div class="clear-blocks"></div>
                </div>
                <div style="clear: both"></div>
            </div>
            <div>
                <div class="top-tab col-xs-12" id="map">
                    <div class="shop-map shop-map--square"></div>
                    <div class="spacer--2"></div>
                </div>
                <div style="clear: both"></div>
            </div>
            <div class="metro_width">
                <div id="metro" class="top-tab col-xs-12 column-8 column-center subway-map">
                    <div class="preloader">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
    </div>
</div>
