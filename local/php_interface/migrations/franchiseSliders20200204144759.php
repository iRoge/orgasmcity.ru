<?php

namespace Sprint\Migration;

class franchiseSliders20200204144759 extends Version
{
    protected $description = "Добавляет ИБ для двух слайдеров на странице франчайзинг, наполняет их тестовым контентом";

    protected $moduleVersion = "3.12.12";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->saveIblockType(array(
            'ID' => 'CONTENT',
            'SECTIONS' => 'Y',
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'IN_RSS' => 'N',
            'SORT' => '500',
            'LANG' =>
                array(
                    'ru' =>
                        array(
                            'NAME' => 'Контент',
                            'SECTION_NAME' => '',
                            'ELEMENT_NAME' => '',
                        ),
                    'en' =>
                        array(
                            'NAME' => 'Content',
                            'SECTION_NAME' => '',
                            'ELEMENT_NAME' => '',
                        ),
                ),
        ));
        $iblockId = $helper->Iblock()->saveIblock(array(
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LID' =>
                array(
                    0 => 's1',
                ),
            'CODE' => 'franchise_img_slider',
            'NAME' => 'Франчайзинг слайдер изображений',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'LIST_PAGE_URL' => '/franchise/',
            'DETAIL_PAGE_URL' => '',
            'SECTION_PAGE_URL' => '/franchise/',
            'CANONICAL_PAGE_URL' => '',
            'PICTURE' => null,
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'RSS_TTL' => '24',
            'RSS_ACTIVE' => 'Y',
            'RSS_FILE_ACTIVE' => 'N',
            'RSS_FILE_LIMIT' => null,
            'RSS_FILE_DAYS' => null,
            'RSS_YANDEX_ACTIVE' => 'N',
            'XML_ID' => '',
            'INDEX_ELEMENT' => 'Y',
            'INDEX_SECTION' => 'Y',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'RIGHTS_MODE' => 'S',
            'SECTION_PROPERTY' => 'N',
            'PROPERTY_INDEX' => 'N',
            'VERSION' => '1',
            'LAST_CONV_ELEMENT' => '0',
            'SOCNET_GROUP_ID' => null,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'EXTERNAL_ID' => '',
            'LANG_DIR' => '/',
            'SERVER_NAME' => 'respect-shoes.ru',
            'ELEMENT_ADD' => 'Добавить элемент',
            'ELEMENT_EDIT' => 'Изменить элемент',
            'ELEMENT_DELETE' => 'Удалить элемент',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
        ));
        $helper->Iblock()->saveIblockFields($iblockId, array(
            'IBLOCK_SECTION' =>
                array(
                    'NAME' => 'Привязка к разделам',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'KEEP_IBLOCK_SECTION_ID' => 'N',
                        ),
                ),
            'ACTIVE' =>
                array(
                    'NAME' => 'Активность',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'Y',
                ),
            'ACTIVE_FROM' =>
                array(
                    'NAME' => 'Начало активности',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'ACTIVE_TO' =>
                array(
                    'NAME' => 'Окончание активности',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SORT' =>
                array(
                    'NAME' => 'Сортировка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '0',
                ),
            'NAME' =>
                array(
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'PREVIEW_PICTURE' =>
                array(
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'FROM_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'DELETE_WITH_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'PREVIEW_TEXT_TYPE' =>
                array(
                    'NAME' => 'Тип описания для анонса',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                ),
            'PREVIEW_TEXT' =>
                array(
                    'NAME' => 'Описание для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'DETAIL_PICTURE' =>
                array(
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' =>
                        array(
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'DETAIL_TEXT_TYPE' =>
                array(
                    'NAME' => 'Тип детального описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                ),
            'DETAIL_TEXT' =>
                array(
                    'NAME' => 'Детальное описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'XML_ID' =>
                array(
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'CODE' =>
                array(
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'UNIQUE' => 'N',
                            'TRANSLITERATION' => 'N',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ),
                ),
            'TAGS' =>
                array(
                    'NAME' => 'Теги',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_NAME' =>
                array(
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_PICTURE' =>
                array(
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'FROM_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'DELETE_WITH_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'SECTION_DESCRIPTION_TYPE' =>
                array(
                    'NAME' => 'Тип описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                ),
            'SECTION_DESCRIPTION' =>
                array(
                    'NAME' => 'Описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_DETAIL_PICTURE' =>
                array(
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'SECTION_XML_ID' =>
                array(
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_CODE' =>
                array(
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'UNIQUE' => 'N',
                            'TRANSLITERATION' => 'N',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ),
                ),
        ));
        $helper->Iblock()->saveGroupPermissions($iblockId, array(
            'administrators' => 'X',
            'everyone' => 'R',
            'content_editor' => 'W',
        ));
        $helper->Iblock()->saveProperty($iblockId, array(
            'NAME' => 'Стиль франшизы',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'CODE' => 'STYLE_FRANCHISE',
            'DEFAULT_VALUE' => '',
            'PROPERTY_TYPE' => 'L',
            'ROW_COUNT' => '1',
            'COL_COUNT' => '30',
            'LIST_TYPE' => 'C',
            'MULTIPLE' => 'Y',
            'XML_ID' => '',
            'FILE_TYPE' => '',
            'MULTIPLE_CNT' => '5',
            'LINK_IBLOCK_ID' => '0',
            'WITH_DESCRIPTION' => 'N',
            'SEARCHABLE' => 'N',
            'FILTRABLE' => 'N',
            'IS_REQUIRED' => 'Y',
            'VERSION' => '1',
            'USER_TYPE' => null,
            'USER_TYPE_SETTINGS' => null,
            'HINT' => '',
            'VALUES' =>
                array(
                    0 =>
                        array(
                            'VALUE' => 'Respect Classic',
                            'DEF' => 'N',
                            'SORT' => '1',
                            'XML_ID' => 'RESPECT_CLASSIC_STYLE',
                        ),
                    1 =>
                        array(
                            'VALUE' => 'Respect New',
                            'DEF' => 'N',
                            'SORT' => '2',
                            'XML_ID' => 'RESPECT_NEW_STYLE',
                        ),
                ),
        ));
        $helper->UserOptions()->saveElementForm($iblockId, array(
            'Элемент' =>
                array(
                    'ID' => 'ID',
                    'DATE_CREATE' => 'Создан',
                    'TIMESTAMP_X' => 'Изменен',
                    'ACTIVE' => 'Активность',
                    'ACTIVE_FROM' => 'Начало активности',
                    'ACTIVE_TO' => 'Окончание активности',
                    'NAME' => 'Название',
                    'XML_ID' => 'Внешний код',
                    'SORT' => 'Сортировка',
                    'IBLOCK_ELEMENT_PROP_VALUE' => 'Значения свойств',
                    'PROPERTY_STYLE_FRANCHISE' => 'Стиль франшизы',
                ),
            'Подробно' =>
                array(
                    'DETAIL_PICTURE' => 'Детальная картинка',
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
            'Разделы' =>
                array(
                    'SECTIONS' => 'Разделы',
                ),
        ));
        $iblockId = $helper->Iblock()->saveIblock(array(
            'IBLOCK_TYPE_ID' => 'CONTENT',
            'LID' =>
                array(
                    0 => 's1',
                ),
            'CODE' => 'franchise_html_slider',
            'NAME' => 'Франчайзинг слайдер HTML',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'LIST_PAGE_URL' => '/franchise/',
            'DETAIL_PAGE_URL' => '',
            'SECTION_PAGE_URL' => '',
            'CANONICAL_PAGE_URL' => '',
            'PICTURE' => null,
            'DESCRIPTION' => '',
            'DESCRIPTION_TYPE' => 'text',
            'RSS_TTL' => '24',
            'RSS_ACTIVE' => 'Y',
            'RSS_FILE_ACTIVE' => 'N',
            'RSS_FILE_LIMIT' => null,
            'RSS_FILE_DAYS' => null,
            'RSS_YANDEX_ACTIVE' => 'N',
            'XML_ID' => '',
            'INDEX_ELEMENT' => 'Y',
            'INDEX_SECTION' => 'Y',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'RIGHTS_MODE' => 'S',
            'SECTION_PROPERTY' => 'N',
            'PROPERTY_INDEX' => 'N',
            'VERSION' => '1',
            'LAST_CONV_ELEMENT' => '0',
            'SOCNET_GROUP_ID' => null,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'EXTERNAL_ID' => '',
            'LANG_DIR' => '/',
            'SERVER_NAME' => 'respect-shoes.ru',
            'ELEMENT_ADD' => 'Добавить элемент',
            'ELEMENT_EDIT' => 'Изменить элемент',
            'ELEMENT_DELETE' => 'Удалить элемент',
            'SECTION_ADD' => 'Добавить раздел',
            'SECTION_EDIT' => 'Изменить раздел',
            'SECTION_DELETE' => 'Удалить раздел',
        ));
        $helper->Iblock()->saveIblockFields($iblockId, array(
            'IBLOCK_SECTION' =>
                array(
                    'NAME' => 'Привязка к разделам',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'KEEP_IBLOCK_SECTION_ID' => 'N',
                        ),
                ),
            'ACTIVE' =>
                array(
                    'NAME' => 'Активность',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'Y',
                ),
            'ACTIVE_FROM' =>
                array(
                    'NAME' => 'Начало активности',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'ACTIVE_TO' =>
                array(
                    'NAME' => 'Окончание активности',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SORT' =>
                array(
                    'NAME' => 'Сортировка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '0',
                ),
            'NAME' =>
                array(
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'PREVIEW_PICTURE' =>
                array(
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'FROM_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'DELETE_WITH_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'PREVIEW_TEXT_TYPE' =>
                array(
                    'NAME' => 'Тип описания для анонса',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                ),
            'PREVIEW_TEXT' =>
                array(
                    'NAME' => 'Описание для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'DETAIL_PICTURE' =>
                array(
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'DETAIL_TEXT_TYPE' =>
                array(
                    'NAME' => 'Тип детального описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'html',
                ),
            'DETAIL_TEXT' =>
                array(
                    'NAME' => 'Детальное описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'XML_ID' =>
                array(
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'CODE' =>
                array(
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'UNIQUE' => 'N',
                            'TRANSLITERATION' => 'N',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ),
                ),
            'TAGS' =>
                array(
                    'NAME' => 'Теги',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_NAME' =>
                array(
                    'NAME' => 'Название',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_PICTURE' =>
                array(
                    'NAME' => 'Картинка для анонса',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'FROM_DETAIL' => 'N',
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'DELETE_WITH_DETAIL' => 'N',
                            'UPDATE_WITH_DETAIL' => 'N',
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'SECTION_DESCRIPTION_TYPE' =>
                array(
                    'NAME' => 'Тип описания',
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => 'text',
                ),
            'SECTION_DESCRIPTION' =>
                array(
                    'NAME' => 'Описание',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_DETAIL_PICTURE' =>
                array(
                    'NAME' => 'Детальная картинка',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'SCALE' => 'N',
                            'WIDTH' => '',
                            'HEIGHT' => '',
                            'IGNORE_ERRORS' => 'N',
                            'METHOD' => 'resample',
                            'COMPRESSION' => 95,
                            'USE_WATERMARK_TEXT' => 'N',
                            'WATERMARK_TEXT' => '',
                            'WATERMARK_TEXT_FONT' => '',
                            'WATERMARK_TEXT_COLOR' => '',
                            'WATERMARK_TEXT_SIZE' => '',
                            'WATERMARK_TEXT_POSITION' => 'tl',
                            'USE_WATERMARK_FILE' => 'N',
                            'WATERMARK_FILE' => '',
                            'WATERMARK_FILE_ALPHA' => '',
                            'WATERMARK_FILE_POSITION' => 'tl',
                            'WATERMARK_FILE_ORDER' => null,
                        ),
                ),
            'SECTION_XML_ID' =>
                array(
                    'NAME' => 'Внешний код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' => '',
                ),
            'SECTION_CODE' =>
                array(
                    'NAME' => 'Символьный код',
                    'IS_REQUIRED' => 'N',
                    'DEFAULT_VALUE' =>
                        array(
                            'UNIQUE' => 'N',
                            'TRANSLITERATION' => 'N',
                            'TRANS_LEN' => 100,
                            'TRANS_CASE' => 'L',
                            'TRANS_SPACE' => '-',
                            'TRANS_OTHER' => '-',
                            'TRANS_EAT' => 'Y',
                            'USE_GOOGLE' => 'N',
                        ),
                ),
        ));
        $helper->Iblock()->saveGroupPermissions($iblockId, array(
            'administrators' => 'X',
            'everyone' => 'R',
            'content_editor' => 'W',
        ));
        $helper->UserOptions()->saveElementForm($iblockId, array(
            'Элемент' =>
                array(
                    'ID' => 'ID',
                    'DATE_CREATE' => 'Создан',
                    'TIMESTAMP_X' => 'Изменен',
                    'ACTIVE' => 'Активность',
                    'ACTIVE_FROM' => 'Начало активности',
                    'ACTIVE_TO' => 'Окончание активности',
                    'NAME' => 'Название',
                    'XML_ID' => 'Внешний код',
                    'SORT' => 'Сортировка',
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
            'Разделы' =>
                array(
                    'SECTIONS' => 'Разделы',
                ),
        ));
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists('franchise_img_slider', 'CONTENT');
        $helper->Iblock()->deleteIblockIfExists('franchise_html_slider', 'CONTENT');
    }
}
