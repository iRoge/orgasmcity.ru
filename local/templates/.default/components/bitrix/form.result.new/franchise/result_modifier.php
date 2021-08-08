<?php
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

const HL_BLOCK_ID = 211;
if ($arResult["isFormErrors"] == 'N' && !empty($_REQUEST['formresult']) && $_REQUEST['formresult'] == 'addok') {
    \Bitrix\Main\Loader::includeModule('iblock');

    //Загружаю ответы вебформы
    CForm::GetResultAnswerArray(
        intval($_REQUEST['WEB_FORM_ID']),
        $arrColumns,
        $arrAnswers,
        $arrAnswersVarname,
        array("RESULT_ID" => intval($_REQUEST['RESULT_ID']))
    );

    $franchiseAnswers = [];
    foreach ($arrAnswersVarname[intval($_REQUEST['RESULT_ID'])] as $answerKey => $answerValue) {
        $answerValue = $answerValue[0];
        $franchiseAnswers[$answerKey] = $answerValue['USER_TEXT'];
    }

    // Получение E-mail адресов
    CModule::IncludeModule('highloadblock');
    function GetEntityDataClass($HlBlockId)
    {
        if (empty($HlBlockId) || $HlBlockId < 1) {
            return false;
        }
        $hlblock = HLBT::getById($HlBlockId)->fetch();
        $entity = HLBT::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();
        return $entity_data_class;
    }
    $entity_data_class = GetEntityDataClass(HL_BLOCK_ID);
    $rsData = $entity_data_class::getList(array(
        'select' => array('UF_EMAIL'),
        'filter' => array('UF_NAME' => 'Франчайзинг')
    ));
    $emails = $rsData->fetch();

    // Создаю событие для отправки письма
    CEvent::SendImmediate(
        "FRANCHISE_FORM_SUBMIT",
        SITE_ID,
        array_merge([
            'FRANCHISE_RESULT_EMAIL' => $emails,
            ], $franchiseAnswers),
        'Y'
    );
}
