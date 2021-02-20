<?php

namespace Sprint\Migration;

class transferCollectionSortingToHB20191212145346 extends Version
{
    protected $description = "Копирует значения сортировки коллекций в свойства сортировка HL-блока Коллекции. Метод отката ничего не делает!!";

    private const COLLECTION_IBLOCK_ID = 22;
    private const COLLECTION_HLBLOCK_NAME = 'Collection';

    public function up()
    {
        $helper = $this->getHelperManager();
        $allIBElements = $helper->Iblock()->getElements(
            self::COLLECTION_IBLOCK_ID,
            [],
            ['PROPERTY_COLLECTION', 'NAME', 'SORT']
        );

        $allHLElements =  $helper->Hlblock()->getElements(self::COLLECTION_HLBLOCK_NAME);

        $HLBlock_XML_ID_With_ID = [];

        foreach ($allHLElements as $element) {
            $HLBlock_XML_ID_With_ID[$element['UF_XML_ID']] = $element['ID'];
        }

        $dataManager = $helper->Hlblock()->getDataManager(self::COLLECTION_HLBLOCK_NAME);

        foreach ($allIBElements as $element) {
            $id = $HLBlock_XML_ID_With_ID[$element['PROPERTY_COLLECTION_VALUE']];
            $data = [
                'UF_SORT' => $element['SORT']
            ];

            $dataManager::update($id, $data);
        }
    }

    public function down()
    {
        //your code ...
    }
}
