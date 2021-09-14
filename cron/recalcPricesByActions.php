<?php
include("config.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
while (ob_get_level()) {
    ob_end_flush();
}
set_time_limit(0);
ini_set('memory_limit', '2048M');
\Qsoft\Helpers\PriceUtils::recalcPrices();
