<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
global $LOCATION;
?>
<ul class="from-ul">
    <li class="from-ul-li"><?= $LOCATION->getRegion(true) ?></li>
</ul>
