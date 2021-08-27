<?php

if (isset($arResult["DETAIL_PICTURE"]['DESCRIPTION']) && $arResult["DETAIL_PICTURE"]['DESCRIPTION'] != '') {
    $arResult["DETAIL_PICTURE"]['LINK'] = $arResult["DETAIL_PICTURE"]['DESCRIPTION'];

    if (substr($arResult["DETAIL_PICTURE"]['LINK'], 0, 4) == 'http') {
        $arResult["DETAIL_PICTURE"]['TARGET'] = "_blank";
    }
}

if (strlen($arResult['ACTIVE_TO']) > 0) {
    $arResult['DISPLAY_ACTIVE_TO'] = CIBlockFormatProperties::DateFormat($arParams['ACTIVE_DATE_FORMAT'], MakeTimeStamp($arResult['ACTIVE_TO'], CSite::GetDateFormat()));
} else {
    $arResult['DISPLAY_ACTIVE_TO'] = '';
}

$cp = $this->__component;

if (is_object($cp)) {
    $cp->arResult['DETAIL_TEXT'] = empty($arResult['DETAIL_TEXT']) ? $arResult['PREVIEW_TEXT'] : $arResult['DETAIL_TEXT'];
    $cp->arResult['ENABLE_SUBSCRIBE'] = !empty($arResult['DISPLAY_PROPERTIES']['ENABLE_SUBSCRIBE']['VALUE_XML_ID']);
    $cp->arResult['ENABLE_CONTEST'] = !empty($arResult['DISPLAY_PROPERTIES']['ENABLE_CONTEST']['VALUE_XML_ID']);
    $cp->arResult['CONTEST_END'] = empty($arResult['PROPERTIES']['CONTEST_END']['VALUE']['TEXT']) ? '' : $arResult['PROPERTIES']['CONTEST_END']['~VALUE']['TEXT'];
    $cp->arResult['CONTEST_RULES'] = empty($arResult['PROPERTIES']['CONTEST_RULES']['VALUE']['TEXT']) ? '' : $arResult['PROPERTIES']['CONTEST_RULES']['~VALUE']['TEXT'];

    $cp->arResult['CONTEST_INSTA_RESULT_EMAILS'] = empty($arResult['PROPERTIES']['CONTEST_INSTA_RESULT_EMAILS']['VALUE']) ? '' : $arResult['PROPERTIES']['CONTEST_INSTA_RESULT_EMAILS']['~VALUE'];
    $cp->arResult['CONTEST_THANKYOU_TEXT'] = empty($arResult['PROPERTIES']['CONTEST_THANKYOU_TEXT']['VALUE']['TEXT']) ? '' : $arResult['PROPERTIES']['CONTEST_THANKYOU_TEXT']['~VALUE']['TEXT'];
    $cp->arResult['CONTEST_TYPE'] = ($arResult['PROPERTIES']['CONTEST_TYPE']['VALUE_XML_ID'] === 'Y');

    // Текст кнопки "принять участие"
    $cp->arResult['CONTEST_BTN_ENROLL_TEXT'] = empty($arResult['PROPERTIES']['CONTEST_BTN_ENROLL_TEXT']['VALUE']) ? 'Принять участие' : $arResult['PROPERTIES']['CONTEST_BTN_ENROLL_TEXT']['~VALUE'];
    // Показывать ли правила проведения конкурса
    $cp->arResult['CONTEST_RULES_SHOW'] = !!($arResult['PROPERTIES']['CONTEST_RULES_SHOW']['VALUE_XML_ID'] === 'Y');

    CModule::IncludeModule('iblock');

    if (!empty($arResult['PROPERTIES']['CONTEST_BTN_COLOR']['VALUE'])) {
        $rs = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => $arResult['PROPERTIES']['CONTEST_BTN_COLOR']['LINK_IBLOCK_ID'],
                "ID" => $arResult['PROPERTIES']['CONTEST_BTN_COLOR']['VALUE']
            ),
            false,
            ['nTopCount' => 1],
            ['ID', 'PROPERTY_COLOR']
        );

        $elem = $rs->Fetch();

        if (!empty($elem['PROPERTY_COLOR_VALUE'])) {
            $cp->arResult['CONTEST_BTN_COLOR'] = $elem['PROPERTY_COLOR_VALUE'];
        } else {
            $cp->arResult['CONTEST_BTN_COLOR'] = '';
        }
    } else {
        $cp->arResult['CONTEST_BTN_COLOR'] = '';
    }

    if (!empty($arResult['PROPERTIES']['CONTEST_BTN_TEXT_COLOR']['VALUE'])) {
        $rs = CIBlockElement::GetList(
            array(),
            array(
                "IBLOCK_ID" => $arResult['PROPERTIES']['CONTEST_BTN_TEXT_COLOR']['LINK_IBLOCK_ID'],
                "ID" => $arResult['PROPERTIES']['CONTEST_BTN_TEXT_COLOR']['VALUE']
            ),
            false,
            ['nTopCount' => 1],
            ['ID', 'PROPERTY_COLOR']
        );

        $elem = $rs->Fetch();

        if (!empty($elem['PROPERTY_COLOR_VALUE'])) {
            $cp->arResult['CONTEST_BTN_TEXT_COLOR'] = $elem['PROPERTY_COLOR_VALUE'];
        } else {
            $cp->arResult['CONTEST_BTN_TEXT_COLOR'] = '';
        }
    } else {
        $cp->arResult['CONTEST_BTN_TEXT_COLOR'] = '';
    }

    $cp->SetResultCacheKeys([
        'DETAIL_TEXT', 'ENABLE_SUBSCRIBE'
    ]);
}
