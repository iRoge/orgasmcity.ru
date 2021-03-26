<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;
if (! empty($arResult['CSS'])) {
    $APPLICATION->AddHeadString("<style type=\"text/css\">\n{$arResult['CSS']}</style>");
}