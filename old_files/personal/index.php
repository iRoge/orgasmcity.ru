<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Персональный раздел");
?>
<div class="tt-shopping-layout">
	<div class="tt-wrapper">
		<h3 class="tt-title">ИСТОРИЯ ЗАКАЗОВ</h3>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.personal.order.list",
	"opt",
	Array(
		"ACTIVE_DATE_FORMAT" => "d.m.Y",
		"ALLOW_INNER" => "N",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"DEFAULT_SORT" => "STATUS",
		"DISALLOW_CANCEL" => "N",
		"HISTORIC_STATUSES" => array(0=>"AC",1=>"C",2=>"DE",3=>"F",),
		"NAV_TEMPLATE" => "",
		"ONLY_INNER_FULL" => "N",
		"ORDERS_PER_PAGE" => "20",
		"PATH_TO_BASKET" => "",
		"PATH_TO_CANCEL" => "order/cancel/#ID#/",
		"PATH_TO_CATALOG" => "order/",
		"PATH_TO_COPY" => "",
		"PATH_TO_DETAIL" => "order/detail/#ID#/",
		"PATH_TO_PAYMENT" => "order/payment/",
		"REFRESH_PRICES" => "N",
		"RESTRICT_CHANGE_PAYSYSTEM" => array(0=>"AC",1=>"C",2=>"DE",3=>"DS",4=>"F",5=>"IC",6=>"P",7=>"PR",8=>"RS",9=>"SC",10=>"SP",11=>"ZE",12=>"ZS",),
		"SAVE_IN_SESSION" => "N",
		"SET_TITLE" => "N",
		"STATUS_COLOR_AC" => "gray",
		"STATUS_COLOR_C" => "gray",
		"STATUS_COLOR_DE" => "gray",
		"STATUS_COLOR_DS" => "gray",
		"STATUS_COLOR_F" => "gray",
		"STATUS_COLOR_IC" => "gray",
		"STATUS_COLOR_IR" => "gray",
		"STATUS_COLOR_N" => "green",
		"STATUS_COLOR_P" => "yellow",
		"STATUS_COLOR_PR" => "gray",
		"STATUS_COLOR_PSEUDO_CANCELLED" => "red",
		"STATUS_COLOR_RS" => "gray",
		"STATUS_COLOR_SC" => "gray",
		"STATUS_COLOR_SP" => "gray",
		"STATUS_COLOR_WC" => "gray",
		"STATUS_COLOR_WP" => "gray",
		"STATUS_COLOR_Z" => "gray",
		"STATUS_COLOR_ZE" => "gray",
		"STATUS_COLOR_ZS" => "gray"
	)
);?>
<a href="order/" class="btn btn-border">Перейти на страницу заказов</a>
	</div>
	<div class="tt-wrapper">
		<h3 class="tt-title">ЛИЧНЫЕ ДАННЫЕ</h3>
		 <?$APPLICATION->IncludeComponent(
	"fire:main.profile",
	"opt_text",
Array()
);?> <a href="profile/" class="btn btn-border">Изменить данные или пароль</a>
	</div>
</div>
 <br><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");?>