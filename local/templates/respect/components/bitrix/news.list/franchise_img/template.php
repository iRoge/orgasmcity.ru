<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$this->setFrameMode(true);
if (!empty($arResult['RESPECT_CLASSIC_STYLE']) || !empty($arResult['RESPECT_NEW_STYLE'])) :
    ?>
    <section class="choice franchise-container">
        <div class="choiсe__head">
            <h2>
                Выбирайте тот стиль франшизы, который вам ближе
            </h2>
            <div class="choiсe__togglers">
                <button class="choiсe__toggle choiсe__toggle--classic active-toggle" id="choiсe-classic">Respect
                    Classic
                </button>
                <button class="choiсe__toggle choiсe__toggle--new" id="choiсe-new">Respect New</button>
            </div>
        </div>
        <div class="choiсe__carousel-wrapper">
            <div class="choiсe__carousel choiсe__carousel--active" id="classic-carousel">
                <div class="swiper-wrapper">
                    <? if (isset($arResult['RESPECT_CLASSIC_STYLE'])) : ?>
                        <? foreach ($arResult['RESPECT_CLASSIC_STYLE'] as $imgURL) : ?>
                            <a class="swiper-slide choiсe__fancybox-link" data-fancybox="classic-images"
                               href="<?= $imgURL ?>">
                                <img src="<?= $imgURL ?>" alt="" width="419" height="280">
                            </a>
                        <? endforeach ?>
                    <? endif ?>
                </div>
                <button class="slick-arrow carousel--prev classic-carousel--prev"></button>
                <button class="slick-arrow carousel--next classic-carousel--next"></button>
            </div>
            <div class="choiсe__carousel" id="new-carousel">
                <div class="swiper-wrapper">
                    <? if (isset($arResult['RESPECT_NEW_STYLE'])) : ?>
                        <? foreach ($arResult['RESPECT_NEW_STYLE'] as $imgURL) : ?>
                            <a class="swiper-slide choiсe__fancybox-link" data-fancybox="new-images"
                               href="<?= $imgURL ?>">
                                <img src="<?= $imgURL ?>" alt="" width="419" height="280">
                            </a>
                        <? endforeach ?>
                    <? endif ?>
                </div>
                <button class="slick-arrow carousel--prev new-carousel--prev"></button>
                <button class="slick-arrow carousel--next new-carousel--next"></button>
            </div>
        </div>
    </section>
<? endif ?>