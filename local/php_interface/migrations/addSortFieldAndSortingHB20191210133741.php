<?php

namespace Sprint\Migration;

class addSortFieldAndSortingHB20191210133741 extends Version
{
    protected $description = "Для основных HL-блоков добавляем свойство сортировка, в котором потом можно будет указать значение сортировки для свойства. (по умолчанию проставит везде 500). Сделает HB 'Sorting', в котором будут отражены все сортируемые свойства и можно проставить значение сортировки для них.";

    /**
     * @throws Exceptions\HelperException
     * @return bool|void
     */

    // HL-блоки, которым добавим свойство сортировки ('UF_SORT')
    private const HL_BLOCKS_NAME_LIST = [
        'Brand' => 'Бренд',
        'Liningmaterial' => 'Материал подкладки',
        'Uppermaterial' => 'Материал верха',
        'Heelheight' => 'Высота каблука',
        'Color' => 'Цвет',
        'Line' => 'Линия',
        'Materialsole' => 'Материал подошвы',
        'Typeproduct' => 'Тип изделия для Интернет-магазина',
        'Shoe' => 'Номер колодки',
        'Model' => 'Номер модели',
        'Manufacturer' => 'Номер фабрики',
        'Rhodeproduct' => 'Род изделия',
        'Season' => 'Сезон',
        'Style' => 'Стиль',
        'Collection' => 'Коллекция',
        'Vid' => 'Вид номенклатуры',
        'Category' => 'Категория товара',
        'Country' => 'Страна происхождения',
        'Metro' => 'Станции метро',
        'Colorsfilter' => 'Базовые цвета',
        'Subtypeproduct' => 'Вид изделия для Интернет-магазина',
    ];

    public function up()
    {
        // Добавляет свойство сортировка для HL-блоков
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            try {
                $this->addSortFieldToHLBlock($HLBlockName);
            } catch (Exceptions\HelperException $e) {
                if (strpos($e->getMessage(), 'There is no data to update') !== 0) {
                    echo 'Error: ' . $e->getMessage() . '<br>';
                } else {
                    throw ($e);
                }
            }
        }

