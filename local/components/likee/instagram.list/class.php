<?php

class LikeeInstagramListComponent extends CBitrixComponent
{
    const LOGIN = 'respectshoes';

    public function onPrepareComponentParams($arParams)
    {
        $arParams['COUNT'] = intval($arParams['COUNT']);
        if (!$arParams['COUNT']) {
            $arParams['COUNT'] = 12;
        }
        
        /*$arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 180000;
        }*/

        return $arParams;
    }

    public function executeComponent()
    {
        if (!\Bitrix\Main\Loader::includeModule('likee.site')) {
            return false;
        }

        if ($this->startResultCache()) {
            $instashoppingTag = COption::GetOptionString("likee", "instashopping_tag", '#instashopping');

            CBitrixComponent::includeComponentClass('likee:instashopping');
            try {
                $arTempItems = LikeeInstashoppingComponent::getItemsByTag($instashoppingTag);
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

            $this->arResult['USER'] = self::LOGIN;
            $this->arResult['ITEMS'] = array_slice($arTempItems, 0, 4);

            if (empty($this->arResult['ITEMS'])) {
                $this->startResultCache();
            }

            $this->includeComponentTemplate();
        }
    }

    public function send($sUrl)
    {
        $arResult = [];

        $content = file_get_contents($sUrl);
        if ($content && false !== strpos($content, 'window._sharedData')) {
            $content = explode("window._sharedData = ", $content)[1];
            $content = explode(";</script>", $content)[0];
            
            $data = json_decode($content, true);
            $arResult = $data['entry_data']['ProfilePage'][0];
        }

        return $arResult;
    }

    public function loadItems()
    {
        $arItems = array();

        $sUrlBase = 'https://instagram.com';
        $sUrl = $sUrlBase .'/' .self::LOGIN ;
        $sUrlApi = $sUrl; //  . '/?__a=1&max_id=10';

        $arData = $this->send($sUrlApi);

        if ($arData && is_array($arData) && is_array($arData['graphql']['user']['edge_owner_to_timeline_media']['edges'])) {
            $i = 1;
            foreach ($arData['graphql']['user']['edge_owner_to_timeline_media']['edges'] as $arItem) {
                if (empty($arItem['node'])) {
                    continue;
                }
                $arItem = $arItem['node'];

                if ($arItem['__typename']=='GraphImage') {
                    $arItems[] = array(
                        'ID' => $arItem['id'],
                        'LINK' => $sUrlBase.'/p/'.$arItem['shortcode'].'/',
                        'SRC' => $arItem['display_url'],
                        'WIDTH' => $arItem['dimensions']['width'],
                        'HEIGHT' => $arItem['dimensions']['height'],
                        'NAME' => 'instagramm-sheregesh'
                    );

                    if ($i++ >= $this->arParams['COUNT']) {
                        break;
                    }
                }
            }
        }

        $this->arResult['LOAD_MORE'] = empty($arData['graphql']['user']['edge_owner_to_timeline_media']['page_info']['has_next_page']) ? 'N' : 'Y';

        return $arItems;
    }
}
