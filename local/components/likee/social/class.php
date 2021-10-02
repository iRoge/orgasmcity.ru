<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * Project: Dveplanety
 * Date: 2016-11-25
 * Time: 16:43:06
 */
class LikeeSocialComponent extends \CBitrixComponent
{
    public function onPrepareComponentParams($arParams)
    {
        return $arParams;
    }

    public function executeComponent()
    {
        if ($this->startResultCache()) {
            foreach($this->arParams as $sKey => $sParam) {
                $this->arResult[$sKey] = $sParam;
            }
            $this->includeComponentTemplate();
        }
    }
}