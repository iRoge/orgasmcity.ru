<?php use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var \CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var array $templateData */
/** @var \CBitrixComponent $component */
$this->setFrameMode(true);
$showTimer = strtotime(date('d.m.Y H:i:s')) <= strtotime($arResult['DATE_TO']);
if ($showTimer) { ?>
    <div class="countdown-wrapper">
        <div
                class="countdown"
                data-date="<?=$arResult['ARRAY_DATE_TO']['day']?>-<?=$arResult['ARRAY_DATE_TO']['month']?>-<?=$arResult['ARRAY_DATE_TO']['year']?>"
                data-time="<?=$arResult['ARRAY_DATE_TO']['hour'] ?: '00'?>:<?=$arResult['ARRAY_DATE_TO']['minute'] ?: '00'?>:<?=$arResult['ARRAY_DATE_TO']['second'] ?: '00'?>"
        >
            <span class="countdown-title">До конца акции:</span>
            <div class="day"><span class="num"></span><span class="word"></span></div>
            <div class="hour"><span class="num"></span><span class="word"></span></div>
            <div class="min"><span class="num"></span><span class="word"></span></div>
            <div class="sec"><span class="num"></span><span class="word"></span></div>
        </div>
    </div>
<?php } ?>
<div class="action-closed-wrapper" <?=$showTimer ? 'hidden' : ''?>>
    <span class="action-closed-span">Акция завершена!</span>
</div>