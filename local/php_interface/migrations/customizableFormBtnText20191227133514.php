<?php

namespace Sprint\Migration;

class customizableFormBtnText20191227133514 extends Version
{
    protected $description = "Добавляет в ИБ Акции свойство 'Текст кнопки отправки формы'.";

  /**
   * @throws Exceptions\HelperException
   * @return bool|void
   */
    public function up()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');
        $helper->Iblock()->saveProperty($iblockId, array(
        'NAME' => 'Текст кнопки отправки формы',
        'ACTIVE' => 'Y',
        'SORT' => '500',
        'CODE' => 'CONTEST_FORM_BTN_TEXT',
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
        'PROPERTY_CONTEST_FORM_BTN_TEXT' => 'Текст кнопки отправки формы',
        'PROPERTY_CONTEST_FIELDS_NAME' => 'Поле: Имя',
        'PROPERTY_CONTEST_FIELDS_PHONE' => 'Поле: Телефон',
        'PROPERTY_CONTEST_FIELDS_BIRTHDATE' => 'Поле: Дата рождения',
        'PROPERTY_CONTEST_FIELDS_INSTA' => 'Поле: Инстаграм',
        'PROPERTY_CONTEST_FIELDS_FILE' => 'Поле: Приложить файл',
        ),
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->getIblockIdIfExists('ACTIONS', 'CONTENT');

        $helper->Iblock()->deleteProperty($iblockId, 'CONTEST_FORM_BTN_TEXT');

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
}
