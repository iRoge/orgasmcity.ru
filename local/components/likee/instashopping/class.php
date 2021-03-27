<?php

use MetzWeb\Instagram\Instagram;

class LikeeInstashoppingComponent extends CBitrixComponent
{
    const API_KEY = 'cd6af13c3954406b9dfc2ad52e545608';
    const API_SECRET = 'd55d73f6affe4817bb1da78c6ccf2bd0 ';
    const LOGIN = 'respectshoes';

    public static $arItems = [];

    public function onPrepareComponentParams($arParams)
    {
        $arParams['COUNT'] = intval($arParams['COUNT']);
        if (!$arParams['COUNT']) {
            $arParams['COUNT'] = 12;
        }

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 180000;
        }

        $arParams['TAG'] = empty($arParams['TAG']) ? '' : trim($arParams['TAG']);
        
        $arParams['PAGEN'] = (!empty($_REQUEST['PAGEN_1']) && is_numeric($_REQUEST['PAGEN_1'])) ? intval($_REQUEST['PAGEN_1']) : 0;

        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            try {
                $arItems = self::getItemsByTag($this->arParams['TAG']);
            } catch (Exception $ex) {
                $this->abortResultCache();
                CEvent::Send(
                    "INSTASHOPPING_TOKEN",
                    SITE_ID,
                    array(
                        "ERROR_MESSAGE" => $ex->getMessage(),
                    )
                );
                COption::RemoveOption("likee", "instashopping_token");
                return false;
            }
            $itemsTotal = count($arItems);
            $currentPageOffset = intval($this->arParams['PAGEN']) * intval($this->arParams['COUNT']);

            if ($currentPageOffset < $itemsTotal) {
                $this->arResult['ITEMS'] = array_slice($arItems, $currentPageOffset, $this->arParams['COUNT']);
            } else {
                $this->arResult['ITEMS'] = [];
            }
            
            $this->arResult['USER'] = self::LOGIN;
            $this->arResult['NEXT_PAGE'] = (($currentPageOffset + $this->arParams['COUNT']) >= $itemsTotal) ? false : $this->arParams['PAGEN'] + 1;

            $this->includeComponentTemplate();
        }
    }

    public static function getItemsByTag($tagIncluded = '')
    {
        $arItems = [];
        
        $obCache = \Bitrix\Main\Application::getCache();
        $cacheId = 'likee_instashopping_'.$tagIncluded;

        if ($obCache->initCache(21600, $cacheId, '/likee')) {
            $arItems = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            try {
                $arItems = self::loadItems($tagIncluded);
            } catch (Exception $e) {
                $obCache->AbortDataCache();
                throw new Exception($e->getMessage());
            }
            $obCache->endDataCache($arItems);
        }

        return $arItems;
    }

    public static function loadItems($tagIncluded = '')
    {
        $arItems = [];
        $arItemsArtReference = [];

        try {
            require($_SERVER["DOCUMENT_ROOT"] . "/local/vendor/autoload.php");

            $instagram = new Instagram(self::API_KEY);
            $instagram->setAccessToken(COption::GetOptionString("likee", "instashopping_token"));
        } catch (\Exception $e) {
            return $arItems;
        }

        $counter = 1;
        $maxRequest = 10;
        $limit = 90;

        $media = $instagram->getUserMedia('self', $limit);
        if (!empty($media->meta->error_type)) {
            throw new Exception($media->meta->error_message);
        }
        do {
            if (empty($media->data)) {
                break;
            }
            
            foreach ($media->data as $key => $data) {
                if ('image' != $data->type || empty($data->caption->text)) {
                    continue;
                }

                $caption = $data->caption->text;
                if (!empty($tagIncluded) && false === strpos($caption, $tagIncluded)) {
                    continue;
                }
                if (!preg_match_all('/арт:\s*([-_\/0-9a-zа-я]+)/iu', $caption, $artMatches, PREG_SET_ORDER)) {
                    continue;
                }

                $key = count($arItems);
                $arTmp = [
                    'IMG' => $data->images->standard_resolution->url,
                    'CAPTION' => $data->caption->text,
                    'LIKES_COUNT' => $data->likes->count,
                    'COMMENTS_COUNT' => $data->comments->count,
                    'ITEMS' => []
                ];

                foreach ($artMatches as $art) {
                    $art = trim($art[1]);
                    $art = mb_strtoupper($art);

                    $arTmp['ITEMS'][$art] = [
                        'ART' => $art,
                        'NAME' => '',
                        'DETAIL_PAGE_URL' => '',
                    ];

                    $arItemsArtReference[$art][] = $key;
                }

                $arItems[] = $arTmp;
            }

            $counter ++;
            $media = $instagram->pagination($media);
        } while ($media &&  $counter < $maxRequest);

        // ищем товары
        if ($arItemsArtReference) {
            $arFilter = [
                'IBLOCK_ID' => IBLOCK_CATALOG,
                '=PROPERTY_ARTICLE' => array_keys($arItemsArtReference)
            ];
            $arSelect = ['ID', 'IBLOCK_ID', 'ACTIVE', 'NAME', 'DETAIL_PAGE_URL', 'PROPERTY_ARTICLE'];
            $rsItems = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

            while ($arFields = $rsItems->GetNext()) {
                $art = $arFields['PROPERTY_ARTICLE_VALUE'];
                $art = mb_strtoupper($art);

                foreach ($arItemsArtReference[$art] as $key) {
                    if ('N' == $arFields['ACTIVE']) {
                        unset($arItems[$key]['ITEMS'][$art]);
                        continue;
                    }

                    $arItems[$key]['ITEMS'][$art]['NAME'] = $arFields['NAME'];
                    $arItems[$key]['ITEMS'][$art]['DETAIL_PAGE_URL'] = $arFields['DETAIL_PAGE_URL'];
                }
            }
        }

        return $arItems;
    }
}
