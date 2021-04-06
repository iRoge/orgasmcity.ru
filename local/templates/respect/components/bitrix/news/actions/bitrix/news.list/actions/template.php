<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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


$bxajaxid = CAjax::GetComponentID($component->__name, $component->__template->__name, "");
?>
<div class="cnt_<?= $bxajaxid ?>">
<? if ($arResult['FIRST_ITEM']): ?>
    <?
    $arItem = $arResult['FIRST_ITEM'];
    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
    // by nable 30.08.2018
    // <div class="container container--no-padding">
    ?>
    <div class="container--no-padding">
        <div class="column-10">
			<?if ($arItem['SLIDER']): ?>
			<div class="slider">
				<div rel="slider" class="slides">
					<?foreach ($arItem['SLIDER'] as $arSliderItem):  ?>
						<a style="background-image: url(<?=$arSliderItem['SRC']; ?>)" href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="slides-item" <?if(isset($arItem['TARGET'])):?> target="<?=$arItem['TARGET']?>" <?endif;?> ></a>
					<?endforeach; ?>
				</div>
			</div>
			<?else:?>
				<a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="banner" <?if(isset($arItem['TARGET'])):?> target="<?=$arItem['TARGET']?>" <?endif;?> >
					<img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['PREVIEW_PICTURE']['ALT']; ?>" width="100%">
				</a>			
			<?endif;?>
        </div>
    </div>
<? endif; ?>
<? if (!empty($arResult['ITEMS'])): ?>
	<div>
		<div class="column-10">
			<? $iCounter = 1; ?>
			<? foreach ($arResult["ITEMS"] as $iKey => $arItem): ?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
				?>
				<? if ($iCounter == 3)://третий элемент одиночный ?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<div class="">
						<div class="column-10">							
							<a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="banner" <?if(isset($arItem['TARGET'])):?> target="<?=$arItem['TARGET']?>" <?endif;?> >
								<img width="100%" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['PREVIEW_PICTURE']['ALT']; ?>">
							</a>
						</div>
					</div>
					<? $iCounter = 1;
					continue; ?>
				<? else: //остальные елементы по два в ряд ?>
					<? if ($iCounter == 1): ?>
						<div class="">
					<? endif; ?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
					?>
					<div class="column-5 column-md-2 <?if ($iCounter%2==0):?>padding-left<?else:?>padding-right<?endif;?>" title="<?= $iCounter ?>">
						<a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="banner" <?if(isset($arItem['TARGET'])):?> target="<?=$arItem['TARGET']?>" <?endif;?> >
							<img width="100%" src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['PREVIEW_PICTURE']['ALT']; ?>">
						</a>
					</div>
					<? if ($iCounter == 2 || $iKey == count($arResult["ITEMS"]) - 1)://если это второй или последний элемент ?>
						</div>
					<? endif; ?>
				<? endif; ?>
				<? $iCounter++; ?>
			<? endforeach; ?>
		</div>
	</div>
<? endif; ?>
</div>

<? if ($arParams["DISPLAY_BOTTOM_PAGER"] && $arResult["NAV_RESULT"]->nEndPage > 1 && $arResult["NAV_RESULT"]->NavPageNomer < $arResult["NAV_RESULT"]->nEndPage): ?>    
    <!--<div class="container">
        <div class="column-8 column-center text--center">
            <?= $arResult["NAV_STRING"] ?>
        </div>
    </div>-->
	<div class="container show-more" id="btn_<?= $bxajaxid ?>">
		<div class="column-4 pre-3 column-md-2">
			<a 
				data-ajax-id="<?= $bxajaxid; ?>"
				data-show-more="<?= $arResult["NAV_RESULT"]->NavNum ?>"
				data-next-page="<?= ($arResult["NAV_RESULT"]->NavPageNomer + 1) ?>"
				data-max-page="<?= $arResult["NAV_RESULT"]->nEndPage ?>"
				data-append="true"
				class="button button--xxl button--transparent button--block js-show-more"
			>
				Смотреть все
			</a>
		</div>
	</div>
<? endif; ?>
