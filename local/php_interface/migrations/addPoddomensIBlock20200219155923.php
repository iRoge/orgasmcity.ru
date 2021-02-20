<?php

namespace Sprint\Migration;

class addPoddomensIBlock20200219155923 extends Version
{
    protected $description = "Создает инфоблок SEO для всех страниц";

    protected $moduleVersion = "3.12.12";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        // Создаем инфоблок Поддомены

        $helper = $this->getHelperManager();
        $iblockId = $helper->Iblock()->saveIblock(array(
            'IBLOCK_TYPE_ID' => 'SYSTEM',
            'LID' =>
                array(
                    0 => 's1',
                ),
            'CODE' => 'poddomens',
            'NAME' => 'SEO для всех страниц',
            'ACTIVE' => 'Y',
            'SORT' => '500',
            'LIST_PAGE_URL' => '#SITE_DIR#/SYSTEM/index.php?ID=#IBLOCK_ID#',
            'DETAIL_PAGE_URL' => '#SITE_DIR#/SYSTEM/detail.php?ID=#ELEMENT_ID#',
            'SECTION_PAGE_URL' => '#SITE_DIR#/SYSTEM/list.php?SECTION_ID=#SECTION_ID#',
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
            'XML_ID' => 'poddomens',
            'INDEX_ELEMENT' => 'N',
            'INDEX_SECTION' => 'N',
            'WORKFLOW' => 'N',
            'BIZPROC' => 'N',
            'SECTION_CHOOSER' => 'L',
            'LIST_MODE' => '',
            'RIGHTS_MODE' => 'S',
            'SECTION_PROPERTY' => 'N',
            'PROPERTY_INDEX' => 'N',
            'VERSION' => '2',
            'LAST_CONV_ELEMENT' => '0',
            'SOCNET_GROUP_ID' => null,
            'EDIT_FILE_BEFORE' => '',
            'EDIT_FILE_AFTER' => '',
            'SECTIONS_NAME' => 'Разделы',
            'SECTION_NAME' => 'Раздел',
            'ELEMENTS_NAME' => 'Элементы',
            'ELEMENT_NAME' => 'Элемент',
            'EXTERNAL_ID' => 'poddomens',
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
                    'IS_REQUIRED' => 'Y',
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

        // Создаем пользовательские поля

        $helper = $this->getHelperManager();
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'IBLOCK_SYSTEM:poddomens_SECTION',
            'FIELD_NAME' => 'UF_P_VARIABLE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_P_VARIABLE',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'SIZE' => 50,
                    'ROWS' => 1,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Географическая переменная #GEO#',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Географическая переменная #GEO#',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Географическая переменная #GEO#',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Географическая переменная #GEO#',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Географическая переменная #GEO#',
                ),
        ));

        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'IBLOCK_SYSTEM:poddomens_SECTION',
            'FIELD_NAME' => 'UF_TITLE_VARIABLE',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => 'UF_TITLE_VARIABLE',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
                array(
                    'SIZE' => 50,
                    'ROWS' => 1,
                    'REGEXP' => '',
                    'MIN_LENGTH' => 0,
                    'MAX_LENGTH' => 0,
                    'DEFAULT_VALUE' => '',
                ),
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Переменная заголовка #TITLE#',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Переменная заголовка #TITLE#',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Переменная заголовка #TITLE#',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Переменная заголовка #TITLE#',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Переменная заголовка #TITLE#',
                ),
        ));

        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'IBLOCK_SYSTEM:poddomens_SECTION',
            'FIELD_NAME' => 'UF_AREAL',
            'USER_TYPE_ID' => 'all_region_link',
            'XML_ID' => 'UF_AREAL',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => null,
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Территория',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Территория',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Территория',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Территория',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Территория',
                ),
        ));
        $helper->UserTypeEntity()->saveUserTypeEntity(array(
            'ENTITY_ID' => 'IBLOCK_SYSTEM:poddomens_SECTION',
            'FIELD_NAME' => 'UF_CAPITAL_CITY',
            'USER_TYPE_ID' => 'all_region_link',
            'XML_ID' => 'UF_CAPITAL_CITY',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'N',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' => null,
            'EDIT_FORM_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Столица',
                ),
            'LIST_COLUMN_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Столица',
                ),
            'LIST_FILTER_LABEL' =>
                array(
                    'en' => '',
                    'ru' => 'Столица',
                ),
            'ERROR_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Столица',
                ),
            'HELP_MESSAGE' =>
                array(
                    'en' => '',
                    'ru' => 'Столица',
                ),
        ));
        $helper->UserOptions()->saveSectionForm($iblockId, array(
            'Раздел' =>
                array(
                    'ID' => 'ID',
                    'DATE_CREATE' => 'Создан',
                    'TIMESTAMP_X' => 'Изменен',
                    'ACTIVE' => 'Раздел активен',
                    'IBLOCK_SECTION_ID' => 'Родительский раздел',
                    'NAME' => 'Название',
                    'CODE' => 'Символьный код',
                    'UF_AREAL' => 'Территория',
                    'UF_CAPITAL_CITY' => 'Столица',
                    'UF_P_VARIABLE' => 'Географическая переменная ',
                    'UF_TITLE_VARIABLE' => 'Переменная заголовка ',
                ),
            'SEO' =>
                array(
                    'IPROPERTY_TEMPLATES_SECTION' => 'Настройки для разделов',
                    'IPROPERTY_TEMPLATES_SECTION_META_TITLE' => 'Шаблон META TITLE',
                    'IPROPERTY_TEMPLATES_SECTION_META_KEYWORDS' => 'Шаблон META KEYWORDS',
                    'IPROPERTY_TEMPLATES_SECTION_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
                    'IPROPERTY_TEMPLATES_SECTION_PAGE_TITLE' => 'Заголовок раздела',
                    'IPROPERTY_TEMPLATES_ELEMENT' => 'Настройки для элементов',
                    'IPROPERTY_TEMPLATES_ELEMENT_META_TITLE' => 'Шаблон META TITLE',
                    'IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS' => 'Шаблон META KEYWORDS',
                    'IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION' => 'Шаблон META DESCRIPTION',
                    'IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE' => 'Заголовок товара',
                    'IPROPERTY_TEMPLATES_SECTIONS_PICTURE' => 'Настройки для изображений разделов',
                    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_ALT' => 'Шаблон ALT',
                    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                    'IPROPERTY_TEMPLATES_SECTION_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                    'IPROPERTY_TEMPLATES_SECTIONS_DETAIL_PICTURE' => 'Настройки для детальных картинок разделов',
                    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
                    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                    'IPROPERTY_TEMPLATES_SECTION_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                    'IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE' => 'Настройки для картинок анонса элементов',
                    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT' => 'Шаблон ALT',
                    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                    'IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                    'IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE' => 'Настройки для детальных картинок элементов',
                    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT' => 'Шаблон ALT',
                    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE' => 'Шаблон TITLE',
                    'IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME' => 'Шаблон имени файла',
                    'IPROPERTY_TEMPLATES_MANAGEMENT' => 'Управление',
                    'IPROPERTY_CLEAR_VALUES' => 'Очистить кеш вычисленных значений',
                ),
        ));

        $iblockId = $helper->Iblock()->getIblockIdIfExists(
            'poddomens',
            'SYSTEM'
        );

        $helper->Iblock()->addSectionsFromTree(
            $iblockId,
            array(
                0 =>
                    array(
                        'NAME' => 'Воронеж',
                        'CODE' => 'voronezh',
                        'SORT' => '500',
                        'ACTIVE' => 'Y',
                        'XML_ID' => '',
                        'DESCRIPTION' => '',
                        'DESCRIPTION_TYPE' => 'text',
                        'CHILDS' =>
                            array(
                                0 =>
                                    array(
                                        'NAME' => 'Возврат товара',
                                        'CODE' => 'company_repayment',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => 'company_repayment',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                    ),
                                1 =>
                                    array(
                                        'NAME' => 'Для мужчин',
                                        'CODE' => 'dlya_muzhchin',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => 'dlya_muzhchin',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Обувь',
                                                        'CODE' => 'obuv',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => 'obuv',
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Кеды',
                                                                        'CODE' => 'kedy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => 'kedy',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                2 =>
                                    array(
                                        'NAME' => 'Статьи',
                                        'CODE' => 'articles',
                                        'SORT' => '500',
                                        'ACTIVE' => 'N',
                                        'XML_ID' => 'articles',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                    ),
                                3 =>
                                    array(
                                        'NAME' => 'Главная',
                                        'CODE' => '/',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => 'index',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                    ),
                                4 =>
                                    array(
                                        'NAME' => 'Каталог (для группировок и спецразделов)',
                                        'CODE' => 'catalog',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Скидки (sale)',
                                                        'CODE' => 'sale',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Новинки (new)',
                                                        'CODE' => 'new',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Избранное',
                                                        'CODE' => 'favorites',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
                1 =>
                    array(
                        'NAME' => 'Страховка для территории без поддоменов',
                        'CODE' => 'strahovkaNoPoddomen',
                        'SORT' => '500',
                        'ACTIVE' => 'Y',
                        'XML_ID' => 'strahovkaNoPoddomen',
                        'DESCRIPTION' => '',
                        'DESCRIPTION_TYPE' => 'text',
                        'CHILDS' =>
                            array(
                                0 =>
                                    array(
                                        'NAME' => 'Главная страница',
                                        'CODE' => '/',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => '',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                    ),
                                1 =>
                                    array(
                                        'NAME' => 'Женщинам',
                                        'CODE' => 'dlya_zhenshchin',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Женская обувь',
                                                        'CODE' => 'obuv',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Балетки',
                                                                        'CODE' => 'baletki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Босоножки',
                                                                        'CODE' => 'bosonozhki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Ботильоны',
                                                                        'CODE' => 'botilony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Ботинки',
                                                                        'CODE' => 'botinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Ботфорты',
                                                                        'CODE' => 'botforty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Дутики',
                                                                        'CODE' => 'dutiki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Кеды',
                                                                        'CODE' => 'kedy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                7 =>
                                                                    array(
                                                                        'NAME' => 'Кроссовки',
                                                                        'CODE' => 'krossovki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                8 =>
                                                                    array(
                                                                        'NAME' => 'Лоферы',
                                                                        'CODE' => 'lofery',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                9 =>
                                                                    array(
                                                                        'NAME' => 'Мокасины',
                                                                        'CODE' => 'mokasiny',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                10 =>
                                                                    array(
                                                                        'NAME' => 'Полуботинки',
                                                                        'CODE' => 'polubotinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                11 =>
                                                                    array(
                                                                        'NAME' => 'Полусапоги',
                                                                        'CODE' => 'polusapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                12 =>
                                                                    array(
                                                                        'NAME' => 'Сабо',
                                                                        'CODE' => 'sabo',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                13 =>
                                                                    array(
                                                                        'NAME' => 'Сандалии',
                                                                        'CODE' => 'sandalii',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                14 =>
                                                                    array(
                                                                        'NAME' => 'Сапоги',
                                                                        'CODE' => 'sapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                15 =>
                                                                    array(
                                                                        'NAME' => 'Слипоны',
                                                                        'CODE' => 'slipony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                16 =>
                                                                    array(
                                                                        'NAME' => 'Туфли',
                                                                        'CODE' => 'tufli_',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                17 =>
                                                                    array(
                                                                        'NAME' => 'Туфли закрытые',
                                                                        'CODE' => 'tufli_zakrytye',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                18 =>
                                                                    array(
                                                                        'NAME' => 'Туфли лодочки',
                                                                        'CODE' => 'tufli_lodochki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                19 =>
                                                                    array(
                                                                        'NAME' => 'Туфли открытые',
                                                                        'CODE' => 'tufli_otkrytye',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                20 =>
                                                                    array(
                                                                        'NAME' => 'Угги',
                                                                        'CODE' => 'uggi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                21 =>
                                                                    array(
                                                                        'NAME' => 'Сланцы',
                                                                        'CODE' => 'slantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                22 =>
                                                                    array(
                                                                        'NAME' => 'Шлепанцы',
                                                                        'CODE' => 'shlepantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                23 =>
                                                                    array(
                                                                        'NAME' => 'Эспадрильи',
                                                                        'CODE' => 'espadrili',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                24 =>
                                                                    array(
                                                                        'NAME' => 'Мюли',
                                                                        'CODE' => 'myuli',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                25 =>
                                                                    array(
                                                                        'NAME' => 'Пантолеты',
                                                                        'CODE' => 'pantolety',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                26 =>
                                                                    array(
                                                                        'NAME' => 'Резиновая обувь',
                                                                        'CODE' => 'rezinovaya_obuv',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Сумки',
                                                        'CODE' => 'sumki',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Рюкзаки',
                                                                        'CODE' => 'ryukzaki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Сумки',
                                                                        'CODE' => 'sumki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Клатчи',
                                                                        'CODE' => 'klatchi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Аксессуары',
                                                        'CODE' => 'aksessuary',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ремни',
                                                                        'CODE' => 'remni',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Бижутерия',
                                                                        'CODE' => 'bizhuteriya',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Шарфы и платки',
                                                                        'CODE' => 'sharfy_i_platki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Головные уборы',
                                                                        'CODE' => 'golovnye_ubory',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Очки',
                                                                        'CODE' => 'ochki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Кошельки',
                                                                        'CODE' => 'koshelki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Зонты',
                                                                        'CODE' => 'zonty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                2 =>
                                    array(
                                        'NAME' => 'Мужчинам',
                                        'CODE' => 'dlya_muzhchin',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Мужская обувь',
                                                        'CODE' => 'obuv',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ботинки',
                                                                        'CODE' => 'botinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Кеды',
                                                                        'CODE' => 'kedy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Кроссовки',
                                                                        'CODE' => 'krossovki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Мокасины',
                                                                        'CODE' => 'mokasiny',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Полуботинки',
                                                                        'CODE' => 'polubotinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Полусапоги',
                                                                        'CODE' => 'polusapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Сабо',
                                                                        'CODE' => 'sabo',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                7 =>
                                                                    array(
                                                                        'NAME' => 'Сандалии',
                                                                        'CODE' => 'sandalii',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                8 =>
                                                                    array(
                                                                        'NAME' => 'Слипоны',
                                                                        'CODE' => 'slipony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                9 =>
                                                                    array(
                                                                        'NAME' => 'Челси',
                                                                        'CODE' => 'chelsi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                10 =>
                                                                    array(
                                                                        'NAME' => 'Эспадрильи',
                                                                        'CODE' => 'espadrili',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                11 =>
                                                                    array(
                                                                        'NAME' => 'Шлепанцы',
                                                                        'CODE' => 'shlepantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                12 =>
                                                                    array(
                                                                        'NAME' => 'Пантолеты',
                                                                        'CODE' => 'pantolety',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                13 =>
                                                                    array(
                                                                        'NAME' => 'Угги',
                                                                        'CODE' => 'uggi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                14 =>
                                                                    array(
                                                                        'NAME' => 'Лоферы',
                                                                        'CODE' => 'lofery',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                15 =>
                                                                    array(
                                                                        'NAME' => 'Сапоги',
                                                                        'CODE' => 'sapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                16 =>
                                                                    array(
                                                                        'NAME' => 'Дутики',
                                                                        'CODE' => 'dutiki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Сумки',
                                                        'CODE' => 'sumki',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Сумки',
                                                                        'CODE' => 'sumki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Рюкзаки',
                                                                        'CODE' => 'ryukzaki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Портфели',
                                                                        'CODE' => 'portfeli',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Аксессуары',
                                                        'CODE' => 'aksessuary',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ремни',
                                                                        'CODE' => 'remni',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Зонты',
                                                                        'CODE' => 'zonty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Головные уборы',
                                                                        'CODE' => 'golovnye_ubory',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Шарфы и платки',
                                                                        'CODE' => 'sharfy_i_platki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Кошельки',
                                                                        'CODE' => 'koshelki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                3 =>
                                    array(
                                        'NAME' => 'Каталог (для группировок и спецразделов)',
                                        'CODE' => 'catalog',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Скидки (sale)',
                                                        'CODE' => 'sale',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Новинки (new)',
                                                        'CODE' => 'new',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Избранное',
                                                        'CODE' => 'favorites',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                    ),
                                            ),
                                    ),

                            ),
                    ),
                2 =>
                    array(
                        'NAME' => 'Страховка для территории с поддоменами',
                        'CODE' => 'strahovkaPoddomen',
                        'SORT' => '500',
                        'ACTIVE' => 'Y',
                        'XML_ID' => 'strahovkaPoddomen',
                        'DESCRIPTION' => '',
                        'DESCRIPTION_TYPE' => 'text',
                        'CHILDS' =>
                            array(
                                0 =>
                                    array(
                                        'NAME' => 'Главная страница',
                                        'CODE' => '/',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => '',
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                    ),
                                1 =>
                                    array(
                                        'NAME' => 'Женщинам',
                                        'CODE' => 'dlya_zhenshchin',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Женская обувь',
                                                        'CODE' => 'obuv',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Балетки',
                                                                        'CODE' => 'baletki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Босоножки',
                                                                        'CODE' => 'bosonozhki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Ботильоны',
                                                                        'CODE' => 'botilony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Ботинки',
                                                                        'CODE' => 'botinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Ботфорты',
                                                                        'CODE' => 'botforty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Дутики',
                                                                        'CODE' => 'dutiki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Кеды',
                                                                        'CODE' => 'kedy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                7 =>
                                                                    array(
                                                                        'NAME' => 'Кроссовки',
                                                                        'CODE' => 'krossovki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                8 =>
                                                                    array(
                                                                        'NAME' => 'Лоферы',
                                                                        'CODE' => 'lofery',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                9 =>
                                                                    array(
                                                                        'NAME' => 'Мокасины',
                                                                        'CODE' => 'mokasiny',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                10 =>
                                                                    array(
                                                                        'NAME' => 'Полуботинки',
                                                                        'CODE' => 'polubotinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                11 =>
                                                                    array(
                                                                        'NAME' => 'Полусапоги',
                                                                        'CODE' => 'polusapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                12 =>
                                                                    array(
                                                                        'NAME' => 'Сабо',
                                                                        'CODE' => 'sabo',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                13 =>
                                                                    array(
                                                                        'NAME' => 'Сандалии',
                                                                        'CODE' => 'sandalii',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                14 =>
                                                                    array(
                                                                        'NAME' => 'Сапоги',
                                                                        'CODE' => 'sapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                15 =>
                                                                    array(
                                                                        'NAME' => 'Слипоны',
                                                                        'CODE' => 'slipony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                16 =>
                                                                    array(
                                                                        'NAME' => 'Туфли',
                                                                        'CODE' => 'tufli_',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                17 =>
                                                                    array(
                                                                        'NAME' => 'Туфли закрытые',
                                                                        'CODE' => 'tufli_zakrytye',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                18 =>
                                                                    array(
                                                                        'NAME' => 'Туфли лодочки',
                                                                        'CODE' => 'tufli_lodochki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                19 =>
                                                                    array(
                                                                        'NAME' => 'Туфли открытые',
                                                                        'CODE' => 'tufli_otkrytye',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                20 =>
                                                                    array(
                                                                        'NAME' => 'Угги',
                                                                        'CODE' => 'uggi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                21 =>
                                                                    array(
                                                                        'NAME' => 'Сланцы',
                                                                        'CODE' => 'slantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                22 =>
                                                                    array(
                                                                        'NAME' => 'Шлепанцы',
                                                                        'CODE' => 'shlepantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                23 =>
                                                                    array(
                                                                        'NAME' => 'Эспадрильи',
                                                                        'CODE' => 'espadrili',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                24 =>
                                                                    array(
                                                                        'NAME' => 'Мюли',
                                                                        'CODE' => 'myuli',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                25 =>
                                                                    array(
                                                                        'NAME' => 'Пантолеты',
                                                                        'CODE' => 'pantolety',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                26 =>
                                                                    array(
                                                                        'NAME' => 'Резиновая обувь',
                                                                        'CODE' => 'rezinovaya_obuv',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Сумки',
                                                        'CODE' => 'sumki',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Рюкзаки',
                                                                        'CODE' => 'ryukzaki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Сумки',
                                                                        'CODE' => 'sumki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Клатчи',
                                                                        'CODE' => 'klatchi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Аксессуары',
                                                        'CODE' => 'aksessuary',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ремни',
                                                                        'CODE' => 'remni',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Бижутерия',
                                                                        'CODE' => 'bizhuteriya',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Шарфы и платки',
                                                                        'CODE' => 'sharfy_i_platki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Головные уборы',
                                                                        'CODE' => 'golovnye_ubory',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Очки',
                                                                        'CODE' => 'ochki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Кошельки',
                                                                        'CODE' => 'koshelki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Зонты',
                                                                        'CODE' => 'zonty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                2 =>
                                    array(
                                        'NAME' => 'Мужчинам',
                                        'CODE' => 'dlya_muzhchin',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Мужская обувь',
                                                        'CODE' => 'obuv',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ботинки',
                                                                        'CODE' => 'botinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Кеды',
                                                                        'CODE' => 'kedy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Кроссовки',
                                                                        'CODE' => 'krossovki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Мокасины',
                                                                        'CODE' => 'mokasiny',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Полуботинки',
                                                                        'CODE' => 'polubotinki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                5 =>
                                                                    array(
                                                                        'NAME' => 'Полусапоги',
                                                                        'CODE' => 'polusapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                6 =>
                                                                    array(
                                                                        'NAME' => 'Сабо',
                                                                        'CODE' => 'sabo',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                7 =>
                                                                    array(
                                                                        'NAME' => 'Сандалии',
                                                                        'CODE' => 'sandalii',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                8 =>
                                                                    array(
                                                                        'NAME' => 'Слипоны',
                                                                        'CODE' => 'slipony',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                9 =>
                                                                    array(
                                                                        'NAME' => 'Челси',
                                                                        'CODE' => 'chelsi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                10 =>
                                                                    array(
                                                                        'NAME' => 'Эспадрильи',
                                                                        'CODE' => 'espadrili',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                11 =>
                                                                    array(
                                                                        'NAME' => 'Шлепанцы',
                                                                        'CODE' => 'shlepantsy',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                12 =>
                                                                    array(
                                                                        'NAME' => 'Пантолеты',
                                                                        'CODE' => 'pantolety',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                13 =>
                                                                    array(
                                                                        'NAME' => 'Угги',
                                                                        'CODE' => 'uggi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                14 =>
                                                                    array(
                                                                        'NAME' => 'Лоферы',
                                                                        'CODE' => 'lofery',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                15 =>
                                                                    array(
                                                                        'NAME' => 'Сапоги',
                                                                        'CODE' => 'sapogi',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                16 =>
                                                                    array(
                                                                        'NAME' => 'Дутики',
                                                                        'CODE' => 'dutiki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Сумки',
                                                        'CODE' => 'sumki',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Сумки',
                                                                        'CODE' => 'sumki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Рюкзаки',
                                                                        'CODE' => 'ryukzaki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Портфели',
                                                                        'CODE' => 'portfeli',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Аксессуары',
                                                        'CODE' => 'aksessuary',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Ремни',
                                                                        'CODE' => 'remni',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Зонты',
                                                                        'CODE' => 'zonty',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                2 =>
                                                                    array(
                                                                        'NAME' => 'Головные уборы',
                                                                        'CODE' => 'golovnye_ubory',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                3 =>
                                                                    array(
                                                                        'NAME' => 'Шарфы и платки',
                                                                        'CODE' => 'sharfy_i_platki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                                4 =>
                                                                    array(
                                                                        'NAME' => 'Кошельки',
                                                                        'CODE' => 'koshelki',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => '',
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                    ),
                                                            ),
                                                    ),
                                            ),
                                    ),
                                3 =>
                                    array(
                                        'NAME' => 'Каталог (для группировок и спецразделов)',
                                        'CODE' => 'catalog',
                                        'SORT' => '500',
                                        'ACTIVE' => 'Y',
                                        'XML_ID' => null,
                                        'DESCRIPTION' => '',
                                        'DESCRIPTION_TYPE' => 'text',
                                        'CHILDS' =>
                                            array(
                                                0 =>
                                                    array(
                                                        'NAME' => 'Скидки (sale)',
                                                        'CODE' => 'sale',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                1 =>
                                                    array(
                                                        'NAME' => 'Новинки (new)',
                                                        'CODE' => 'new',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                        'CHILDS' =>
                                                            array(
                                                                0 =>
                                                                    array(
                                                                        'NAME' => 'Женщинам',
                                                                        'CODE' => 'dlya_zhenshchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Женская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Балетки',
                                                                                                        'CODE' => 'baletki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Босоножки',
                                                                                                        'CODE' => 'bosonozhki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботильоны',
                                                                                                        'CODE' => 'botilony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботфорты',
                                                                                                        'CODE' => 'botforty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли',
                                                                                                        'CODE' => 'tufli_',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                17 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли закрытые',
                                                                                                        'CODE' => 'tufli_zakrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                18 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли лодочки',
                                                                                                        'CODE' => 'tufli_lodochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                19 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Туфли открытые',
                                                                                                        'CODE' => 'tufli_otkrytye',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                20 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                21 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сланцы',
                                                                                                        'CODE' => 'slantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                22 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                23 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                24 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мюли',
                                                                                                        'CODE' => 'myuli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                25 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                26 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Резиновая обувь',
                                                                                                        'CODE' => 'rezinovaya_obuv',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Клатчи',
                                                                                                        'CODE' => 'klatchi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Бижутерия',
                                                                                                        'CODE' => 'bizhuteriya',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Очки',
                                                                                                        'CODE' => 'ochki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                                1 =>
                                                                    array(
                                                                        'NAME' => 'Мужчинам',
                                                                        'CODE' => 'dlya_muzhchin',
                                                                        'SORT' => '500',
                                                                        'ACTIVE' => 'Y',
                                                                        'XML_ID' => null,
                                                                        'DESCRIPTION' => '',
                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                        'CHILDS' =>
                                                                            array(
                                                                                0 =>
                                                                                    array(
                                                                                        'NAME' => 'Мужская обувь',
                                                                                        'CODE' => 'obuv',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ботинки',
                                                                                                        'CODE' => 'botinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кеды',
                                                                                                        'CODE' => 'kedy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кроссовки',
                                                                                                        'CODE' => 'krossovki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Мокасины',
                                                                                                        'CODE' => 'mokasiny',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полуботинки',
                                                                                                        'CODE' => 'polubotinki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                5 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Полусапоги',
                                                                                                        'CODE' => 'polusapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                6 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сабо',
                                                                                                        'CODE' => 'sabo',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                7 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сандалии',
                                                                                                        'CODE' => 'sandalii',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                8 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Слипоны',
                                                                                                        'CODE' => 'slipony',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                9 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Челси',
                                                                                                        'CODE' => 'chelsi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                10 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Эспадрильи',
                                                                                                        'CODE' => 'espadrili',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                11 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шлепанцы',
                                                                                                        'CODE' => 'shlepantsy',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                12 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Пантолеты',
                                                                                                        'CODE' => 'pantolety',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                13 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Угги',
                                                                                                        'CODE' => 'uggi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                14 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Лоферы',
                                                                                                        'CODE' => 'lofery',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                15 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сапоги',
                                                                                                        'CODE' => 'sapogi',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                16 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Дутики',
                                                                                                        'CODE' => 'dutiki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                1 =>
                                                                                    array(
                                                                                        'NAME' => 'Сумки',
                                                                                        'CODE' => 'sumki',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Сумки',
                                                                                                        'CODE' => 'sumki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Рюкзаки',
                                                                                                        'CODE' => 'ryukzaki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Портфели',
                                                                                                        'CODE' => 'portfeli',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                                2 =>
                                                                                    array(
                                                                                        'NAME' => 'Аксессуары',
                                                                                        'CODE' => 'aksessuary',
                                                                                        'SORT' => '500',
                                                                                        'ACTIVE' => 'Y',
                                                                                        'XML_ID' => null,
                                                                                        'DESCRIPTION' => '',
                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                        'CHILDS' =>
                                                                                            array(
                                                                                                0 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Ремни',
                                                                                                        'CODE' => 'remni',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                1 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Зонты',
                                                                                                        'CODE' => 'zonty',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                2 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Головные уборы',
                                                                                                        'CODE' => 'golovnye_ubory',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => null,
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                3 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Шарфы и платки',
                                                                                                        'CODE' => 'sharfy_i_platki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                                4 =>
                                                                                                    array(
                                                                                                        'NAME' => 'Кошельки',
                                                                                                        'CODE' => 'koshelki',
                                                                                                        'SORT' => '500',
                                                                                                        'ACTIVE' => 'Y',
                                                                                                        'XML_ID' => '',
                                                                                                        'DESCRIPTION' => '',
                                                                                                        'DESCRIPTION_TYPE' => 'text',
                                                                                                    ),
                                                                                            ),
                                                                                    ),
                                                                            ),
                                                                    ),
                                                            ),
                                                    ),
                                                2 =>
                                                    array(
                                                        'NAME' => 'Избранное',
                                                        'CODE' => 'favorites',
                                                        'SORT' => '500',
                                                        'ACTIVE' => 'Y',
                                                        'XML_ID' => null,
                                                        'DESCRIPTION' => '',
                                                        'DESCRIPTION_TYPE' => 'text',
                                                    ),
                                            ),
                                    ),
                            ),
                    ),
            )
        );
    }

    public function down()
    {
        $helper = $this->getHelperManager();
        try {
            $helper->Iblock()->deleteIblockIfExists("poddomens");
            $helper->UserTypeEntity()->deleteUserTypeEntitiesIfExists("IBLOCK_SYSTEM:poddomens_SECTION", ['UF_CAPITAL_CITY', 'UF_TITLE_VARIABLE', 'UF_P_VARIABLE', 'UF_AREAL']);
        } catch (Exceptions\HelperException $e) {
        }
    }
}
