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
?>

<select class="selectize selectize--inline" onchange="document.location = this.value;">
    <? foreach ($arResult['CITIES'] as $arCity): ?>
        <? $bSelected = !empty($arResult['CURRENT']) && $arResult['CURRENT']['ID'] == $arCity['ID']; ?>
        <?if(strpos($_SERVER["HTTP_USER_AGENT"], "Firefox")!==false){$action='set_city';}else{$action='set_filter_city';}?>
        <option value="<?= $APPLICATION->GetCurPageParam('action='.$action.'&city_id=' . $arCity['ID'], ['city_id', 'action']); ?>"<? if ($bSelected): ?> selected<? endif; ?>><?= $arCity['CITY_NAME']; ?></option>
    <? endforeach; ?>
</select>