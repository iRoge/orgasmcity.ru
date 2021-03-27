<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])): ?>
        <div class="column-10 pre-1 column-md-2">
            <? foreach ($arResult['ITEMS'] as $iKey => $arItem): ?>
                <div class="container mb-3 mb-phone-2">
                    <a href="#refund-<?= $arItem['ID']; ?>"
                       class="tenders__link dropdown-toggle bold">
                        <?= $arItem['NAME'] ?>
                    </a>
                    <div id="refund-<?= $arItem['ID']; ?>" style="display:none;">
                            <div class="tender-section"><?= $arItem['DESCRIPTION'] ?></div>
                            <div class="column-10 column-md-2 refund-content">
                                <?if(sizeof($arItem['LIST'])):?>
                                    <ul class="tender-list">
                                        <? foreach ($arItem['LIST'] as $n => $item): ?>
                                            <li>
                                                <a href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a>
                                            </li>
                                        <?endforeach;?>
                                    </ul>
                                <?endif;?>
                            </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
<? endif; ?>