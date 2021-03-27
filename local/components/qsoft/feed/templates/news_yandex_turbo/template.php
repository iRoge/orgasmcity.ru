<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
$siteUrl = 'https://respect-shoes.ru';
unset($arResult['FEED_SETTINGS']);
?>
<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss xmlns:yandex="http://news.yandex.ru" xmlns:media="http://search.yahoo.com/mrss/"
     xmlns:turbo="http://turbo.yandex.ru" version="2.0">
    <channel>
        <title>Новости</title>
        <link><?= $siteUrl ?>/</link>
        <description>Новости обувной компании Respect</description>
        <yandex:related type="infinity">
            <? foreach ($arResult as $arNews) : ?>
                <link url="<?= $siteUrl . $arNews['DETAIL_PAGE_URL']; ?>"><?= $arNews['NAME']; ?>></link>
            <? endforeach; ?>
        </yandex:related>
        <? foreach ($arResult as $arNews) : ?>
            <item turbo="true">
                <link><?= $siteUrl . $arNews['DETAIL_PAGE_URL']; ?></link>
                <turbo:content>
                    <![CDATA[
                    <header>
                        <h1><?= $arNews['NAME']; ?></h1>
                        <figure>
                            <img src="<?= $siteUrl . $arNews['PREVIEW_PICTURE']; ?>">
                        </figure>
                    </header>
                    <pubDate><?= date('r', date_timestamp_get($arNews['DATE_SORT'])); ?></pubDate>
                    <? if (!empty($arNews['IS_LOOKBOOK'])) :
                        foreach ($arNews['SLIDES'] as $arSlideInfo) :
                            foreach (['LEFT', 'RIGHT'] as $page) :
                                if (!empty($arSlideInfo[$page]['IMG'])) :
                                    if (!empty($arSlideInfo[$page]['LINK'])) :
                                        ?><a href="<?= mb_strpos($arSlideInfo[$page]['LINK'], '://') ? $arSlideInfo[$page]['LINK'] : $siteUrl . $arSlideInfo[$page]['LINK']; ?>">
                                        <img src="<?= $siteUrl . $arSlideInfo[$page]['IMG']; ?>">
                                        </a><?
                                    else :
                                        ?><img src="<?= $siteUrl . $arSlideInfo[$page]['IMG']; ?>"><?
                                    endif; ?>
                                <? endif; ?>
                            <? endforeach; ?>
                            <? if (!empty($arSlideInfo['DETAIL_TEXT'])) : ?>
                                <?= $arSlideInfo['DETAIL_TEXT']; ?>
                            <? endif; ?>
                        <? endforeach; ?>
                    <? else : ?>
                        <? if ($arNews['PROPERTY_PICTURE_POSITION_VALUE'] == 'Детальная картинка над текстом' && !empty($arNews['DETAIL_PICTURE'])) : ?>
                            <? if (!empty($arNews['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                                <a href="<?= mb_strpos($arNews['PROPERTY_PHOTO_LINK_VALUE'], '://') ? $arNews['PROPERTY_PHOTO_LINK_VALUE'] : $siteUrl . $arNews['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                                    <img src="<?= $siteUrl . $arNews['DETAIL_PICTURE']; ?>">
                                </a>
                            <? else : ?>
                                <img src="<?= $siteUrl . $arNews['DETAIL_PICTURE']; ?>">
                            <? endif; ?>
                        <? endif ?>
                        <?= $arNews['DETAIL_TEXT']; ?>
                        <? if ($arNews['PROPERTY_PICTURE_POSITION_VALUE'] == 'Детальная картинка под текстом' && !empty($arNews['DETAIL_PICTURE'])) : ?>
                            <? if (!empty($arNews['PROPERTY_PHOTO_LINK_VALUE'])) : ?>
                                <a href="<?= mb_strpos($arNews['PROPERTY_PHOTO_LINK_VALUE'], '://') ? $arNews['PROPERTY_PHOTO_LINK_VALUE'] : $siteUrl . $arNews['PROPERTY_PHOTO_LINK_VALUE']; ?>">
                                    <img src="<?= $siteUrl . $arNews['DETAIL_PICTURE']; ?>">
                                </a>
                            <? else : ?>
                                <img src="<?= $siteUrl . $arNews['DETAIL_PICTURE']; ?>">
                            <? endif; ?>
                        <? endif; ?>
                    <? endif; ?>
                    ]]>
                </turbo:content>
            </item>
        <? endforeach; ?>
    </channel>
</rss>
