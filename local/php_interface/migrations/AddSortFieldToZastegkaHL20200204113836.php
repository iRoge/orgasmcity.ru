<?php

namespace Sprint\Migration;

class AddSortFieldToZastegkaHL20200204113836 extends Version
{
    protected $description = "Добавляет свойствосортировки для HL блоков (застежка). Устанавливает по умолчанию 500 для всех элементов";

    /**
     * @return bool|void
     * @throws Exceptions\HelperException
     */

    // HL-блоки, которым добавим свойство сортировки ('UF_SORT')
    private const HL_BLOCKS_NAME_LIST = [
        'Zastegka' => 'Тип застежки',
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
    private function deleteSortFieldFromHLBlock($HLBlockName)
    {
        $helper = $this->getHelperManager();
        $hlblockId = $helper->Hlblock()->getHlblockId($HLBlockName);

        $helper->Hlblock()->deleteField($hlblockId, 'UF_SORT');
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
}
