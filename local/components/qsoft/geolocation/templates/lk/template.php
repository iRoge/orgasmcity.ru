<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;

$location = $arResult['LOCATION_NAME'] == $LOCATION->getRegion() ? $arResult['LOCATION_NAME'] : $arResult['LOCATION_NAME'] . ', ' . $LOCATION->getRegion();
?>

<span class="col-xs-12 js-profile-input-lk user-region from-ul-li"><?= $location; ?></span>
