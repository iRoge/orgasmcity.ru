<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try {
    $groupsIblockId = \Likee\Site\Helpers\IBlock::getIBlockId('CATALOG_GROUPS');

    $rsGroups = \CIBlockElement::GetList(
        [],
        [
            'IBLOCK_ID' => $groupsIblockId,
            'ACTIVE' => 'Y',
        ]
    );

    while ($rsGroup = $rsGroups->GetNextElement()) {
        $arGroupElement = $rsGroup->GetFields();
        $arGroupElementProps = $rsGroup->GetProperties();

        $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($arGroupElement["IBLOCK_ID"], $arGroupElement["ID"]);
        $arGroupElement["IPROPERTY_VALUES"] = $ipropValues->getValues();
    
        $arGroupElement['FILTER'] = [];
        
        if (!empty($arGroupElementProps['SECTION']['VALUE'])) {
            $arGroupElement['FILTER']['SECTION_ID'] = $arGroupElementProps['SECTION']['VALUE'];
            $arGroupElement['FILTER']['INCLUDE_SUBSECTIONS'] = 'Y';
        }
    
        if (!empty($arGroupElementProps['PRICE_FROM']['VALUE']) || !empty($arGroupElementProps['PRICE_TO']['VALUE'])) {
            $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'] = [0, PHP_INT_MAX];
    
            if (! empty($arGroupElementProps['PRICE_FROM']['VALUE'])) {
                $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'][0] = $arGroupElementProps['PRICE_FROM']['VALUE'];
            }
                
            if (! empty($arGroupElementProps['PRICE_TO']['VALUE'])) {
                $arGroupElement['FILTER']['><PROPERTY_MINIMUM_PRICE'][1] = $arGroupElementProps['PRICE_TO']['VALUE'];
            }
        }
    
        unset($arGroupElementProps['SECTION'], $arGroupElementProps['PRICE_FROM'], $arGroupElementProps['PRICE_TO']);
    
        foreach ($arGroupElementProps as $propCode => $propData) {
            if (empty($propData['VALUE'])) {
                continue;
            }

            /**
             * Предполагаем, что по предложениям всегда ищем в списках
             */
            if (0 === strpos($propCode, 'OFFERS_')) {
                $propCode = substr($propCode, strrpos($propCode, 'OFFERS_')+7);

                $arPropData = false === strpos($propData['VALUE'], ',') ? [$propData['VALUE']] : explode(',', $propData['VALUE']);
                $arPropData = array_map('trim', $arPropData);

                $propertyEnums = \CIBlockPropertyEnum::GetList([], [
                    'IBLOCK_ID' => IBLOCK_OFFERS,
                    'CODE' => $propCode,
                ]);
                while($arEnumFields = $propertyEnums->GetNext()) {
                    $i = array_search($arEnumFields['VALUE'], $arPropData);
                    if (false !== $i) {
                        $arPropData[$i] = $arEnumFields['ID'];
                    }
                }
                unset($arEnumFields, $propertyEnums);

                $arGroupElement['FILTER']['OFFERS']['PROPERTY_'.$propCode] = $arPropData;
            } else {
                if (is_string($propData['VALUE']) && false !== strpos($propData['VALUE'], ',')) {
                    $propData['VALUE'] = explode(',', $propData['VALUE']);
                    $propData['VALUE'] = array_map('trim', $propData['VALUE']);
                }
                $arGroupElement['FILTER']['PROPERTY_'.$propCode] = $propData['VALUE'];
            }
        }
        
        $arGroupSections[$arGroupElement['CODE']] = $arGroupElement;
    }
    
    unset($groupsIblockId, $rsGroups, $rsGroup, $arGroupElement);

} catch (\Exception $e) {}