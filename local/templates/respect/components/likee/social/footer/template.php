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
        'name' => 'TELEGRAM',
        'icon' => 'telegram.png',
    ],
    [
        'name' => 'INSTAGRAM',
        'icon' => 'insta.png',
    ],
    [
        'name' => 'VK',
        'icon' => 'vk.png',
    ],
    [
        'name' => 'WHATSAPP',
        'icon' => 'whatsapp.png',
    ],
];
$i = 0;?>

<?php foreach ($icons as $value) { ?>
    <?php if (!empty($arResult[$value['name']])) { ?>
        <a target="_blank" href="<?=$arResult[$value['name']]?>">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/<?=$value['icon']?>"/>
        </a>
    <?php }?>
<?php } ?>

