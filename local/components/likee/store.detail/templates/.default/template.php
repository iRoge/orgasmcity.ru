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
/** @global CDatabase $DB */
?>

<script>
    $(function () {
        window.application.addUrl({
            'shopList': '<?= $APPLICATION->GetCurPage(); ?>?show_map=y'
        });
    });
</script>
<div class="clearfix"></div>
<div class="container-fluid shop-header">
    <div class="container col-md-7 padding-o">
        <div class="column-8 column-center">
            <div class="<? !empty($arResult['BELONG']) and print ' shop-header__container--with-bonus' ?>">

                <? if (!empty($arResult['ADDRESS'])) : ?>
                    <div class="shop-header__item">
                        <section class="shop-card__address"><?= $arResult['ADDRESS']; ?></section>
                    </div>
                <? endif; ?>

                <? if (!empty($arResult['PHONES'])) : ?>
                    <div class="shop-header__item shop-header__item--phones">
                        <? foreach ($arResult['PHONES'] as $sPhone) : ?>
                            <section class="shop-card__phones">
                                <span><i class="icon icon-phone"></i><a href="tel:<?= $sPhone; ?>"><?= $sPhone; ?></a></span>
                            </section>
                        <? endforeach; ?>
                    </div>
                <? endif; ?>

                <? if (!empty($arResult['SCHEDULE'])) : ?>
                    <div class="shop-header__item">
                        <div class="shop-card__work-time">
                            <span><i class="icon icon-clock"></i><?= $arResult['SCHEDULE']; ?></span>
                        </div>
                    </div>
                <? endif; ?>

                <? if (!empty($arResult['BELONG'])) : ?>
                    <div class="shop-header__item">
                        <?php if ($arResult['BELONG'] == 'f') :?>
                            <span class="shop-card__bonus shop-card__bonus--f">Дисконтная программа</span>
                        <?php else :?>
                            <a class="shop-card__bonus" href="/company_bonus/">Бонусная программа</a>
                        <?php endif;?>
                    </div>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>
<? if ($arResult['RESERV']) : ?>
<a href="/catalog/?set_filter=Y&storages_availability=<?=$arResult['ID']?>" style="border: 0px;font-size: 16px" class="button button--third button--large">Ассортимент магазина</a>
<? endif; ?>
<? if (!empty($arResult['PHOTO_SRC'])) : ?>
    <div class="container--no-padding">
        <div class="column-10">
            <div id="<?= $arResult['PHOTO_CLASS'] ?>" class="shop-photo-slider slick--no-inline">
                <? foreach ($arResult['PHOTO_SRC'] as $sPhoto) : ?>
                    <img src="<?= $sPhoto; ?>" alt="">
                <? endforeach; ?>
            </div>
        </div>
    </div>
<? endif; ?>


<div class="container--no-padding">
    <div class="column-10">
        <div class="shop-maps">
            <div class="shop-google-map <?= empty($arResult['SCHEME']) ? ' full-width' : '' ?>" style="min-height: 500px;">
                <div id="shop-map" class="shop-map"></div>
            </div>

            <? if (!empty($arResult['SCHEME']) && is_array($arResult['SCHEME'])) : ?>
                <div class="shop-inner-map">
                    <a href="<?= $arResult['SCHEME']['SRC']; ?>" class="in-popup">
                        <img src="<?= $arResult['SCHEME']['THUMB']; ?>" alt="Схема проезда">
                    </a>
                </div>
            <? endif; ?>
        </div>
    </div>
</div>

<div class="container show-more">
    <div class="column-2 column-center" style="width: 100%; text-align: center;margin: 70px 0;">
        <a href="<?= $arParams['PATH_TO_LISTSTORES']; ?>" class="btn-grey">
            К списку магазинов
        </a>
    </div>
</div>
