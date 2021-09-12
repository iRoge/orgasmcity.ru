<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;
$region = $LOCATION->getRegion();
$city = $LOCATION->getName();
if ($region != $city) {
    $city = $city.", ".$region;
} ?>
<div class="form__field form__field--1-2">
    <div class="form__elem cart-city-input"><?=$city?></div>
</div>
