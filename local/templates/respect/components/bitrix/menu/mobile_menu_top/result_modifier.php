<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

if (\Bitrix\Main\Loader::includeModule('likee.site')) {
    $arResult = \Likee\Site\Helpers\Menu::menuTreeBuild($arResult);
}

try {
    $colorsIblockId = \Likee\Site\Helpers\IBlock::getIBlockId('COLORS');
    
    $arSelect = array("ID", "NAME", 'IBLOCK_ID','PROPERTY_COLOR');
    $arFilter = array("IBLOCK_ID"=>$colorsIblockId, "ACTIVE"=>"Y");
    $res = CIBlockElement::GetList(array(), $arFilter, false, array("nPageSize"=>5000), $arSelect);
    while ($ob = $res->Fetch()) {
        $arColors[$ob['ID']] = $ob;
    }
    foreach ($arResult as $key => $item) {
        if ($item['PARAMS']['PROPS']['UF_TEXT_C']) {
            $arResult[$key]['PARAMS']['PROPS']['UF_TEXT_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_TEXT_C']];
        }
        if ($item['PARAMS']['PROPS']['UF_BG_C']) {
            $arResult[$key]['PARAMS']['PROPS']['UF_BG_COLOR'] = $arColors[$item['PARAMS']['PROPS']['UF_BG_C']];
        }
    }
    $arButtons=array();
    foreach ($arResult as $key => $item) {
        if (isset($item['PARAMS']['PROPS'])) {
            $arButtons[]=$item;
            unset($arResult[$key]);
        }
    }
    foreach ($arResult as $key => $item) {
        foreach ($item['ITEMS'] as $key_items => $val_items) {
            if (empty(str_replace('&nbsp;', '', $val_items['TEXT']))) {
                if ($val_items['IS_PARENT']) {
                    $arNewColl[$key]=$val_items['ITEMS'];
                }
                unset($item['ITEMS'][$key_items]);
            }
        }
        
        foreach ($item['ITEMS'] as $key_items => $val_items) {
            if (strpos($val_items['LINK'], 'obuv')!==false) {
                foreach ($arNewColl[$key] as $key_col => $val_col) {
                    $val_items['ITEMS'][]=$val_col;
                }
                $item['ITEMS'][$key_items]=$val_items;
            }
        }
        
        if (!empty($arButtons) && isset($arButtons)) {
            foreach ($arButtons as $keybut => $val) {
                $need_ins=false;
                $is_first=false;
                foreach ($val['ITEMS'] as $key_val => $item_val) {
                    if (strpos($item_val['LINK'], $item['LINK'])!==false) {
                        if (strpos($val['LINK'], $item['LINK'])===false) {
                            $val['LINK'].=str_replace("/", "", $item['LINK'])."/";
                        }
                        $need_ins=true;
                        if (strpos($item_val['LINK'], 'new')!==false) {
                            $is_first=true;
                        }
                    } else {
                        unset($val['ITEMS'][$key_val]);
                    }
                }
                if ($need_ins) {
                    if ($is_first) {
                        $item_tmp=array();
                        $item_tmp[]=$val;
                        foreach ($item['ITEMS'] as $key_items => $val_items) {
                            $item_tmp[]=$val_items;
                        }
                        $item['ITEMS']=$item_tmp;
                    } else {
                        $item['ITEMS'][]=$val;
                    }
                }
            }
        }
        $arResult[$key]=$item;
    }
} catch (\Exception $e) {
}
