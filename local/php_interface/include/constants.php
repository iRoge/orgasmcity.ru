<?
define('DOMEN_NAME', 'hookahmove.ru');
define('BX_DISABLE_INDEX_PAGE', true);

// ID инфоблоков
define('IBLOCK_CATALOG', 5);
define('IBLOCK_OFFERS', 6);
define('IBLOCK_VENDORS', 4);

define('HL_ARTICLES_SKU', 217);
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/local/logs/log.log');
define('BUY_IN_CREDIT', false); //не забудьте активировать платежную систему
define('ONLINE_STORE_ID', 209); //особенный склад, он не показывается в списке товаров, для него не доступен самовывоз
define('OFFER_FILENAME', '/upload/files/offer.pdf'); // Путь к файлу с политикой конфиденциальности

// ID типов местоположений
define('LOCATION_TYPE_CITY', 5); //Тип местоположения - город
define('LOCATION_TYPE_REGION', 3); //Тип местоположения - регион

define('ONLINE_PAYMENT_CODES', array('SBERBANK')); //Массив кодов платежных систем для оплаты онлайн
define('HOST_USE_TP', true);

// Пароли и ключи доступа к сервисам
define('DADATA_TOKEN', '5d7f5ca931710f1ee0da45fa0627f84378149d4b'); // Token dadata
define('DADATA_SECRET_TOKEN', 'fff4fdba3305081a999a5334e73ed7b00a11e9e0'); // Secret token dadata
define('DADATA_MAX_REQUESTS', 10000); // Максимальное количество запросов в дадату в день

// Телефон поддержки
define('SUPPORT_PHONE', '+7(999)999-99-99'); // Путь к файлу с политикой конфиденциальности