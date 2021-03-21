<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Fire-2.ru");
$APPLICATION->SetPageProperty("keywords", "секс шоп, сексшоп, секс-игрушки");
$APPLICATION->SetPageProperty("title", "Огонь 2.0 - готовый интернет-магазин интим-товаров и классического белья.");
$APPLICATION->SetTitle("Fire-2.ru");
?>
<? CAdvBanner_all::SetCurUri($APPLICATION->GetCurPageParam('root_section='.$_COOKIE['root_section'], ['root_section'], true));?>
<div class="tt-offset-35 container-indent">
		<div class="container">
<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	".default", 
	array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => "/include_areas/index_top.php",
	)
);?>
		</div>
	</div>
	<div class="container-indent0">
		<div class="container">
			<div class="row flex-sm-row-reverse tt-layout-promo-box">
				<div class="col-sm-12 col-md-6">
					<div class="row">
<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"opt_tt", 
	array(
		"TYPE" => "index_top_small",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "Y",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "opt_tt",
		"QUANTITY" => "2",
		"root_section" => $_COOKIE["root_section"]
	)
);
?>
						<div class="col-sm-12">
<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"opt_tt", 
	array(
		"TYPE" => "index_top_mid",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "Y",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "opt_tt",
		"QUANTITY" => "1",
		"root_section" => $_COOKIE["root_section"]
	)
);
?>
<?='<!--LCP-->'?>
						</div>
					</div>
				</div>
				<div class="col-sm-12 col-md-6">
<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"opt_tt", 
	array(
		"TYPE" => "index_top_big",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "Y",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "opt_tt",
		"QUANTITY" => "1",
		"root_section" => $_COOKIE["root_section"]
	)
);
?>
				</div>
			</div>
		</div>
	</div>
	<div class="container-indent1">
		<div class="container container-fluid-custom-mobile-padding">
			<div class="tt-block-title text-left">
				<h2 class="h1 tt-title">КАТАЛОГ ТОВАРОВ</h2>
			</div>
<?
$APPLICATION->IncludeComponent(
	"fire:catalog.section.list", 
	"opt_index", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "5",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "2"
	),
	false
);
?>
		</div>
	</div>
	<div class="container-indent1">
		<div class="container container-fluid-custom-mobile-padding">
			<div class="tt-block-title text-left">
				<h2 class="tt-title">СПЕЦИАЛЬНОЕ ПРЕДЛОЖЕНИЕ</h2>
			</div>
<?
$APPLICATION->IncludeComponent(
	"fire:catalog.section.list", 
	"opt_bestseller", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "5",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"COUNT_ELEMENTS" => "Y",
		"TOP_DEPTH" => "1",
		"COMPONENT_TEMPLATE" => "opt_bestseller",
		"DISPLAY_COMPARE" => "Y",
		"DISPLAY_WISHLIST" => "Y"
	),
	false
);
?>
		</div>
	</div>
	<div class="container-indent">
		<div class="container">
			<div class="row tt-layout-promo-box">
<?$APPLICATION->IncludeComponent(
	"bitrix:advertising.banner", 
	"opt_tt", 
	array(
		"TYPE" => "index_mid",
		"CACHE_TYPE" => "A",
		"NOINDEX" => "Y",
		"CACHE_TIME" => "36000000",
		"COMPONENT_TEMPLATE" => "opt_tt",
		"QUANTITY" => "2"
	)
);
?>
			</div>
		</div>
	</div>
	<div class="container-indent">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 col-md-4">
					<h6 class="tt-title-sub">НОВИНКИ</h6>
