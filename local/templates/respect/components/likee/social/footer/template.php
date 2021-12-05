<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/** @var array $arResult */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var LikeeSocialComponent $component */
$this->setFrameMode(true);
$icons = [
    [
        'name' => 'INSTAGRAM',
        'icon' => 'Instagram.svg',
        'color' => '#FF8D74'
    ],
    [
        'name' => 'WhatsApp',
        'icon' => 'Whatsapp.svg',
        'color' => '#BDFBA0'
    ],
    [
        'name' => 'TELEGRAM',
        'icon' => 'Telegram.svg',
        'color' => '#B3E6FF'
    ],
    [
        'name' => 'VK.COM',
        'icon' => 'Vk.svg',
        'color' => '#0194FF'
    ],
    [
        'name' => 'Spotify',
        'icon' => 'Spotifi.svg',
        'color' => '#63B861'
    ],
    [
        'name' => 'FACEBOOK',
        'icon' => 'Facebook.svg',
        'color' => '#1877F2'
    ],
    [
        'name' => 'TWITTER',
        'icon' => 'Twitter.svg',
        'color' => '#1d9bf0'
    ],
];
?>

<?php foreach ($icons as $value) { ?>
    <?php if (!empty($arResult[$value['name']])) { ?>
        <a class="footer-element social-element" target="_blank" href="<?=$arResult[$value['name']]?>">
            <img width="24" height="24" src="<?=SITE_TEMPLATE_PATH?>/img/svg/<?=$value['icon']?>"/>
            <span class="social-element-text" style="color: <?=$value['color']?>"><?=$value['name']?></span>
        </a>
    <?php }?>
<?php } ?>

