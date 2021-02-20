<?php

namespace Sprint\Migration;

class extendContestFunctionality20191218185420 extends Version
{
    protected $description = "Добавляем поля в ИБ Акции. Новые вопросы формы конкурса инстаграм. Обновление почтового шаблона.";

  /**
   * @throws Exceptions\HelperException
   * @return bool|void
   */
    public function up()
    {
      // Добавляем поля в ИБ Акции
        $this->addFieldsToActionsIBlock();
      // Добавляем в Вебформу два поля Акции и включаем существующие поля в фильтр
        $this->addWebFormFields();
      // Обновляем почтовый шаблон
        $this->updateMailtemplate();
    }

    public function down()
    {
      // Удаляем поля из ИБ Акции
        $this->deleteFieldsFromActionsIBlock();
      // Удаляем из Вебформы два поля Акции и убираем существующие поля из фильтра
        $this->deleteWebFormFields();
      // Откатываем почтовый шаблон
        $this->undoUpdateMailtemplate();
    }

    private function addFieldsToActionsIBlock()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Надпись кнопки "Принять участие"',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_BTN_ENROLL_TEXT',
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
        'FILTRABLE' => 'N',
        'IS_REQUIRED' => 'N',
        'VERSION' => '1',
        'USER_TYPE' => null,
        'USER_TYPE_SETTINGS' => null,
        'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Название формы заявки',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FORM_TITLE',
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
        'FILTRABLE' => 'N',
        'IS_REQUIRED' => 'N',
        'VERSION' => '1',
        'USER_TYPE' => null,
        'USER_TYPE_SETTINGS' => null,
        'HINT' => '',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Поле: Имя',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FIELDS_NAME',
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
        'FILTRABLE' => 'N',
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
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Поле: Телефон',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FIELDS_PHONE',
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
        'FILTRABLE' => 'N',
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
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Поле: Дата рождения',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FIELDS_BIRTHDATE',
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
        'FILTRABLE' => 'N',
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
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Поле: Инстаграм',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FIELDS_INSTA',
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
        'FILTRABLE' => 'N',
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
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Поле: Приложить файл',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FIELDS_FILE',
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
        'FILTRABLE' => 'N',
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
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Отображать правила конкурса',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_RULES_SHOW',
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
        'FILTRABLE' => 'N',
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
        'PROPERTY_CONTEST_RULES_SHOW' => 'Отображать правила конкурса',
        'PROPERTY_CONTEST_RULES' => 'Правила конкурса',
        'PROPERTY_CONTEST_INSTA_RESULT_EMAILS' => 'Email-ы для приема заявок',
        'PROPERTY_CONTEST_THANKYOU_TEXT' => 'Спасибо',
        'PROPERTY_CONTEST_BTN_COLOR' => 'Цвет кнопки',
        'PROPERTY_CONTEST_BTN_TEXT_COLOR' => 'Цвет текста кнопки',
        'PROPERTY_CONTEST_BTN_ENROLL_TEXT' => 'Надпись кнопки "Принять участие"',
        'PROPERTY_CONTEST_FORM_TITLE' => 'Название формы заявки',
        'PROPERTY_CONTEST_FIELDS_NAME' => 'Поле: Имя',
        'PROPERTY_CONTEST_FIELDS_PHONE' => 'Поле: Телефон',
        'PROPERTY_CONTEST_FIELDS_BIRTHDATE' => 'Поле: Дата рождения',
        'PROPERTY_CONTEST_FIELDS_INSTA' => 'Поле: Инстаграм',
        'PROPERTY_CONTEST_FIELDS_FILE' => 'Поле: Приложить файл',
        ),
        ));
    }

    private function deleteFieldsFromActionsIBlock()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');

        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_BTN_ENROLL_TEXT');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FORM_TITLE');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_RULES_SHOW');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FIELDS_NAME');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FIELDS_PHONE');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FIELDS_BIRTHDATE');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FIELDS_INSTA');
        $helper->Iblock()->deletePropertyIfExists($iblockId, 'CONTEST_FIELDS_FILE');

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

    private function addWebFormFields()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array(
        'SID' => 'CONTEST_FORM',
        'USE_CAPTCHA' => 'Y'
        ));
        $formHelper->saveFields($formId, array(
        0 =>
        array(
        'ACTIVE' => 'Y',
        'TITLE' => 'ACTION_ID',
        'TITLE_TYPE' => 'text',
        'SID' => 'ACTION_ID',
        'C_SORT' => '10',
        'ADDITIONAL' => 'N',
        'REQUIRED' => 'Y',
        'IN_FILTER' => 'N',
        'IN_RESULTS_TABLE' => 'N',
        'IN_EXCEL_TABLE' => 'N',
        'FIELD_TYPE' => '',
        'IMAGE_ID' => null,
        'COMMENTS' => '',
        'FILTER_TITLE' => '',
        'RESULTS_TABLE_TITLE' => '',
        'ANSWERS' =>
        array(
          0 =>
          array(
            'MESSAGE' => ' ',
            'VALUE' => '',
            'FIELD_TYPE' => 'hidden',
            'FIELD_WIDTH' => '0',
            'FIELD_HEIGHT' => '0',
            'FIELD_PARAM' => '',
            'C_SORT' => '100',
            'ACTIVE' => 'Y',
          ),
        ),
        'VALIDATORS' =>
        array(),
        ),
        1 =>
        array(
        'ACTIVE' => 'Y',
        'TITLE' => 'Акция',
        'TITLE_TYPE' => 'text',
        'SID' => 'ACTION_NAME',
        'C_SORT' => '50',
        'ADDITIONAL' => 'N',
        'REQUIRED' => 'Y',
        'IN_FILTER' => 'Y',
        'IN_RESULTS_TABLE' => 'Y',
        'IN_EXCEL_TABLE' => 'Y',
        'FIELD_TYPE' => '',
        'IMAGE_ID' => null,
        'COMMENTS' => '',
        'FILTER_TITLE' => '',
        'RESULTS_TABLE_TITLE' => 'Акция',
        'ANSWERS' =>
        array(
          0 =>
          array(
            'MESSAGE' => ' ',
            'VALUE' => '',
            'FIELD_TYPE' => 'hidden',
            'FIELD_WIDTH' => '0',
            'FIELD_HEIGHT' => '0',
            'FIELD_PARAM' => '',
            'C_SORT' => '100',
            'ACTIVE' => 'Y',
          ),
        ),
        'VALIDATORS' =>
        array(),
        ),
        2 =>
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
        'FIELD_TYPE' => '',
        'IMAGE_ID' => null,
        'COMMENTS' => '',
        'FILTER_TITLE' => 'Ваше имя',
        'RESULTS_TABLE_TITLE' => 'Имя',
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
        3 =>
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
        'FIELD_TYPE' => '',
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
        4 =>
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
        'FIELD_TYPE' => '',
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
        5 =>
        array(
        'ACTIVE' => 'Y',
        'TITLE' => 'Дата рождения',
        'TITLE_TYPE' => 'text',
        'SID' => 'BIRTHDATE',
        'C_SORT' => '400',
        'ADDITIONAL' => 'N',
        'REQUIRED' => 'Y',
        'IN_FILTER' => 'Y',
        'IN_RESULTS_TABLE' => 'Y',
        'IN_EXCEL_TABLE' => 'Y',
        'FIELD_TYPE' => '',
        'IMAGE_ID' => null,
        'COMMENTS' => '',
        'FILTER_TITLE' => '',
        'RESULTS_TABLE_TITLE' => 'Дата рождения',
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
            'C_SORT' => '100',
            'ACTIVE' => 'Y',
          ),
        ),
        'VALIDATORS' =>
        array(),
        ),
        6 =>
        array(
        'ACTIVE' => 'Y',
        'TITLE' => 'Ссылка на профиль в Instagram',
        'TITLE_TYPE' => 'text',
        'SID' => 'INSTAGRAM',
        'C_SORT' => '500',
        'ADDITIONAL' => 'N',
        'REQUIRED' => 'Y',
        'IN_FILTER' => 'Y',
        'IN_RESULTS_TABLE' => 'Y',
        'IN_EXCEL_TABLE' => 'Y',
        'FIELD_TYPE' => '',
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
        7 =>
        array(
        'ACTIVE' => 'Y',
        'TITLE' => 'Прикрепить файл',
        'TITLE_TYPE' => 'text',
        'SID' => 'FORM_FILE',
        'C_SORT' => '600',
        'ADDITIONAL' => 'N',
        'REQUIRED' => 'N',
        'IN_FILTER' => 'N',
        'IN_RESULTS_TABLE' => 'Y',
        'IN_EXCEL_TABLE' => 'Y',
        'FIELD_TYPE' => '',
        'IMAGE_ID' => null,
        'COMMENTS' => '',
        'FILTER_TITLE' => 'Прикрепить файл',
        'RESULTS_TABLE_TITLE' => 'Прикрепленный файл',
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

    private function deleteWebFormFields()
    {
        $helper = $this->getHelperManager();
        $formHelper = $helper->Form();
        $formId = $formHelper->saveForm(array(
        'SID' => 'CONTEST_FORM',
        'USE_CAPTCHA' => 'Y'
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

    private function updateMailtemplate()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->deleteEventMessage([
        'EVENT_NAME' => 'CONTEST_FORM_SUBMIT',
        'SUBJECT' => 'Конкурс инстаграм',
        ]);
        $helper->Event()->saveEventMessage('CONTEST_FORM_SUBMIT', array(
        'LID' =>
        array(
        0 => 's1',
        ),
        'ACTIVE' => 'Y',
        'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
        'EMAIL_TO' => '#CONTEST_INSTA_RESULT_EMAILS#',
        'SUBJECT' => 'Конкурс инстаграм: #ACTION_NAME#',
        'MESSAGE' => 'Название конкурса: #ACTION_NAME#<br>
     <br>
    #NAME#
    #PHONE#
    #EMAIL#
    #BIRTHDATE#
    #INSTAGRAM#',
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

    private function undoUpdateMailtemplate()
    {
        $helper = $this->getHelperManager();
        $helper->Event()->deleteEventMessage([
        'EVENT_NAME' => 'CONTEST_FORM_SUBMIT',
        'SUBJECT' => 'Конкурс инстаграм: #ACTION_NAME#',
        ]);
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
}
