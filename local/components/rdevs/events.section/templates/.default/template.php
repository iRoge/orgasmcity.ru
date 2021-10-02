<h1 class="zagolovok zagolovok--catalog">
    <?
    $APPLICATION->ShowTitle(false);
    ?>
</h1>
<div class="col-xs-12 padding-o">
    <div class="main">
        <? if ($arResult['IS_AJAX']) {
            $APPLICATION->RestartBuffer();
        } ?>
        <div class="col-lg-12 js-event-container">
            <div class="event-container">
                <?
                $all = stripos($GLOBALS['GTM_PROPS']['PAGE_TYPE'], 'все события') !== false || stripos($GLOBALS['GTM_PROPS']['PAGE_TYPE'], 'весь блог') !== false;
                foreach ($arResult['EVENTS'] as $key => $arEvent) { ?>
                    <div class="event banner_item">
                        <a href="<?= $arEvent['PROPERTY_ELEMENT_LINK_VALUE'] ?: $arEvent['DETAIL_PAGE_URL'] ?>"
                           class="event-link">
                            <div class="event-block-1-2">
                                <div class="event-block-1-2-content">
                                    <img src="<?= $arEvent['PREVIEW_PICTURE'] ?>">
                                </div>
                            </div>
                            <div class="event-block-1-2">
                                <div class="event-block-1-2-content">
                                    <div class="event-bottom-container">
                                      <span style="font-size: 18px;font-weight: 600;"><?= $arEvent['NAME']; ?></span>
                                        <span style="color: red"> <?= $arEvent['DATE_END'] ? 'Акция завершена<br>' : '' ?></span>
                                        <span><?= $arEvent['DATE_STRING'] ?></span>
                                        <div class="js-event-text-box-wp" style="font-family: 'gilroyRegular';color: #000000;font-size: 16px;font-weight: 400;visibility: hidden">
                                            <div style="height: 100%" class="js-event-text-box" data-full-text="<?= $arEvent['PREVIEW_TEXT'] ?>"><?= $arEvent['PREVIEW_TEXT'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <? } ?>
            </div>
            <?= $arResult['NAV_STRING'] ?>
        </div>
        <? if ($arResult['IS_AJAX']) {
            exit;
        } ?>
    </div>
</div>
