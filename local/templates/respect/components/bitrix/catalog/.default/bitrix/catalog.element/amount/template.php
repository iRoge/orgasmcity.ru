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

CBitrixComponent::includeComponentClass('likee:order');

if (empty($arParams['COLOR']))
    $arParams['COLOR'] = key($arResult['MATRIX']);
?>

<script>
    BX.message({'RESERVED_STORES_LIST': '<?=json_encode($arResult['JSON_SHOPS'])?>'});
</script>

<form id="one-click-form" class="product-page product b-element-one-click js-reserv<? $arResult['NO_SIZES'] and print ' has-no-sizes'; ?>" action="/order/" method="post">

	<?= bitrix_sessid_post(); ?>
	<input type="hidden" name="DELIVERY" value="<?= LikeeOrder::DELIVERY_PICKUP_ID; ?>">
	<input type="hidden" name="DELIVERY_STORE_ID" value="">
	<input type="hidden" name="PAYMENT" value="<?= LikeeOrder::DEFAULT_PAYMENT_SYSTEM; ?>">
	<input type="hidden" name="action" value="create">
	<input type="hidden" name="USER_DESCRIPTION" value="Резервирование товара">
	<input type="hidden" name="RESERVATION" value="Y">


	<div class="product-preorder">	
		<header>
			<div class="product-preorder__title">Наличие в магазинах</div>
		</header>
		<main>
			<div class="container">
				<aside class="column-33 column-md-2">
				
				<?if (!empty($arResult['PICTURE']) && is_array($arResult['PICTURE'])):?>
					<a href="<?= $arResult['DETAIL_PICTURE']['SRC']; ?>" class="product-preorder__media">
						<img src="<?= $arResult['PICTURE']['SRC']; ?>" alt="<?= $arResult['PICTURE']['ALT']; ?>">
					</a>
				<?endif;?>
				
				<div class="product-preorder__short-info">
					<? if (!empty($arResult['NAME'])): ?>
					<div class="product-preorder__name">
						<?=$arResult['NAME']; ?>
					</div>
					<? endif; ?>
					<? if (!empty($arResult['ARTICLE'])): ?>
						<div class="product-preorder__sku">Арт: <?= $arResult['ARTICLE']; ?></div>
					<? endif; ?>
				</div>
				
				<div class="product-preorder__info">
					<?if (!empty($arResult['MIN_PRICE'])):?>
						<div class="product-preorder__cost" style="position: relative;">
							<?= $arResult['MIN_PRICE']['PRINT_DISCOUNT_VALUE']; ?>
							<? if ($arResult['MIN_PRICE']['VALUE'] > $arResult['MIN_PRICE']['DISCOUNT_VALUE']): ?>
								<div class="product__old-cost" style="top: 2em;">
									<?= $arResult['MIN_PRICE']['PRINT_VALUE']; ?>
								</div>
							<? endif; ?>
						</div>
					<?endif;?>
				
					<?if ($arResult['BONUS'] > 0):?>
						<div class="widget__bonus">
							<div class="widget__bonus-count">+<?= $arResult['BONUS']; ?></div>
							<div class="widget__bonus-text">бонусов</div>					
						</div>						
					<?endif;?>				
			
				</div>
				
				<div class="product-preorder__size">
					<label>Размеры</label>					

					<div class="size-selector size-selector--wrap js-size-selector">
						<? foreach ($arResult['SIZES'] as $sSize => $arSize): ?>
							<?
							$iOfferID = intval($arSize['OFFER_ID']);
                            $bCanReserved = $iOfferID > 0 && $arSize['CATALOG_AVAILABLE'] == 'Y' && \Likee\Site\Helpers\Catalog::productCanBeReserved($iOfferID, $arSize['STORES']);
							$sClass = '';
							if (!$bCanReserved)
								$sClass = 'missed';
							?>
							<a class="<?= $sClass; ?> js-reserve-select" data-offer-id="<?=$iOfferID;?>" ><?= $sSize; ?></a>
							
							<input
								type="radio"
								name="PRODUCTS[]"
								value="<?= $iOfferID; ?>"
								class="js-offer js-offer-<?= $iOfferID; ?>"
								data-quantity="<?=$arSize['QUANTITY']; ?>"
								<? if (!$bCanBuy): ?>disabled<? endif; ?>
							>					
							
							
						<? endforeach; ?>
						<div class="alert alert--danger js-offer-error" style="display: none;">
							<div class="alert-content">
								<i class="icon icon-exclamation-circle"></i>
								Выберите размер для резервирования
							</div>
						</div>
					</div>					
				</div>
				
				<!--<div class="product-preorder__form container">
					<div class="input-group">
						<input name="PROPS[FIO]" placeholder="ФИО" type="text" required>
					</div>
					<div class="input-group">
						<input type="email" name="PROPS[EMAIL]" placeholder="Email" required>
					</div>
					<div class="input-group">
						<input class="phone" type="text" name="PROPS[PHONE]" placeholder="Телефон" required>
					</div>
					<div class="alert alert--danger js-stores-error" style="display: none;">
						<div class="alert-content">
							<i class="icon icon-exclamation-circle"></i>
							Выберите магазин для резервирования
						</div>
					</div>
					<button form="one-click-form" class="js-preorder-submit button button--xxl button--primary button--outline">Резервировать</button>
				</div>-->			
				
				</aside>

				<article class="column-66 column-md-2">
					<div class="tabs tabs--shop js-tabs">
						<a data-target="#list" class="tabs-item active">Списком</a>
						<a data-target="#map" class="tabs-item ">На карте</a>
						<a data-target="#subway" class="tabs-item">На схеме метро</a>
					</div>
					<div class="container tabs-targets">
						<div id="list" class="active" data-init="list">
							<div class="preorder-list" id="reserved-shop-list">			
							
							</div>
						</div>
						<div id="map" data-init="map">							
							<div 
							class="shop-map--square js-shop-list-map shop-map"
							id="reserved-map"
							data-lat="<?= $arResult['LOCATION']['LAT'] ?>"
							data-lon="<?= $arResult['LOCATION']['LON'] ?>"
							>		
							</div>							
						</div>
						<div id="subway" class="subway-map" data-init="metro">
							<div class="preloader"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>						
						</div>
					</div>
				</article>
				
			</div>		
		</main>		
	</div>	
</form>
