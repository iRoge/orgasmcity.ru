<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$respectOptions = [];
$respectOptions['POPUP_FO_ACTIVE'] = COption::GetOptionInt("likee", "popup_fo_active", 1);
$respectOptions['POPUP_FO_PAGE'] = COption::GetOptionInt("likee", "popup_fo_page", 40);
$respectOptions['POPUP_FO_CATALOG'] = COption::GetOptionInt("likee", "popup_fo_catalog", 120);
$respectOptions['POPUP_FO_ONCE'] = COption::GetOptionInt("likee", "popup_fo_once", 0);
$respectOptions['MAIN_SLIDER_SPEED'] = COption::GetOptionInt("likee", "main_slider_speed", 10);
$respectOptions['POPUP_BANNER_PATH'] = [];
$respectOptions['POPUP_BANNER_UTM'] = COption::GetOptionString("likee", "popup_b_utm", '');


try {
    $bannerIblockId = \Qsoft\Helpers\IBlockHelper::getIBlockId('POPUP_ADS');

    $obCache = \Bitrix\Main\Application::getInstance()->getCache();
    $sCacheDir = '/likee/site/';

    if ($obCache->initCache(604800, 'likee.popup-banners', $sCacheDir)) {
        $respectOptions['POPUP_BANNER_PATH'] = $obCache->getVars();
    } elseif (\Bitrix\Main\Loader::includeModule('iblock') && $obCache->startDataCache()) {
        \Bitrix\Main\Application::getInstance()->getTaggedCache()->startTagCache($sCacheDir);
        \Bitrix\Main\Application::getInstance()->getTaggedCache()->RegisterTag('iblock_id_'.$bannerIblockId);

        $res = \CIBlockElement::GetList(
            [],
            [
                'IBLOCK_ID' => $bannerIblockId,
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y'
            ],
            false,
            false,
            ['IBLOCK_ID', 'ID', 'PROPERTY_URL']
        );
        while($arFields = $res->GetNext(false, false)) {
            $respectOptions['POPUP_BANNER_PATH'][] = $arFields['PROPERTY_URL_VALUE'];
        }
        unset($res, $arFields);

        \Bitrix\Main\Application::getInstance()->getTaggedCache()->endTagCache();

        $obCache->endDataCache($respectOptions['POPUP_BANNER_PATH']);
    }
} catch (\Exception $e) {}

?>
<script type="text/javascript" data-skip-moving="true">
window.RESPECT_OPTIONS = <?=CUtil::PhpToJSObject($respectOptions)?>;
</script>