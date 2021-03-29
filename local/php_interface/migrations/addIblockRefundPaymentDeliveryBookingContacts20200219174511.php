<?php

#TODO: проверил, используется для страниц "Оплата", "Возврат" и т.д. ... Удалить лишнее и можно юзать

namespace Sprint\Migration;

class addIblockRefundPaymentDeliveryBookingContacts20200219174511 extends Version
{
    protected $description = "Создает ИБ Возврат, Оплата, Доставка, Резерв в магазине, Контакты";

    protected $moduleVersion = "3.12.12";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */
    public function up()
    {
        $this->createIB('payment', 'Оплата');
        $this->createIB('refundNew', 'Возврат');
        $this->createIB('delivery', 'Доставка');
        $this->createIB('contacts', 'Контакты');
        $this->createIB('reserv', 'Резерв в магазине');
    }

    public function down()
    {
        $this->deleteIB('payment');
        $this->deleteIB('refundNew');
        $this->deleteIB('delivery');
        $this->deleteIB('contacts');
        $this->deleteIB('reserv');
    }

    private function createIB($code, $name)
    {
        try {
            $helper = $this->getHelperManager();
            $iblockId = $helper->Iblock()->saveIblock(array(
                'IBLOCK_TYPE_ID' => 'CONTENT',
                'LID' =>
                    array(
                        0 => 's1',
                    ),
                'CODE' => $code,
                'NAME' => $name,
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'LIST_PAGE_URL' => '#SITE_DIR#/CONTENT/index.php?ID=#IBLOCK_ID#',
                'DETAIL_PAGE_URL' => '#SITE_DIR#/CONTENT/detail.php?ID=#ELEMENT_ID#',
                'SECTION_PAGE_URL' => '#SITE_DIR#/CONTENT/list.php?SECTION_ID=#SECTION_ID#',
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
                'XML_ID' => $code,
                'INDEX_ELEMENT' => 'Y',
                'INDEX_SECTION' => 'Y',
                'WORKFLOW' => 'N',
                'BIZPROC' => 'N',
                'SECTION_CHOOSER' => 'L',
                'LIST_MODE' => '',
                'RIGHTS_MODE' => 'S',
                'SECTION_PROPERTY' => null,
                'PROPERTY_INDEX' => null,
                'VERSION' => '2',
                'LAST_CONV_ELEMENT' => '0',
                'SOCNET_GROUP_ID' => null,
                'EDIT_FILE_BEFORE' => '',
                'EDIT_FILE_AFTER' => '',
                'SECTIONS_NAME' => 'Разделы',
                'SECTION_NAME' => 'Раздел',
                'ELEMENTS_NAME' => 'Элементы',
                'ELEMENT_NAME' => 'Элемент',
                'EXTERNAL_ID' => 'payment',
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
                        'IS_REQUIRED' => 'Y',
                        'DEFAULT_VALUE' =>
                            array(
                                'UNIQUE' => 'Y',
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
            for ($i = 1; $i <= 20; $i++) {
                $helper->Iblock()->saveProperty($iblockId, array(
                    'NAME' => 'Секция ' . $i,
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'SECTION_' . $i . '_TEXT',
                    'DEFAULT_VALUE' =>
                        array(
                            'TEXT' => '',
                            'TYPE' => 'HTML',
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
                    'FILTRABLE' => 'N',
                    'IS_REQUIRED' => 'N',
                    'VERSION' => '2',
                    'USER_TYPE' => 'HTML',
                    'USER_TYPE_SETTINGS' =>
                        array(
                            'height' => 200,
                        ),
                    'HINT' => '',
                ));
                $helper->Iblock()->saveProperty($iblockId, array(
                    'NAME' => 'Название секции ' . $i,
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'SECTION_' . $i . '_NAME',
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
                    'VERSION' => '2',
                    'USER_TYPE' => null,
                    'USER_TYPE_SETTINGS' => null,
                    'HINT' => '',
                ));
                $helper->Iblock()->saveProperty($iblockId, array(
                    'NAME' => 'Активность секции ' . $i,
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'SECTION_' . $i . '_ACTIVE',
                    'DEFAULT_VALUE' => 0,
                    'PROPERTY_TYPE' => 'N',
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
                    'VERSION' => '2',
                    'USER_TYPE' => 'SASDCheckboxNum',
                    'USER_TYPE_SETTINGS' =>
                        array(
                            'VIEW' =>
                                array(
                                    0 => 'Нет',
                                    1 => 'Да',
                                ),
                        ),
                    'HINT' => '',
                ));
                $helper->Iblock()->saveProperty($iblockId, array(
                    'NAME' => 'Сворачивать секцию ' . $i,
                    'ACTIVE' => 'Y',
                    'SORT' => '500',
                    'CODE' => 'SECTION_' . $i . '_COLLAPSE',
                    'DEFAULT_VALUE' => 0,
                    'PROPERTY_TYPE' => 'N',
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
                    'VERSION' => '2',
                    'USER_TYPE' => 'SASDCheckboxNum',
                    'USER_TYPE_SETTINGS' =>
                        array(
                            'VIEW' =>
                                array(
                                    0 => 'Нет',
                                    1 => 'Да',
                                ),
                        ),
                    'HINT' => '',
                ));
            }

            $helper->Iblock()->saveProperty($iblockId, array(
                'NAME' => 'Местоположение',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'LOCATION',
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
                'IS_REQUIRED' => 'Y',
                'VERSION' => '2',
                'USER_TYPE' => 'ALL_REGION_CODE',
                'USER_TYPE_SETTINGS' => null,
                'HINT' => '',
            ));
            $helper->UserOptions()->saveElementForm($iblockId, array(
                'Элемент' =>
                    array(
                        'ID' => 'ID',
                        'DATE_CREATE' => 'Создан',
                        'TIMESTAMP_X' => 'Изменен',
                        'ACTIVE' => 'Активность',
                        'NAME' => 'Название',
                        'CODE' => 'Символьный код',
                        'XML_ID' => 'Внешний код',
                        'IBLOCK_ELEMENT_PROP_VALUE' => 'Значения свойств',
                        'PROPERTY_LOCATION' => 'Местоположение',
                    ),
                'Анонс' =>
                    array(
                        'PREVIEW_PICTURE' => 'Картинка для анонса',
                        'PREVIEW_TEXT' => 'Описание для анонса',
                    ),
                'Секции' =>
                    array(
                        'PROPERTY_SECTION_1_NAME' => 'Название секции 1',
                        'PROPERTY_SECTION_1_ACTIVE' => 'Активность секции 1',
                        'PROPERTY_SECTION_1_COLLAPSE' => 'Сворачивать секцию 1',
                        'PROPERTY_SECTION_1_TEXT' => 'Секция 1',
                        'PROPERTY_SECTION_2_NAME' => 'Название секции 2',
                        'PROPERTY_SECTION_2_ACTIVE' => 'Активность секции 2',
                        'PROPERTY_SECTION_2_COLLAPSE' => 'Сворачивать секцию 2',
                        'PROPERTY_SECTION_2_TEXT' => 'Секция 2',
                        'PROPERTY_SECTION_3_NAME' => 'Название секции 3',
                        'PROPERTY_SECTION_3_ACTIVE' => 'Активность секции 3',
                        'PROPERTY_SECTION_3_COLLAPSE' => 'Сворачивать секцию 3',
                        'PROPERTY_SECTION_3_TEXT' => 'Секция 3',
                        'PROPERTY_SECTION_4_NAME' => 'Название секции 4',
                        'PROPERTY_SECTION_4_ACTIVE' => 'Активность секции 4',
                        'PROPERTY_SECTION_4_COLLAPSE' => 'Сворачивать секцию 4',
                        'PROPERTY_SECTION_4_TEXT' => 'Секция 4',
                        'PROPERTY_SECTION_5_NAME' => 'Название секции 5',
                        'PROPERTY_SECTION_5_ACTIVE' => 'Активность секции 5',
                        'PROPERTY_SECTION_5_COLLAPSE' => 'Сворачивать секцию 5',
                        'PROPERTY_SECTION_5_TEXT' => 'Секция 5',
                        'PROPERTY_SECTION_6_NAME' => 'Название секции 6',
                        'PROPERTY_SECTION_6_ACTIVE' => 'Активность секции 6',
                        'PROPERTY_SECTION_6_COLLAPSE' => 'Сворачивать секцию 6',
                        'PROPERTY_SECTION_6_TEXT' => 'Секция 6',
                        'PROPERTY_SECTION_7_NAME' => 'Название секции 7',
                        'PROPERTY_SECTION_7_ACTIVE' => 'Активность секции 7',
                        'PROPERTY_SECTION_7_COLLAPSE' => 'Сворачивать секцию 7',
                        'PROPERTY_SECTION_7_TEXT' => 'Секция 7',
                        'PROPERTY_SECTION_8_NAME' => 'Название секции 8',
                        'PROPERTY_SECTION_8_ACTIVE' => 'Активность секции 8',
                        'PROPERTY_SECTION_8_COLLAPSE' => 'Сворачивать секцию 8',
                        'PROPERTY_SECTION_8_TEXT' => 'Секция 8',
                        'PROPERTY_SECTION_9_NAME' => 'Название секции 9',
                        'PROPERTY_SECTION_9_ACTIVE' => 'Активность секции 9',
                        'PROPERTY_SECTION_9_COLLAPSE' => 'Сворачивать секцию 9',
                        'PROPERTY_SECTION_9_TEXT' => 'Секция 9',
                        'PROPERTY_SECTION_10_NAME' => 'Название секции 10',
                        'PROPERTY_SECTION_10_ACTIVE' => 'Активность секции 10',
                        'PROPERTY_SECTION_10_COLLAPSE' => 'Сворачивать секцию 10',
                        'PROPERTY_SECTION_10_TEXT' => 'Секция 10',
                        'PROPERTY_SECTION_11_NAME' => 'Название секции 11',
                        'PROPERTY_SECTION_11_ACTIVE' => 'Активность секции 11',
                        'PROPERTY_SECTION_11_COLLAPSE' => 'Сворачивать секцию 11',
                        'PROPERTY_SECTION_11_TEXT' => 'Секция 11',
                        'PROPERTY_SECTION_12_NAME' => 'Название секции 12',
                        'PROPERTY_SECTION_12_ACTIVE' => 'Активность секции 12',
                        'PROPERTY_SECTION_12_COLLAPSE' => 'Сворачивать секцию 12',
                        'PROPERTY_SECTION_12_TEXT' => 'Секция 12',
                        'PROPERTY_SECTION_13_NAME' => 'Название секции 13',
                        'PROPERTY_SECTION_13_ACTIVE' => 'Активность секции 13',
                        'PROPERTY_SECTION_13_COLLAPSE' => 'Сворачивать секцию 13',
                        'PROPERTY_SECTION_13_TEXT' => 'Секция 13',
                        'PROPERTY_SECTION_14_NAME' => 'Название секции 14',
                        'PROPERTY_SECTION_14_ACTIVE' => 'Активность секции 14',
                        'PROPERTY_SECTION_14_COLLAPSE' => 'Сворачивать секцию 14',
                        'PROPERTY_SECTION_14_TEXT' => 'Секция 14',
                        'PROPERTY_SECTION_15_NAME' => 'Название секции 15',
                        'PROPERTY_SECTION_15_ACTIVE' => 'Активность секции 15',
                        'PROPERTY_SECTION_15_COLLAPSE' => 'Сворачивать секцию 15',
                        'PROPERTY_SECTION_15_TEXT' => 'Секция 15',
                        'PROPERTY_SECTION_16_NAME' => 'Название секции 16',
                        'PROPERTY_SECTION_16_ACTIVE' => 'Активность секции 16',
                        'PROPERTY_SECTION_16_COLLAPSE' => 'Сворачивать секцию 16',
                        'PROPERTY_SECTION_16_TEXT' => 'Секция 16',
                        'PROPERTY_SECTION_17_NAME' => 'Название секции 17',
                        'PROPERTY_SECTION_17_ACTIVE' => 'Активность секции 17',
                        'PROPERTY_SECTION_17_COLLAPSE' => 'Сворачивать секцию 17',
                        'PROPERTY_SECTION_17_TEXT' => 'Секция 17',
                        'PROPERTY_SECTION_18_NAME' => 'Название секции 18',
                        'PROPERTY_SECTION_18_ACTIVE' => 'Активность секции 18',
                        'PROPERTY_SECTION_18_COLLAPSE' => 'Сворачивать секцию 18',
                        'PROPERTY_SECTION_18_TEXT' => 'Секция 18',
                        'PROPERTY_SECTION_19_NAME' => 'Название секции 19',
                        'PROPERTY_SECTION_19_ACTIVE' => 'Активность секции 19',
                        'PROPERTY_SECTION_19_COLLAPSE' => 'Сворачивать секцию 19',
                        'PROPERTY_SECTION_19_TEXT' => 'Секция 19',
                        'PROPERTY_SECTION_20_NAME' => 'Название секции 20',
                        'PROPERTY_SECTION_20_ACTIVE' => 'Активность секции 20',
                        'PROPERTY_SECTION_20_COLLAPSE' => 'Сворачивать секцию 20',
                        'PROPERTY_SECTION_20_TEXT' => 'Секция 20',
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
            ));
        } catch (Exceptions\HelperException $e) {
            echo 'Error: ' . $e->getMessage() . '<br>';
        }
    }

    private function deleteIB($code)
    {
        $helper = $this->getHelperManager();
        $helper->Iblock()->deleteIblockIfExists($code, 'CONTENT');
    }
}
