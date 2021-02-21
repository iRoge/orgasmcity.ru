<?

use Bitrix\Highloadblock as HL; 
use Bitrix\Main\Entity; 

AddEventHandler('main', 'OnBeforeEventSend', Array("SiteFormMail", "Feedback"));
class SiteFormMail {
   function Feedback(&$arFields, $arTemplate)   {
        $feedbackFormId = COption::GetOptionInt('respect.feedback', "feedback_form_id");
        
		if (!$feedbackFormId || $arTemplate['EVENT_NAME'] != 'FORM_FILLING_SIMPLE_FORM_'.$feedbackFormId) {
            return;
        }

        $arSiteFields = CEvent::GetSiteFieldsArray(SITE_ID);
        $arSubjectList = [];

        $arFields['RECIPIENT_EMAIL'] = $arSiteFields['DEFAULT_EMAIL_FROM'];

        $hlblockId = COption::GetOptionInt('respect.feedback', "hlblock_id");
        if ($hlblockId) {
            CModule::IncludeModule("highloadblock"); 

            $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch(); 
            $obEntity = HL\HighloadBlockTable::compileEntity($hlblock);
            $strEntityDataClass = $obEntity->getDataClass();

            $rsData = $strEntityDataClass::getList([
                'select' => ['*'],
                'order' => ['UF_SORT' => 'ASC']
            ]);
            while ($arItem = $rsData->Fetch()) {
                if (! empty($arItem['UF_NAME'])) {
                    $arSubjectList[$arItem['UF_NAME']] = $arItem['UF_EMAIL'];
                }
            }

            unset($hlblock, $obEntity, $strEntityDataClass);
        }

        if ($arSubjectList && !empty($arFields['FEEDBACK_SUBJECT']) && !empty($arSubjectList[$arFields['FEEDBACK_SUBJECT']])) {
            $arFields['RECIPIENT_EMAIL'] = $arSubjectList[$arFields['FEEDBACK_SUBJECT']];
        }

        CModule::IncludeModule("form");
        if ($arFile = CFormResult::GetFileByAnswerID($arFields['RS_RESULT_ID'], 14)){
            $arFields['FILE_LINK'] = CFile::GetPath($arFile['USER_FILE_ID']);
        }
   }
}