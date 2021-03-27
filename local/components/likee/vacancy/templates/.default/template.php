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
/** @var LikeeVacancyComponent $component */
$this->setFrameMode(true);
#$
?>
<div class="clearfix vacancy-block">
    <div class="column-6 column-center column-md-2">
        <div class="container--gutters">
            <? if (!empty($arResult['ITEMS'])): ?>
                <? $iCount = 0; ?>
                <? foreach (array_chunk($arResult['ITEMS'], 2) as $iKey => $arItems): ?>
                    <div class="column-5">
                        <? foreach ($arItems as $arItem): ?>
                            <?
                            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
                            ?>
                            <section class="vacancy vacancy--dropdown" id="<?= $arItem['CONTROL_ID'] ?>">
                                <header>
                                    <div class="vacancy__header-content">
                                        <div class="vacancy__title">
                                            <?= $arItem['NAME'] ?>
                                        </div>
                                        <div class="vacancy__subtitle">
                                            <? if (!empty($arItem['SALARY_FROM']['VALUE'])): ?>
                                                от <?= \CCurrencyLang::CurrencyFormat($arItem['SALARY_FROM']['VALUE'], 'RUB') ?>
                                            <? endif; ?>
                                            <? if (!empty($arItem['SALARY_TO']['VALUE'])): ?>
                                                до <?= \CCurrencyLang::CurrencyFormat($arItem['SALARY_TO']['VALUE'], 'RUB') ?>
                                            <? endif; ?>
                                        </div>
                                    </div>
                                </header>
                                <article>
                                    <dl>
                                        <? if ($arItem['DUTIES']['VALUE']['~TEXT']): ?>
                                            <dt><?= $arItem['DUTIES']['NAME'] ?>:</dt>
                                            <dd><?= $arItem['DUTIES']['VALUE']['~TEXT'] ?></dd>
                                        <? endif; ?>

                                        <? if ($arItem['REQUIREMENTS']['VALUE']['~TEXT']): ?>
                                            <dt><?= $arItem['REQUIREMENTS']['NAME'] ?>:</dt>
                                            <dd><?= $arItem['REQUIREMENTS']['VALUE']['~TEXT'] ?></dd>
                                        <? endif; ?>

                                        <? if ($arItem['CONDITIONS']['VALUE']['~TEXT']): ?>
                                            <dt><?= $arItem['CONDITIONS']['NAME'] ?>:</dt>
                                            <dd><?= $arItem['CONDITIONS']['VALUE']['~TEXT'] ?></dd>
                                        <? endif; ?>

                                        <? if ($arItem['TYPE']['VALUE']['~TEXT']): ?>
                                            <dt><?= $arItem['TYPE']['NAME'] ?>:</dt>
                                            <dd><?= $arItem['TYPE']['VALUE']['~TEXT'] ?></dd>
                                        <? endif; ?>
                                    </dl>
                                </article>
                            </section>
                        <? endforeach; ?>
                    </div>
                <? endforeach; ?>
            <? endif; ?>
        </div>
    </div>
</div>
<div class="spacer--3"></div>
