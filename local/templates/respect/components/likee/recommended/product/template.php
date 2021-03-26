<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
	<div class="bestsellers col-xs-12">
		<div class="main">
			<h2 class="zagolovok">Вам может понравиться</h2>
			<div class="bestsel col-xs-12">
				<? foreach ($arResult['ITEMS'] as $arItem): ?>
					<div style="outline: none;">
						<div class="col-xs-12">
							<? foreach ($arItem['LABELS'] as $sClass => $arLabel) : ?>
								<div class="<?= strtolower($arLabel['NAME']) ?>-sel"><?= $arLabel['NAME'] ?></div>
							<? endforeach; ?>
							<div class="bestsel-one">
								<div class="photo-bestsel">
									<a href="<?= $arItem['DETAIL_PAGE_URL']; ?>">
										<img src="<?= $arItem['PICTURE']['SRC']; ?>" />
									</a>
								</div>
								<div class="about-bestsel">
									<p class="name-sel"><?= $arItem['NAME']; ?></p>
									<div class="info-sel">
										<?
										$bDiscount = $arItem['MIN_PRICE']['VALUE'] > $arItem['MIN_PRICE']['DISCOUNT_VALUE'];
                                        ?>
										<? if ($bDiscount): ?>
											<p class="price-sel"><?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></p>
                                        <? else: ?>
											<p class="price-sel"><?= $arItem['MIN_PRICE']['PRINT_VALUE']; ?></p>
                                        <? endif; ?>
										
										<? if (!empty($arItem['CAN_BUY'])): ?>
											<a data-id="<?= $arItem['ID'] ?>" title="Купить с доставкой" class="shortcut js-add-to-basket" href="<?= $arItem['DETAIL_PAGE_URL']; ?>">
												<img src="<?= SITE_TEMPLATE_PATH; ?>/img/cart-bestsel.png">
											</a>
                                        <? endif; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
                <? endforeach; ?>
			</div>
		</div>
	</div>
<? endif; ?>