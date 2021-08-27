<?php

$arResult['BTN_STYLE_ATTR'] = 'style="';
if (!empty($_REQUEST['btn_color'])) {
    $arResult['BTN_STYLE_ATTR'] .= 'background-color: #' . htmlspecialchars($_REQUEST['btn_color']) . ';';
}
if (!empty($_REQUEST['btn_text_color'])) {
    $arResult['BTN_STYLE_ATTR'] .= 'color: #' . htmlspecialchars($_REQUEST['btn_text_color']) . ';';
}
$arResult['BTN_STYLE_ATTR'] .= '"';

// Получаем свойства акции
$actionId = intval($_REQUEST['action_id']);
$rs = CIBlockElement::GetList(
    [],
    [
        'ID' => $actionId,
        'IBLOCK_ID' => IBLOCK_BLOG,
    ],
    false,
    ['nTopCount' => 1],
    [
        'NAME',
        'PROPERTY_CONTEST_THANKYOU_TEXT',
        'PROPERTY_CONTEST_INSTA_RESULT_EMAILS',
        'PROPERTY_CONTEST_BTN_ENROLL_TEXT',
        'PROPERTY_CONTEST_FORM_TITLE',
        'PROPERTY_CONTEST_RULES_SHOW',
        'PROPERTY_CONTEST_FIELDS_NAME',
        'PROPERTY_CONTEST_FIELDS_PHONE',
        'PROPERTY_CONTEST_FIELDS_BIRTHDATE',
        'PROPERTY_CONTEST_FIELDS_INSTA',
        'PROPERTY_CONTEST_FIELDS_FILE_1',
        'PROPERTY_CONTEST_FIELDS_FILE_2',
        'PROPERTY_CONTEST_BTN_FILE_1',
        'PROPERTY_CONTEST_BTN_FILE_2',
        'PROPERTY_CONTEST_CHECK_FILE_1',
        'PROPERTY_CONTEST_CHECK_FILE_2',
        'PROPERTY_CONTEST_FORM_BTN_TEXT'
    ]
);

$elem = $rs->Fetch();

$arResult['CONTEST_FORM_TITLE'] = empty($elem['PROPERTY_CONTEST_FORM_TITLE_VALUE']) ? 'Заявка на участие в конкурсе' : $elem['PROPERTY_CONTEST_FORM_TITLE_VALUE'];
$arResult['CONTEST_FORM_BTN_TEXT'] = empty($elem['PROPERTY_CONTEST_FORM_BTN_TEXT_VALUE']) ? 'Отправить сообщение' : $elem['PROPERTY_CONTEST_FORM_BTN_TEXT_VALUE'];
$arResult['CONTEST_RULES_SHOW'] = !empty($elem['PROPERTY_CONTEST_RULES_SHOW_VALUE']);
$arResult['ACTION_ID'] = $actionId;
$arResult['ACTION_NAME'] = $elem['NAME'];
$arResult['CONTEST_FIELDS_NAME'] = !empty($elem['PROPERTY_CONTEST_FIELDS_NAME_VALUE']);
$arResult['CONTEST_FIELDS_PHONE'] = !empty($elem['PROPERTY_CONTEST_FIELDS_PHONE_VALUE']);
$arResult['CONTEST_FIELDS_BIRTHDATE'] = !empty($elem['PROPERTY_CONTEST_FIELDS_BIRTHDATE_VALUE']);
$arResult['CONTEST_FIELDS_INSTA'] = !empty($elem['PROPERTY_CONTEST_FIELDS_INSTA_VALUE']);
$arResult['CONTEST_FIELDS_FILE_1'] = !empty($elem['PROPERTY_CONTEST_FIELDS_FILE_1_VALUE']);
$arResult['CONTEST_FIELDS_FILE_2'] = !empty($elem['PROPERTY_CONTEST_FIELDS_FILE_2_VALUE']);
$arResult['CONTEST_BTN_FILE_1'] = empty($elem['PROPERTY_CONTEST_BTN_FILE_1_VALUE']) ? 'Прикрепить файл 1' : $elem['PROPERTY_CONTEST_BTN_FILE_1_VALUE'];
$arResult['CONTEST_BTN_FILE_2'] = empty($elem['PROPERTY_CONTEST_BTN_FILE_2_VALUE']) ? 'Прикрепить файл 2' : $elem['PROPERTY_CONTEST_BTN_FILE_2_VALUE'];
$arResult['CONTEST_CHECK_FILE_1'] = !empty($elem['PROPERTY_CONTEST_CHECK_FILE_1_VALUE']);
$arResult['CONTEST_CHECK_FILE_2'] = !empty($elem['PROPERTY_CONTEST_CHECK_FILE_2_VALUE']);

