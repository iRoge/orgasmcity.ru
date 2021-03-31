<?
if (!defined('ERROR_404')) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');
}

CHTTP::SetStatus('404 Not Found');
@define('ERROR_404', 'Y');
@define('HIDE_TITLE', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('404 ошибка: Страница не найдена - интернет-магазин Respect Yourself');
global $LOCATION;
?>
    <div class="page-error-404">
        <div class="error-block-404">
            <div class="error-block__title">Ошибка 404</div>
            <div class="error-block__subtitle">Мы не можем найти то, что вы ищете.</div>
        </div>
    </div>

<?
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');