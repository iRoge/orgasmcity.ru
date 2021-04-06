<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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

<form action="<?= $APPLICATION->GetCurPage(false); ?>" class="product-list product-size">
    <input type="hidden" name="action" value="ADD2BASKET">
    <input type="hidden" name="ajax_basket" value="Y">

    <div class="container">
        <table class="attributes">
            <tr>
                <td>
                    <div class="size-selector size-selector--wrap">
                        <? foreach ($arResult['SIZES'] as $arSize): ?>
                            <?
                            $iOfferID = intval($arSize['OFFER_ID']);
                            $bCanBuy = $iOfferID > 0 && $arSize['CATALOG_AVAILABLE'] == 'Y' && \Likee\Site\Helpers\Catalog::productCanBuy($iOfferID, $arSize['STORES']);
                            $ID = 'buy_modal_size_' . $iOfferID;
                            ?>
                            <input id="<?= $ID; ?>" type="radio" name="id" value="<?= $iOfferID; ?>"<?= !$bCanBuy ? ' disabled' : ''; ?>>
                            <label for="<?= $ID; ?>"><?= $arSize['VALUE'] ?></label>
                        <? endforeach; ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="container">
        <button type="submit" class="buttonFastBuy">ок</button>
    </div>
</form>