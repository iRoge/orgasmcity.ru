<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeSelectCityComponent $component */

$this->setFrameMode(true);
//print_r($_COOKIE['FILTER_CITY']);
//print_r($GLOBALS['CITY_FILTER']['CITY_NAME']);

?>

<select class="selectize selectize--inline" name="filter_cities" !onchange="document.location = this.value;">
    <? foreach ($arResult['CITIES'] as $arCity): $bSelected = false;?>
        <? //$bSelected = !empty($arResult['CURRENT']) && $arResult['CURRENT']['ID'] == $arCity['ID']; ?>
        <?/*if(strpos($_SERVER["HTTP_USER_AGENT"], "Firefox")!==false){$action='set_city';}else{$action='set_filter_city';}*/?>
        <?$action='set_city';?>
        <?if($_COOKIE['FILTER_CITY']):?>
			<? if ($_COOKIE['FILTER_CITY']==$arCity['ID']){$bSelected=true;}?>
        <?else:?>
			<? if ($GLOBALS['CITY_FILTER']['CITY_NAME']==$arCity['CITY_NAME']){$bSelected=true;} ?>
        <?endif?>
        <option value="<?= $APPLICATION->GetCurPageParam('action='.$action.'&city_id=' . $arCity['ID'], ['city_id', 'action']); ?>"
        	<? if ($bSelected): ?> selected<? endif; ?>><?= $arCity['CITY_NAME']; ?></option>
    <? endforeach; ?>
</select>