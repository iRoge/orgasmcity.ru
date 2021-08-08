<?php

namespace Sprint\Migration;

class FeedbackSubjects20180119191731 extends Version {

    protected $description = "";

    public function up(){
        $helper = new HelperManager();

        $hlblockId = $helper->Hlblock()->addHlblockIfNotExists([
            'NAME' => 'FeedbackSubjects',
            'TABLE_NAME' => 'b_feedback_subjects',
        ]);

        if ($hlblockId) {
            $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_NAME', [
                'USER_TYPE_ID' => 'string',
                'SETTINGS' => [
                    'SIZE' => '50',
                ],
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Название темы',
                    'en' => 'Название темы',
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'Название темы',
                    'en' => 'Название темы',
                ],
            ]);
            $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_EMAIL', [
                'USER_TYPE_ID' => 'string',
                'SETTINGS' => [
                    'SIZE' => '50',
                ],
                'EDIT_FORM_LABEL' => [
                    'ru' => 'E-mail получателя',
                    'en' => 'E-mail получателя',
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'E-mail получателя',
                    'en' => 'E-mail получателя',
                ],
            ]);
            $helper->UserTypeEntity()->addUserTypeEntityIfNotExists('HLBLOCK_'.$hlblockId, 'UF_SORT', [
                'USER_TYPE_ID' => 'integer',
                'SETTINGS' => [
                    'DEFAULT_VALUE' => '50',
                ],
                'EDIT_FORM_LABEL' => [
                    'ru' => 'Сортировка',
                    'en' => 'Сортировка',
                ],
                'LIST_COLUMN_LABEL' => [
                    'ru' => 'Сортировка',
                    'en' => 'Сортировка',
                ],
            ]);

            \Bitrix\Main\Config\Option::set('respect.feedback', "hlblock_id", $hlblockId);
        }

        // создание вебформы
        \Bitrix\Main\Loader::includeModule('form');

        $arFeedbackForm = [
            "NAME" => "Обратная связь",
            "SID" => "FEEDBACK_FORM",
            "C_SORT" => 100,
            "BUTTON" => "Отправить",
            "STAT_EVENT1" => "form",
            "arSITE" => ["s1"],
            "arMENU" => ["ru" => "Обратная связь", "en" => "Feedback Form"]
        ];
        
        $feedbackFormId = \CForm::Set($arFeedbackForm);
        if ($feedbackFormId) {
            \Bitrix\Main\Config\Option::set('respect.feedback', "feedback_form_id", $feedbackFormId);

            $arQuestionFields = [
                'FORM_ID' => $feedbackFormId, 
                'ACTIVE' => 'Y',
                'SID' => 'FEEDBACK_SUBJECT',
                'TITLE' => 'Тема сообщения',
                'FILTER_TITLE' => 'Тема сообщения',
                'FIELD_TYPE' => 'text',
                'REQUIRED' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y'
            ];
            \CFormField::Set($arQuestionFields);

            $arFields_status = [
                "FORM_ID" => $feedbackFormId,
                "C_SORT" => 100,
                "ACTIVE" => "Y",
                "TITLE"	=> "DEFAULT",
                "DESCRIPTION" => "DEFAULT",
                "CSS" => "statusgreen",
                "DEFAULT_VALUE" => "Y",
                "arPERMISSION_VIEW"	=> [0],
                "arPERMISSION_MOVE"	=> [0],
                "arPERMISSION_EDIT"	=> [0],
                "arPERMISSION_DELETE" => [0],
            ];
            \CFormStatus::Set($arFields_status);
        }
    }

    public function down(){
        $helper = new HelperManager();

        $helper->Hlblock()->deleteHlblockIfExists('FeedbackSubjects');
        \Bitrix\Main\Config\Option::delete('respect.feedback', [
            'name' => 'hlblock_id'
        ]);

        $feedbackFormId = \Bitrix\Main\Config\Option::get('respect.feedback', "feedback_form_id");
        if ($feedbackFormId) {
            \Bitrix\Main\Loader::includeModule('form');
            \CForm::Delete($feedbackFormId);
        }

        \Bitrix\Main\Config\Option::delete('respect.feedback', [
            'name' => 'feedback_form_id'
        ]);
    }

}
