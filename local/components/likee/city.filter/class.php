<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Site
 * Date: 2017-01-31
 * Time: 17:59:25
 */
class LikeeFilterCityComponent extends \Likee\Site\Component
{
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        if ($arParams['CACHE_TIME'] <= 0)
            $arParams['CACHE_TIME'] = 604800;

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        if (!$this->loaderModules(['likee.site', 'likee.location'])) {
            return false;
        }

        if (!empty($this->request->get('action'))) {
            if ($this->processRequest()) {
                LocalRedirect($APPLICATION->GetCurPageParam('', ['action', 'city_id']));
            }
        }

        if ($this->StartResultCache()) {
            $this->arResult['CITIES'] = \Likee\Location\Location::all();
            $this->abortResultCache();
        }

        if (!empty($_SESSION['LIKEE_CITY_FILTER_ID']) && array_key_exists($_SESSION['LIKEE_CITY_FILTER_ID'], $this->arResult['CITIES'])) {
            $this->arResult['CURRENT'] = $this->arResult['CITIES'][$_SESSION['LIKEE_CITY_FILTER_ID']];
        } else {
            $this->arResult['CURRENT'] = \Likee\Location\Location::getCurrent();
        }

        $GLOBALS['CITY_FILTER'] = $this->arResult['CURRENT'];

        $this->includeComponentTemplate();
    }

    public function processRequest()
    {
        $sAction = htmlspecialchars(trim($this->request->get('action')));

        if (!in_array($sAction, ['set_filter_city']))
            return false;

        if ($sAction == 'set_filter_city') {
            $_SESSION['LIKEE_CITY_FILTER_ID'] = intval($this->request->get('city_id'));
        }

        return true;
    }
}