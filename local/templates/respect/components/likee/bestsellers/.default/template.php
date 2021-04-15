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
/** @var LikeeBestsellersComponent $component */

$this->setFrameMode(true);
?>
<div class="col-xs-12">
  <div class="bestsellers main">
    <h2 class="zagolovok">Бестселлеры</h2>
    <div class="bestsel">
     <? foreach ($arResult['PRODUCTS'] as $iKey => $arItem): ?>
        <div style="outline: none;">
          <div class="col-xs-12">
            <? include 'element.php'; ?>
          </div>
        </div>
      <? endforeach; ?>
    </div>
  </div>
</div> 