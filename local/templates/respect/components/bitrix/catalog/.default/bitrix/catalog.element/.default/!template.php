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

if (empty($arParams['COLOR']))
    $arParams['COLOR'] = key($arResult['MATRIX']);
?>

<script>

    $(function () {
        window.application.addUrl({
            'shopList': '<?= $APPLICATION->GetCurPage(); ?>?action=get_amount_json',
            'shopListPage': '<?= $APPLICATION->GetCurPage(); ?>?action=get_amount',
            'product': '<?= $APPLICATION->GetCurPage(); ?>?action=get_one_click'
        });
    });

    BX.message({
        'CATALOG_ELEMENT_TEMPLATE_PATH': '<?= $templateFolder; ?>',
        'IS_PARTNER': '<?= \Likee\Site\User::isPartner() ? 'Y' : 'N'; ?>',
        'ONE_CLICK_URL': '<?= $APPLICATION->GetCurPage(); ?>?action=get_one_click'
    });
</script>

<? if (!empty($arResult)): ?>
	<div class="product-page">
	<div class="wrap col-sm-6" style="padding: 15px;">
		<div id="example5" class="slider-pro">
			<div class="sp-slides">
				<? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto): ?>
					<div class="sp-slide">
						<img class="sp-image" src="<?= $arPhoto['SRC']; ?>" data-src="<?= $arPhoto['SRC']; ?>" alt="" />
					</div>
				<? endforeach; ?>
			</div>
			<div class="sp-thumbnails">
				<? foreach ($arResult['PHOTOS'] as $iKey => $arPhoto): ?>
					<div class="sp-thumbnail">
						<div class="sp-thumbnail-image-container">
							<img class="sp-thumbnail-image" src="<?= $arPhoto['THUMB']; ?>" alt="" />
						</div>
					</div>
				<? endforeach; ?>
			</div>
		</div>
		
		<? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
			<div class="col-sm-12 hidden-xs info--" style="margin-right: 20px;margin-top: 50px">
				<? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty): ?>
					<? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])): ?>
						<div class="p3">
							<div class="l3"><?= $arProperty['NAME']; ?></div>
							<div class="r3"><?= $arProperty['VALUE']; ?></div>
						</div>
					<? endif; ?>
				<? endforeach; ?>
				<div class="opisanie-after">Описание</div>
			</div>
		<? endif; ?>
	</div>
			  
	<div class="col-sm-6 right-cartochka col-xs-12">
		<? foreach ($arResult['LABELS'] as $sClass => $arLabel) : ?>
			<div class="sale-sela <?= $sClass ?>"><?= $arLabel['NAME'] ?></div>
        <? endforeach; ?>
		
		<? if (!empty($arResult['ARTICLE'])): ?>
			<p class="grey-cart">Арт. <?= $arResult['ARTICLE']; ?></p>
		<? endif; ?>
		<h2 class="h2-cart"><?= $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']; ?></h2>	
		<?
		$bDiscount = $arResult['MIN_PRICE']['VALUE'] > $arResult['MIN_PRICE']['DISCOUNT_VALUE'];
		$bShowBonusInfo = $bDiscount || 'red' == $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'];
		?>
		<? if ($bDiscount): ?>
			<p class="price"><?= $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?></p>
			<? !empty($arResult['MIN_PRICE']['DISCOUNT_PCT']) and print '<p class="percents">-'.$arResult['MIN_PRICE']['DISCOUNT_PCT'].'%</p>'; ?>
			<p class="old-price"><?= $arResult['MIN_PRICE']['PRINT_VALUE']; ?></p>
		<? else: ?>
			<p class="price"><?= $arResult['MIN_PRICE']['PRINT_VALUE']; ?></p>
			<? !empty($arResult['MIN_PRICE']['DISCOUNT_PCT']) and print '<p class="percents">-'.$arResult['MIN_PRICE']['DISCOUNT_PCT'].'%</p>'; ?>
		<? endif; ?>
				
		<? if ($arResult['BONUS'] > 0): ?>
			<div class="plus-cart-bns" style="margin-top: 15px;">
				<p class="plus-cartochka"><b>+<?= $arResult['BONUS']; ?></b> бонусов</p>
				<div style="clear: both"></div>
			</div>
		<? endif; ?>
        <p class="grey-under">
			*<?= 'red' == $arResult['MIN_PRICE']['DISCOUNT_SEGMENT'] ? 'бонусная программа не действует' : 'по условиям бонусной программы' ?> 
			*цены на сайте могут отличаться от цен в магазинах
		</p>
		<hr class="hr-cartochka" />
				
		<? if ($arResult['CATALOG_AVAILABLE'] == 'Y' && !empty($arResult['SIZES'])): ?>
			<h3 class="after-hr-cart">Размер</h3>
			
			<form method="post" name="name" style="width: 100%;" class="form-after-cart js-action-form">
				<input type="hidden" name="action" value="ADD2BASKET">
				
				<div style="display: block; width: 100%;" class="js-size-selector">
					<? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
						<?
						$iOfferID = intval($arSize['OFFER_ID']);
						$bCanBuy = $arSize['CAN_BUY'];
						$bCanReserved = $arSize['CAN_RESERVED'];
									
						$sClass = '';
						if (!$bCanBuy && !$bCanReserved)
							$sClass = 'missed';
						?>
						<? if ($bCanBuy || $bCanReserved): ?>
							<div class="top-minus <?= $sClass; ?>">
								<input type="radio" name="id" 
										id="offer-<?= $iOfferID; ?>" 
										class="radio1 js-offer js-offer-<?= $iOfferID; ?>" 
										value="<?= $iOfferID; ?>" />
								<label for="offer-<?= $iOfferID; ?>" data-offer-id="<?= $iOfferID; ?>"><?= $sSize; ?></label>
							</div>
						<? endif; ?>
					<? endforeach; ?>
					
					<div style="clear: both"></div>
				</div>
				
				<div class="alert alert--danger js-offer-error" style="display: none;">
					<div class="alert-content">
						<i class="icon icon-exclamation-circle"></i>
                        Выберите размер для продолжения заказа
					</div>
                </div>
				
				<? if ($arResult['CATALOG_AVAILABLE'] == 'Y'): ?>
					<? if ('Y' == $arResult['AVAILABILITY_IN_REGION']): ?>
						<?if(isset($arResult['SECTION_SIZES_TAB']) && !$arResult['NO_SIZES']):?>
							<div class="sizes-popup-area">
								<a class="sizes-popup" href="#">Руководство по размерам</a>
                                <div class="sizes-popup-block" style="display:none;">
									<div class="tab-size-block">
										<?=$arResult['SECTION_SIZES_TAB'];?>
                                    </div>
								</div>
							</div>
                        <?endif;?>
						<? include 'buy_block.php'; ?>
					<? endif; ?>
                <? endif; ?>
			
			</form>

			<hr class="hr-cartochka" />
		<? endif; ?>
				
		<? if (!empty($arResult['COLORS'])): ?>
			<h3>Другие цвета</h3>
			<? foreach ($arResult['COLORS'] as $arColor): ?>
				<a href="<?= $arColor['DETAIL_PAGE_URL']; ?>" class="a-others">
					<div style="padding: 7px; display: inline-block; margin-right: 10px; margin-top: 10px;">
						  <img src="<?= $arColor['FILE']; ?>" alt="<?= $arColor['NAME']; ?>" />
					</div>
				</a>
			<? endforeach; ?>
		<? endif; ?>
	</div>

	<? if (!empty($arResult['DISPLAY_PROPERTIES'])): ?>
		<div class="hidden-lg hidden-md hidden-sm col-xs-12 info--" style="margin-left: 20px;margin-top: 50px">
			<? foreach ($arResult['DISPLAY_PROPERTIES'] as $arProperty): ?>
				<? if (!empty($arProperty['VALUE']) && !is_array($arProperty['VALUE'])): ?>
					<div class="p3">
						<div class="l3"><?= $arProperty['NAME']; ?></div>
						<div class="r3"><?= $arProperty['VALUE']; ?></div>
					</div>
				<? endif; ?>
			<? endforeach; ?>
		</div>
	<? endif; ?>

	</div>
<? else: ?>
    <div class="container">
        <div class="column-8 pre-1">
            <div class="alert alert-danger">Элемент не найден!</div>
        </div>
    </div>
<? endif; ?>