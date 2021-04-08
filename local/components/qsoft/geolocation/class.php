<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Sale\Location\LocationTable;
use Qsoft\Helpers\ComponentHelper;

class GeolocationComponent extends ComponentHelper
{
    const POPULAR_LOCALITIES_TABLE = 'b_popular_locality';
    const COLUMNS_COUNT = 3;

    public function onPrepareComponentParams($arParams)
    {
        parent::onPrepareComponentParams($arParams);

        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0) {
            $arParams['CACHE_TIME'] = 31536000;
        }

        return $arParams;
    }

    public function executeComponent()
    {
        global $LOCATION;
        $this->arResult['LOCATION_NAME'] = $LOCATION->getName();
        $this->arResult['POPULAR_LOCALITIES'] = $this->getPopularLocalities();
        $this->IncludeComponentTemplate();
    }

    private function getPopularLocalities(): array
    {
        global $DB;
        $arLocalities = [];

        if ($this->initCache('popular_localities')) {
            $arLocalities = $this->getCachedVars('popular_localities');
        } elseif ($this->startCache()) {
            $this->startTagCache();
            $this->registerTag('popular_localities');

            $dbResponse = $DB->Query(
                'SELECT location_code, name FROM ' . self::POPULAR_LOCALITIES_TABLE . ' ORDER BY sort;'
            );

            while ($arLocality = $dbResponse->Fetch()) {
                $arLocalities[] = [
                    'location_code' => trim($arLocality['location_code']),
                    'name' => trim($arLocality['name']),
                ];
            }

            if (!empty($arLocalities)) {
                $columnSize = ceil(count($arLocalities) / self::COLUMNS_COUNT);
                $arLocalities = array_chunk($arLocalities, $columnSize, true);
                $this->endTagCache();
                $this->saveToCache('popular_localities', $arLocalities);
            } else {
                $this->abortTagCache();
                $this->abortCache();
            }
        }

        return $arLocalities;
    }
}
