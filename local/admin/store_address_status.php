<?php

$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../../");
sleep(1);
if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/update_store_address.tmp')) {
    $data = unserialize(file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/update_store_address.tmp'));
    if ($data['count'] == 0) {
        deleteTmpFile();
    }
}
header('Content-Type: application/json');
echo json_encode($data);
function deleteTmpFile()
{
    unlink($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/update_store_address.tmp');
}
