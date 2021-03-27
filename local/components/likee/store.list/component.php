<?
/** @var CBitrixComponent $this */

global $LOCATION;
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global CCacheManager $CACHE_MANAGER */
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
$arParams['LOCATION']['REGION'] = $LOCATION->getRegion(true);
$arParams['PHONE'] = (isset($arParams['PHONE']) && $arParams['PHONE'] == 'Y' ? 'Y' : 'N');
$arParams['SCHEDULE'] = (isset($arParams['SCHEDULE']) && $arParams['SCHEDULE'] == 'Y' ? 'Y' : 'N');

$arParams['PATH_TO_ELEMENT'] = (isset($arParams['PATH_TO_ELEMENT']) ? trim($arParams['PATH_TO_ELEMENT']) : '');
if ($arParams['PATH_TO_ELEMENT'] == '') {
    $arParams['PATH_TO_ELEMENT'] = 'store/#store_id#';
}

$arParams['MAP_TYPE'] = (int)(isset($arParams['MAP_TYPE']) ? $arParams['MAP_TYPE'] : 0);
$arParams['SET_TITLE'] = (isset($arParams['SET_TITLE']) && $arParams['SET_TITLE'] == 'Y' ? 'Y' : 'N');

if (!isset($arParams['CACHE_TIME'])) {
    $arParams['CACHE_TIME'] = 0;
}

$arParams['CACHE_TIME'] = 0;

if ($this->startResultCache()) {
    if (!\Bitrix\Main\Loader::includeModule('catalog')) {
        $this->abortResultCache();
        ShowError(GetMessage('CATALOG_MODULE_NOT_INSTALL'));
        return;
    }

    $storeName = mb_strtolower($arParams['STORE_NAME']);

    $arResult['TITLE'] = GetMessage('SCS_DEFAULT_TITLE');
    $arResult['MAP'] = $arParams['MAP_TYPE'];

    $arFilter = [];
    $arFilter['ACTIVE'] = 'Y';
    $arFilter['UF_REGION'] = $arParams['LOCATION']['REGION'];
    $arFilter['UF_DONT_SHOW'] = 'N';

    if (defined('ONLINE_STORE_ID') && ONLINE_STORE_ID > 0) {
        $arFilter['!ID'] = ONLINE_STORE_ID;
    }

    $rsStores = CCatalogStore::GetList(
        // ['SORT' => 'ASC'], закомменчено КА
        ['TITLE' => 'ASC'],
        $arFilter,
        false,
        false,
        [
            'ID',
            'TITLE',
            'ADDRESS',
            'DESCRIPTION',
            'GPS_N',
            'GPS_S',
            'IMAGE_ID',
            'PHONE',
            'SCHEDULE',
            'SITE_ID',
            'UF_METRO',
            'UF_PHONES',
            'UF_METRO_CODE',
            'UF_BELONG',
            'UF_REGION',
            'UF_METRO_DADATA',
            'UF_METRO_CODE',
        ]
    );

    $storages = $LOCATION->getStorages();

    $arStores = [];
    while ($arStore = $rsStores->Fetch()) {
        $sStoreSite = (string)$arStore['SITE_ID'];

        if (!empty($sStoreSite) && $sStoreSite != SITE_ID) {
            continue;
        }

        $arStore['METRO'] = [];
        foreach (json_decode($arStore['UF_METRO_DADATA']) as $iMetro) {
            $arStore['METRO'][$iMetro->name] = $iMetro->name;
        }

        $sUrl = CComponentEngine::MakePathFromTemplate($arParams['PATH_TO_ELEMENT'], ['store_id' => $arStore['ID']]);

        $arStore['IMAGE_ID'] = intval($arStore['IMAGE_ID']);

        $arStore['PICTURE'] = [];
        if ($arStore['IMAGE_ID'] > 0) {
            $arStore['PICTURE'] = CFile::GetFileArray($arStore['IMAGE_ID']);
        }

        $arStores[$arStore['ID']] = [
            'ID' => $arStore['ID'],
            'TITLE' => $arStore['TITLE'],
            'PHONES' => array_filter(array_merge([$arStore['PHONE']], unserialize($arStore['UF_PHONES']) ?: [])),
            'SCHEDULE' => $arStore['SCHEDULE'],
            'IMAGE_ID' => $arStore['IMAGE_ID'],
            'PICTURE' => $arStore['PICTURE'],
            'GPS_N' => $arStore['GPS_N'],
            'GPS_S' => $arStore['GPS_S'],
            'ADDRESS' => $arStore['ADDRESS'],
            'URL' => $sUrl,
            'DESCRIPTION' => (string)$arStore['DESCRIPTION'],
            'METRO' => $arStore['METRO'],
            'METRO_CODE' => $arStore['UF_METRO_CODE'],
            'BELONG' => (empty($arStore['UF_BELONG']) ? false : (1 != $arStore['UF_BELONG'] ? 'f' : 'r')),
            'UF_REGION' => $arStore['UF_REGION'],
            'RESERV' => $storages[$arStore['ID']][1],
        ];
        if (!empty($storeName)) {
            if (stristr(mb_strtolower($arStore['TITLE']), $storeName) === false && stristr(mb_strtolower($arStore['ADDRESS']), $storeName) === false) {
                $findMetro = false;
                foreach ($arStore['METRO'] as $metroItem) {
                    if (stristr(mb_strtolower($metroItem), $storeName) !== false) {
                        $findMetro = true;
                    }
                }
                if (!$findMetro) {
                    unset($arStores[$arStore['ID']]);
                }
            }
        }
    }

    $arResult['STORES'] = $arStores;

    $this->endResultCache();
}

if ($_REQUEST['metro_id']) {
    foreach ($arResult['STORES'] as $pid => $arStore) {
        if (empty($arStore['METRO'][$_REQUEST['metro_id']])) {
            unset($arResult['STORES'][$pid]);
        }
    }
}

if (\Likee\Site\Helper::isAjax()) {
    if ($_REQUEST['search'] == 'y') {
        $APPLICATION->RestartBuffer();
        $this->IncludeComponentTemplate('stores');
        exit;
    } elseif ($_REQUEST['show_map'] == 'y') {
        $arShopsJson = [];
        foreach ($arResult['STORES'] as &$arStore) {
            if (!empty($arStore['GPS_N']) && !empty($arStore['GPS_S'])) {
                $arShopsJson[] = [
                    'title' => $arStore['TITLE'],
                    'address' => $arStore['ADDRESS'],
                    'distance' => '',
                    'subway' => implode('<br>', $arStore['METRO'][0]),
                    'subway_trans' => $arStore['METRO_CODE'],
                    'worktime' => $arStore['SCHEDULE'],
                    'phone' => implode('<br>', $arStore['PHONES']),
                    'coordinates' => [
                        'lat' => floatval(str_replace(',', '.', $arStore['GPS_N'])),
                        'lng' => floatval(str_replace(',', '.', $arStore['GPS_S'])),
                    ],
                ];
            }
        }
        $APPLICATION->RestartBuffer();
        header('Content-type: application/json');
        echo json_encode(['shops' => $arShopsJson]);
        \Likee\Site\Helper::stopApplication();
    }
}

$this->IncludeComponentTemplate();

if ($arParams['SET_TITLE'] == 'Y') {
    $APPLICATION->SetTitle($arParams['TITLE']);
}