        // Русифицирует HL-блоки
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            try {
                $this->addLangToHLBlock($HLBlockName, $HLBlockRuLangName);
            } catch (Exceptions\HelperException $e) {
                if (strpos($e->getMessage(), 'There is no data to update') !== 0) {
                    echo 'Error: ' . $e->getMessage() . '<br>';
                } else {
                    throw ($e);
                }
            }
        }

        // Добавляет HL-блокам поля UF_NAME и UF_XL_ID в фильтр
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            $this->addFilterToHLBlock($HLBlockName);
        }

        //Устанавливает значение по умолчанию свойства сортировки для HL-блоков
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            try {
                $this->setSortFieldWithDefaultValueInHLBlock($HLBlockName, 500);
            } catch (Exceptions\HelperException $e) {
                if (strpos($e->getMessage(), 'There is no data to update') !== 0) {
                    echo 'Error: ' . $e->getMessage() . '<br>';
                } else {
                    throw ($e);
                }
            }
        }

        // Создает HL-блок Sorting
        $this->createSortingHLBlock();

        // Добавляет в HL-блок Sorting все сортируемые свойства
        $this->addAllSortingFieldsWithDefaultSort();
    }

    public function down()
    {
        // Удаляет свойство сортировки для HL-блоков
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            try {
                $this->deleteSortFieldFromHLBlock($HLBlockName);
            } catch (Exceptions\HelperException $e) {
                if (strpos($e->getMessage(), 'There is no data to update') !== 0) {
                    echo 'Error: ' . $e->getMessage() . '<br>';
                } else {
                    throw ($e);
                }
            }
        }

        // Удаляет русификацию HL-блоков
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            try {
                $this->deleteLangFromHLBlock($HLBlockName);
            } catch (Exceptions\HelperException $e) {
                if (strpos($e->getMessage(), 'There is no data to update') !== 0) {
                    echo 'Error: ' . $e->getMessage() . '<br>';
                } else {
                    throw ($e);
                }
            }
        }

        // Убирает у HL-блоков поля UF_NAME и UF_XL_ID из фильтра
        foreach (self::HL_BLOCKS_NAME_LIST as $HLBlockName => $HLBlockRuLangName) {
            $this->deleteFilterFromHLBlock($HLBlockName);
        }

        // Удаляет HL-блок Sorting
        $this->deleteSortingHLBlock();
    }

    private function setSortFieldWithDefaultValueInHLBlock($HLBlockName, $fieldValue)
    {
        $helper = $this->getHelperManager();
        $dataManager = $helper->Hlblock()->getDataManager($HLBlockName);

        $data = [
            'UF_SORT' => $fieldValue
        ];

        $elements =  $helper->Hlblock()->getElements($HLBlockName);

        foreach ($elements as $element) {
            $dataManager::update($element['ID'], $data);
        }
    }

    private function deleteSortFieldFromHLBlock($HLBlockName)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockId($HLBlockName);

        $helper->Hlblock()->deleteField($hlblockId, 'UF_SORT');
    }

    private function addAllSortingFieldsWithDefaultSort()
    {
        $helper = $this->getHelperManager();

        $sortings = [
            [
                'UF_NAME' => 'Сортировка по товару', // Название сортировки
                'UF_HB_NAME' => 'SORT', // HL-блок сортировки
                'UF_ENABLED' => 1, // Участвует в сортировке?
                'UF_SORT' => 100, // Сортировка
            ],
            [
                'UF_NAME' => 'Сортировка по коллекции',
                'UF_HB_NAME' => 'COLLECTION',
                'UF_ENABLED' => 1,
                'UF_SORT' => 110,
            ],
            [
                'UF_NAME' => 'Сортировка по бренду',
                'UF_HB_NAME' => 'BRAND',
                'UF_ENABLED' => 0,
                'UF_SORT' => 200,
            ],
            [
                'UF_NAME' => 'Сортировка по материалу подкладки',
                'UF_HB_NAME' => 'LININGMATERIAL',
                'UF_ENABLED' => 0,
                'UF_SORT' => 200,
            ],
            [
                'UF_NAME' => 'Сортировка по материалу верха',
                'UF_HB_NAME' => 'UPPERMATERIAL',
                'UF_ENABLED' => 0,
                'UF_SORT' => 200,
            ],
            [
                'UF_NAME' => 'Сортировка по сезону',
                'UF_HB_NAME' => 'SEASON',
                'UF_ENABLED' => 0,
                'UF_SORT' => 200,
            ],
            [
                'UF_NAME' => 'Сортировка по роду изделия',
                'UF_HB_NAME' => 'RHODEPRODUCT',
                'UF_ENABLED' => 0,
                'UF_SORT' => 200,
            ],
        ];

        foreach ($sortings as $fields) {
            $helper->Hlblock()->addElement('Sorting', $fields);
        }
    }

    private function addSortFieldToHLBlock($HLBlockName, $defaultValue = 500)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockId($HLBlockName);

        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_SORT',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => 'UF_SORT',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'I',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
            array(
                'SIZE' => 20,
                'MIN_VALUE' => 0,
                'MAX_VALUE' => 0,
                'DEFAULT_VALUE' => $defaultValue,
            ),
            'EDIT_FORM_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'LIST_COLUMN_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'LIST_FILTER_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'ERROR_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
            'HELP_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
        ));
    }

    private function addLangToHLBlock($HLBlockName, $HLBlockRuLangName)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->updateHlblockIfExists(
            $HLBlockName,
            [
                'NAME' => $HLBlockName,
                'LANG' =>
                [
                    'ru' => ['NAME' => $HLBlockRuLangName],
                ],
            ]
        );
        return $hlblockId;
    }

    private function deleteLangFromHLBlock($HLBlockName)
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->updateHlblockIfExists(
            $HLBlockName,
            [
                'NAME' => $HLBlockName,
                'LANG' => [
                    'ru' => ''
                ]
            ]
        );
    }

    private function addFilterToHLBlock($HLBlockName)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockId($HLBlockName);

        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_NAME',
            'SHOW_FILTER' => 'S',
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_XML_ID',
            'SHOW_FILTER' => 'S',
        ));
    }

    private function deleteFilterFromHLBlock($HLBlockName)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockId($HLBlockName);

        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_NAME',
            'SHOW_FILTER' => 'N',
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_XML_ID',
            'SHOW_FILTER' => 'N',
        ));
    }

    private function createSortingHLBlock()
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->saveHlblock(array(
            'NAME' => 'Sorting',
            'TABLE_NAME' => 'b_1c_dict_sorting',
            'LANG' =>
            array(
                'ru' =>
                array(
                    'NAME' => 'Сортировка сортировок',
                ),
            ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_NAME',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
            array(
                'SIZE' => 20,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'DEFAULT_VALUE' => '',
            ),
            'EDIT_FORM_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Название поля сортировки',
            ),
            'LIST_COLUMN_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Название поля сортировки',
            ),
            'LIST_FILTER_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Название поля сортировки',
            ),
            'ERROR_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
            'HELP_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_HB_NAME',
            'USER_TYPE_ID' => 'string',
            'XML_ID' => '',
            'SORT' => '101',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
            array(
                'SIZE' => 20,
                'ROWS' => 1,
                'REGEXP' => '',
                'MIN_LENGTH' => 0,
                'MAX_LENGTH' => 0,
                'DEFAULT_VALUE' => '',
            ),
            'EDIT_FORM_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Поле сортировки',
            ),
            'LIST_COLUMN_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Поле сортировки',
            ),
            'LIST_FILTER_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Поле сортировки',
            ),
            'ERROR_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
            'HELP_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_ENABLED',
            'USER_TYPE_ID' => 'boolean',
            'XML_ID' => '',
            'SORT' => '100',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
            array(
                'DEFAULT_VALUE' => 0,
                'DISPLAY' => 'DROPDOWN',
                'LABEL' =>
                array(
                    0 => 'Нет',
                    1 => 'Да',
                ),
                'LABEL_CHECKBOX' => '',
            ),
            'EDIT_FORM_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Участвует в сортировке',
            ),
            'LIST_COLUMN_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Участвует в сортировке',
            ),
            'LIST_FILTER_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Участвует в сортировке',
            ),
            'ERROR_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
            'HELP_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
        ));
        $helper->Hlblock()->saveField($hlblockId, array(
            'FIELD_NAME' => 'UF_SORT',
            'USER_TYPE_ID' => 'integer',
            'XML_ID' => '',
            'SORT' => '102',
            'MULTIPLE' => 'N',
            'MANDATORY' => 'N',
            'SHOW_FILTER' => 'S',
            'SHOW_IN_LIST' => 'Y',
            'EDIT_IN_LIST' => 'Y',
            'IS_SEARCHABLE' => 'N',
            'SETTINGS' =>
            array(
                'SIZE' => 20,
                'MIN_VALUE' => 0,
                'MAX_VALUE' => 0,
                'DEFAULT_VALUE' => 100,
            ),
            'EDIT_FORM_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'LIST_COLUMN_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'LIST_FILTER_LABEL' =>
            array(
                'en' => '',
                'ru' => 'Сортировка',
            ),
            'ERROR_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
            'HELP_MESSAGE' =>
            array(
                'en' => '',
                'ru' => '',
            ),
        ));
    }

    private function deleteSortingHLBlock()
    {
        $helper = $this->getHelperManager();
        $helper->Hlblock()->deleteHlblockIfExists('Sorting');
    }
}