<?
global $arrFilter;
$arrFilter = ['PROPERTY_new_VALUE' => 1, 'SECTION_ID' => $_COOKIE['root_section'], 'INCLUDE_SUBSECTIONS' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'];
?>

<?$APPLICATION->IncludeComponent(
	"fire:catalog.items", 
	"opt_small", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "rand",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "Y",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "catalog",
		"IMG_HEIGHT" => "194",
		"IMG_WIDTH" => "155",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "size",
			1 => "color",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "size",
			1 => "color",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "86400",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "3",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE" => array(
			0 => "vendor",
		),
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "blue",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?>
				</div>
				<div class="divider visible-xs"></div>
				<div class="col-sm-6 col-md-4">
					<h6 class="tt-title-sub">ТОВАРЫ СО СКИДКОЙ</h6>
<?
global $arrFilter;
$arrFilter = ['!PROPERTY_sale' => false, 'SECTION_ID' => $_COOKIE['root_section'], 'INCLUDE_SUBSECTIONS' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'];
?>

<?$APPLICATION->IncludeComponent(
	"fire:catalog.items", 
	"opt_small", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "rand",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "Y",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "catalog",
		"IMG_HEIGHT" => "194",
		"IMG_WIDTH" => "155",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "size",
			1 => "color",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "size",
			1 => "color",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "86400",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "3",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE" => array(
			0 => "vendor",
		),
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "blue",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?>
				</div>
				<div class="divider visible-sm visible-xs"></div>
				<div class="col-sm-6 col-md-4">
					<h6 class="tt-title-sub">ХИТЫ ПРОДАЖ</h6>
<?
global $arrFilter;
$arrFilter = ['PROPERTY_bestseller_VALUE' => 1, 'SECTION_ID' => $_COOKIE['root_section'], 'INCLUDE_SUBSECTIONS' => 'Y', 'SECTION_GLOBAL_ACTIVE' => 'Y'];
?>
<?$APPLICATION->IncludeComponent(
	"fire:catalog.items", 
	"opt_small", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADD_PICT_PROP" => "-",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_TO_BASKET_ACTION" => "ADD",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BACKGROUND_IMAGE" => "-",
		"BASKET_URL" => "/personal/cart/",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"CONVERT_CURRENCY" => "N",
		"DETAIL_URL" => "",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"ELEMENT_SORT_FIELD" => "rand",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_ORDER2" => "desc",
		"FILTER_NAME" => "arrFilter",
		"HIDE_NOT_AVAILABLE" => "Y",
		"IBLOCK_ID" => "5",
		"IBLOCK_TYPE" => "catalog",
		"IMG_HEIGHT" => "194",
		"IMG_WIDTH" => "155",
		"LABEL_PROP" => "-",
		"LINE_ELEMENT_COUNT" => "3",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"OFFERS_CART_PROPERTIES" => array(
			0 => "size",
			1 => "color",
		),
		"OFFERS_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "5",
		"OFFERS_PROPERTY_CODE" => array(
			0 => "size",
			1 => "color",
			2 => "",
		),
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "desc",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "86400",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "3",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		"PRODUCT_DISPLAY_MODE" => "N",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => array(
		),
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "",
		"PRODUCT_SUBSCRIPTION" => "N",
		"PROPERTY_CODE" => array(
			0 => "vendor",
		),
		"SECTION_CODE" => "",
		"SECTION_ID" => "",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_URL" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SET_BROWSER_TITLE" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "Y",
		"SHOW_ALL_WO_SECTION" => "Y",
		"SHOW_CLOSE_POPUP" => "N",
		"SHOW_DISCOUNT_PERCENT" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"TEMPLATE_THEME" => "blue",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "N"
	),
	false
);?>
				</div>
			</div>
		</div>
	</div>
	<div class="container-indent">
<?
global $arrFilter;
$arrFilter = ['!PREVIEW_PICTURE'=>false];
?>
<?$APPLICATION->IncludeComponent(
	"fire:items.list", 
	"opt_producers", 
	array(
		"CACHE_TIME" => "86400",
		"CACHE_TYPE" => "A",
		"COMPONENT_TEMPLATE" => "opt_producers",
		"DISPLAY_PANEL" => "Y",
		"IBLOCK_ID" => "4",
		"IBLOCK_TYPE" => "catalog",
		"SORT_BY" => "RAND",
		"SORT_ORDER" => "ASC",
		//"ELEMENT_ID" => $_REQUEST["ELEMENT_ID"],
		"LIMIT" => "8",
		"DETAIL_URL" => "",
		"FILTER_NAME" => "arrFilter",
		"CACHE_FILTER" => "Y"
	),
	false
);?>
	</div>
	<div class="container-indent">
		<div class="container">
			<div class="tt-block-title">
				<h2 class="tt-title">ПОСЛЕДНИЕ ЗАПИСИ БЛОГА</h2>
				<div class="tt-description">ОБЗОРЫ НОВИНОК РЫНКА И АНОНСЫ РАСПРОДАЖ</div>
			</div>
<?$APPLICATION->IncludeComponent(
	"fire:blog.line", 
	"opt", 
	array(
		"ACTIVE_DATE_FORMAT" => "j M Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"IBLOCK_ID" => "9",
		"IBLOCK_TYPE" => "blog",
		"NEWS_COUNT" => "3",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "DESC",
		"SORT_ORDER2" => "ASC",
		"COMPONENT_TEMPLATE" => "opt"
	),
	false
);?>
		</div>
	</div>
	<div class="container-indent">
		<div class="container-fluid">
			<div class="tt-block-title">
				<h2 class="tt-title"><a href="#">@ ПОДПИШИСЬ</a> НА НАС</h2>
				<div class="tt-description">INSTAGRAM</div>
			</div>
			<div class="row">
<?/*$APPLICATION->IncludeComponent(
	"fire:blog.list", 
	"opt_gallery", 
	array(
		"ACTIVE_DATE_FORMAT" => "j M Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"DETAIL_URL" => "",
		"IBLOCK_ID" => "16",
		"IBLOCK_TYPE" => "catalog",
		"NEWS_COUNT" => "6",
		"SORT_BY1" => "ID",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "RAND",
		"SORT_ORDER2" => "ASC",
		"COMPONENT_TEMPLATE" => "opt_gallery",
		"FILTER_NAME" => "",
		"FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"CHECK_DATES" => "Y",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_FILTER" => "Y",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SET_TITLE" => "N",
		"SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N",
		"SET_META_DESCRIPTION" => "N",
		"SET_LAST_MODIFIED" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"PARENT_SECTION" => "",
		"PARENT_SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Фото",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "Y",
		"MESSAGE_404" => ""
	),
	false
);*/?>
			</div>
		</div>
	</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>