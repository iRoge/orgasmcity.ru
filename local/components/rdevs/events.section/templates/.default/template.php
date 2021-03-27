<h1 class="zagolovok zagolovok--catalog">
    <?
    $APPLICATION->ShowTitle(false);
    ?>
</h1>
<div class="col-xs-12 padding-o">
    <div class="main">

        <div class="event-menu">
            <? $parentLink = $arResult['SECTIONS']['MENU'][0];
            unset($arResult['SECTIONS']['MENU'][0]);?>
            <a href="<?= $parentLink['LINK'] ?>"
               class="event-menu-link <?= $parentLink['EXTERNAL_ID'] == $arParams['CURRENT_SECTION'] ? 'active' : '' ?>"
            ><?= $parentLink['NAME'] ?></a>
            <div class="event-menu-child">
                <? foreach ($arResult['SECTIONS']['MENU'] as $arSection) : ?>
                    <a href="<?= $arSection['LINK'] ?>"
                       class="event-menu-link <?= $arSection['EXTERNAL_ID'] == $arParams['CURRENT_SECTION'] ? 'active' : '' ?>"
                    ><?= $arSection['NAME'] ?></a>
                <? endforeach; ?>
            </div>
        </div>
        <? if ($arResult['IS_AJAX']) {
            $APPLICATION->RestartBuffer();
        } ?>
        <div class=" js-event-container">
            <div class="event-container">
                <?
                $all = stripos($GLOBALS['GTM_PROPS']['PAGE_TYPE'], 'все события') !== false || stripos($GLOBALS['GTM_PROPS']['PAGE_TYPE'], 'весь блог') !== false;
                foreach ($arResult['EVENTS'] as $key => $arEvent) :
                    $dataProps = 'data-rblock-id="' . $arEvent['ID'] .'" '; // id
                    $dataProps .= 'data-rblock-name="' . $arEvent['IB_NAME'] . ($arEvent['GTM_TYPE'] && !$all ? ' | ' . $arEvent['GTM_TYPE'] : '') . '" ';  // Тип
                    //$dataProps .= 'data-prod-brand="Respect" ';  // Бренд
                    $dateActive = $arParams['IBLOCK_CODE'] == 'blog' ? $arEvent['UF_DATE_ACTIVE_FROM'] : $arEvent['DATE_ACTIVE_FROM'];
                    $dataProps .= 'data-prod-creative="' . $arEvent['NAME'] . ($dateActive ? ' | ' . $dateActive : '' ) . '" ';  // Название и начало активности
                    $dataProps .= 'data-prod-position="' . ($key + 1) . '" ';  // Номер
                    ?>
                    <div class="event banner_item" <?= $dataProps ?>>
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
                                      <span style="font-size: 18px;
font-weight: 600;"><?= $arEvent['NAME']; ?></span>
                                        <span style="color: red"> <?= $arEvent['DATE_END'] ? 'Акция завершена<br>' : '' ?></span>
                                        <span><?= $arEvent['DATE_STRING'] ?></span>

                                        <div class="js-event-text-box-wp" style="font-family: 'firalight';

color: #4e4e4e;font-size: 16px;
font-weight: 400;visibility: hidden">
                                            <div style="height: 100%" class="js-event-text-box" data-full-text="<?= $arEvent['PREVIEW_TEXT'] ?>"><?= $arEvent['PREVIEW_TEXT'] ?></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>


                <? endforeach; ?>


            </div>
            <?= $arResult['NAV_STRING'] ?>
        </div>
        <? if ($arResult['IS_AJAX']) {
            exit;
        } ?>
    </div>
</div>
