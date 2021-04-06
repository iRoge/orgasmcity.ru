<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
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
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="col-xs-12 news padding-o">
    <div class="main">
<? $APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "",
    array(
                                "PATH" => "",
                                "SITE_ID" => "s1",
                                "START_FROM" => "0"
                        )
); ?>
<?
/*
        <a href="/">Главная</a> <img src="'.SITE_TEMPLATE_PATH.'/img/bc-right.png" /> Новости
*/ ?>
<? /*
      <h2 class="zagolovok">Новости</h2>
      <div class="main-top col-xs-12 news-top">
        <div class="main">
          <div class="col-md-7 col-xs-12 padding-o">
            <div class="in-main-top col-md-11 col-md-offset-0 col-sm-6 col-sm-offset-1 in-news-top">
              <span>Новости&nbsp;&nbsp;&nbsp; —</span>
              <h2>Respect в ТРК Vegas Кунцево</h2>
              <p>
                Супер тренд 2018 года. Полуботинки для девушек на тонкой подошве.
                Это всегда очень удобно, комфортно или сильно.
                Носите ботинки и любите себя!
              </p>
              <a href="#">Подробнее</a>
            </div>
          </div>
        </div>
      </div>

      <div class="in-main-top col-md-11 col-md-offset-0 col-sm-6 col-sm-offset-1 in-news-top2">
        <h2>Respect в ТРК Vegas Кунцево</h2>
        <a href="#">Подробнее</a>
      </div>
*/ ?>
<p class="rubrics">Рубрики:</p>
<? foreach ($arResult['SECTIONS'] as $arSection) : ?>
<input type="radio" name="<?= $arSection['SECTION_PAGE_URL']; ?>"<? if ($arSection['CURRENT'] == 'Y') :
    ?> checked="checked"<?
                          endif; ?>  id="<?= $arSection['SECTION_PAGE_URL']; ?>"/>
<label for="vkl1"><?= $arSection['NAME']; ?></label>
<? endforeach; ?>
