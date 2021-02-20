<?php

namespace Sprint\Migration;

class addHeelHeightTypePropertyToTags20200214172225 extends Version
{
    protected $description = "Добавляет к тегам свойство степени высоты каблука и стиля";
    private $iblock_tags_code = 'CATALOG_TAGS';

    public function up()
    {
        $helper = new HelperManager();
        $IBLOCK_TAGS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_tags_code);

        $helper->Iblock()->addPropertyIfNotExists($IBLOCK_TAGS_ID, [
            'NAME' => 'Степень высоты каблука',
            'CODE' => 'HEELHEIGHT_TYPE',
            'PROPERTY_TYPE' => 'L',
            'USER_TYPE' => null,
            'MULTIPLE' => 'N',
            'USER_TYPE_SETTINGS' => null,
            'VALUES' => [
                [
                    'VALUE' => 'Высокий',
                    'DEF' => 'N',
                    'SORT' => '500',
                    'XML_ID' => 'high',
                ],
                [
                    'VALUE' => 'Средний',
                    'DEF' => 'N',
                    'SORT' => '500',
                    'XML_ID' => 'mid',
                ],
                [
                    'VALUE' => 'Низкий',
                    'DEF' => 'N',
                    'SORT' => '500',
                    'XML_ID' => 'low',
                ],
                [
                    'VALUE' => 'Без каблука',
                    'DEF' => 'N',
                    'SORT' => '500',
                    'XML_ID' => 'without',
                ],
            ],
        ]);
    }

    public function down()
    {
        $helper = new HelperManager();
        $IBLOCK_TAGS_ID = $helper->Iblock()->getIblockIdIfExists($this->iblock_tags_code);
        $helper->Iblock()->deletePropertyIfExists($IBLOCK_TAGS_ID, 'HEELHEIGHT_TYPE');
    }
}
