<?

use Qsoft\Helpers\ComponentHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class QsoftInfopageComponent extends ComponentHelper
{

    private $arAllPagesVariant; //варианты страницы для всех местоположений
    private $arGeo; //массив город - область - регион - страна

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);
        $this->tryParseInt($arParams['CACHE_TIME'], 86400);
        return $arParams;
    }

    public function executeComponent()
    {
        global $LOCATION;
        $this->loadInfopage();
        $this->arGeo = $LOCATION->getParentCodes();
        //ищем ближайший подходящий по местоположению вариант страницы
        foreach ($this->arGeo as $currentGeo) {
            if (isset($this->arAllPagesVariant[$currentGeo])) {
                $this->arResult = $this->arAllPagesVariant[$currentGeo];
                break;
            }
        }
        $this->setSeo();
        $this->includeComponentTemplate();
    }
    private function setSeo()
    {
        global $APPLICATION;
        $seo = $this->loadSeoElement();
        if ($this->arParams['SET_TITLE'] != 'N') {
            $APPLICATION->SetTitle($seo['ELEMENT_PAGE_TITLE'] ?: $seo['SECTION_PAGE_TITLE']);
        }
        $APPLICATION->SetPageProperty("title", $seo['ELEMENT_META_TITLE']?:$seo['SECTION_META_TITLE']);
        $APPLICATION->SetPageProperty("keywords", $seo['ELEMENT_META_KEYWORDS']?:$seo['SECTION_META_KEYWORDS']);
        $APPLICATION->SetPageProperty("description", $seo['ELEMENT_META_DESCRIPTION']?:$seo['SECTION_META_DESCRIPTION']);
    }

    private function loadSeoElement()
    {
        if ($this->initCache('infopage_' . $this->arParams['IBLOCK_CODE'] . '_seo_' . $this->arResult['ID'])) {
            $seo = $this->getCachedVars('seo');
        } elseif ($this->startCache()) {
            $ipropValues = new \Bitrix\Iblock\InheritedProperty\ElementValues($this->arResult['IBLOCK_ID'], $this->arResult['ID']);
            $seo = $ipropValues->getValues();
            if (!empty($seo)) {
                $this->endTagCache();
                $this->saveToCache('seo', $seo);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }
        return $seo;
    }

    private function loadInfopage() //получаем варианты страницы для всех местоположений
    {
        if ($this->initCache('infopage_' . $this->arParams['IBLOCK_CODE'])) {
            $arResult = $this->getCachedVars('allRegionPage');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag('infopage_' . $this->arParams['IBLOCK_CODE']);

            $arFilter = array(
                'IBLOCK_CODE' => $this->arParams['IBLOCK_CODE'],
                'ACTIVE' => 'Y',
            );
            $arSelect = array(
                'IBLOCK_ID',
                'ID',
                "NAME",
                "PREVIEW_TEXT",
                "PREVIEW_PICTURE",
                "PROPERTY_*",
            );

            $res = CIBlockElement::GetList('', $arFilter, false, '', $arSelect);

            while ($arItem = $res->GetNextElement()) {
                $arProp = $arItem->GetProperties();
                $arItem = $arItem->GetFields();
                $arLocation = $arProp['LOCATION']['VALUE'];
                foreach ((array)$arLocation as $location) {
                    if (isset($arResult[$location])) {
                        continue;
                    }
                    $arIdPreview[$location] = $arItem['PREVIEW_PICTURE'];
                    if (stripos($arItem['PREVIEW_TEXT'], '#BTNS#') != 0) {
                        $arResult[$location]['SHOW_BUTTONS'] = true;
                        $elem = explode('#BTNS#', $arItem['PREVIEW_TEXT']);
                        $arResult[$location]['PREVIEW_TEXT_1'] = $elem[0];
                        $arResult[$location]['PREVIEW_TEXT_2'] = $elem[1];
                    } else {
                        $arResult[$location]['PREVIEW_TEXT'] = $arItem['PREVIEW_TEXT'];
                    }
                    $arResult[$location]['PREVIEW_PICTURE'] = $arItem['PREVIEW_PICTURE'];
                    $arResult[$location]['ID'] = $arItem['ID'];
                    $arResult[$location]['IBLOCK_ID'] = $arItem['IBLOCK_ID'];
                    foreach ($arProp as $propName => $prop) {
                        preg_match('`SECTION_(.*)_.*`', $propName, $matches);
                        preg_match('`SECTION_.*_(.*)`', $propName, $val);
                        if ($val[1] != 'TEXT') {
                            $arResult[$location]['ITEMS'][$matches[1]][$val[1]] = $prop['VALUE'];
                        } else {
                            $arResult[$location]['ITEMS'][$matches[1]][$val[1]] = $prop['~VALUE']['TEXT'];
                        }
                    }
                }
            }

            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $arNewImg[$arLocationImg['ID']] = '/' . COption::GetOptionString('main', 'upload_dir') .
                    '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'];
            }
            foreach ($arResult as &$arResultNew) {
                $arResultNew['PREVIEW_PICTURE'] = $arNewImg[$arResultNew['PREVIEW_PICTURE']];
            }

            if (!empty($arResult)) {
                $this->endTagCache();
                $this->saveToCache('allRegionPage', $arResult);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        $this->arAllPagesVariant = $arResult;
    }
}
