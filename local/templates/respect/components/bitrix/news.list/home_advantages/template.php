<?php

use Bitrix\Main\Localization\Loc;

global $DEVICE;
$counter = 0;
$isMobile = $DEVICE->isMobile() || $DEVICE->isTablet();
?>

<section class="advantages-section-wrapper">
    <h2 class="home-advantages-header"><?= Loc::getMessage('HOME_ADVANTAGES_BLOCK_TITLE') ?></h2>
    <? if (!$isMobile) : ?>
    <div class="grid">
    <? endif; ?>
        <?
        foreach ($arResult['ITEMS'] as $item) :
            $counter++;
            ?>
            <? if ($isMobile && ($counter % 3) == 1) : ?>
            <div class="grid">
            <? endif; ?>
            <a <?= !empty($item['CODE']) ? 'href="' . $item['CODE'] . '"' : '' ?>">
            <figure class="wrapper padded-container">
                <img class="img centered" src="<?= $arResult['IMG_SOURCES'][$item['PROPERTIES']['IMG']['VALUE']] ?>"
                     alt="">
                <figcaption class="advantages-text"><?= $item['DETAIL_TEXT'] ?></figcaption>
            </figure>
            </a>
            <? if ($isMobile && ($counter % 3) == 0) : ?>
            </div>
            <? endif; ?>
        <? endforeach; ?>
        <? if (!$isMobile) : ?>
    </div>
        <? endif; ?>
</section>

