<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
?>
<div class="categories col-xs-12">
    <div class="main">
        <? if ($arResult['ITEMS']) : ?>
            <? foreach ($arResult['ITEMS'] as $arItem) : ?>
            <div class="col-sm-4 col-xs-12">
                <div class="cat-one in-event" style="padding: 0!important;">
                    <a href="<?= $arItem['PROPERTIES']['ACTIVE_DIRECT_LINK']['VALUE'] ? $arItem['PROPERTIES']['DIRECT_LINK']['VALUE'] : $arItem['DETAIL_PAGE_URL'] ?>">
                        <img class="col-xs-12" src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" style="height: 355px">
                    </a>
                    <div class="text-in-event">
                        <?if (date('d.m.Y H:i:s', time()) >= $arItem['DATE_ACTIVE_TO']) :?>
                            <p>Акция завершена</p>
                        <?endif;?>
                        <? if ($arItem['ACTIVE_FROM'] && $arItem['DATE_ACTIVE_TO']) :?>
                            <span><?=$arItem['ACTIVE_FROM'] . ' - ' . $arItem['DATE_ACTIVE_TO']?></span>
                        <? elseif ($arItem['ACTIVE_FROM']) :?>
                            <span><?=$arItem['ACTIVE_FROM']?></span>
                        <? elseif ($arItem['DATE_ACTIVE_TO']) :?>
                            <span><?='Завершение акции - ' . $arItem['DATE_ACTIVE_TO']?></span>
                        <? endif; ?>
                        <a href="<?= $arItem['PROPERTIES']['ACTIVE_DIRECT_LINK']['VALUE'] ? $arItem['PROPERTIES']['DIRECT_LINK']['VALUE'] : $arItem['DETAIL_PAGE_URL'] ?>">
                            <h4 class="text-in-magaz-title"><?=$arItem['NAME']?></h4>
                        </a>
                        <p><?= $arItem['PROPERTIES']['TEXT']['VALUE']['TEXT']?></p>
                    </div>
                </div>
            </div>
            <? endforeach; ?>
            <? if (!empty($arResult['NAV_STRING'])) : ?>
                <?= $arResult['NAV_STRING']; ?>
            <? endif; ?>
        <? else : ?>
            <div class="page-massage">В данном разделе записи отсутствуют</div>
        <? endif; ?>
    </div>
</div>
