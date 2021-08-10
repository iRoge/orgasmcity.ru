<?php

if ($arResult["isFormNote"] == "Y"): ?>
<div class="product-preorder-success">
	<article>
        <?/*=$arResult["FORM_NOTE"];*/?>
        Спасибо. Ваша заявка отправлена
	</article>
	<footer>
		<button class="js-popup-close button button--xxl button--primary button--outline">ОК</button>
	</footer>
</div>
<?php else: ?>
	<?=$arResult["FORM_HEADER"]?>
	<?
	foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) {
		if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
			print $arQuestion["HTML_CODE"];
		}
	}
	?>
	<?if ($arResult["isFormErrors"] == "Y"):?>
        <div class="alert alert--danger">
            <div class="alert-content">
                <i class="icon icon-exclamation-circle"></i>
                <?=$arResult["FORM_ERRORS_TEXT"];?>
            </div>
        </div>
    <?endif;?>

    <?
    if(CModule::IncludeModule("iblock")) {

        $obCIBlockElement = new CIBlockElement();
        $obCIBlockSection = new CIBlockSection();

        $tender = [];
        $senderArr = [];


        // Выбираем тендер по ID
        $arSortTender = [
            "SORT" => "ASC"
        ];
        $arFilterTender = [
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ID" => $arParams["TENDER_ID"],
            "ACTIVE_DATE" => "Y",
            "ACTIVE" => "Y"
        ];
        $arSelectTender = [
            "ID",
            "NAME",
            "PROPERTY_EMAIL",
            "IBLOCK_SECTION_ID"
        ];


        $resTender = $obCIBlockElement->GetList(
            $arSortTender,
            $arFilterTender,
            false,
            ["nTopCount" => 1],
            $arSelectTender
        );
        if ($ar_resTender = $resTender->GetNext()){
            $tender = $ar_resTender;
            $senderArr[] = $ar_resTender['PROPERTY_EMAIL_VALUE'];
        }


        // Выбираем категорию тендера (для определения Email группы)
        $arSortSection = [
            "SORT" => "ASC"
        ];
        $arFilterSection = [
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "ID" => $tender['IBLOCK_SECTION_ID'],
            "ACTIVE" => "Y"
        ];
        $arSelectSection = [
            "ID",
            "NAME",
            "UF_*"
        ];

        $resSection = $obCIBlockSection->GetList(
            $arSortSection,
            $arFilterSection,
            false,
            $arSelectSection
        );
        while ($ar_resSection = $resSection->GetNext()){
            $tender['SECTION'][$ar_resSection["ID"]] = $ar_resSection;
            $senderArr[] = $ar_resSection['UF_EMAIL'];
        }
    }

    
    foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) : 
        $label = $arQuestion["CAPTION"].($arQuestion["REQUIRED"] == "Y" ? ' *' : '');
    ?>
        <?if(in_array($arQuestion["STRUCTURE"][0]["FIELD_ID"], [11, 12, 13])):?>
            <div style="display:none;">
                <?if($arQuestion["STRUCTURE"][0]["FIELD_ID"] == 11):?>
                    <input type="text" class="form-control" name="form_text_<?=$arQuestion["STRUCTURE"][0]["ID"]?>" value="<?=join(',', $senderArr)?>" >
                <?elseif($arQuestion["STRUCTURE"][0]["FIELD_ID"] == 12):?>
                    <input type="text" class="form-control" name="form_text_<?=$arQuestion["STRUCTURE"][0]["ID"]?>" value="<?=$tender['NAME']?>" >
                <?elseif($arQuestion["STRUCTURE"][0]["FIELD_ID"] == 13):?>
                    <input type="text" class="form-control" name="form_text_<?=$arQuestion["STRUCTURE"][0]["ID"]?>" value="<?=$tender['ID']?>" >
                <?endif;?>
            </div>
        <?else:?>
            <div class="input-group">
                <?if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'text' || $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'email'):?>
                    <?=str_replace(array('inputtextarea"', 'inputtext"'), 'form-control" placeholder="'.$label.'"', $arQuestion["HTML_CODE"]); ?>
                <?elseif ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea'): ?>
                    <?=str_replace(array('inputtextarea"', 'inputtext"'), 'form-control" placeholder="'.$label.'"', $arQuestion["HTML_CODE"]); ?>
                <?else:?>
                    <label><?=$arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif;?></label>
                    <?=$arQuestion["HTML_CODE"]?>
                <?endif;?>
            </div>
        <?endif;?>
	<?endforeach;?>
    <hr />
	<div class="alert">
		<input <?=(intval($arResult["F_RIGHT"]) < 10 ? "disabled=\"disabled\"" : "");?> type="submit" class="button button--xxl button--primary button--outline" name="web_form_submit" value="<?=htmlspecialcharsbx(strlen(trim($arResult["arForm"]["BUTTON"])) <= 0 ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>" />
	</div>
    <?=$arResult["FORM_FOOTER"]?>
<?php endif; ?>