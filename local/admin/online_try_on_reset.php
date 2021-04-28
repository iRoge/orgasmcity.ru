<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
global $USER;
if (!$USER->IsAdmin() && !in_array(12, $USER->GetUserGroupArray())) {
    $APPLICATION->AuthForm('Доступ запрещен');
}
$APPLICATION->SetTitle('Сброс свойства «Онлайн примерочная»');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

use Bitrix\Main\Application;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\DB\Exception as DatabaseException;

if ($_POST['reset']) {
    try {
        if (!$_POST['confirm']) {
            throw new Exception('Вы не согласились с предупреждением.');
        }
        
        if (!defined('IBLOCK_CATALOG')) {
            throw new Exception('Не определён ID инфоблока "Каталог товаров".');
        }

        $onlineTryOnProperty = PropertyTable::getList([
            'select' => ['ID'],
            'filter' => ['=IBLOCK_ID' => IBLOCK_CATALOG, '=CODE' => 'ONLINE_TRY_ON'],
            'limit' => 1,
        ]);
        $onlineTryOnPropertyID = (int) $onlineTryOnProperty->fetch()['ID'];

        if (empty($onlineTryOnPropertyID)) {
            throw new Exception('Свойство «Онлайн примерочная» (ONLINE_TRY_ON) не найдено.');
        }

        $database = Application::getConnection();
        //$database->query('DELETE FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID = ' . $onlineTryOnPropertyID);
        $database->query('UPDATE b_iblock_element_prop_s16 SET PROPERTY_' . $onlineTryOnPropertyID . ' = ""');
        $message = [
            'MESSAGE' => 'Сброс свойства «Онлайн примерочная» для всех товаров успешно произведён.',
            'TYPE' => 'OK',
        ];
    } catch (DatabaseException $e) {
        $message = [
            'MESSAGE' => 'При обращении к БД произошла ошибка:<br>' . $e->getMessage(),
            'TYPE' => 'ERROR',
        ];
    } catch (Exception $e) {
        $message = [
            'MESSAGE' => $e->getMessage(),
            'TYPE' => 'ERROR',
        ];
    }
}
?>

<form method="post">
    <?php if (!empty($message)) : ?>
        <?= CAdminMessage::ShowMessage($message); ?>
    <?php endif; ?>
    
    <div class="adm-info-message-wrap">
        <div class="adm-info-message">
            <p>
                <span style="color: red">Внимание!</span> У всех товаров будет сброшено свойство «Онлайн примерочная».
            </p>
        </div>
    </div>

    <label>
        <input type="checkbox" name="confirm" value="Y">
        Я понимаю, что будет удалено свойство «Онлайн примерочная» у всех элементов
    </label>
    <br><br>

    <input type="submit" name="reset" value="Сбросить" class="adm-btn-save">
</form>

<?php
require_once($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/include/epilog_admin.php');
