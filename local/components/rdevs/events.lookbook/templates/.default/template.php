<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
global $APPLICATION;
?>

<script>
    let currentSlideNum = <?= $arResult['CURRENT_SLIDE_NUM'] ?>;
    let countSlide = <?= $arResult['COUNT_SLIDE'] ?>;
</script>

<div style="display: flex;flex-direction: row">

    <div class="slider-prev"></div>
    <div class="event-content">
        <div class="lookbook-title js-add-slider">
            <? // цикл по разворотам
            $i = 0;
            foreach ($arResult['LOOKBOOK_2PAGES'] as $twoPagesNum => $arTwoPages) : ?>
                <div class="text-slide">
                    <h1 class="zagolovok zagolovok--catalog" data-slide-title-num="<?= $i++; ?>">
                        <? $i - 1 == $arResult['CURRENT_SLIDE_NUM'] ? $APPLICATION->ShowTitle(false) : '' ?>
                    </h1>
                </div>
                <div class="fake-slide" data-slide-title-num="<?= $i++; ?>"></div>
            <? endforeach; ?>
        </div>
        <div class="lookbook-desktop js-add-slider">
            <? // цикл по разворотам
            $i = 0;
            foreach ($arResult['LOOKBOOK_2PAGES'] as $twoPagesNum => $arTwoPages) : ?>
                <? // цикл по страницам
                foreach (['LEFT', 'RIGHT'] as $page) : ?>
                    <? if (!empty($arTwoPages[$page])) : ?>
                        <div data-page-num="<?= $i++ ?>" class="<?= $arTwoPages['ONE_PAGE'] ? 'text-slide' : '' ?>">
                            <div style="display: flex;flex-direction: row;" <?= $arTwoPages['ONE_PAGE'] ? 'class="one-slide-full"' : '' ?> >
                                <div>
                                    <? if ($arTwoPages[$page]['MULTI_LINKS']) : ?>
                                        <div class="stock-banner__wrapper">
                                            <img class="js-lookbook--img stock-banner__img"
                                                 src="<?= $arTwoPages[$page]['IMG']['SRC'] ?>" alt="">
                                            <? foreach ($arTwoPages[$page]['MULTI_LINKS'] as $arLink) : ?>
                                                <a class="product-link" <?= !empty($arLink['LINK']) ? 'href="' . $arLink['LINK'] . '"' : '' ?>
                                                   style="<?= $arLink['STYLE'] ?>; outline: none">
                                                    <div class="product-link-hint">
                                                        <span class="hint-caption"><?= !empty($arLink['MESS']) ? $arLink['MESS'] : $arLink['TEXT'] ?></span><br>
                                                    </div>
                                                </a>
                                            <? endforeach ?>
                                        </div>
                                    <? elseif (!empty($arTwoPages[$page]['LINK'])) : ?>
                                        <a href="<?= $arTwoPages[$page]['LINK']; ?>" class="slides-item">
                                            <img class="js-lookbook--img stock-banner__img"
                                                 src="<?= $arTwoPages[$page]['IMG']['SRC'] ?>" alt="">
                                        </a>
                                    <? else : ?>
                                        <div class="slides-item">
                                            <img class="js-lookbook--img stock-banner__img"
                                                 src="<?= $arTwoPages[$page]['IMG']['SRC'] ?>" alt="">
                                        </div>
                                    <? endif; ?>
                                </div>
                            </div>
                        </div>
                    <? else : ?>
                        <div data-page-num="<?= $i++ ?>" class="fake-slide">
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            <? endforeach; ?>
        </div>

        <div class="lookbook-nav js-add-slider">
            <? // цикл по разворотам
            foreach ($arResult['LOOKBOOK_2PAGES'] as $twoPagesNum => $arTwoPages) : ?>
                <div>
                    <div style="display: flex;flex-direction: row; padding:0 5px;">
                        <? // цикл по страницам
                        $onePage = false;
                        if (empty($arTwoPages['LEFT']['IMG']['SRC_PREVIEW']) || empty($arTwoPages['RIGHT']['IMG']['SRC_PREVIEW'])) {
                            $onePage = true;
                        }
                        foreach (['LEFT', 'RIGHT'] as $page) :?>
                            <? if (!empty($arTwoPages[$page]['IMG']['SRC_PREVIEW'])) : ?>
                                <img style="width: auto;" <?= ($onePage && !empty($arTwoPages[$page]['IMG']['SRC_PREVIEW'])) ?'class="slide-one-center"':''?> src="<?= $arTwoPages[$page]['IMG']['SRC_PREVIEW'] ?>" alt="">
                            <? endif; ?>
                        <? endforeach; ?>
                    </div>
                </div>
                <div></div>
            <? endforeach; ?>
        </div>

        <div class="lookbook-text js-add-slider">
            <? // цикл по разворотам
            $i = 0;
            foreach ($arResult['LOOKBOOK_2PAGES'] as $twoPagesNum => $arTwoPages) : ?>
                <div class="text-slide">
                    <span data-slide-text-num="<?= $i++; ?>"><?= $i - 1 == $arResult['CURRENT_SLIDE_NUM'] ? $arTwoPages['SEO_TEXT'] : '' ?></span>
                </div>
                <div class="fake-slide" data-slide-text-num="<?= $i++; ?>"></div>
            <? endforeach; ?>
        </div>
    </div>
    <div class="slider-next"></div>
</div>