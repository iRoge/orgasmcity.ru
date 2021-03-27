<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\Component\Tools;
use Bitrix\Iblock\InheritedProperty\ElementValues;

class EventsLookbookComponent extends EventsComponent
{
    private string $cacheSeoDir = 'seo';
    private array $srcSize = ['width' => 126, 'height' => 100];


    public function executeComponent()
    {
        $this->initCache();
        $this->arResult['LOOKBOOK_2PAGES'] = $this->loadLookbook();
        if ($this->request->isAjaxRequest()) {
            Functions::exitJson(['text' => $this->arResult['LOOKBOOK_2PAGES'][$_REQUEST['num']]['SEO_TEXT'], 'seo' => $this->loadSeoSection()[$_REQUEST['num']]]);
            return false;
        }

        if ($this->arParams['CURRENT_PAGE_NUM'] > count($this->arResult['LOOKBOOK_2PAGES'])) {
            Tools::process404('', true, true, true);
        }
        if (empty($this->arParams['CURRENT_PAGE_NUM'])) {
            $this->arResult['CURRENT_SLIDE_NUM'] = 0;
        } else {
            $this->arResult['CURRENT_SLIDE_NUM'] = $this->arParams['CURRENT_PAGE_NUM'] * 2 - 2;
        }
        $this->loadSeoSection();
        $this->createChain();

        $this->IncludeComponentTemplate();
        return false;
    }

