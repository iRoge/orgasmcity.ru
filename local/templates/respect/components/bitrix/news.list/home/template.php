<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

$this->setFrameMode(true);
?>
<? if (!empty($arResult['ITEMS'])): ?>
<div class="our-history col-xs-12">
	<div class="main">
		<h2 class="zagolovok">Наша история</h2>
		<div class="col-xs-12">
			<div id="wrap">
				<? foreach ($arResult['ITEMS'] as $arItem): ?>
					<?
					$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
					$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'));
					?>
					<a id="<?= $this->GetEditAreaId($arItem['ID']); ?>" href="<?= $arItem['DETAIL_PAGE_URL']; ?>" class="a-bez-un">
						<div class="in-news">
							<div class="col-xs-12" style="padding: 0!important;">
								<div class="news-sel"><?= $arItem['DISPLAY_ACTIVE_FROM']; ?></div>
								<img src="<?= SITE_TEMPLATE_PATH; ?>/img/news1.png" class="col-xs-12"/>
							</div>
							<div class="text-in-news col-xs-12">
								<h4><?= $arItem['NAME']; ?></h4>
								<p>Небольшой превью текст, в котором мы рассказываем о новости.</p><br />
							</div>
						</div>
					</a>
				<? endforeach; ?>
				<a class="a-bez-un" style="visibility: hidden; height: 1px;"><div class="in-news"></div></a>
				<div class="clear-blocks"></div>
			</div>
			<div style="width: 100%; text-align: center; margin-top: 30px;">
				<a href="<?= $arResult['ITEMS'][0]['LIST_PAGE_URL']; ?>" class="btn-grey">Все новости</a>
			</div>
		</div>
	</div>
</div>	
<? endif; ?>
