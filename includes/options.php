<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$respectOptions = [];
$respectOptions['MAIN_SLIDER_SPEED'] = COption::GetOptionInt("likee", "main_slider_speed", 10);

?>
<script type="text/javascript" data-skip-moving="true">
window.RESPECT_OPTIONS = <?=CUtil::PhpToJSObject($respectOptions)?>;
</script>