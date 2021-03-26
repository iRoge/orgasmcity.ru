<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var LikeeInstagramListComponent $component */
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

$this->setFrameMode(true);
?>
<div class="instashoping col-xs-12">
    <div class="main">
        <a class="zagolovok-link" href='/instashopping/'><h2 class="zagolovok">InstaShopping</h2></a>
        <div class="bestsel col-xs-12">
            <? foreach ($arResult['ITEMS'] as $arMedia) :
                $name = $arResult['USER'];
                $url = '/instashopping/';
                
                foreach ($arMedia['ITEMS'] as $arItem) {
                    if ($arItem['NAME']) {
                        $name = $arItem['NAME'];
                        $url = $arItem['DETAIL_PAGE_URL'];
                    }
                }
                ?>
                <div style="outline: none;">
                    <div class="col-xs-12 instashop-one js-instashoping-item">
                        <img src="<?= $arMedia['IMG'] ?>" alt="<?= $name; ?>">
                        <a href="<?= $url; ?>" target="_blank">
                            <div class="instashop-one-hover">
                                <p>@<?= $arResult['USER'] ?></p>
                                <div>
                                  <img src="<?= SITE_TEMPLATE_PATH; ?>/img/like-white.png" /><p><?= $arMedia['LIKES_COUNT'] ?></p>
                                  <img src="<?= SITE_TEMPLATE_PATH; ?>/img/comment-bubble.png" /><p><?= $arMedia['COMMENTS_COUNT'] ?></p>
                                  <img src="<?= SITE_TEMPLATE_PATH; ?>/img/shopping-cart.png" />
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            <? endforeach; ?>
        </div>
    </div>
</div>
