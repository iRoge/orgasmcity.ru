<?php

namespace Sprint\Migration;

class instagramContest20191216165038 extends Version
{
    protected $description = "Добавляет вебформу для конкурса инстаграм. Создает почтовые событие и шаблон. В ИБ Акции добавляет поля для конкурса инстаграм. Включает расширенный режим работы Вебформ.";

    public function up()
    {
        // Настройка модуля Вебформы
        $this->setAdvancedWebFormsInFormModuleSettings();
        // Создает Вебформу
        $this->createWebForm();
        // Создает почтовый шаблон и тип почтового события
        $this->createMailTemplate();
        // Добавляет свойства к ИБ Акции для реализации конкурса инстаграм
        $this->createAdditionalActionProperties();
    }

    public function down()
    {
        // Удаляет Вебформу
        $this->deleteWebForm();
        // Удаляет почтовый шаблон и тип почтового события
        $this->deleteMailTemplate();
        // Удаляет свойства к ИБ Акции для реализации конкурса инстаграм
        $this->deleteAdditionalActionProperties();
        // Настройка модуля Вебформы
        $this->setSimpleWebFormsInFormModuleSettings();
    }

    private function createWebForm()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array(
            'NAME' => 'Заявка на участие в конкурсе',
            'SID' => 'CONTEST_FORM',
            'BUTTON' => 'Сохранить',
            'C_SORT' => '300',
            'FIRST_SITE_ID' => null,
            'IMAGE_ID' => null,
            'USE_CAPTCHA' => 'Y',
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'FORM_TEMPLATE' => '',
            'USE_DEFAULT_TEMPLATE' => 'Y',
            'SHOW_TEMPLATE' => null,
            'MAIL_EVENT_TYPE' => 'FORM_FILLING_CONTEST_FORM',
            'SHOW_RESULT_TEMPLATE' => null,
            'PRINT_RESULT_TEMPLATE' => null,
            'EDIT_RESULT_TEMPLATE' => null,
            'FILTER_RESULT_TEMPLATE' => null,
            'TABLE_RESULT_TEMPLATE' => null,
            'USE_RESTRICTIONS' => 'N',
            'RESTRICT_USER' => '0',
            'RESTRICT_TIME' => '0',
            'RESTRICT_STATUS' => null,
            'STAT_EVENT1' => 'form',
            'STAT_EVENT2' => '',
            'STAT_EVENT3' => '',
            'LID' => null,
            'C_FIELDS' => '0',
            'QUESTIONS' => '5',
            'STATUSES' => '1',
            'arSITE' =>
            array(
                0 => 's1',
            ),
            'arMENU' =>
            array(
                'ru' => 'Заявка на участие в конкурсе',
                'en' => 'Contest Request',
            ),
            'arGROUP' =>
            array(
                'everyone' => '10',
                'modules_sa' => '30',
            ),
            'arMAIL_TEMPLATE' =>
            array(),
        ));
        $formHelper->saveStatuses($formId, array(
            0 =>
            array(
                'CSS' => 'statusgreen',
                'C_SORT' => '100',
                'ACTIVE' => 'Y',
                'TITLE' => 'DEFAULT',
                'DESCRIPTION' => 'DEFAULT',
                'DEFAULT_VALUE' => 'Y',
                'HANDLER_OUT' => '',
                'HANDLER_IN' => '',
            ),
        ));
        $formHelper->saveFields($formId, array(
            0 =>
            array(
                'ACTIVE' => 'Y',
                'TITLE' => 'Ваше имя',
                'TITLE_TYPE' => 'text',
                'SID' => 'NAME',
                'C_SORT' => '100',
                'ADDITIONAL' => 'N',
                'REQUIRED' => 'Y',
                'IN_FILTER' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y',
                'FIELD_TYPE' => 'text',
                'IMAGE_ID' => null,
                'COMMENTS' => '',
                'FILTER_TITLE' => 'Ваше имя',
                'RESULTS_TABLE_TITLE' => 'Ваше имя',
                'ANSWERS' =>
                array(
                    0 =>
                    array(
                        'MESSAGE' => ' ',
                        'VALUE' => '',
                        'FIELD_TYPE' => 'text',
                        'FIELD_WIDTH' => '0',
                        'FIELD_HEIGHT' => '0',
                        'FIELD_PARAM' => '',
                        'C_SORT' => '0',
                        'ACTIVE' => 'Y',
                    ),
                ),
                'VALIDATORS' =>
                array(),
            ),
            1 =>
            array(
                'ACTIVE' => 'Y',
                'TITLE' => 'Телефон',
                'TITLE_TYPE' => 'text',
                'SID' => 'PHONE',
                'C_SORT' => '200',
                'ADDITIONAL' => 'N',
                'REQUIRED' => 'Y',
                'IN_FILTER' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y',
                'FIELD_TYPE' => 'text',
                'IMAGE_ID' => null,
                'COMMENTS' => '',
                'FILTER_TITLE' => 'Телефон',
                'RESULTS_TABLE_TITLE' => 'Телефон',
                'ANSWERS' =>
                array(
                    0 =>
                    array(
                        'MESSAGE' => ' ',
                        'VALUE' => '',
                        'FIELD_TYPE' => 'text',
                        'FIELD_WIDTH' => '0',
                        'FIELD_HEIGHT' => '0',
                        'FIELD_PARAM' => '',
                        'C_SORT' => '0',
                        'ACTIVE' => 'Y',
                    ),
                ),
                'VALIDATORS' =>
                array(),
            ),
            2 =>
            array(
                'ACTIVE' => 'Y',
                'TITLE' => 'Адрес email',
                'TITLE_TYPE' => 'text',
                'SID' => 'EMAIL',
                'C_SORT' => '300',
                'ADDITIONAL' => 'N',
                'REQUIRED' => 'Y',
                'IN_FILTER' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y',
                'FIELD_TYPE' => 'text',
                'IMAGE_ID' => null,
                'COMMENTS' => '',
                'FILTER_TITLE' => 'Адрес email',
                'RESULTS_TABLE_TITLE' => 'Адрес email',
                'ANSWERS' =>
                array(
                    0 =>
                    array(
                        'MESSAGE' => ' ',
                        'VALUE' => '',
                        'FIELD_TYPE' => 'email',
                        'FIELD_WIDTH' => '0',
                        'FIELD_HEIGHT' => '0',
                        'FIELD_PARAM' => '',
                        'C_SORT' => '0',
                        'ACTIVE' => 'Y',
                    ),
                ),
                'VALIDATORS' =>
                array(),
            ),
            3 =>
            array(
                'ACTIVE' => 'Y',
                'TITLE' => 'Ссылка на профиль в Instagram',
                'TITLE_TYPE' => 'text',
                'SID' => 'INSTAGRAM',
                'C_SORT' => '400',
                'ADDITIONAL' => 'N',
                'REQUIRED' => 'Y',
                'IN_FILTER' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y',
                'FIELD_TYPE' => 'text',
                'IMAGE_ID' => null,
                'COMMENTS' => '',
                'FILTER_TITLE' => 'Ссылка на профиль в Instagram',
                'RESULTS_TABLE_TITLE' => 'Ссылка на профиль в Instagram',
                'ANSWERS' =>
                array(
                    0 =>
                    array(
                        'MESSAGE' => ' ',
                        'VALUE' => '',
                        'FIELD_TYPE' => 'text',
                        'FIELD_WIDTH' => '0',
                        'FIELD_HEIGHT' => '0',
                        'FIELD_PARAM' => '',
                        'C_SORT' => '0',
                        'ACTIVE' => 'Y',
                    ),
                ),
                'VALIDATORS' =>
                array(),
            ),
            4 =>
            array(
                'ACTIVE' => 'Y',
                'TITLE' => 'Прикрепить файл',
                'TITLE_TYPE' => 'text',
                'SID' => 'FORM_FILE',
                'C_SORT' => '500',
                'ADDITIONAL' => 'N',
                'REQUIRED' => 'N',
                'IN_FILTER' => 'Y',
                'IN_RESULTS_TABLE' => 'Y',
                'IN_EXCEL_TABLE' => 'Y',
                'FIELD_TYPE' => 'text',
                'IMAGE_ID' => null,
                'COMMENTS' => '',
                'FILTER_TITLE' => 'Прикрепить файл',
                'RESULTS_TABLE_TITLE' => 'Прикрепить файл',
                'ANSWERS' =>
                array(
                    0 =>
                    array(
                        'MESSAGE' => ' ',
                        'VALUE' => '',
                        'FIELD_TYPE' => 'file',
                        'FIELD_WIDTH' => '0',
                        'FIELD_HEIGHT' => '0',
                        'FIELD_PARAM' => '',
                        'C_SORT' => '0',
                        'ACTIVE' => 'Y',
                    ),
                ),
                'VALIDATORS' =>
                array(
                    0 =>
                    array(
                        'ACTIVE' => 'Y',
                        'C_SORT' => '100',
                        'PARAMS' =>
                        array(
                            'SIZE_FROM' => 0,
                            'SIZE_TO' => 5242880,
                        ),
                        'NAME' => 'file_size',
                    ),
                    1 =>
                    array(
                        'ACTIVE' => 'Y',
                        'C_SORT' => '200',
                        'PARAMS' =>
                        array(
                            'EXT' => 0,
                            'EXT_CUSTOM' => 'jpg, jpeg, bmp, gif, png, doc, docx, xls, xlsx, pdf, ppt',
                        ),
                        'NAME' => 'file_type',
                    ),
                ),
            ),
        ));
    }

    private function deleteWebForm()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form()->deleteFormIfExists('CONTEST_FORM');
    }

    private function createMailTemplate()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->saveEventType('CONTEST_FORM_SUBMIT', array(
            'LID' => 'ru',
            'NAME' => 'Отправка результатов с формы конкурса',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventType('CONTEST_FORM_SUBMIT', array(
            'LID' => 'en',
            'NAME' => 'Sending WebForm results',
            'DESCRIPTION' => '',
            'SORT' => '150',
        ));
        $helper->Event()->saveEventMessage('CONTEST_FORM_SUBMIT', array(
            'LID' =>
            array(
                0 => 's1',
            ),
            'ACTIVE' => 'Y',
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => '#CONTEST_INSTA_RESULT_EMAILS#',
            'SUBJECT' => 'Конкурс инстаграм',
            'MESSAGE' => 'Конкурс инстаграм<br>
   <br>
   Имя: #NAME#<br>
   Телефон: #PHONE#<br>
   Email: #EMAIL#<br>
   Ссылка на профиль Instagram: #INSTAGRAM#<br>',
            'BODY_TYPE' => 'html',
            'BCC' => '',
            'REPLY_TO' => '',
            'CC' => '',
            'IN_REPLY_TO' => '',
            'PRIORITY' => '',
            'FIELD1_NAME' => null,
            'FIELD1_VALUE' => null,
            'FIELD2_NAME' => null,
            'FIELD2_VALUE' => null,
            'SITE_TEMPLATE_ID' => '',
            'ADDITIONAL_FIELD' =>
            array(),
            'LANGUAGE_ID' => '',
        ));
    }

    private function deleteMailTemplate()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->deleteEventType([
            'EVENT_NAME' => 'CONTEST_FORM_SUBMIT',
            'LID' => 'ru'
        ]);
        $helper->Event()->deleteEventMessage([
            'EVENT_NAME' => 'CONTEST_FORM_SUBMIT',
            'SUBJECT' => 'Конкурс инстаграм'
        ]);
    }

    private function createAdditionalActionProperties()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');
        $colorsIblockId = $helper->Iblock()->getIblockIdIfExists('COLORS', 'SYSTEM');
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Email-ы для приема заявок',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'CONTEST_INSTA_RESULT_EMAILS',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Цвет кнопки',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'CONTEST_BTN_COLOR',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'E',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => $colorsIblockId,
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'EList',
            'USER_TYPE_SETTINGS' =>
            array(
                'size' => 1,
                'width' => 0,
                'group' => 'N',
                'multiple' => 'N',
            ),
            'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Цвет текста кнопки',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'CONTEST_BTN_TEXT_COLOR',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'E',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => $colorsIblockId,
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'EList',
            'USER_TYPE_SETTINGS' =>
            array(
                'size' => 1,
                'width' => 0,
                'group' => 'N',
                'multiple' => 'N',
            ),
            'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Спасибо',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'CONTEST_THANKYOU_TEXT',
            'DEFAULT_VALUE' =>
            array(
                'TYPE' => 'HTML',
                'TEXT' => '',
            ),
            'PROPERTY_TYPE' => 'S',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'L',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => 'HTML',
            'USER_TYPE_SETTINGS' =>
            array(
                'height' => 200,
            ),
            'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Тип конкурса инстаграм',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'CONTEST_TYPE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'N',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'Y',
            'IS_REQUIRED' => 'N',
            'VERSION' => '1',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
            'VALUES' =>
            array(
                0 =>
                array(
                    'VALUE' => 'Да',
                    'DEF' => 'N',
                    'SORT' => '500',
                    'XML_ID' => 'Y',
                ),
            ),
        ));
        $helper->UserOptions()->saveElementForm($iblockId, array(
            'Элемент' =>
            array(
                'ACTIVE' => 'Активность',
                'ACTIVE_FROM' => 'Начало активности',
                'ACTIVE_TO' => 'Окончание активности',
                'NAME' => 'Название',
                'CODE' => 'Символьный код',
                'XML_ID' => 'Внешний код',
                'SORT' => 'Сортировка',
                'PROPERTY_268' => 'Показывать большим',
                'PROPERTY_311' => 'Показывать средним с товарами',
                'PROPERTY_SHOW_IN_BESTSELLERS' => 'Показывать в бестселерах',
                'PROPERTY_SHOW_IN_ORDER_SUCCESS' => 'Показывать при успешном оформлении заказа',
                'PROPERTY_ENABLE_SUBSCRIBE' => 'Включить подписку в акции',
                'PROPERTY_PRODUCTS' => 'Товары',
                'PROPERTY_SLIDER_PICTURES' => 'Картинки для слайдера',
            ),
            'Анонс' =>
            array(
                'PREVIEW_PICTURE' => 'Картинка для анонса',
                'PREVIEW_TEXT' => 'Описание для анонса',
            ),
            'Подробно' =>
            array(
                'DETAIL_PICTURE' => 'Детальная картинка',
                'DETAIL_TEXT' => 'Детальное описание',
            ),
            'SEO' =>
            array(
                'IPROPERTY_TEMPLATES_ELEMENT_META_TITLE' => 'Шаблон META TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS' => 'Шаблон META KEYWORDS',
                'IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
                'IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE' => 'Заголовок элемента',
                'IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE' => 'Настройки для картинок анонса элементов',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT' => 'Шаблон ALT',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                'IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE' => 'Настройки для детальных картинок элементов',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                'SEO_ADDITIONAL' => 'Дополнительно',
                'TAGS' => 'Теги',
            ),
            'Конкурс' =>
            array(
                'PROPERTY_ENABLE_CONTEST' => 'Включить конкурс',
                'PROPERTY_CONTEST_TYPE' => 'Тип конкурса Instagram',
                'PROPERTY_CONTEST_END' => 'Завершение конкурса',
                'PROPERTY_CONTEST_RULES' => 'Правила конкурса',
                'PROPERTY_CONTEST_INSTA_RESULT_EMAILS' => 'Email-ы для приема заявок',
                'PROPERTY_CONTEST_THANKYOU_TEXT' => 'Спасибо',
                'PROPERTY_CONTEST_BTN_COLOR' => 'Цвет кнопки',
                'PROPERTY_CONTEST_BTN_TEXT_COLOR' => 'Цвет текста кнопки',
            ),
        ));
    }

    private function deleteAdditionalActionProperties()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');

        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_INSTA_RESULT_EMAILS');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_BTN_COLOR');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_BTN_TEXT_COLOR');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_THANKYOU_TEXT');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_TYPE');

        $helper->UserOptions()->saveElementForm($iblockId, array(
            'Элемент' =>
            array(
                'ACTIVE' => 'Активность',
                'ACTIVE_FROM' => 'Начало активности',
                'ACTIVE_TO' => 'Окончание активности',
                'NAME' => 'Название',
                'CODE' => 'Символьный код',
                'XML_ID' => 'Внешний код',
                'SORT' => 'Сортировка',
                'PROPERTY_268' => 'Показывать большим',
                'PROPERTY_311' => 'Показывать средним с товарами',
                'PROPERTY_SHOW_IN_BESTSELLERS' => 'Показывать в бестселерах',
                'PROPERTY_SHOW_IN_ORDER_SUCCESS' => 'Показывать при успешном оформлении заказа',
                'PROPERTY_ENABLE_SUBSCRIBE' => 'Включить подписку в акции',
                'PROPERTY_PRODUCTS' => 'Товары',
                'PROPERTY_SLIDER_PICTURES' => 'Картинки для слайдера',
            ),
            'Анонс' =>
            array(
                'PREVIEW_PICTURE' => 'Картинка для анонса',
                'PREVIEW_TEXT' => 'Описание для анонса',
            ),
            'Подробно' =>
            array(
                'DETAIL_PICTURE' => 'Детальная картинка',
                'DETAIL_TEXT' => 'Детальное описание',
            ),
            'SEO' =>
            array(
                'IPROPERTY_TEMPLATES_ELEMENT_META_TITLE' => 'Шаблон META TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS' => 'Шаблон META KEYWORDS',
                'IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
                'IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE' => 'Заголовок элемента',
                'IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE' => 'Настройки для картинок анонса элементов',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT' => 'Шаблон ALT',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                'IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE' => 'Настройки для детальных картинок элементов',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                'SEO_ADDITIONAL' => 'Дополнительно',
                'TAGS' => 'Теги',
            ),
            'Конкурс' =>
            array(
                'PROPERTY_ENABLE_CONTEST' => 'Включить конкурс',
                'PROPERTY_CONTEST_END' => 'Завершение конкурса',
                'PROPERTY_CONTEST_RULES' => 'Правила конкурса',
            ),
        ));
    }

    private function setAdvancedWebFormsInFormModuleSettings()
    {
        $helper = $this->getHelperManager();
        $helper->Option()->saveOption(array(
            'MODULE_ID' => 'form',
            'NAME' => 'SIMPLE',
            'VALUE' => 'N',
            'DESCRIPTION' => null,
            'SITE_ID' => null,
        ));
    }

    private function setSimpleWebFormsInFormModuleSettings()
    {
        $helper = $this->getHelperManager();
        $helper->Option()->saveOption(array(
            'MODULE_ID' => 'form',
            'NAME' => 'SIMPLE',
            'VALUE' => 'Y',
            'DESCRIPTION' => null,
            'SITE_ID' => null,
        ));
    }
}