if ($arResult["isFormErrors"] == 'N' && !empty($_REQUEST['formresult']) && $_REQUEST['formresult'] == 'addok') {
    \Bitrix\Main\Loader::includeModule('iblock');

    if (!empty($elem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE']) && is_array($elem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE'])) {
        $thankyouText = $elem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE']['TEXT'];
    } elseif (!empty($elem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE'])) {
        $thankyouText = $elem['PROPERTY_CONTEST_THANKYOU_TEXT_VALUE'];
    } else {
        $thankyouText = '';
    }

    //Загружаю ответы вебформы
    CForm::GetResultAnswerArray(
        intval($_REQUEST['WEB_FORM_ID']),
        $arrColumns,
        $arrAnswers,
        $arrAnswersVarname,
        array("RESULT_ID" => intval($_REQUEST['RESULT_ID']))
    );

    $contestAnswers = [];
    $fileId = null;
    $contestMemberEmail = null;
    $contestMemberBirthdate = null;

    foreach ($arrAnswersVarname[intval($_REQUEST['RESULT_ID'])] as $answerKey => $answerValue) {
        $answerValue = $answerValue[0];

        if ($answerKey == 'EMAIL') {
            $contestMemberEmail = $answerValue['USER_TEXT'];
        } elseif ($answerKey == 'BIRTHDATE') {
            $contestMemberBirthdate = $answerValue['USER_TEXT'];
        }

        if ($answerValue['FIELD_TYPE'] == 'file') {
            $fileId[] = $answerValue['USER_FILE_ID'];
        } elseif ($answerValue['USER_TEXT'] == '-') {
            // Если в поле формы устновлено значение-заглушка, то не показываем его в письме
            $contestAnswers[$answerKey] = '';
        } elseif ($answerKey == 'ACTION_NAME') {
            // Не показываем название поля для поля акции (особенность почтового шаблона)
            $contestAnswers[$answerKey] = $answerValue['USER_TEXT'];
        } else {
            // Генерируем текст для поля в виде "Название_поля: значение_поля"
            $contestAnswers[$answerKey] = $answerValue['RESULTS_TABLE_TITLE'] . ': ' . $answerValue['USER_TEXT'] . '<br>';
        }
    }

    // Создаю событие для отправки письма
    CEvent::SendImmediate(
        "CONTEST_FORM_SUBMIT",
        SITE_ID,
        array_merge([
            "CONTEST_INSTA_RESULT_EMAILS" => $elem['PROPERTY_CONTEST_INSTA_RESULT_EMAILS_VALUE'],
            "ACTION_ID" => $actionId,
            'FORM_ID' => intval($_REQUEST['WEB_FORM_ID']),
            'FORM_RESULT_ID' => intval($_REQUEST['RESULT_ID']),
        ], $contestAnswers),
        'Y',
        '',
        $fileId
    );

    // Sailplay интеграция
    if (!empty($contestMemberEmail)) {
        $user = SailPlayApi::getUserByMail($contestMemberEmail, true, true);

        if ($user === false) {
            // Создаем пользователя
            $sailplay_anon_user_id = intval(COption::GetOptionInt("likee", "sailplay_anon_user_id", ''));
            // Делаем 3 попытки на создание пользователя
            for ($i = 0; $i < 3; $i++) {
                $data = [
                    'ID' => $sailplay_anon_user_id
                ];
                // Добавляем дату рождения новому пользователю SP, если заполнена
                if (!empty($contestMemberBirthdate) && $contestMemberBirthdate != '-') {
                    $data['PERSONAL_BIRTHDAY'] = $contestMemberBirthdate;
                }

                $user = SailPlayApi::addUser($contestMemberEmail, $data, 'email');
                $sailplay_anon_user_id++;
                if ($user !== false) {
                    break;
                }
            }
            COption::SetOptionInt("likee", "sailplay_anon_user_id", $sailplay_anon_user_id);
        } elseif (empty($user->birth_date) &&
            (!empty($contestMemberBirthdate) &&
                $contestMemberBirthdate != '-')
        ) {
            // Если пользователь существует и дата рождения заполнена, но дата рождения в SP пустая - заполняем
            $user = SailPlayApi::updateUser(
                'mail',
                $contestMemberEmail,
                ['PERSONAL_BIRTHDAY' => $contestMemberBirthdate]
            );
        }

        SailPlayApi::userSubscribe($contestMemberEmail, ['email_all'], 'email');
        SailPlayApi::userAddTags($contestMemberEmail, ['Подписка на E-mail в акции: ' . $elem['NAME']], 'email');
    }

    ?>
    <div class="insta_contest_form" style="text-align: center">
        <div class="contest-popup-container">
            <?= $thankyouText ?>
        </div>
        <button class="close-popup-btn btn-enroll button-primary button-xl button-bigger contest-close-btn" <?= $arResult['BTN_STYLE_ATTR'] ?? '' ?> >OK</button>
    </div>
    <style>
        .contest-close-btn{
         margin-top: 10px;
        }
    </style>
    <?
                                                                                            exit;
}
