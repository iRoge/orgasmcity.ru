<?php

namespace Sprint\Migration;

class MobileBannerMain20190918184808 extends Version
{

    protected $description = "Mobile Banner Main";

    public function up()
    {
        $helper = $this->getHelperManager();


        try {
            $iblockId = $helper->Iblock()->saveIblock(array(
                'IBLOCK_TYPE_ID' => 'CONTENT',
                'LID' =>
                    array(
                        0 => 's1',
                    ),
                'CODE' => 'main_slider_mobile_4',
                'NAME' => 'Слайдер на главной мобильный 4',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'LIST_PAGE_URL' => '',
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
                'XML_ID' => null,
                'INDEX_ELEMENT' => 'N',
                'INDEX_SECTION' => 'N',
                'WORKFLOW' => 'N',
                'BIZPROC' => 'N',
                'SECTION_CHOOSER' => 'L',
                'LIST_MODE' => '',
                'RIGHTS_MODE' => 'S',
                'SECTION_PROPERTY' => null,
                'PROPERTY_INDEX' => null,
                'VERSION' => '1',
                'LAST_CONV_ELEMENT' => '0',
                'SOCNET_GROUP_ID' => null,
                'EDIT_FILE_BEFORE' => '',
                'EDIT_FILE_AFTER' => '',
                'SECTIONS_NAME' => 'Разделы',
                'SECTION_NAME' => 'Раздел',
                'ELEMENTS_NAME' => 'Элементы',
                'ELEMENT_NAME' => 'Элемент',
                'EXTERNAL_ID' => null,
                'LANG_DIR' => '/',
                'SERVER_NAME' => 'respect-shoes.ru',
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

            $helper->Iblock()->saveProperty($iblockId, array(
                'NAME' => 'Местоположение',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'LOCATION',
                'DEFAULT_VALUE' => null,
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'Y',
                'XML_ID' => null,
                'FILE_TYPE' => null,
                'MULTIPLE_CNT' => '5',
                'LINK_IBLOCK_ID' => '0',
                'WITH_DESCRIPTION' => 'N',
                'SEARCHABLE' => 'N',
                'FILTRABLE' => 'N',
                'IS_REQUIRED' => 'N',
                'VERSION' => '1',
                'USER_TYPE' => null,
                'USER_TYPE_SETTINGS' => null,
                'HINT' => null,
            ));
            $helper->Iblock()->saveProperty($iblockId, array(
                'NAME' => 'Ссылка',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'LINK',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Изображение для мобильных',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'MOBILE_IMAGE',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'F',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Ссылка для мобильных',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'MOBILE_LINK',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Видео',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'VIDEO',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'F',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'Y',
                'XML_ID' => null,
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
                'NAME' => 'Превью для видео',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'VIDEO_PREVIEW',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'F',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Автовоспроизведение',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'AUTOPLAY',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'L',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'C',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                                'VALUE' => 'да',
                                'DEF' => 'N',
                                'SORT' => '500',
                                'XML_ID' => 'a450805a9e8373197975d09d1ed905d3',
                            ),
                    ),
            ));
            $helper->Iblock()->saveProperty($iblockId, array(
                'NAME' => 'Заглушка',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'VIDEO_GAG',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'F',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Ссылка для заглушки',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'VIDEO_GAG_LINK',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                'NAME' => 'Домены',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'RESPECTDOMAINS',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'S',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'L',
                'MULTIPLE' => 'Y',
                'XML_ID' => null,
                'FILE_TYPE' => '',
                'MULTIPLE_CNT' => '5',
                'LINK_IBLOCK_ID' => '0',
                'WITH_DESCRIPTION' => 'N',
                'SEARCHABLE' => 'N',
                'FILTRABLE' => 'N',
                'IS_REQUIRED' => 'N',
                'VERSION' => '1',
                'USER_TYPE' => 'directory',
                'USER_TYPE_SETTINGS' =>
                    array(
                        'size' => 1,
                        'width' => 0,
                        'group' => 'N',
                        'multiple' => 'N',
                        'TABLE_NAME' => 'b_respect_domains',
                    ),
                'HINT' => '',
            ));
            $helper->Iblock()->saveProperty($iblockId, array(
                'NAME' => 'Скрыть на выбранных',
                'ACTIVE' => 'Y',
                'SORT' => '500',
                'CODE' => 'RESPECTDOMAINS_HIDDEN',
                'DEFAULT_VALUE' => '',
                'PROPERTY_TYPE' => 'L',
                'ROW_COUNT' => '1',
                'COL_COUNT' => '30',
                'LIST_TYPE' => 'C',
                'MULTIPLE' => 'N',
                'XML_ID' => null,
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
                        'SORT' => 'Сортировка',
                        'PREVIEW_PICTURE' => 'Картинка для анонса',
                        'PROPERTY_MOBILE_IMAGE' => 'Изображение для мобильных',
                        'PROPERTY_LINK' => 'Ссылка',
                        'PROPERTY_MOBILE_LINK' => 'Ссылка для мобильных',
                        'PROPERTY_VIDEO' => 'Видео',
                        'PROPERTY_AUTOPLAY' => 'Автовоспроизведение',
                        'PROPERTY_VIDEO_PREVIEW' => 'Превью для видео',
                        'PROPERTY_VIDEO_GAG' => 'Заглушка',
                        'PROPERTY_VIDEO_GAG_LINK' => 'Ссылка для заглушки',
                        'edit1_csection1' => 'Настройки отображения',
                        'PROPERTY_RESPECTDOMAINS' => 'Домены',
                        'PROPERTY_RESPECTDOMAINS_HIDDEN' => 'Скрыть на выбранных',
                    ),
            ));
        } catch (Exceptions\HelperException $e) {
            $this->outError($e->getMessage());
        }
    }

    public function down()
    {
        $helper = $this->getHelperManager();

        $helper->Iblock()->deleteIblockIfExists("main_slider_mobile_4", "CONTENT");
    }
}
