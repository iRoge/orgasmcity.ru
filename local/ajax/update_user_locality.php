<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $LOCATION;

if (!$_POST['location_code']) {
    exit(json_encode(
        array(
            'status' => 'fail',
        )
    ));
}

if ($_POST['location_code'] === 'auto') {
    $res = $LOCATION->locateUserByIP(true);
    $res['status'] = 'success';
    exit(json_encode($res, JSON_UNESCAPED_UNICODE));
}

if ($LOCATION->checkLocationCode($_POST['location_code'])) {
    exit(json_encode(
        array(
            'status' => 'success',
        )
    ));
}

exit(json_encode(
    array(
        'status' => 'fail',
    )
));
