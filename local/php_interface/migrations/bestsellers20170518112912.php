<?php

namespace Sprint\Migration;


use Bitrix\Main\Loader;
use Likee\Site\Helpers\IBlock;

class bestsellers20170518112912 extends Version
{

    protected $description = "свойства для bestsellers";

    public function up()
    {
        $helper = new HelperManager();


        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->addPropertyIfNotExists($iIBlockID, [
            'CODE' => 'SHOW_IN_BESTSELLERS',
            'NAME' => 'Показывать в бестселлерах',
            'PROPERTY_TYPE' => 'L',
            'LIST_TYPE' => 'C',
            'VALUES' => [
                [
                    'VALUE' => 'Да',
                    'XML_ID' => 'Y'
                ]
            ]
        ]);

        if (Loader::includeModule('likee.site')) {
            $iEnum = IBlock::getEnumIdByCode('Y', 'SHOW_IN_BESTSELLERS', $iIBlockID);

            if ($iEnum > 0) {
                $rsItems = \CIBlockElement::GetList(
                    [],
                    [
                        'IBLOCK_ID' => $iIBlockID,
                        'CODE' => [
                            'tufli_lodochki_s75_087388',
                            'tufli_lodochki_s75_084809',
                            'tufli_lodochki_s75_084802',
                            'tufli_lodochki_s56_085584',
                            'tufli_lodochki_s56_085584',
                            'kozhanye_tufli_lodochki_nebesnogo_tsveta_ss75_094171',
                            'lakovye_tufli_na_massivnoy_podoshve_is56_093976'
                        ]
                    ],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID']
                );

                while ($arItem = $rsItems->Fetch()) {
                    \CIBlockElement::SetPropertyValuesEx($arItem['ID'], $arItem['IBLOCK_ID'], [
                        'SHOW_IN_BESTSELLERS' => $iEnum
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $helper = new HelperManager();

        $iIBlockID = $helper->Iblock()->getIblockId('CATALOG', 'test');
        $helper->Iblock()->deletePropertyIfExists($iIBlockID, 'SHOW_IN_BESTSELLERS');
    }
}
