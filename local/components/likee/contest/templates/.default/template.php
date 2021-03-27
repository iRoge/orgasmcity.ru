<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<!--noindex-->
<div class="container">
    <div class="column-8 column-center">
        <div class="contest contest--<?=($arResult['STATS']['TOTAL'] ? 'process' : 'opened')?><?=($arResult['AUTOSTART'] ? ' contest--autostart' : '')?>">
            <div class="contest__stat">
                <h3>Ваша статистика участия в конкурсе</h3>
                <div class="contest__stat-result">Всего просмотрено: <span class="contest__stat-value js-contest-total"><?=$arResult['STATS']['TOTAL']?></span></div>
                <div class="contest__stat-result">Всего понравилось:  <span class="contest__stat-value js-contest-liked"><?=$arResult['STATS']['LIKES']?></span></div>
                <div class="contest__stat-result">Всего не понравилось:  <span class="contest__stat-value js-contest-disliked"><?=$arResult['STATS']['DISLIKES']?></span></div>
                <div class="contest__stat-result">Осталось оценить:  <span class="contest__stat-value js-contest-remains"><?=$arResult['STATS']['REMAINS']?></span></div>
                
                <?if ($arResult['ITEMS']) :?>
                    <button type="button" class="button button--primary button--xl button--bigger js-contest-go">Продолжить</button>
                <?endif;?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" data-skip-moving="true">
window.RESPECT_CONTEST = <?=CUtil::PhpToJSObject($arResult['ITEMS'])?>;
window.RESPECT_CONTEST_START = <?=($arResult['STATS']['TOTAL'] ? 'false' : 'true')?>;
</script>
<!--/noindex-->