<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
if (!empty($arResult["ITEMS"])) :
    ?>
    <div class=" franchise-page production__container">
        <div class="franchise-container">

            <h2 class="production__title">Продукция бренда</h2>
        </div>
    </div>
    <div class="franchise-page production__container ">
        <div class="franchise-container production__carousel-container">
            <div class="production__carousel swiper-wrapper">
                <? foreach ($arResult["ITEMS"] as $arItem) : ?>
                    <div class="production__slide swiper-slide">
                        <? if (is_array($arItem["DETAIL_PICTURE"])) : ?>
                            <div class="production__carousel-img-container">
                                <img src="<?= $arItem["DETAIL_PICTURE"]["SRC"] ?>" width="719" height="665">
                            </div>
                        <? endif ?>
                        <div class="production__carousel-item">
                            <h3><?= $arItem["NAME"] ?></h3>
                            <?= $arItem["DETAIL_TEXT"]; ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </div>
            <div class="slick-dots">
            </div>
        </div>
    </div>
<? endif ?>