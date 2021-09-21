<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

?>

<div class="col-xs-12 brands-block-wrapper">
    <div class="main">
        <div class="brand-wrap col-xs-12">
            <?php if (!empty($arResult['BRANDS'])) : ?>
                <?php foreach ($arResult['BRANDS'] as $brand) : ?>
                <div class="in-vendor">
                     <a href="<?=$brand['SECTION_PAGE_URL'];?>" title="<?=$brand['NAME'];?>" style="text-decoration: none">
                        <?php if ($brand['PREVIEW_PICTURE']) { ?>
                            <div class="col-xs-12 card__img-box" style="padding: 0!important;">
                                <img src="<?=$brand['PREVIEW_PICTURE']?>" alt="<?=$brand['NAME'];?>" class="col-xs-12"/>
                            </div>
                            <div class="col-xs-12 text-in-vendor">
                                <span class="text-in-vendor-title"><?=$brand['NAME'];?></span>
                            </div>
                        <?php } else { ?>
                            <div class="col-xs-12 text-in-vendor">
                                <span class="vendor-no-img-text text-in-vendor-title"><?=$brand['NAME'];?></span>
                            </div>
                        <?php } ?>
                     </a>
                </div>
                <?php endforeach; ?>
                <div class="clear-blocks"></div>
            <?php else : ?>
                <div class="container column-center text--center" style="padding: 25px 0 45px; font-size: 1.35rem;">
                    <p>Бренды не найдены!</p>
                </div>
            <?php endif; ?>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