    private function loadLookbook(): array
    {
        $fullLookbook = [];
        $arIdsTwoPages = [];
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], $this->arParams['CURRENT_ELEMENT'], $this->cacheDir)) {
            $fullLookbook = $this->cache->getVars()['fullLookbook'];
            $arAllPagesArt = $this->cache->getVars()['arAllPagesArt'];
            $arIdsTwoPages = $this->cache->getVars()['arIdsTwoPages'];
        } elseif ($this->cache->StartDataCache()) {
            $arAllPagesArt = [];

            $res = CIBlockSection::GetList(
                ['SORT' => 'DESC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    'CODE' => $this->arParams['CURRENT_ELEMENT'],
                ],
                false,
                [
                    'ID',
                ]
            );

            $arItemSection = $res->Fetch();
            if (empty($arItemSection)) {
                Tools::process404("", true, true, true);
            }

            $res = CIBlockElement::GetList(
                ['SORT' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_CODE' => $this->arParams['IBLOCK_CODE'],
                    'IBLOCK_SECTION_ID' => $arItemSection['ID'],
                ],
                false,
                false,
                [
                    'ID',
                    'IBLOCK_ID',
                    'NAME',
                    'PREVIEW_PICTURE',
                    'DETAIL_PICTURE',
                    'DETAIL_TEXT',
                    'PROPERTY_IMG_LINKS_LEFT',
                    'PROPERTY_IMG_LINKS_RIGHT',
                    'PROPERTY_IMG_LINK_LEFT',
                    'PROPERTY_IMG_LINK_RIGHT',
                ]
            );
            $arIdPreview = [];
            $slideNum = 1;
            while ($arItemElement = $res->Fetch()) {
                $arIdsTwoPages[$slideNum] = $arItemElement['ID'];

                $ar2Page = [
                    'NUM' => $slideNum,
                    'NAME' => $arItemElement['NAME'],
                    'SEO_TEXT' => $arItemElement['DETAIL_TEXT'],
                    'LEFT' => $arItemElement['PREVIEW_PICTURE'] ? ['IMG' => $arItemElement['PREVIEW_PICTURE']] : false,
                    'RIGHT' => $arItemElement['DETAIL_PICTURE'] ? ['IMG' => $arItemElement['DETAIL_PICTURE']] : false,
                ];

                if ($arItemElement['PREVIEW_PICTURE']) {
                    $arIdPreview[] = $arItemElement['PREVIEW_PICTURE']; //левая страница
                }
                if ($arItemElement['DETAIL_PICTURE']) {
                    $arIdPreview[] = $arItemElement['DETAIL_PICTURE']; //левая страница
                }

                foreach (['LEFT', 'RIGHT'] as $page) {
                    if (!$ar2Page[$page]) {
                        $ar2Page['ONE_PAGE'] = true;
                        continue;
                    }
                    if (!empty($arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'])) {
                        $ar2Page[$page]['MULTI_LINKS'] = $arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'];
                        foreach ($arItemElement['PROPERTY_IMG_LINKS_' . $page . '_VALUE'] as $arLinkInfo) {
                            if (!empty($arLinkInfo['ART'])) {
                                $arAllPagesArt[$arLinkInfo['ART']]['ART'] = $arLinkInfo['ART'];
                            }
                        }
                    } else {
                        $ar2Page[$page]['LINK'] = $arItemElement['PROPERTY_IMG_LINK_' . $page . '_VALUE'];
                    }
                }

                $fullLookbook[$slideNum] = $ar2Page;
                $slideNum++;
            }

            $arNewImg = [];
            $arImg = CFile::GetList('', array('@ID' => $arIdPreview));
            while ($arLocationImg = $arImg->GetNext()) {
                $resizeSrc = Functions::ResizeImageGet($arLocationImg, $this->srcSize);
                $arNewImg[$arLocationImg['ID']] = [
                    'SRC' => '/' . COption::GetOptionString('main', 'upload_dir') . '/' . $arLocationImg['SUBDIR'] . '/' . $arLocationImg['FILE_NAME'],
                    'SRC_PREVIEW' => $resizeSrc['src'],
                    'HEIGHT' => $arLocationImg['HEIGHT'],
                    'WIDTH' => $arLocationImg['WIDTH'],
                ];
            }
            foreach ($fullLookbook as &$arResultNew) {
                foreach (['LEFT', 'RIGHT'] as $page) {
                    if (empty($arResultNew[$page])) {
                        $arResultNew['ONE_PAGE'] = true;
                        continue;
                    }
                    $arResultNew[$page]['IMG'] = $arNewImg[$arResultNew[$page]['IMG']];
                    if ($arResultNew[$page]['MULTI_LINKS']) {
                        foreach ($arResultNew[$page]['MULTI_LINKS'] as $num => $arLinkInfo) {
                            $arResultNew[$page]['MULTI_LINKS'][$num]['STYLE'] = $this->getBannerLinkStyle($arLinkInfo['COORD'], $arResultNew[$page]['IMG']['WIDTH'], $arResultNew[$page]['IMG']['HEIGHT']);
                        }
                    }
                }
            }
            unset($arResultNew);

            if (!empty($fullLookbook)) {
                $this->cache->EndDataCache([
                    'fullLookbook' => $fullLookbook,
                    'arAllPagesArt' => $arAllPagesArt,
                    'arIdsTwoPages' => $arIdsTwoPages,
                ]);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        if (!empty($arAllPagesArt)) {
            $arAllPagesArt = $this->checkProducts($arAllPagesArt);
            foreach ($fullLookbook as &$arTwoPages) {
                foreach (['LEFT', 'RIGHT'] as $page) {
                    if (empty($arTwoPages[$page]) || empty($arTwoPages[$page]['MULTI_LINKS'])) {
                        continue;
                    }
                    foreach ($arTwoPages[$page]['MULTI_LINKS'] as $num => $arLinkInfo) {
                        if ($arLinkInfo['ART']) {
                            $arTwoPages[$page]['MULTI_LINKS'][$num]['LINK'] = $arAllPagesArt[$arLinkInfo['ART']]['LINK'];
                            $arTwoPages[$page]['MULTI_LINKS'][$num]['MESS'] = $arAllPagesArt[$arLinkInfo['ART']]['MESS'];
                        }
                    }
                }
            }
            unset($arTwoPages);
        }

        $this->arResult['COUNT_SLIDE'] = count($fullLookbook) * 2;
        $this->arResult['ALL_TWOPAGES_IDS'] = $arIdsTwoPages;

        return $fullLookbook;
    }

    private function getBannerLinkStyle($val, $w, $h): string
    {
        $val = explode(",", trim($val));
        return "left:" . (intval(100 * $val[0] / $w)) . "%;" .
            "top:" . (intval(100 * $val[1] / $h)) . "%;" .
            "right:" . (100 - intval(100 * $val[2] / $w)) . "%;" .
            "bottom:" . (100 - intval(100 * $val[3] / $h)) . "%";
    }

    private function checkProducts($arArt)
    {
        global $LOCATION;

        $resultArt = $LOCATION->getDataToArticle(array_keys($arArt));
        foreach ($resultArt as $art => $res) {
            if ($res == 'nedostupen') {
                $arArt[$art]['LINK'] = false;
                $arArt[$art]['MESS'] = 'НЕ ДОСТУПЕН ДЛЯ ЗАКАЗА В ВАШЕМ ГОРОДЕ';
                continue;
            }

            if ($res == 'wait') {
                $arArt[$art]['LINK'] = false;
                $arArt[$art]['MESS'] = 'ОЖИДАЕТСЯ ПОСТУПЛЕНИЕ';
                continue;
            }

            $arArt[$art]['LINK'] = '/' . $res . '/';
            $arArt[$art]['MESS'] = 'ПЕРЕЙТИ В КАТАЛОГ';
        }

        return $arArt;
    }

    private function loadSeoSection(): array
    {
        global $APPLICATION;
        $seo = [];

        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'lookbook_' . $this->arParams['CURRENT_ELEMENT'], $this->cacheSeoDir)) {
            $seo = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            foreach ($this->arResult['ALL_TWOPAGES_IDS'] as $num => $twoPagesId) {
                $ipropValues = new ElementValues($this->arParams['IBLOCK_ID'], $twoPagesId);
                $seo[$num] = $ipropValues->getValues();
            }

            if (!empty($seo)) {
                $this->cache->EndDataCache($seo);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        $APPLICATION->SetTitle($seo[$this->arParams['CURRENT_PAGE_NUM']]['ELEMENT_PAGE_TITLE'] ?: $seo[$this->arParams['CURRENT_PAGE_NUM']]['SECTION_PAGE_TITLE']);
        $APPLICATION->SetPageProperty("title", $seo[$this->arParams['CURRENT_PAGE_NUM']]['ELEMENT_META_TITLE'] ?: $seo[$this->arParams['CURRENT_PAGE_NUM']]['SECTION_META_TITLE']);
        $APPLICATION->SetPageProperty("keywords", $seo[$this->arParams['CURRENT_PAGE_NUM']]['ELEMENT_META_KEYWORDS'] ?: $seo[$this->arParams['CURRENT_PAGE_NUM']]['SECTION_META_KEYWORDS']);
        $APPLICATION->SetPageProperty("description", $seo[$this->arParams['CURRENT_PAGE_NUM']]['ELEMENT_META_DESCRIPTION'] ?: $seo[$this->arParams['CURRENT_PAGE_NUM']]['SECTION_META_DESCRIPTION']);

        return $seo;
    }
}
