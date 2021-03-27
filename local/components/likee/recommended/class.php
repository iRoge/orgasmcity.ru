<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Site
 * Date: 2017-04-20
 * Time: 19:23:26
 */

CBitrixComponent::includeComponentClass('bitrix:catalog.recommended.products');

class LikeeRecommendedComponent extends CCatalogRecommendedProductsComponent
{
    protected function getRecommendedIds($productId, $propertyName)
    {
        if (!$productId)
            return array();

        $elementIterator = CIBlockElement::getList(
            array(),
            array('ID' => $productId),
            false,
            false,
            array('ID', 'IBLOCK_ID', 'IBLOCK_SECTION_ID')
        );

        $linked = array();
        $element = $elementIterator->getNextElement();

        if (!$element)
            return -1;

        $arItem = $element->getFields();
        $props = $element->getProperties();
        $linked = $props[$propertyName]['VALUE'];


        if (!empty($linked)) {
            $arFilter = \Likee\Site\Helpers\Catalog::getDefaultFilter([
                'ID' => $linked
            ]);
        } else {
            $arFilter = \Likee\Site\Helpers\Catalog::getDefaultFilter([
                'IBLOCK_ID' => $arItem['IBLOCK_ID'],
                'SECTION_ID' => $arItem['IBLOCK_SECTION_ID']
            ]);
        }

        $productIterator = CIBlockElement::getList(
            array('RAND' => 'ASC'),
            $arFilter,
            false,
            array('nTopCount' => $this->arParams['PAGE_ELEMENT_COUNT']),
            array('ID')
        );

        $ids = array();
        while ($item = $productIterator->fetch())
            $ids[] = $item['ID'];

        return $ids;
    }
}