<div class="lis js-lis-section">
    <!--RestartBuffer-->
    <div class="lis__list container">
        <? foreach ($arResult['ITEMS'] as $arMedia) : ?>
        <div class="column-25 column-md-1 column-xs-1">
            <div class="lis__media" style="background-image:url('<?= $arMedia['IMG'] ?>')" data-img="<?= $arMedia['IMG'] ?>">
                <div class="lis__media-info">
                    <div class="lis__media-account">@<?= $arResult['USER'] ?></div>
                    <div class="lis__media-value-wrapper">
                        <div class="lis__media-value lis__media-value--likes"><?= $arMedia['LIKES_COUNT'] ?></div>
                        <div class="lis__media-value lis__media-value--comment"><?= $arMedia['COMMENTS_COUNT'] ?></div>
                    </div>
                    <div class="lis__media-value lis__media-value--buy"></div>
                </div>
                <div class="lis__media-full hidden">
                    <? foreach ($arMedia['ITEMS'] as $arItem) : ?>
                        <? if ($arItem['NAME']) : ?>
                            <div class="lis-full__item">
                                <div class="lis-full__name"><?= $arItem['NAME'] ?></div>
                                <a class="lis-full__button button button--outline" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">Подробнее</a>
                            </div>
                        <? endif; ?>
                    <? endforeach; ?>
                    <div class="lis-full__caption"><?= nl2br($arMedia['CAPTION']) ?></div>
                </div>
            </div>
        </div>
        <? endforeach; ?>
    </div>
    <? if (!empty($arResult['NEXT_PAGE'])): ?>
        <div class="container show-more js-show-more-box">
            <div class="column-8 pre-1">
                <div class="container show-more">
                    <div class="column-8 pre-1">
                        <a href="<?= $APPLICATION->GetCurPageParam('PAGEN_1='.$arResult['NEXT_PAGE'], ['PAGEN_1', 'load_more']); ?>"
                            class="button button--xl button--transparent button--block js-load-more-btn load-more-btn">
                                Показать еще
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <? else: ?>
        <div class="spacer--3"></div>
    <? endif; ?>
    <!--RestartBuffer-->
</div>