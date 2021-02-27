<?php

use Qsoft\Helpers\IBlockHelper;

if (empty($_GET['url'])) {
	exit;
}

$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__DIR__));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $arrFilter;

$arrFilter = [];
$arrFilter['ACTIVE'] = 'Y';

// фильтр по элементам с адресом или пустым адресом (глобальный)
$arrFilter[] = [
	"LOGIC" => "OR",
	['=PROPERTY_URL' => $_GET['url']],
	['=PROPERTY_URL' => false],
];

// фильтр по свойсту показа группам пользователей
if ($USER->IsAuthorized()) {
	$arrFilter[] = [
		"LOGIC" => "OR",
		['PROPERTY_USERS_VALUE' => 'Авторизованным'],
		['PROPERTY_USERS_VALUE' => 'Всем'],
	];
} else {
	$arrFilter[] = [
		"LOGIC" => "OR",
		['PROPERTY_USERS_VALUE' => 'Неавторизованным'],
		['PROPERTY_USERS_VALUE' => 'Всем'],
	];
}

try {
    $iblockId = IBlockHelper::getIBlockId('POPUP_ADS');

	$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"popup-banner",
		Array(
			"ACTIVE_DATE_FORMAT" => "d.m.Y",
			"ADD_SECTIONS_CHAIN" => "N",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"AJAX_OPTION_HISTORY" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"CACHE_FILTER" => "N",
			"CACHE_GROUPS" => "Y",
			"CACHE_TIME" => "36000000",
			"CACHE_TYPE" => "A",
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"DISPLAY_BOTTOM_PAGER" => "Y",
			"DISPLAY_DATE" => "Y",
			"DISPLAY_NAME" => "Y",
			"DISPLAY_PICTURE" => "Y",
			"DISPLAY_PREVIEW_TEXT" => "Y",
			"DISPLAY_TOP_PAGER" => "N",
			"FIELD_CODE" => array("ID", "IBLOCK_ID", "NAME", "DETAIL_PICTURE"),
			"FILTER_NAME" => "arrFilter",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"IBLOCK_ID" => $iblockId,
			"IBLOCK_TYPE" => "CONTENT",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"INCLUDE_SUBSECTIONS" => "N",
			"MESSAGE_404" => "N",
			"NEWS_COUNT" => "10",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => ".default",
			"PAGER_TITLE" => "Новости",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"PREVIEW_TRUNCATE_LEN" => "",
			"PROPERTY_CODE" => array("DURATION", "LINK", "URL", ""),
			"SET_BROWSER_TITLE" => "N",
			"SET_LAST_MODIFIED" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_STATUS_404" => "N",
			"SET_TITLE" => "N",
			"SHOW_404" => "N",
			"SORT_BY1" => "SORT",
			"SORT_BY2" => "ACTIVE_FROM",
			"SORT_ORDER1" => "ASC",
			"SORT_ORDER2" => "DESC"
		)
	);
} catch (\Exception $e) {

}