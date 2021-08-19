<?php

use Bitrix\Main\Localization\Loc;

global $DEVICE;
$counter = 0;
?>

<div class="advantages-section-wrapper">
    <h2 class="home-advantages-header"><?= Loc::getMessage('HOME_ADVANTAGES_BLOCK_TITLE') ?></h2>
    <div class="advantages-list-wrapper main">
        <?php foreach ($arResult['ITEMS'] as $item) {
            $counter++;
            ?>
            <a class="advantages-element col-lg-2 col-md-2 col-sm-2 col-xs-6" <?= !empty($item['CODE']) ? 'href="' . $item['CODE'] . '"' : '' ?>>
                <img width="100%" src="<?= $arResult['IMG_SOURCES'][$item['PROPERTIES']['IMG']['VALUE']] ?>"
                     alt="<?=$item['DETAIL_TEXT']?>">
            </a>
        <? } ?>
    </div>
</div>

