<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetTitle('Личный кабинет');

$APPLICATION->IncludeComponent(
    'orgasmcity:user.bonus.info',
    'default',
    []
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
