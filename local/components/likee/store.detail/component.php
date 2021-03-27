<?
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
/** @global CCacheManager $CACHE_MANAGER */

global $LOCATION;
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$arParams['STORE'] = (isset($arParams['STORE']) ? (int)$arParams['STORE'] : 0);
if ($arParams['STORE'] <= 0) {
    ShowError(GetMessage('STORE_NOT_EXIST'));
    return;
}

$arParams['MAP_TYPE'] = (int)(isset($arParams['MAP_TYPE']) ? $arParams['MAP_TYPE'] : 0);

$arParams['SET_TITLE'] = (isset($arParams['SET_TITLE']) && $arParams['SET_TITLE'] == 'Y' ? 'Y' : 'N');

if (!isset($arParams['CACHE_TIME'])) {
    $arParams['CACHE_TIME'] = 3600;
}

if ($this->startResultCache()) {
    if (!\Bitrix\Main\Loader::includeModule('catalog')) {
        $this->abortResultCache();
        ShowError(GetMessage('CATALOG_MODULE_NOT_INSTALL'));
        return;
    }

    $rsStore = CCatalogStore::GetList(
        [
            'ID' => 'ASC'
        ],
        [
            'ID' => $arParams['STORE'],
            'ACTIVE' => 'Y'
        ],
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
            'UF_CITY',
            'UF_METRO',
            'UF_DRIVING',
            'UF_PICTURES',
            'UF_PHONES',
            'UF_BELONG'
        ]
    );

    $arResult = $rsStore->Fetch();

    if (!$arResult) {
        $this->abortResultCache();
        ShowError(GetMessage('STORE_NOT_EXIST'));
        return;
    }

    $arResult['PHONES'] = array_filter(array_merge([$arResult['PHONE']], unserialize($arResult['UF_PHONES']) ?: []));

    $arResult['METRO'] = [];
    foreach (unserialize($arResult['UF_METRO']) as $iMetro) {
        $obEntity = \Likee\Site\Helpers\HL::getEntityClassByTableName('b_1c_dict_metro');
        if (!empty($obEntity) && is_object($obEntity)) {
            $sClass = $obEntity->getDataClass();
            $arMetro = $sClass::getRowById($iMetro);
            $arResult['METRO'][] = $arMetro['UF_NAME'];
        }
    }


    foreach (unserialize($arResult['UF_PICTURES']) as $iPhoto) {
        $arResult['PHOTO_SRC'][] = \Likee\Site\Helper::getResizePath($iPhoto, 640, 440);
    }

    switch (count($arResult['PHOTO_SRC'])) {
        case 0:
        case 1:
            $arResult['PHOTO_CLASS'] = "shop-photo-slider-1";
            break;
        case 2:
            $arResult['PHOTO_CLASS'] = "shop-photo-slider-2";
            break;
        default:
            $arResult['PHOTO_CLASS'] = "shop-photo-slider";
    }
    if (!empty($arResult['UF_DRIVING'])) {
        $arResult['SCHEME'] = [
            'THUMB' => \Likee\Site\Helper::getResizePath($arResult['UF_DRIVING'], 640, 640),
            'SRC' => CFile::GetPath($arResult['UF_DRIVING']),
        ];
    }

    unset($storeIterator);

    $arResultMAP[] = $arParams['MAP_TYPE'];

    if (isset($arParams['PATH_TO_LISTSTORES'])) {
        $arResult['LIST_URL'] = CComponentEngine::makePathFromTemplate($arParams['PATH_TO_LISTSTORES']);
    }

    $arResult['PHONES'] = array_filter(array_merge([$arResult['PHONE']], unserialize($arResult['UF_PHONES']) ?: []));

    $arResult['LOCATION'] = [];
    $arLocations = \Likee\Location\Location::all();
    foreach ($arLocations as $arLocation) {
        if ($arLocation['CITY_NAME'] == $arResult['UF_CITY']) {
            $arResult['LOCATION'] = $arLocation;
            break;
        }
    }

    $arResult['RESERV'] = $LOCATION->getStorages()[$arResult['ID']][1];
    $arResult['BELONG'] = (empty($arResult['UF_BELONG']) ? false : (1 != $arResult['UF_BELONG'] ? 'f' : 'r'));

    $this->includeComponentTemplate();
}

if (\Likee\Site\Helper::isAjax()) {
    if ($_REQUEST['show_map'] == 'y') {
        $iLon = floatval(str_replace(',', '.', $arResult['GPS_S']));
        $iLat = floatval(str_replace(',', '.', $arResult['GPS_N']));

        if (empty($iLon) && !empty($arResult['LOCATION']['LON'])) {
            $iLon = floatval($arResult['LOCATION']['LON']);
        }

        if (empty($iLat) && !empty($arResult['LOCATION']['LAT'])) {
            $iLat = floatval($arResult['LOCATION']['LAT']);
        }

        $arShopsJson = [
            [
                'title' => $arResult['TITLE'],
                'address' => $arResult['ADDRESS'],
                'distance' => '',
                'subway' => implode('<br>', $arResult['METRO']),
                'worktime' => $arResult['SCHEDULE'],
                'phone' => implode('<br>', $arResult['PHONES']),
                'coordinates' => [
                    'lat' => $iLat,
                    'lng' => $iLon
                ]
            ]
        ];

        $APPLICATION->RestartBuffer();
        header('Content-type: application/json');
        echo json_encode([
            'shops' => $arShopsJson,
            'pageTitle' => $arResult['TITLE'],
        ]);
        \Likee\Site\Helper::stopApplication();
    }
}

$APPLICATION->SetTitle($this->arResult['TITLE']);
$APPLICATION->AddChainItem($this->arResult['TITLE']);
