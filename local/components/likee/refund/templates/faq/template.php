<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

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
/** @var LikeeRefundComponent $component */
$this->setFrameMode(true);
?>

<? if (!empty($arResult['ITEMS'])) : ?>
    <div class="container container--gutters nopadding-x">
        <div class="column-10 pre-1 column-md-2">
            <? foreach ($arResult['ITEMS'] as $iKey => $arItem) : ?>
                <?
                $bOpen = !empty($arItem['PROPERTY_OPEN_VALUE']);
                ?>
                <div class="container mb-3 mb-phone-2 nopadding-x">
                    <a href="#refund-<?= $arItem['ID']; ?>"
                       class="dropdown-toggle<? if ($bOpen) :
                            ?> dropdown-toggle--expanded<?
                                             endif; ?>">
                        <?= $arItem['NAME'] ?>
                    </a>
                    <div id="refund-<?= $arItem['ID']; ?>"<? if (!$bOpen) :
                        ?> style="display:none;"<?
                                    endif; ?>>
                        <div class="container">
                            <div class="column-10 column-md-2 refund-content"><?= $arItem['PREVIEW_TEXT'] ?></div>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>