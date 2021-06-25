<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

?>

<div class="col-xs-12 padding-o">
    <div class="main">
        <div class="brand-wrap col-xs-12">
            <? if (!empty($arResult['BRANDS'])) : ?>
                <? foreach ($arResult['BRANDS'] as $brand) : ?>
                <div class="in-vendor">
                     <a href="<?= $brand['SECTION_PAGE_URL']; ?>" title="<?= $brand['NAME']; ?>" style="text-decoration: none">
                        <div class="col-xs-12 card__img-box" style="padding: 0!important;">
                            <img src="<?=$brand['PREVIEW_PICTURE'] ?? '/local/templates/respect/img/question.png'?>" alt="<?= $brand['NAME']; ?>" class="col-xs-12"/>
                        </div>
                        <div class="col-xs-12 text-in-vendor">
                            <h4 class="text-in-vendor-title"><?= $brand['NAME']; ?></h4>
                        </div>
                     </a>
                </div>
                <? endforeach; ?>
                <div class="clear-blocks"></div>
            <? else : ?>
                <div class="container column-center text--center" style="padding: 25px 0 45px; font-size: 1.35rem;">
                    <p>Бренды не найдены!</p>
                </div>
            <? endif; ?>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
