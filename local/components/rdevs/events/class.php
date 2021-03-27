<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Iblock\Component\Tools;

class EventsComponent extends CBitrixComponent
{
    protected object $cache;
    protected string $cacheDir = '/';

    protected function initCache()
    {
        $this->cacheDir = $this->arParams['CACHE_DIR'];
        $this->cache = new CPHPCache();
    }

    public function executeComponent()
    {
        $this->initCache();
        $arVariables = [];
        $componentPage = CComponentEngine::ParseComponentPath(
            $this->arParams['SEF_FOLDER'],
            $this->arParams['SEF_URL_TEMPLATES'],
            $arVariables
        );
        if ($componentPage === false && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == $this->arParams['SEF_FOLDER']) {
            $componentPage = $this->arParams['SEF_DEFAULT_TEMPLATE'];
            $arVariables['SECTION_CODE'] = $this->arParams['DEFAULT_SECTION']['EXTERNAL_ID'];
        }
        // Если определить файл шаблона не удалось, показываем  страницу 404 Not Found
        if (empty($componentPage)) {
            Tools::process404('', true, true, true);
            return;
        }

        $this->arParams['CURRENT_SECTION'] = $arVariables['SECTION_CODE'];
        $this->arParams['CURRENT_ELEMENT'] = $arVariables['ELEMENT_CODE'];
        $this->arParams['CURRENT_PAGE_NUM'] = $arVariables['PAGE_NUM'];

        if ($this->arParams['IBLOCK_CODE'] == 'blog' && $componentPage == 'element') {
            if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'onlyLookbooksCodes', $this->arParams['CACHE_DIR'])) {
                $arLookbookCodes = $this->cache->getVars();
            } elseif ($this->cache->StartDataCache()) {
                $res = CIBlockSection::GetList(
                    [],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                        'UF_IS_LOOKBOOK' => true,
                    ],
                    false,
                    [
                        'CODE',
                    ]
                );
                while ($arItem = $res->Fetch()) {
                    $arLookbookCodes[$arItem['CODE']] = $arItem['CODE'];
                }

                if (!empty($arLookbookCodes)) {
                    $this->cache->EndDataCache($arLookbookCodes);
                } else {
                    $this->cache->AbortDataCache();
                }
            }
            if (isset($arLookbookCodes[$arVariables['ELEMENT_CODE']])) {
                $url = $this->arParams['DEFAULT_SECTION']['LINK'] . $arVariables['SECTION_CODE'] . '/' . $arVariables['ELEMENT_CODE'] . '/1/';
                LocalRedirect($url, false, '301 Moved permanently');
            }
        }
        $this->arParams['CURRENT_SECTION'] = $arVariables['SECTION_CODE'];
        $this->arParams['CURRENT_ELEMENT'] = $arVariables['ELEMENT_CODE'];
        $this->arParams['CURRENT_PAGE_NUM'] = $arVariables['PAGE_NUM'];

        $this->IncludeComponentTemplate($componentPage);
    }

    protected function loadSectionCache()
    {
        $arSection = [];
        if ($this->cache->initCache($this->arParams['CACHE_TIME'], 'sections', $this->cacheDir)) {
            $arSection = $this->cache->getVars();
        } elseif ($this->cache->StartDataCache()) {
            $res = CIBlockSection::GetList(
                ['SORT' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
                    '<DEPTH_LEVEL' => 3,

                ],
                false,
                [
                    'ID',
                    'EXTERNAL_ID',
                    'NAME',
                    'SECTION_PAGE_URL',
                ]
            );

            while ($arItem = $res->Fetch()) {
                if ($arItem['EXTERNAL_ID'] == $this->arParams['DEFAULT_SECTION']['EXTERNAL_ID']) {
                    $arSection['MENU'][0] = [
                        'ID' => $arItem['ID'],
                        'NAME' => $arItem['NAME'],
                        'EXTERNAL_ID' => $arItem['EXTERNAL_ID'],
                        'LINK' => str_replace('/#SECTION_CODE#', '', $arItem['SECTION_PAGE_URL'])
                    ];
                } else {
                    $arSection['MENU'][$arItem['ID']] = [
                        'NAME' => $arItem['NAME'],
                        'EXTERNAL_ID' => $arItem['EXTERNAL_ID'],
                        'LINK' => str_replace('#SECTION_CODE#', $arItem['EXTERNAL_ID'], $arItem['SECTION_PAGE_URL']),
                    ];
                    //возможно, это не самое оптимальное решение, но пусть будет так
                    $arSection['CHAIN'][$arItem['EXTERNAL_ID']] = [
                        'TITLE' => $arItem['NAME'],
                        'LINK' => str_replace('#SECTION_CODE#', $arItem['EXTERNAL_ID'], $arItem['SECTION_PAGE_URL']),
                    ];
                }
            }
            if (count($arSection['MENU']) > 1) {
                $this->cache->EndDataCache($arSection);
            } else {
                $this->cache->AbortDataCache();
            }
        }
        return $arSection;
    }

    protected function createChain()
    {
        global $APPLICATION;

        $arSection = $this->loadSectionCache();

        $APPLICATION->AddChainItem(
            $this->arParams['DEFAULT_SECTION']['TITLE'],
            $this->arParams['DEFAULT_SECTION']['LINK']
        );
        $APPLICATION->AddChainItem(
            $arSection['CHAIN'][$this->arParams['CURRENT_SECTION']]['TITLE'],
            $arSection['CHAIN'][$this->arParams['CURRENT_SECTION']]['LINK']
        );
        $GLOBALS['SEO_PAGE_ELEMENT'] = true;
    }
}
