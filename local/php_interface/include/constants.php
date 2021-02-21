<?
define('DOMEN_NAME', 'respect-shoes.ru');
define('BX_DISABLE_INDEX_PAGE', true);
define('IBLOCK_CATALOG', 16);
define('IBLOCK_OFFERS', 17);
define('IBLOCK_GROUPS', 24);
define('IBLOCK_TAGS', 30);
define('HL_ARTICLES_SKU', 217);
define('IBLOCK_FEEDS', 'FEEDS_CONFIG_NEW');
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/local/logs/log.log');
define('BUY_IN_CREDIT', false); //не забудьте активировать платежную систему
define('ONLINE_STORE_ID', 209); //особенный склад, он не показывается в списке товаров, для него не доступен самовывоз
define('OFFER_FILENAME', '/upload/files/offer.pdf'); // Путь к файлу с политикой конфиденциальности
define('LOCATION_TYPE_CITY', 5); //Тип местоположения - город
define('LOCATION_TYPE_REGION', 3); //Тип местоположения - регион
define('ONLINE_PAYMENT_CODES', array('SBERBANK')); //Массив кодов платежных систем для оплаты онлайн
define('HOST_USE_TP', true);