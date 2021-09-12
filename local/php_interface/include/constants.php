<?php
const DOMAIN_NAME = 'orgasmcity.ru';
const BX_DISABLE_INDEX_PAGE = true;

const DEFAULT_KEYWORDS = 'купить секс игрушки в москве, секс шоп, москва, магазин эротики, интим товары, интим магазин, город, оргазм, город оргазма';

// ID инфоблоков
const IBLOCK_CATALOG = 5;
const IBLOCK_OFFERS = 6;
const IBLOCK_VENDORS = 4;
const IBLOCK_SUBSCRIBERS = 60;
const IBLOCK_MAILINGS = 61;
const IBLOCK_MINI_BANNERS = 22;
const IBLOCK_GROUPS = 64;
const IBLOCK_BLOG = 66;
const IBLOCK_FEEDBACK = 79;

const MAIN_SECTION_ID = 574;

const HL_ARTICLES_SKU = 217;
define('LOG_FILENAME', $_SERVER['DOCUMENT_ROOT'] . '/local/logs/log.log');
const OFFER_FILENAME = '/upload/files/offer.pdf'; // Путь к файлу с политикой конфиденциальности

// ID типов местоположений
const LOCATION_TYPE_CITY = 5; //Тип местоположения - город
const LOCATION_TYPE_REGION = 3; //Тип местоположения - регион

const MOSCOW_SELF_DELIVERY_ID = 2; // ID доставки самовывоза в Мск

const ONLINE_PAYMENT_CODES = ['TINKOFF']; //Массив кодов платежных систем для оплаты онлайн

// Пароли и ключи доступа к сервисам
const DADATA_TOKEN = '5d7f5ca931710f1ee0da45fa0627f84378149d4b'; // Token dadata
const DADATA_SECRET_TOKEN = 'fff4fdba3305081a999a5334e73ed7b00a11e9e0'; // Secret token dadata
const DADATA_MAX_REQUESTS = 10000; // Максимальное количество запросов в дадату в день

// Телефон поддержки
const SUPPORT_PHONE = '+7 (495) 197-78-69';

// Размеры картинок в каталоге (большая и маленькая)
const CATALOG_SMALL_IMG_HEIGHT = 300;
const CATALOG_BIG_IMG_HEIGHT = 600;

// Тинькофф эквайринг
const TINKOFF_TERMINAL_ID = '1625037822712';
const TINKOFF_TERMINAL_PASSWORD = '45l8bavs8jlhrq80';