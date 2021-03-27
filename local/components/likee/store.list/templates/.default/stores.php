<?php
/** @var CBitrixComponent $component */
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
?>
<div class="shop-cards shop-block">
    <div class="shop-block__wrapper">
    <? if (!empty($arResult['STORES'])) : ?>
        <? foreach ($arResult['STORES'] as $pid => $arStore) : ?>
            <div class="in-magaz">
                <div class="col-xs-12" style="padding: 0!important;">
                    <? if (!empty($arStore['BELONG'])) : ?>
                        <div class="bonus-prog"><? print ('r' == $arStore['BELONG'] ? 'Бонусная' : 'Дисконтная'); ?><br/>программа
                        </div>
                    <? endif; ?>
                    <a href="<?= $arStore['URL']; ?>" title="<?= $arStore['TITLE']; ?>">
                        <img src="<?= $arStore['PICTURE']['SRC']; ?>" alt="<?= $arStore['TITLE']; ?>" class="col-xs-12"/>
                    </a>
                </div>
                <div class="text-in-magaz col-xs-12">
                    <a href="<?= $arStore['URL']; ?>" title="<?= $arStore['TITLE']; ?>">
                        <h4 class="text-in-magaz-title"><?= $arStore['TITLE']; ?></h4>
                    </a>
                    <? if (!empty($arStore['ADDRESS'])) :
                        ?><p><?= $arStore['ADDRESS']; ?></p><br/><?
                    endif; ?>
                    <? if (!empty($arStore['SCHEDULE'])) :
                        ?><p><img
                            src="<?= SITE_TEMPLATE_PATH ?>/img/time.png"/> <?= $arStore['SCHEDULE']; ?></p><?
                    endif; ?>
                    <?php foreach ((array) $arStore['PHONES'] as $phone) : ?>
                        <a href="tel:<?= $phone ?>">
                            <img src="<?= SITE_TEMPLATE_PATH ?>/img/call-answer.png" />
                            <?= $phone ?>
                        </a>
                        <br>
                    <?php endforeach; ?>
                    <?php if ($arStore['RESERV']) : ?>
                    <a style="border: 0px; height: auto" href="/catalog/?set_filter=Y&storages_availability=<?= $arStore['ID'] ?>" class="button button--third button--small">Ассортимент магазина</a>
                    <?php endif; ?>
                </div>
            </div>
        <? endforeach ?>
    </div>
    <? else : ?>
    <div class="container column-center text--center" style="padding: 25px 0 45px; font-size: 1.35rem;">
        <p>Магазины не найдены!</p>
    </div>
    <? endif; ?>
</div>
