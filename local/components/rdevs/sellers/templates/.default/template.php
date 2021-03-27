<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<div class="sideSellerPanel <?= $arResult['isSeller'] ?>">
    <div class="sellerSelectSide">
        <div class="sellerSelect">
            <p>Магазин
                <br>
                <b><?= $arResult['store']['TITLE'] ?>
                    <br>
                    <?= $arResult['store']['UF_FILIAL'] ?>
                </b>
            </p>
            <p>Текущий продавец
                <br>
                <b class="currentSeller"><?= $arResult['currentStoreSeller']['UF_FULL_NAME'] ?: 'Выберите продавца' ?></b>
            </p>
            <br>
            <p>Выбрать другого продавца</p>
            <select class="storeSellerSelect">
                <? foreach ($arResult['storeSellers'] as $seller) { ?>
                    <option value="<?= $seller['ID'] ?>" <?= $arResult['currentStoreSeller']['ID'] == $seller['ID'] ? 'selected' : '' ?>><?= $seller['UF_FULL_NAME'] ?></option>
                <? } ?>
            </select>
        </div>
    </div>
    <div class="sellerPlane <?= $arResult['isSeller'] ?>">
        <p class="currentStoreSeller"><?= $arResult['currentStoreSeller']['SHORT_NAME'] ?: 'Выберите продавца' ?></p>
    </div>
</div>
