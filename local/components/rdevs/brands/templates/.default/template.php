<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? foreach ($arResult["BRANDS"] as $id => $brand) { ?>
    <span style="font-size: 14pt;"><a href="<?= $brand['SECTION_PAGE_URL'] ?>"><?= $brand['NAME'] ?></a><br>
    </span><br>
<? } ?>

