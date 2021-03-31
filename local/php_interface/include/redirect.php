<?
$currentPage = $APPLICATION->GetCurPage();

if ($currentPage == '/press_center/news/') {
    LocalRedirect('/events/news/', false, '301 Moved permanently');
}
if ($currentPage == '/press_center/') {
    LocalRedirect('/events/news/', false, '301 Moved permanently');
}
if ($currentPage == '/actions/') {
    LocalRedirect('/events/actions/', false, '301 Moved permanently');
}
