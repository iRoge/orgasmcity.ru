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

<?if($arResult['ITEM']['ID']):?>
    <div class="tender-text"><?=$arResult['ITEM']['DETAIL_TEXT']?></div>
    <?if(sizeof($arResult['ITEM']['FILES'])):?>
        <div class="tender-files">
            <?foreach($arResult['ITEM']['FILES'] as $file):?>
                <div class="tender-files-each">
                    <a class="ico-file ico-<?=$file['FORMAT']?>" href="<?=$file['SRC']?>" download title="Скачать файл"></a>
                    <a class="text-file" href="<?=$file['SRC']?>" download title="Скачать файл"><?=$file['ORIGINAL_NAME']?></a>
                    <div class="clear"></div>
                </div>
            <?endforeach;?>
        </div>
    <?endif;?>
    <div class="tender-button">
        <a class="button button--primary js-tender-form" href="#" data-tender-name="<?=$arResult['ITEM']['NAME']?>" data-tender-id="<?=$arResult['ITEM']['ID']?>">Оформить заявку</a>
    </div>
<?endif;?>
