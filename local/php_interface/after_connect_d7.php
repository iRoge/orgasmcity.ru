<?php
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET sql_mode=''");
$connection->queryExecute("SET innodb_strict_mode=0");