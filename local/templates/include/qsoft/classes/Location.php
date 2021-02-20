<?

namespace Qsoft;

use Bitrix\Catalog\StoreTable;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\DB\Exception as DatabaseException;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location\LocationTable;
use CModule;
use CIBlockElement;
use CIBlockSection;
use CPHPCache;
use Bitrix\Iblock\InheritedProperty\SectionValues;
use Exception;

class Location
{
    // константы
    private const LOCATION_TYPE = 5;
    private const ALL_USER = 0;
    private const PLATINUM_USER = 1;
    private const GOLDEN_USER = 2;
    private const SILVER_USER = 3;
    private const BRONZE_USER = 4;
    // значения по умолчанию
    public $DEFAULT_BRANCH; // страховочная цена
    private $DEFAULT_ABS_BRANCH; // безусловная цена
    private $DEFAULT_LOCATION; // местоположение
    public $DEFAULT_STORAGES; // склады
    public $arAvStorages;
    public $DONORS_TARGETS; // местоположения к которым добавлять остатки из донорских складов в случае отсутствия таковых
    // код местоположения из битрикса
    public $code = false;
    public $exepRegionFlag = false;
    /*
     * Тип пользователя
     * 0 - не известен
     * 1 - платина
     * 2 - золото
     * 3 - серебро
     * 4 - бронза
     */
    public $userType;
    // флаги типа цены для последнего получения
    public $priceFlags = array(0, 0, 0, 0);
    public $isStranger = false; // флаг нового пользователя

    private $DEFAULT_DISCOUNT;
    // название местоположения (город)
    private $name = false;
    // регион
    private $region = false;
    // страна
    private $country = false;
    // ID филиала
    private $branch = false;
    // склады
    private $storages = array();
    // склады с сортировкой
    public $arStorages = array();
    // подключение к БД
    private $connection = false;
    /**
     * @var CPHPCache
     */
    private $cache;
    // телефон, привязанный к месту
    public $phone = false;
    //правила скидок
    private $rules;
    // массив ценовых акций
    private $arShares;
    // дата последней начавшейся или закончившейся акция
    private $lastStatusChangedShare;
    // свойства продуктов
    private $productProperties;
    // имя хоста
    public $hostName = false;
    // при создании экземпляра класса

    public function __construct()
    {
        Loader::includeModule("sale");
        Loader::includeModule("catalog");
        $this->setDefault();
        $this->initCache();
        $this->getHostName();
        $this->getLocationCode();
    }

    private function getHostName()
    {
        $arCurrentHost = array_reverse(explode('.', $_SERVER['HTTP_HOST']));
        if (strripos($_SERVER['HTTP_HOST'], '.qsoft.ru')) {
            $this->hostName = (HOST_USE_TP ? $arCurrentHost[4] . '.' : '') . $arCurrentHost[3] . '.' . $arCurrentHost[2] . '.' . $arCurrentHost[1] . '.' . $arCurrentHost[0];
            return;
        }
        $this->hostName = (HOST_USE_TP ? $arCurrentHost[2] . '.' : '') . $arCurrentHost[1] . '.' . $arCurrentHost[0];
    }

    private function setDefault()
    {
        $this->DEFAULT_BRANCH = Option::get("respect", "default_branch", 36);
        $this->DEFAULT_ABS_BRANCH = Option::get("respect", "default_abs_branch", 37);
        $this->DEFAULT_DISCOUNT = Option::get("respect", "default_discount", 10);
        try {
            $temp = json_decode(Option::get("respect", "default_location"), JSON_OBJECT_AS_ARRAY);
            if (!empty($temp)) {
                $this->DEFAULT_LOCATION = $temp;
            } else {
                $this->DEFAULT_LOCATION = array(
                    "ID" => 129,
                    "CODE" => "0000073738",
                    "NAME_RU" => "Москва",
                );
            }
        } catch (\Exception $e) {
            $this->DEFAULT_LOCATION = array(
                "ID" => 129,
                "CODE" => "0000073738",
                "NAME_RU" => "Москва",
            );
        }
        try {
            $temp = json_decode(Option::get("respect", "default_storages"), JSON_OBJECT_AS_ARRAY);
            if (!empty($temp)) {
                $this->DEFAULT_STORAGES = $temp;
            } else {
                // склад: интернет-магазин, доставка - да, резерв - нет
                $this->DEFAULT_STORAGES = array(
                    209 => array(1, 0),
                );
            }
        } catch (\Exception $e) {
            // склад: интернет-магазин, доставка - да, резерв - нет
            $this->DEFAULT_STORAGES = array(
                209 => array(1, 0),
            );
        }
        try {
            $temp = json_decode(Option::get("respect", "donors_targets"), JSON_OBJECT_AS_ARRAY);
            if (!empty($temp)) {
                $this->DONORS_TARGETS = $temp;
            } else {
                $this->DONORS_TARGETS = array();
            }
        } catch (\Exception $e) {
            $this->DONORS_TARGETS = array();
        }
    }

    private function getLocationCode()
    {
        // если значение уже установлено, то ничего не делаем
        if ($this->code) {
            return;
        }
        //ищем местоположение по поддомену
        $locationCode = $this->getPoddomen('code');
        if ($locationCode) {
            if ($this->checkLocationCode($locationCode)) {
                return;
            }
        }
        // храним код в сессии
        if (isset($_SESSION["BRANCH"]["LOCATION_CODE"])) {
            if ($this->checkLocationCode($_SESSION["BRANCH"]["LOCATION_CODE"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // наша новая кука
        if (isset($_COOKIE["LOCATION_CODE"])) {
            if ($this->checkLocationCode($_COOKIE["LOCATION_CODE"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // если пользователь не узнан, устанавливаем флаг
        $this->isStranger = true;
        // оставлено для обратной совместимости со старым функционалом
        if (isset($_COOKIE["CURRENT_LOCATION_ID"])) {
            if ($this->checkLocationId($_COOKIE["CURRENT_LOCATION_ID"])) {
                $this->setExeptionRegionIfDefault();
                return;
            }
        }
        // если ничего нет, определяем по IP
        $this->locateUserByIP();
        $this->setExeptionRegionIfDefault();
    }
    //перенаправляет на страницу с поддоменом, если нашли у юзера местоположение с поддоменом
    private function addPoddomenLocation($locationCode)
    {
        if ($this->getPoddomen('check')) {
            return false;
        }
        $poddomen = $this->checkPoddomenCode($locationCode);
        if ($poddomen) {
            header('Refresh:0; url=' . $_SERVER['REQUEST_SCHEME'] . '://'. $poddomen . '.' . $this->hostName . $_SERVER['REQUEST_URI']);
            return false;
        }
        //если не знаем куда направить, но поддомен введен, отправляем на основную
        if ($this->getPoddomen('failCheck')) {
            header('Refresh:0; url=' . $_SERVER['REQUEST_SCHEME'] . '://' . $this->hostName . $_SERVER['REQUEST_URI']);
        }
        return false;
    }
    // получаем и устанавливаем значения по умолчанию
    public function checkLocationCode($code)
    {
        $code = htmlspecialchars($code);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "CODE" => $code,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            return $this->setLocationCode($res);
        }
        return false;
    }

    // устанавливаем код местоположения для экземпляра класса

    private function setLocationCode($arFields)
    {
        $this->addPoddomenLocation($arFields["CODE"]); //перенаправляет на поддомен, если он есть для выбранного местоположения
        $_SESSION["BRANCH"]["LOCATION_CODE"] = $arFields["CODE"];
        if (!$_COOKIE["LOCATION_CODE"] || $_COOKIE["LOCATION_CODE"] != $arFields["CODE"]) {
            // устанавливаем на 30 дней
            setcookie("LOCATION_CODE", $arFields["CODE"], time() - 2592000, "/");
            setcookie("LOCATION_CODE", $arFields["CODE"], time() + 2592000, "/", $this->getCurrentHost());
        }

        // устанавливаем старую куку, чтобы не отвалилась часть функционала, завязанная на ID с неё
        // когда всё переделаем, можно будет убрать
        if (!$_COOKIE["CURRENT_LOCATION_ID"] || $_COOKIE["CURRENT_LOCATION_ID"] != $arFields["ID"]) {
            // устанавливаем на 30 дней
            setcookie("CURRENT_LOCATION_ID", $arFields["ID"], time() - 2592000, "/");
            setcookie("CURRENT_LOCATION_ID", $arFields["ID"], time() + 2592000, "/", $this->getCurrentHost());
        }
        $this->code = $arFields["CODE"];
        $this->name = $arFields["NAME_RU"];
        return true;
    }

    public function getCurrentHost()
    {
        $arCurrentHost = array_reverse(explode('.', $_SERVER['HTTP_HOST']));
        return $arCurrentHost[1] . '.' . $arCurrentHost[0];
    }

    private function checkLocationId($id)
    {
        $id = intval($id);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "ID" => $id,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            return $this->setLocationCode($res);
        }
        return false;
    }

    // получение местоположения по IP

    public function locateUserByIP($return = false)
    {
        if ($res = $this->getLocationByIp($return)) {
            return $res;
        }
        if ($return) {
            return $this->DEFAULT_LOCATION;
        }
        // Если не удалось определить по IP, устанавливаем местоположение по умолчанию
        $this->setLocationCode($this->DEFAULT_LOCATION);
    }

    // получаем IP из всех возможных заголовков

    private function getLocationByIp($return)
    {
        $ip = $this->getIp();
        if (!$ip) {
            return false;
        }
        @mb_internal_encoding("8bit");
        require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/tools/SxGeo.php");
        $SxGeo = new \SxGeo($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/tools/SxGeoCity.dat");
        $city = $SxGeo->getCity($ip);
        @mb_internal_encoding("UTF-8");
        $city = $city["city"]["name_ru"];
        if ($city) {
            return $this->checkLocationName($city, $return);
        }
        return false;
    }

    // проверка местоположения по названию

    private function getIp()
    {
        $headers = array("HTTP_X_REAL_IP", "HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "REMOTE_ADDR");
        foreach ($headers as $header) {
            $ip = @$_SERVER[$header];
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
        return false;
    }

    // проверка местоположения по id

    private function checkLocationName($name, $return)
    {
        $name = htmlspecialchars($name);
        $res = LocationTable::GetList(array(
            "select" => array(
                "ID",
                "CODE",
                "NAME_RU" => "NAME.NAME",
            ),
            "filter" => array(
                "NAME.NAME" => $name,
                "TYPE_ID" => self::LOCATION_TYPE,
                "NAME.LANGUAGE_ID" => "ru",
            ),
        ))->Fetch();
        if (!empty($res)) {
            if ($return) {
                return $res;
            } else {
                return $this->setLocationCode($res);
            }
        }
        return false;
    }

    // проверка местоположения по коду

    private function getUserType()
    {
        global $USER;
        if (!$USER->IsAuthorized()) {
            $this->userType = self::BRONZE_USER;

            return;
        }

        $dbUser = $USER->GetList(
            ($by = 'ID'),
            ($order = 'ASC'),
            [
                'ID' => $USER->GetID(),
            ],
            [
                'SELECT' => ['UF_SAILPLAY_STATUS'],
            ]
        );

        if ($res = $dbUser->Fetch()) {
            $type = $res['UF_SAILPLAY_STATUS'];
            switch ($type) {
                case 'Платиновый':
                    $this->userType = self::PLATINUM_USER;
                    break;
                case 'Золотой':
                    $this->userType = self::GOLDEN_USER;
                    break;
                case 'Серебряный':
                    $this->userType = self::SILVER_USER;
                    break;
                case 'Бронзовый':
                    $this->userType = self::BRONZE_USER;
                    break;
                default:
                    $this->userType = self::BRONZE_USER;
            }
        } else {
            $this->userType = self::BRONZE_USER;
        }
    }

    // устанавливаем код, который уже проверен, разрешен и существует

    private function initCache()
    {
        $this->cache = new CPHPCache();
    }

    public function refreshPriceCache($branches = [])
    {
        if (empty($branches)) {
            $branches = $this->getBranchList();
        }
        if (!is_array($branches)) {
            $branches = [$branches];
        }

        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag('catalogElement');
        $this->cache->Clean('product_prices_shares', 'discounts');
        if (!isset($this->lastStatusChangedShare)) {
            $this->setDateOfLastStatusChangedShare();
        }
        foreach ([self::ALL_USER, self::PLATINUM_USER, self::SILVER_USER, self::GOLDEN_USER, self::BRONZE_USER] as $user_type) {
            foreach ($branches as $branch) {
                $this->cache->Clean('product_prices|' . $branch . '|' . $user_type . '|' . $this->lastStatusChangedShare, 'discounts');
                $this->getProductPricesCache($branch, $user_type);
            }
        }
    }

    private function getBranchList()
    {
        $branches = [];
        $connection = $this->getConnection();
        $res = $connection->query('SELECT id FROM b_respect_branch');

        while ($branch = $res->fetch()) {
            $branches[] = $branch['id'];
        }

        return $branches;
    }

    // получаем название города

    private function getConnection()
    {
        if (!$this->connection) {
            $this->connection = Application::getConnection();
        }
        return $this->connection;
    }

    // получаем название региона

    private function getProductPricesCache($branch, $user_type)
    {
        if (!isset($this->lastStatusChangedShare)) {
            $this->setDateOfLastStatusChangedShare();
        }
        if ($this->cache->InitCache(86400, 'product_prices|' . $branch . '|' . $user_type . '|' . $this->lastStatusChangedShare, 'discounts')) {
            $prices = $this->cache->GetVars()['prices'];
        } elseif ($this->cache->StartDataCache()) {
            if (!isset($this->productProperties)) {
                $this->productProperties = $this->getProductProperties();
            }
            $this->loadDiscountRules();
            $prices = [];
            $connection = $this->getConnection();
            // получаеми безусловные цены
            $res = $connection->query("SELECT branch_id, product_id, price, price1, price_segment_id
            FROM b_respect_product_price
            WHERE branch_id IN (" . $this->DEFAULT_ABS_BRANCH . ", " . $branch . ", " . $this->DEFAULT_BRANCH . ")");
            while ($arItem = $res->Fetch()) {
                if (!empty($prices[$arItem['product_id']]) && $arItem['branch_id'] != $branch) {
                    continue;
                }
                $this->userType = $user_type;
                $prices[$arItem['product_id']] = $this->parsePrice($arItem, $this->productProperties[$arItem['product_id']]);
            }
            $prices = $this->modifyPricesWithShares($prices, $branch);
            if (!$prices) {
                $this->cache->AbortDataCache();
            } else {
                $this->cache->EndDataCache(['prices' => $prices]);
            }
        }

        return $prices ?? [];
    }

    // Получаем даты последней начавшейся и закончившейся акций

    private function setDateOfLastStatusChangedShare()
    {
        $date = '26.12.1991 00:00:00';
        if (empty($this->arShares)) {
            $this->arShares = $this->getShares();
        }

        foreach ($this->arShares as $share) {
            if (strtotime($share['UF_ACTIVE_FROM']) <= strtotime(date("d.m.Y H:i:s")) && strtotime($share['UF_ACTIVE_FROM']) > strtotime($date)) {
                $date = $share['UF_ACTIVE_FROM'];
            }
            if (strtotime($share['UF_ACTIVE_TO']) <= strtotime(date("d.m.Y H:i:s")) && strtotime($share['UF_ACTIVE_TO']) > strtotime($date)) {
                $date = $share['UF_ACTIVE_TO'];
            }
        }
        $this->lastStatusChangedShare = $date;
    }

    // Получаем ценовые акции

    private function getShares()
    {
        $arShares = [];
        if ($this->cache->InitCache(86400, 'product_prices_shares', 'discounts')) {
            $arShares = $this->cache->GetVars()['shares'];
        } elseif ($this->cache->StartDataCache()) {
            CModule::IncludeModule('highloadblock');
            $hlblock = HLBT::getList(array('filter' => array('=NAME' => 'PriceShare')))->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $entity_data_class = $entity->getDataClass();
            $rsData = $entity_data_class::getList(array(
                'filter' => array('UF_ACTIVE' => 'Y'),
                'select' => array('*'),
            ));
            while ($arShare = $rsData->fetch()) {
                if (strtotime($arShare['UF_ACTIVE_TO']) <= strtotime(date("d.m.Y H:i:s"))) {
                    continue;
                }
                if (strtotime($arShare['UF_ACTIVE_FROM']) > strtotime(date("d.m.Y H:i:s"))) {
                    continue;
                }
                $field['articles'] = explode(',', $arShare['UF_ARTICLES']);
                $field['prices'] = explode(',', $arShare['UF_PRICES']);
                $field['prices1'] = explode(',', $arShare['UF_PRICES1']);
                $field['discounts'] = explode(',', $arShare['UF_DISCOUNTS']);
                $field['discounts_bp'] = explode(',', $arShare['UF_DISCOUNTS_BP']);
                $arShares[$arShare['ID']]['id'] = $arShare['ID'];
                $arShares[$arShare['ID']]['name'] = $arShare['UF_NAME'];
                $arShares[$arShare['ID']]['share_type'] = $arShare['UF_SHARE_TYPE'];
                $arShares[$arShare['ID']]['active_from'] = $arShare['UF_ACTIVE_FROM'];
                $arShares[$arShare['ID']]['active_to'] = $arShare['UF_ACTIVE_TO'];
                $arShares[$arShare['ID']]['last_change_date'] = $arShare['UF_LAST_CHANGE_DATE'];
                $arShares[$arShare['ID']]['price_segment'] = $arShare['UF_PRICE_SEGMENT'];
                $arShares[$arShare['ID']]['branches'] = explode(',', $arShare['UF_BRANCHES']);
                while ($article = array_shift($field['articles'])) {
                    $arShares[$arShare['ID']]['products'][$article]['article'] = $article;
                    $arShares[$arShare['ID']]['products'][$article]['price'] = array_shift($field['prices']);
                    $arShares[$arShare['ID']]['products'][$article]['price1'] = array_shift($field['prices1']);
                    $arShares[$arShare['ID']]['products'][$article]['discount'] = array_shift($field['discounts']);
                    $arShares[$arShare['ID']]['products'][$article]['discount_bp'] = array_shift($field['discounts_bp']);
                }
            }
            // Сортировка по после
            uasort($arShares, function ($a, $b) {
                return strtotime($a['last_change_date']) <=> strtotime($b['last_change_date']);
            });
            if (!$arShares) {
                $this->cache->AbortDataCache();
            } else {
                $this->cache->EndDataCache(['shares' => $arShares]);
            }
        }
        $this->arShares = $arShares;
        return $arShares;
    }

    // Модифицируем цены акциями

    private function modifyPricesWithShares($prices, $branch)
    {
        // Нужно взять айдишники по артикулам т.к. цены с продуктами связываются через product_id
        if (!isset($this->productProperties)) {
            $this->productProperties = $this->getProductProperties();
        }
        $articles_ids = [];
        foreach ($this->productProperties as $id => $arData) {
            $articles_ids[$arData['PROPERTY_ARTICLE']] = $id;
        }
        $newPrices = [];
        foreach ($this->arShares as $key => $share) {
            if (!in_array($branch, $share['branches'])) {
                continue;
            }
            foreach ($share['products'] as $article => $product) {
                switch ($share['share_type']) {
                    case 1:
                        $newPrices[$articles_ids[$article]]['SEGMENT'] = $share['price_segment'];
                        $newPrices[$articles_ids[$article]]['PRICE'] = $product['price'] ? $product['price'] : $prices[$articles_ids[$article]]['PRICE'];
                        $newPrices[$articles_ids[$article]]['OLD_PRICE'] = $product['price1'] ? $product['price1'] : $prices[$articles_ids[$article]]['OLD_PRICE'];
                        $newPrices[$articles_ids[$article]]['PERCENT'] = ($newPrices[$articles_ids[$article]]['OLD_PRICE'] > 0) ? intval(100 - round($newPrices[$articles_ids[$article]]['PRICE'] * 100 / $newPrices[$articles_ids[$article]]['OLD_PRICE'])) : 0;
                        break;
                    case 2:
                        $prod_price = $product['price'] ? $product['price'] : (($prices[$articles_ids[$article]]['SEGMENT'] === 'White') ? $prices[$articles_ids[$article]]['OLD_PRICE'] : $prices[$articles_ids[$article]]['PRICE']);
                        $old_price = $prices[$articles_ids[$article]]['OLD_PRICE'];
                        $newPrices[$articles_ids[$article]]['SEGMENT'] = $share['price_segment'];
                        $newPrices[$articles_ids[$article]]['OLD_PRICE'] = $old_price;
                        $newPrices[$articles_ids[$article]]['PRICE'] =  $prod_price - $prod_price * $product['discount']/100;
                        $newPrices[$articles_ids[$article]]['PERCENT'] = round(100 * (($old_price - ($prod_price - $product['discount'] / 100 * $prod_price)) / $old_price));
                        break;
                    case 3:
                        $prod_price = $product['price'] ? $product['price'] : $prices[$articles_ids[$article]]['OLD_PRICE'];
                        $newPrices[$articles_ids[$article]]['SEGMENT'] = $share['price_segment'];
                        $newPrices[$articles_ids[$article]]['PERCENT'] = $product['discount_bp'];
                        $newPrices[$articles_ids[$article]]['OLD_PRICE'] = $prod_price;
                        $newPrices[$articles_ids[$article]]['PRICE'] = $prod_price - $prod_price * $product['discount_bp'] / 100;
                        break;
                }
            }
        }
        return $newPrices + $prices;
    }

    // получаем название страны

    private function getProductProperties()
    {
        $props = [];
        $dbRes = CIBlockElement::GetList(
            ['SORT' => 'ASC'],
            [
                "IBLOCK_ID" => IBLOCK_CATALOG,
            ],
            [
                'ID',
                'PROPERTY_BRAND',
                'PROPERTY_TYPEPRODUCT',
                'PROPERTY_VID',
                'PROPERTY_ARTICLE'
            ]
        );

        while ($item = $dbRes->Fetch()) {
            $props[$item['ID']] = [
                'PROPERTY_BRAND' => $item['PROPERTY_BRAND_VALUE'],
                'PROPERTY_TYPEPRODUCT' => $item['PROPERTY_TYPEPRODUCT_VALUE'],
                'PROPERTY_VID' => $item['PROPERTY_VID_VALUE'],
                'PROPERTY_ARTICLE' => $item['PROPERTY_ARTICLE_VALUE'],
            ];
        }

        return $props;
    }

    // получаем названия страны и региона

    private function loadDiscountRules()
    {
        global $DB;
        $dbRes = $DB->Query('SELECT * FROM qsoft_discounts_rules WHERE active=1');

        $rules = [];
        while ($rule = $dbRes->Fetch()) {
            $key = serialize([
                intval($rule['branch']),
                intval($rule['user_status']),
                $rule['brand'],
                $rule['typeproduct'],
                $rule['vid'],
            ]);
            $rules[$key] = intval($rule['discount']);
        }

        $this->rules = $rules;
    }

    // инициализациия переменной, котрая содержит подключение к БД

    private function parsePrice($arPrice, $props)
    {
        if ($arPrice["price_segment_id"] == "White") {
            $discount = $this->getDiscount($props);

            return array(
                "PRICE" => intval(round($arPrice["price"] * (100 - $discount) / 100)),
                "OLD_PRICE" => intval($arPrice["price"]),
                "PERCENT" => $discount,
                "SEGMENT" => "White",
            );
        } elseif ($arPrice["price_segment_id"] == "Red") {
            return array(
                "PRICE" => intval($arPrice["price"]),
                "OLD_PRICE" => intval($arPrice["price1"]),
                "PERCENT" => ($arPrice["price1"] > 0) ? intval(100 - round($arPrice["price"] * 100 / $arPrice["price1"])) : 0,
                "SEGMENT" => "Red",
            );
        } else {
            return array(
                "PRICE" => intval($arPrice["price"]),
                "OLD_PRICE" => intval($arPrice["price1"]),
                "PERCENT" => ($arPrice["price1"] > 0) ? intval(100 - round($arPrice["price"] * 100 / $arPrice["price1"])) : 0,
                "SEGMENT" => "Yellow",
            );
        }
    }

    // получаем цены на основе филиала, учитывая абсолютную цену

    private function getDiscount($props)
    {
        $rule = $this->getDiscountRule($props);

        return $rule;
    }

    private function getDiscountRule($props)
    {
        $branch = $this->getBranch();
        if (!$this->userType) {
            $this->getUserType();
        }
        //1
        $key = serialize([
            $branch,
            $this->userType,
            $props['PROPERTY_BRAND'],
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //2
        $key = serialize([
            $branch,
            $this->userType,
            $props['PROPERTY_BRAND'],
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //3
        $key = serialize([
            $branch,
            $this->userType,
            $props['PROPERTY_BRAND'],
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //4
        $key = serialize([
            $branch,
            $this->userType,
            'All',
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //5
        $key = serialize([
            $branch,
            $this->userType,
            'All',
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //6
        $key = serialize([
            $branch,
            $this->userType,
            'All',
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //7
        $key = serialize([
            $branch,
            0,
            $props['PROPERTY_BRAND'],
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //8
        $key = serialize([
            $branch,
            0,
            $props['PROPERTY_BRAND'],
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //9
        $key = serialize([
            $branch,
            0,
            $props['PROPERTY_BRAND'],
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //10
        $key = serialize([
            $branch,
            0,
            'All',
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //11
        $key = serialize([
            $branch,
            0,
            'All',
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //12
        $key = serialize([
            $branch,
            0,
            'All',
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //13
        $key = serialize([
            0,
            $this->userType,
            $props['PROPERTY_BRAND'],
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //14
        $key = serialize([
            0,
            $this->userType,
            $props['PROPERTY_BRAND'],
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //15
        $key = serialize([
            0,
            $this->userType,
            $props['PROPERTY_BRAND'],
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //16
        $key = serialize([
            0,
            $this->userType,
            'All',
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //17
        $key = serialize([
            0,
            $this->userType,
            'All',
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //18
        $key = serialize([
            0,
            $this->userType,
            'All',
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //19
        $key = serialize([
            0,
            0,
            $props['PROPERTY_BRAND'],
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //20
        $key = serialize([
            0,
            0,
            $props['PROPERTY_BRAND'],
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //21
        $key = serialize([
            0,
            0,
            $props['PROPERTY_BRAND'],
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //22
        $key = serialize([
            0,
            0,
            'All',
            $props['PROPERTY_TYPEPRODUCT'],
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //23
        $key = serialize([
            0,
            0,
            'All',
            'All',
            $props['PROPERTY_VID'],
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }
        //24
        $key = serialize([
            0,
            0,
            'All',
            'All',
            'All',
        ]);
        if (array_key_exists($key, $this->rules)) {
            return $this->rules[$key];
        }

        return $this->DEFAULT_DISCOUNT;
    }

    public function getParentCodes($code = false, $addFilter = array())
    {
        if (!$code) {
            $code = $this->code;
        }
        $arFilter = array(
            'CODE' => $code,
        );
        // сливаем дополнительный массив с фильтрацией по коду
        $arFilter = array_merge($addFilter, $arFilter);
        // убираем ключи с пустыми значениями
        foreach ($arFilter as $key => $value) {
            if (!$value || empty($value)) {
                unset($arFilter[$key]);
            }
        }
        //получаем все коды от текущего по цепочке вверх
        $res = LocationTable::getList(array(
            'filter' => $arFilter,
            'select' => array(
                'LOC_CODE' => 'PARENTS.CODE',
            ),
            'order' => array(
                'PARENTS.DEPTH_LEVEL' => 'DESC',
            ),
        ));
        $arCodes = array();
        while ($arItem = $res->fetch()) {
            $arCodes[] = $arItem["LOC_CODE"];
        }
        return $arCodes;
    }

    public function getRegion($flag = false)
    {
        if (!$this->region) {
            $this->initLocationParent();
        }
        if ($flag) {
            if ($this->region == 'Москва' || $this->region == 'Московская область') {
                return 'Москва и область';
            }
            if ($this->region == 'Ленинградская область') {
                return 'Санкт-Петербург и область';
            }
        }
        return $this->region;
    }

    private function initLocationParent()
    {
        $res = LocationTable::getList(array(
            'filter' => array(
                'CODE' => $this->code,
                'NAME.NAME' => $this->getName(),
                "PARENTS.TYPE.ID" => [1, 3],
                '=PARENTS.NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=PARENTS.TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ),
            'select' => array(
                'NAME_RU' => 'PARENTS.NAME.NAME',
                'TYPE_CODE' => 'PARENTS.TYPE.ID',
            ),
        ));
        while ($arItem = $res->fetch()) {
            if (intval($arItem["TYPE_CODE"]) == 3) {
                $this->region = $arItem["NAME_RU"];
            }
            if (intval($arItem["TYPE_CODE"]) == 1) {
                $this->country = $arItem["NAME_RU"];
            }
        }
        if (!$this->region) {
            $this->region = $this->getName();
        }
        if (!$this->country) {
            $this->country = $this->getName();
        }
    }
    // получаем название филиала
    // при передаче true получаем названия для всех филиалов

    public function getName()
    {
        if (!$this->name) {
            $this->getLocationCode();
        }
        return $this->name;
    }

    // получаем ID филиала

    public function getCountry()
    {
        if (!$this->country) {
            $this->initLocationParent();
        }
        return $this->country;
    }

    // устанавливаем ID филиала

    public function getProductsPrices($productIds, $branchId = false)
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        if (empty($productIds)) {
            return false;
        }
        if ($branchId) {
            $branch = $branchId;
        } else {
            $branch = $this->getBranch();
        }
        if (!$this->userType) {
            $this->getUserType();
        }
        $user_type = $this->userType;

        $prices = $this->getProductPricesCache($branch, $user_type);
        $arPrices = [];
        foreach ($productIds as $productId) {
            $arPrices[$productId] = $prices[$productId];
        }
        return $arPrices;
    }

    // получаем наличие для лука

    public function getBranchName($all = false)
    {
        $branch = $this->getBranch();
        $connection = $this->getConnection();
        if ($all) {
            $where = "id IN (" . $this->DEFAULT_ABS_BRANCH . ", " . $branch . ", " . $this->DEFAULT_BRANCH . ") LIMIT 3";
        } else {
            $where = "id = " . $branch . " LIMIT 1";
        }
        $res = $connection->query("SELECT id, name FROM b_respect_branch WHERE " . $where);
        $branchesName = array();
        while ($arItem = $res->Fetch()) {
            if ($all && $arItem["id"] == $this->DEFAULT_ABS_BRANCH) {
                $branchesName["abs"] = $arItem["name"];
            }
            if ($arItem["id"] == $branch) {
                $branchesName["fil"] = $arItem["name"];
            }
            if ($all && $arItem["id"] == $this->DEFAULT_BRANCH) {
                $branchesName["def"] = $arItem["name"];
            }
        }
        return $branchesName;
    }

    // получаем ID филиала
    public function getBranch()
    {
        if (!$this->branch) {
            $this->setBranchByLocation();
        }
        return $this->branch;
    }

    // получаем массив доступных продуктов по остаткам в данном местоположении
    public function getAvailableProductsByIds($ids = null)
    {
        $productId_offersId = [];
        $offers_ids = [];
        $dbOffersRes = CIBlockElement::GetList(
            array(),
            [
                'IBLOCK_ID' => IBLOCK_OFFERS,
                'ACTIVE' => 'Y',
            ],
            false,
            false,
            [
                'ID',
                'PROPERTY_CML2_LINK'
            ]
        );
        while ($arItem = $dbOffersRes->getNext()) {
            if ($ids !== null) {
                if (!in_array($arItem['PROPERTY_CML2_LINK_VALUE'], $ids)) {
                    continue;
                }
            }
            if (empty($productId_offersId[$arItem['PROPERTY_CML2_LINK_VALUE']])) {
                $productId_offersId[$arItem['PROPERTY_CML2_LINK_VALUE']] = [];
            }
            $offers_ids[] = $arItem['ID'];
            $productId_offersId[$arItem['PROPERTY_CML2_LINK_VALUE']][] = $arItem['ID'];
        }
        $offersRests = $this->getRests(array_values($offers_ids));

        $arAvailableProds = [];
        foreach ($productId_offersId as $prod_id => $offer_ids) {
            foreach ($offer_ids as $id) {
                if (!empty($offersRests[$id])) {
                    // Если в массиве $arAvailableProds есть id продукта - значит есть хоть одно предложение по продукту с остатком
                    $arAvailableProds[] = $prod_id;
                    continue 2;
                }
            }
        }
        return $arAvailableProds;
    }

    // устанавливаем ID филиала
    private function setBranchByLocation()
    {
        // получаем коды по цепочке вверх
        $arCodes = $this->getParentCodes($this->code);
        // если мы каким-то магическим образом ничего не получили, то устанавливаем дефолтное значение
        if (empty($arCodes)) {
            return $this->branch = $this->DEFAULT_BRANCH;
        }
        // получаем все связи с филиалами по полученным кодам
        $connection = $this->getConnection();
        $res = $connection->query("SELECT location_code, branch_id FROM b_qsoft_location2branch WHERE location_code IN (".implode(",", $arCodes).")");
        $arBranches = array();
        while ($arItem = $res->fetch()) {
            $arBranches[$arItem["location_code"]] = $arItem["branch_id"];
        }
        // поскольку массив уже отсортирован от города до страны, проверяем есть ли привязка до первого совпадения
        foreach ($arCodes as $code) {
            if ($arBranches[$code]) {
                return $this->branch = intval($arBranches[$code]);
            }
        }
        // если ничего нет, то устанавливаем дефолтный филиал
        return $this->branch = $this->DEFAULT_BRANCH;
    }

    // получаем остатки по текущим складам для нужных продуктов
    public function getDataToArticle($arArticles)
    {
        // получаем товары
        $res = CIBlockElement::GetList(
            array(),
            array(
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => IBLOCK_CATALOG,
                'PROPERTY_ARTICLE' => $arArticles,
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'CODE',
                'PROPERTY_ARTICLE',
            )
        );
        $arProducts = array();
        while ($arItem = $res->Fetch()) {
            $arProducts[$arItem["ID"]] = array(
                "ARTICLE" => $arItem["PROPERTY_ARTICLE_VALUE"],
                "CODE" => $arItem["CODE"],
            );
        }
        // если нет товаров, возвращаем все артикулы со статусом "ожидается поступление"
        if (empty($arProducts)) {
            $arResult = array();
            foreach ($arArticles as $article) {
                $arResult[$article] = "wait";
            }
            return $arResult;
        }
        $arProductsIds = array_keys($arProducts);
        // получаем ТП
        $res = CIBlockElement::GetList(
            array(),
            array(
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => IBLOCK_OFFERS,
                'PROPERTY_CML2_LINK' => $arProductsIds,
            ),
            false,
            false,
            array(
                'ID',
                'IBLOCK_ID',
                'PROPERTY_CML2_LINK',
            )
        );
        $arOffers = array();
        while ($arItem = $res->Fetch()) {
            $arOffers[$arItem["ID"]] = $arItem["PROPERTY_CML2_LINK_VALUE"];
        }
        $arOffersIds = array_keys($arOffers);
        // получаем остатки в текущем городе
        $arRests = $this->getRests($arOffersIds, false, true);
        // получаем остатки на сайте
        $arRestsAll = $this->getRests($arOffersIds, false, true, true);
        // формируем результирующий массив для лукбука
        $arResult = array();
        foreach ($arOffers as $offerId => $productId) {
            if (isset($arResult[$arProducts[$productId]["ARTICLE"]]) && !in_array($arResult[$arProducts[$productId]["ARTICLE"]], array("wait", "nedostupen"))) {
                continue;
            }
            if ($arRests[$offerId] > 0) {
                $arResult[$arProducts[$productId]["ARTICLE"]] = $arProducts[$productId]["CODE"];
                continue;
            }
            if ($arResult[$arProducts[$productId]["ARTICLE"]] == "nedostupen") {
                continue;
            }
            if ($arRestsAll[$offerId] > 0) {
                $arResult[$arProducts[$productId]["ARTICLE"]] = "nedostupen";
                continue;
            }
            $arResult[$arProducts[$productId]["ARTICLE"]] = "wait";
        }
        // для отсутствующих артикулов устанавливаем статус "ожидается поступление"
        foreach ($arArticles as $article) {
            if ($arResult[$article]) {
                continue;
            }
            $arResult[$article] = "wait";
        }
        return $arResult;
    }

    // ТП по типу доставки

    public function getRests($productIds, $type = false, $sum = false, $allStorages = false, $stores = false, $intersectStorages = [])
    {
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        if (empty($productIds)) {
            return false;
        }
        if (!$stores) {
            $storages = $this->getStorages($type);
        } else {
            $storages = $stores;
        }
        if (empty($storages)) {
            return array();
        }
        $storagesIds = array_keys($storages);

        if ($intersectStorages != []) {
            $storagesIds = array_intersect($storagesIds, $intersectStorages);
        }

        $where_store = "";
        $where_storage = "";
        if (!$allStorages) {
            $where_store = " and STORE_ID in (" . implode(",", $storagesIds) . ")";
            $where_storage = " and STORAGE_ID in (" . implode(",", $storagesIds) . ")";
        }
        $connection = $this->getConnection();
        // получаем остатки конкретных товаров на конкретных складах за вычитом резервов при наличии больше 0
        $sql = "SELECT * FROM 
                    (SELECT STORE_ID as STORAGE_ID, PRODUCT_ID, SUM(AMOUNT) as QUANTITY
                    FROM b_catalog_store_product
                    WHERE 1=1 " . $where_store . " and PRODUCT_ID in (" . implode(",", $productIds) . ")
                    GROUP BY STORE_ID, PRODUCT_ID
                )prod";
        $res = $connection->query($sql);
        $arRests = array();
        while ($arItem = $res->fetch()) {
            if (!$sum) {
                $arRests[$arItem["PRODUCT_ID"]][$arItem["STORAGE_ID"]] = $arItem["QUANTITY"];
            } else {
                $arRests[$arItem["PRODUCT_ID"]] += $arItem["QUANTITY"];
            }
        }

        return $arRests;
    }

    // ТП по складам

    public function getStorages($type = false, $withoutDonors = false)
    {
        if (!$this->storages) {
            $this->setStoragesByLocation();
            // Добавляем "склады-доноры" к складам региона, если регион имеется в списке "регионов-пациентов" и он не регион-исключение
            if ($this->checkIfLocationIsDonorTarget($this->code) && !$this->exepRegionFlag) {
                foreach ($this->DEFAULT_STORAGES as $key => $store) {
                    $this->storages[$key] = $store;
                    $this->storages[$key][1] = 0;
                }
            }
        }
        if ($type && in_array($type, array(1, 2))) {
            return $this->filterStorages($type);
        }
        if ($withoutDonors) {
            $storages = $this->storages;
            foreach ($this->DEFAULT_STORAGES as $key => $store) {
                unset($storages[$key]);
            }
            return $storages;
        } else {
            return $this->storages;
        }
    }

    private function setStoragesByLocation()
    {
        // получаем коды по цепочке вверх
        $arCodes = $this->getParentCodes($this->code);
        // если мы каким-то магическим образом ничего не получили, то устанавливаем дефолтное значение
        if (empty($arCodes)) {
            $this->exepRegionFlag = true;
            return $this->storages = $this->DEFAULT_STORAGES;
        }
        // получаем все активные склады
        $res = StoreTable::getList(array(
            "select" => array(
                "ID", "TITLE", "ADDRESS", "SORT",
            ),
            "filter" => array(
                "ACTIVE" => "Y",
            ),
        ));
        $arStoragesAct = array();
        while ($arItem = $res->fetch()) {
            $arStoragesAct[$arItem["ID"]] = $arItem;
        }
        // получаем необходимые склады
        $connection = $this->getConnection();
        $res = $connection->query("SELECT LOCATION_CODE, STORAGE_ID, DELIVERY, RESERVE FROM b_respect_location_link WHERE LOCATION_CODE IN (" . implode(",", $arCodes) . ")");
        $arStorages = array();
        while ($arItem = $res->fetch()) {
            $arItem["DELIVERY"] = intval($arItem["DELIVERY"]);
            $arItem["RESERVE"] = intval($arItem["RESERVE"]);
            $arItem["STORAGE_ID"] = intval($arItem["STORAGE_ID"]);
            // фильтруем не активные и с доставка/резервирование "нет"
            if (!$arStoragesAct[$arItem["STORAGE_ID"]] || ($arItem["DELIVERY"] == 0 && $arItem["RESERVE"] == 0)) {
                continue;
            }
            $arStorages[$arItem["LOCATION_CODE"]][$arItem["STORAGE_ID"]] = array($arItem["DELIVERY"], $arItem["RESERVE"]);
            $this->arStorages[$arItem["STORAGE_ID"]] = $arStoragesAct[$arItem["STORAGE_ID"]];
        }
        if (array_intersect_key($this->arStorages, $this->DEFAULT_STORAGES)) {
            $this->exepRegionFlag = true;
        }
        // поскольку массив уже отсортирован от города до страны, проверяем есть ли склады до первого совпадения
        foreach ($arCodes as $code) {
            if ($arStorages[$code]) {
                // добавляем склады по умолчанию на доставку, если нет
                $flag = true;
                foreach ($arStorages[$code] as $storage) {
                    if ($storage[0] == 1) {
                        $flag = false;
                        break;
                    }
                }
                if ($flag) {
                    foreach ($this->DEFAULT_STORAGES as $key => $storage) {
                        // если среди полученных складов нет данного склада, то ставим резерв в "нет"
                        if (!$arStorages[$code][$key]) {
                            $arStorages[$code][$key][1] = 0;
                        }
                        // и в любом случае ставим флаг доставки в "да"
                        $arStorages[$code][$key][0] = 1;
                    }
                    $this->exepRegionFlag = true;
                }
                $arIntersectStorages = array_intersect_key($arStoragesAct, $arStorages[$code]);
                $this->arAvStorages = $arIntersectStorages;
                return $this->storages = $arStorages[$code];
            }
        }
        // если ничего нет, то устанавливаем флаг региона-исключения и устанавливаем дефолтные склады
        $arIntersectStorages = array_intersect_key($arStoragesAct, $this->DEFAULT_STORAGES);
        $this->arAvStorages = $arIntersectStorages;
        foreach ($this->DEFAULT_STORAGES as $key => $storage) {
            if (!$arStorages[$code][$key]) {
                $arStorages[$code][$key][1] = 0;
            }
            // и в любом случае ставим флаг доставки в "да"
            $arStorages[$code][$key][0] = 1;
        }
        $this->exepRegionFlag = true;
        return $this->storages = $arStorages[$code];
    }

    // получаем названия складов

    private function filterStorages($type)
    {
        // просто уменьшаем на 1, чтобы тип соответствовал нужному ключу
        $type--;
        $storages = $this->storages;
        foreach ($storages as $key => $val) {
            if ($val[$type] != 1) {
                unset($storages[$key]);
            }
        }
        return $storages;
    }
    // получаем ID складов с флагами на доставку и резерв
    // false - все склады
    // 1 - только склады с доставкой
    // 2 - только склады с резервом

    public function getTypeSizes($arRests, $arSizes, $type = false)
    {
        $arStorages = $this->getStorages($type);
        $arResult = [
            'DELIVERY' => [],    //ТП на складе
            'RESERVATION' => [], //ТП для резервирования
            'ALL' => []          //ТП везде
        ];
        foreach ($arRests as $offerId => $storages) {
            foreach ($storages as $storageId => $quantity) {
                if ($arStorages[$storageId][0] == 1 && (!$type || $type == 1)) {
                    $arResult['DELIVERY'][$offerId]['SIZE'] = $arSizes[$offerId];
                    // Присваеваем параметр местный или не местный товар. Если регион не исключение (не является Москвой или регионом, который берет остатки московские)
                    // и магазин входит в список дефолтных магазинов (мск), то такой товар поставляется как донорский
                    if (!$this->exepRegionFlag && !empty($this->DEFAULT_STORAGES[$storageId]) && $arResult['DELIVERY'][$offerId]['IS_LOCAL'] != 'Y') {
                        $arResult['DELIVERY'][$offerId]['IS_LOCAL'] = 'N';
                    } else {
                        $arResult['DELIVERY'][$offerId]['IS_LOCAL'] = 'Y';
                    }
                }
                if ($arStorages[$storageId][1] == 1 && (!$type || $type == 2)) {
                    $arResult['RESERVATION'][$offerId]['SIZE'] = $arSizes[$offerId];
                    $arResult['RESERVATION'][$offerId]['IS_LOCAL'] = 'Y';
                }
                $arResult['ALL'][$offerId]['SIZE'] = $arSizes[$offerId];
            }
        }
        foreach ($arResult as $key => $array) {
            asort($arResult[$key]);
        }
        return $arResult;
    }

    // устанавливаем ID складов

    public function getStoreSizes($arRests, $arSizes)
    {
        $arResult = array();
        foreach ($arRests as $offerId => $storages) {
            foreach ($storages as $storageId => $quantity) {
                if ($arSizes[$offerId] === true) {
                    $arResult[$storageId] = array();
                    continue;
                }
                $arResult[$storageId][] = intval($arSizes[$offerId]);
            }
        }
        foreach ($arResult as $key => $array) {
            sort($arResult[$key]);
        }
        return $arResult;
    }

    public function checkStorages($id, $type = false)
    {
        $storages = $this->getStorages($type);
        if (empty($storages)) {
            if (in_array($id, $storages)) {
                return true;
            }
        }
        return false;
    }

    // получаем коды родительских местоположений

    public function getStoragesName($type = false)
    {
        $storages = $this->getStorages($type);
        if (empty($storages)) {
            return array();
        }
        $storagesIds = array_keys($storages);
        $res = StoreTable::getList(array(
            "filter" => array(
                "ID" => $storagesIds,
            ),
            "select" => array(
                "ID",
                "TITLE",
            ),
        ));
        $arStoragesNames = array();
        while ($arItem = $res->Fetch()) {
            $arStoragesNames[$arItem["ID"]] = $arItem["TITLE"];
        }
        return $arStoragesNames;
    }
    // устанавливаем номер телефона, берётся первый номер по цепочке вверх
    private function setRegionPhone()
    {
        $arCodes = $this->getParentCodes($this->code);
        foreach ($arCodes as $code) {
            $item = LocationTable::getByCode($code, array(
                'filter' => array('EXTERNAL.SERVICE.CODE' => "PHONE"),
                'select' => array('EXTERNAL.XML_ID')
            ))->fetch();
            if (!empty($item["SALE_LOCATION_LOCATION_EXTERNAL_XML_ID"])) {
                $this->phone = $item["SALE_LOCATION_LOCATION_EXTERNAL_XML_ID"];
                return;
            }
        }
    }
    // получаем номер телефона
    public function getRegionPhone()
    {
        if (!$this->phone) {
            $this->setRegionPhone();
        }
        return $this->phone;
    }

    //получение SEO тегов из инфоблока Поддомены
    public function getPoddomenSeo($url = '')
    {
        $seo = [];
        $poddomen = $this->getPoddomen(false, true);
        $arSectionParentChild = $this->loadSectionsInPoddomens();

        if (empty($url)) {
            $sections = explode("/", $_SERVER['REQUEST_URI']);
        } else {
            $sections = explode("/", $url);
        }
        foreach ($sections as $sectionCode) {
            if (!empty($sectionCode) && $sectionCode[0] != '?') {
                $arSectionsCode[] = $sectionCode;
            }
        }
        if ($_SERVER['REQUEST_URI'] == '/') {
            $arSectionsCode[] = '/';
        }
        if ($poddomen) {
            array_unshift($arSectionsCode, $poddomen);
            if (isset($arSectionParentChild[$arSectionsCode[0]])) {
                //нашли поддомен
                $arSectionsCode[0] = $arSectionParentChild[$arSectionsCode[0]];
                $seo = $this->findSeoTagsInPoddomens($arSectionsCode, $arSectionParentChild);
            }
            if (count($seo) != 8) {
                //берем страховку
                $arSectionsCode[0] = $arSectionParentChild['strahovkaPoddomen'];
                $newSeo = $this->findSeoTagsInPoddomens($arSectionsCode, $arSectionParentChild);
                $seo = array_merge($newSeo, $seo);
            }
        } else {
            array_unshift($arSectionsCode, $arSectionParentChild['strahovkaNoPoddomen']);
            $seo = $this->findSeoTagsInPoddomens($arSectionsCode, $arSectionParentChild);
        }
        return $seo;
    }
    //получаем структуру секций всех поддоменов
    private function loadSectionsInPoddomens()
    {
        if ($this->cache->InitCache(86400, 'seoPoddomen', 'seo')) {
            $arSectionParentChild = $this->cache->GetVars()['arSectionParentChild'];
        } elseif ($this->cache->StartDataCache()) {
            $rsSection = CIBlockSection::GetList([], ["IBLOCK_CODE"=>"poddomens", 'ACTIVE' => 'Y']);
            while ($arSection = $rsSection->Fetch()) {
                $arSectionParentChild['IBLOCK_ID'] = $arSection['IBLOCK_ID'];
                if (!empty($arSection['IBLOCK_SECTION_ID'])) {
                    $arSectionParentChild[$arSection['IBLOCK_SECTION_ID']][$arSection['IBLOCK_SECTION_ID']] = $arSection['IBLOCK_SECTION_ID'];
                    $arSectionParentChild[$arSection['IBLOCK_SECTION_ID']][$arSection['CODE']] = $arSection['ID'];
                } else {
                    $arSectionParentChild[$arSection['CODE']] = $arSection['ID'];
                    $arSectionParentChild[$arSection['ID']] = [$arSection['ID'] => $arSection['ID']];
                }
            }
            if (!$arSectionParentChild) {
                $this->cache->AbortDataCache();
            } else {
                $this->cache->EndDataCache(['arSectionParentChild' => $arSectionParentChild]);
            }
        }
        return $arSectionParentChild;
    }
    //ищем наиболее подходящие seo из поддоменов и секций
    private function findSeoTagsInPoddomens($arSectionsCode, $arSectionParentChild)
    {
        $arPoddomenUf = $this->loadPoddomenUf();
        if ($this->cache->InitCache(86400, implode('|', $arSectionsCode), 'seo')) {
            $seo = $this->cache->GetVars()['seo'];
        } elseif ($this->cache->StartDataCache()) {
            $seo = [];
            //берем гео переменную из поддомена
            $geo = $arPoddomenUf['GEO'][$arSectionsCode[0]];
            $currentSectionId = $arSectionsCode[0];
            //переопределяем ближайшую секцию внутри поддомена, если она есть в ИБ Поддомены
            foreach ($arSectionsCode as $sectionCode) {
                if (isset($arSectionParentChild[$currentSectionId][$sectionCode])) {
                    $currentSectionId = $arSectionParentChild[$currentSectionId][$sectionCode];
                    $ipropValues = new SectionValues($arSectionParentChild['IBLOCK_ID'], $currentSectionId);
                    //берем seo из секции
                    if (!empty($ipropValues->getValues())) {
                        $newSeo = $ipropValues->getValues();
                        foreach ($newSeo as $tags => $value) {
                            if (!empty($value)) {
                                $seo[$tags] = $value;
                            }
                        }
                    }
                    //берем переменную заголовка из секции
                    if (!empty($arPoddomenUf['TITLE'][$currentSectionId])) {
                        $title = $arPoddomenUf['TITLE'][$currentSectionId];
                    }
                }
            }
            //заменяем якоря в тегах
            if (!empty($seo)) {
                $seo = str_replace("#GEO#", $geo, $seo);
                $seo = str_replace("#TITLE#", $title, $seo);
            }
            $this->cache->EndDataCache(['seo' => $seo]);
        }
        return $seo;
    }
    //получение значений пользовательских полей для всех поддоменов и секций
    private function loadPoddomenUf()
    {
        if ($this->cache->InitCache(86400, 'poddomenUf', 'seo')) {
            $arPoddomenUf = $this->cache->GetVars()['arPoddomenUf'];
        } elseif ($this->cache->StartDataCache()) {
            $iblockId = CIBlockSection::GetList([], ['IBLOCK_CODE' => 'poddomens', 'ACTIVE' => 'Y'], false, ['IBLOCK_ID'])->Fetch();
            $arFilter = array(
                'IBLOCK_ID' => $iblockId['IBLOCK_ID'],
                'ACTIVE' => 'Y',
            );
            $arSelect = array(
                'ID',
                'CODE',
                'UF_*'
            );
            $res = CIBlockSection::GetList('', $arFilter, false, $arSelect);
            while ($arPoddomen = $res->Fetch()) {
                if (!empty($arPoddomen['UF_P_VARIABLE'])) {
                    $arPoddomenUf['GEO'][$arPoddomen['ID']] = $arPoddomen['UF_P_VARIABLE'];
                }
                if (!empty($arPoddomen['UF_TITLE_VARIABLE'])) {
                    $arPoddomenUf['TITLE'][$arPoddomen['ID']] = $arPoddomen['UF_TITLE_VARIABLE'];
                }
                if (!empty($arPoddomen['UF_CAPITAL_CITY'])) {
                    $arPoddomenUf['CAPITAL_CITY'][$arPoddomen['UF_CAPITAL_CITY']] = $arPoddomen['CODE'];
                }
                if (!empty($arPoddomen['UF_AREAL'])) {
                    $arPoddomenUf['AREAL'][$arPoddomen['UF_AREAL']] = $arPoddomen['CODE'];
                }
            }
            if (!$arPoddomenUf) {
                $this->cache->AbortDataCache();
            } else {
                $this->cache->EndDataCache(['arPoddomenUf' => $arPoddomenUf]);
            }
        }
        return $arPoddomenUf;
    }

    public function getPoddomen($return = false, $checkRegion = false)
    {
        if ($checkRegion) {
            return $this->checkPoddomenRegion();
        }

        $pattern = '/(.*).' . $_SERVER['SERVER_NAME'] . '.*/';
        if (HOST_USE_TP) {
            $pattern = '/(.*)\..*\.' . $_SERVER['SERVER_NAME'] . '.*/';
        }
        preg_match($pattern, $_SERVER['HTTP_HOST'], $arPoddomens);
        if (empty($arPoddomens[1])) {
            return false;
        }
        if ($return == 'code') {
            return $this->checkPoddomenName($arPoddomens[1]);
        }
        if ($return == 'check') {
            $arSectionParentChild = $this->loadSectionsInPoddomens();
            return $arSectionParentChild[$arPoddomens[1]] ? true : false;
        }
        if ($return == 'failCheck') {
            $arSectionParentChild = $this->loadSectionsInPoddomens();
            return $arSectionParentChild[$arPoddomens[1]] ? false : true;
        }
        return $arPoddomens[1];
    }

    //по поддомену возвращает код местоположения
    private function checkPoddomenName($poddomen)
    {
        $arPoddomenUf = $this->loadPoddomenUf();
        $arPoddomenUf = array_flip($arPoddomenUf['CAPITAL_CITY']);
        if (isset($arPoddomenUf[$poddomen])) {
            return $arPoddomenUf[$poddomen];
        }
        return false;
    }

    //по коду местоположения возвращает поддомен
    public function checkPoddomenCode($locationCode)
    {
        $arPoddomenUf = $this->loadPoddomenUf();
        if (isset($arPoddomenUf['CAPITAL_CITY'][$locationCode])) {
            return $arPoddomenUf['CAPITAL_CITY'][$locationCode];
        }
        return false;
    }

    //проверяет принадлежность города региону поддомена
    public function checkPoddomenRegion()
    {
        $arRegionCodes = $this->getParentCodes($this->code);
        $arPoddomenUf = $this->loadPoddomenUf();
        foreach ($arRegionCodes as $regionCode) {
            if (isset($arPoddomenUf['AREAL'][$regionCode])) {
                return $arPoddomenUf['AREAL'][$regionCode];
            }
        }
        return false;
    }

    private function setExeptionRegionIfDefault()
    {
        if ($this->code === $this->DEFAULT_LOCATION['CODE']) {
            $this->exepRegionFlag = true;
        }
    }
    /**
     * Возвращает географические координаты текущего местоположения
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getLocationCoords()
    {
        $arRes = LocationTable::getList([
            'filter' => ['CODE' => $this->code],
            'select' => ['LATITUDE', 'LONGITUDE']
        ])->fetch();

        return $arRes;
    }

    // проверка локации на регион-пациент, возвращает boolean
    public function checkIfLocationIsDonorTarget($code)
    {
        return !empty(array_intersect($this->getParentCodes($code), array_column($this->DONORS_TARGETS, 'location_id')));
    }

    // кеширует и возвращает массив уникальных витрин
    public function getUniqueShowcases()
    {
        global $CACHE_MANAGER;
        $vars = [];

        if ($this->cache->InitCache(604800, 'u1nique_showcases', '/local/admin')) {
            $vars = $this->cache->GetVars();
        } elseif ($this->cache->StartDataCache()) {
            $CACHE_MANAGER->StartTagCache('/local/admin');
            $CACHE_MANAGER->RegisterTag('unique_showcases');
            try {
                $database = Application::getConnection();
                $branchList = $database->query('SELECT ID, NAME FROM b_respect_branch;');
                $branches = [];
                while ($branch = $branchList->fetch()) {
                    $branches[$branch['ID']] = $branch['NAME'];
                }
                $locationBranchList = $database->query('SELECT LOCATION_CODE, BRANCH_ID FROM b_qsoft_location2branch;');
                $locationsBranch = [];
                while ($locationBranch = $locationBranchList->fetch()) {
                    $locationsBranch[$locationBranch['LOCATION_CODE']] = $locationBranch['BRANCH_ID'];
                }
                $storeList = StoreTable::getList([
                    'filter' => [
                        'ACTIVE' => 'Y',
                    ],
                    'select' => [
                        'ID',
                        'TITLE',
                    ]
                ]);
                $arStores = [];
                while ($store = $storeList->fetch()) {
                    $arStores[$store['ID']] = $store['TITLE'];
                }
                $locationList = LocationTable::getList([
                    'select' => [
                        'ID',
                        'CODE',
                        'PARENT_ID',
                        'NAME_RU' => 'NAME.NAME',
                        'LOCATION_TYPE' => 'TYPE.ID',
                    ],
                    "order" => array(
                        "TYPE.ID" => "ASC",
                    ),
                ]);
                $locations = [];
                while ($location = $locationList->fetch()) {
                    $locations[$location['ID']] = $location;
                }
                $locationStoreList = $database->query('SELECT LOCATION_CODE, STORAGE_ID, DELIVERY, RESERVE FROM b_respect_location_link ORDER BY LOCATION_CODE ASC, STORAGE_ID ASC;');
                $locationStores = [];
                while ($arItem = $locationStoreList->fetch()) {
                    if (empty($arStores[$arItem["STORAGE_ID"]]) || ($arItem["DELIVERY"] == 0 && $arItem["RESERVE"] == 0)) {
                        continue;
                    }
                    $locationStores[$arItem["LOCATION_CODE"]][$arItem["STORAGE_ID"]] = array($arItem["DELIVERY"], $arItem["RESERVE"]);
                }
                foreach ($locationStores as $locationCode => $storages) {
                    if ($this->checkIfLocationIsDonorTarget($locationCode)) {
                        foreach ($this->DEFAULT_STORAGES as $key => $storage) {
                            // если среди полученных складов нет данного склада, то ставим резерв в "нет"
                            if (!$locationStores[$locationCode][$key]) {
                                $locationStores[$locationCode][$key][1] = 0;
                            }
                            // и в любом случае ставим флаг доставки в "да"
                            $locationStores[$locationCode][$key][0] = 1;
                        }
                    } else {
                        // добавляем склады по умолчанию на доставку, если нет
                        $flag = true;
                        foreach ($storages as $storage) {
                            if ($storage[0] == 1) {
                                $flag = false;
                                break;
                            }
                        }
                        if ($flag) {
                            foreach ($this->DEFAULT_STORAGES as $key => $storage) {
                                // если среди полученных складов нет данного склада, то ставим резерв в "нет"
                                if (!$locationStores[$locationCode][$key]) {
                                    $locationStores[$locationCode][$key][1] = 0;
                                }
                                // и в любом случае ставим флаг доставки в "да"
                                $locationStores[$locationCode][$key][0] = 1;
                            }
                        }
                    }
                }

                $arResult = array();
                foreach ($locations as $id => $arLocation) {
                    $arId = array(
                        "BRANCH" => false,
                        "STORES" => false,
                    );
                    $tempId = $id;
                    while (true) {
                        if (!empty($locationsBranch[$locations[$tempId]["CODE"]])) {
                            $arId["BRANCH"] = $locationsBranch[$locations[$tempId]["CODE"]];
                            break;
                        }
                        if (!empty($locations[$locations[$tempId]["PARENT_ID"]]["CODE"])) {
                            $tempId = $locations[$tempId]["PARENT_ID"];
                        } else {
                            $arId["BRANCH"] = $this->DEFAULT_BRANCH;
                            break;
                        }
                    }
                    $tempId = $id;
                    while (true) {
                        if (!empty($locationStores[$locations[$tempId]["CODE"]])) {
                            $arId["STORES"] = $locationStores[$locations[$tempId]["CODE"]];
                            break;
                        }
                        if (!empty($locations[$locations[$tempId]["PARENT_ID"]]["CODE"])) {
                            $tempId = $locations[$tempId]["PARENT_ID"];
                        } else {
                            $arId["STORES"] = $this->DEFAULT_STORAGES;
                            break;
                        }
                    }

                    $showcaseId = md5(serialize($arId));

                    $locations[$id]["SHOWCASE_ID"] = $showcaseId;
                    if ($arLocation["LOCATION_TYPE"] != 3 && $arLocation["LOCATION_TYPE"] != 4 && $arLocation["LOCATION_TYPE"] != 5) {
                        continue;
                    }
                    $parentShowcaseID = $locations[$arLocation["PARENT_ID"]]["SHOWCASE_ID"];
                    if (!empty($parentShowcaseID)) {
                        if (!empty($arResult[$showcaseId])) {
                            if ($arLocation["LOCATION_TYPE"] == 5 || $arLocation["LOCATION_TYPE"] == 4) {
                                if ($parentShowcaseID != $showcaseId) {
                                    $arResult[$parentShowcaseID]["EXCEPTIONS"][$arLocation["CODE"]] = $arLocation["NAME_RU"];
                                    if ($arLocation["LOCATION_TYPE"] == 5) {
                                        $arResult[$showcaseId]["LOCATIONS"][$arLocation["CODE"]] = "<b>" . $arLocation["NAME_RU"] . "</b>";
                                    } else {
                                        $arResult[$showcaseId]["LOCATIONS"][$arLocation["CODE"]] = $arLocation["NAME_RU"];
                                    }
                                }
                            } else {
                                $arResult[$showcaseId]["LOCATIONS"][$arLocation["CODE"]] = $arLocation['NAME_RU'];
                            }
                        } else {
                            if ($arLocation["LOCATION_TYPE"] == 5 || $arLocation["LOCATION_TYPE"] == 4 && $parentShowcaseID != $showcaseId) {
                                $arResult[$parentShowcaseID]["EXCEPTIONS"][$arLocation["CODE"]] = $arLocation["NAME_RU"];
                                if ($arLocation["LOCATION_TYPE"] == 5) {
                                    $arLocation["NAME_RU"] = "<b>" . $arLocation["NAME_RU"] . "</b>";
                                }
                            }
                            $arResult[$showcaseId] = array(
                                "BRANCH" => $branches[$arId["BRANCH"]],
                                "BRANCH_ID" => $arId["BRANCH"],
                                "STORES" => $arId["STORES"],
                                "EXCEPTIONS" => array(),
                                "LOCATIONS" => array(
                                    $arLocation["CODE"] => $arLocation["NAME_RU"],
                                ),
                            );
                        }
                    }
                }
                uasort($arResult, function ($a, $b) {
                    $diff = strcmp($a["BRANCH"], $b["BRANCH"]);
                    if ($diff) {
                        return $diff;
                    }
                    if (!empty($a["EXCEPTIONS"])) {
                        return -1;
                    }
                    if (!empty($b["EXCEPTIONS"])) {
                        return 1;
                    }
                    return 0;
                });

                $CACHE_MANAGER->EndTagCache();
                $this->cache->EndDataCache([
                    'result' => $arResult,
                    'stores' => $arStores,
                ]);
                $vars = [
                    'result' => $arResult,
                    'stores' => $arStores,
                ];
            } catch (DatabaseException $exception) {
                echo $exception->getMessage();
            } catch (Exception $exception) {
                echo $exception->getMessage();
            }
        }
        return $vars;
    }

    // возвращает уникальную витрину к которой принадлежит пользователь
    public function getUserShowcase($cityLocationCode = false)
    {
        if (!$cityLocationCode) {
            $cityLocationCode = $this->code;
        }
        $parentLocationsCodes = $this->getParentCodes($cityLocationCode);
        $arShowcases = $this->getUniqueShowcases();
        $result = false;
        $exepLocation = false;
        foreach ($arShowcases['result'] as $key => $uniqShowcase) {
            if (in_array($exepLocation, $uniqShowcase['LOCATIONS'])) {
                $result = $key;
                break;
            }
            if (!empty($arIntersectLocation = array_intersect($parentLocationsCodes, array_keys($uniqShowcase['LOCATIONS'])))) {
                if (empty($arExepIntersectLocation = array_intersect($parentLocationsCodes, array_keys($uniqShowcase['EXCEPTIONS'])))) {
                    $result = $key;
                    break;
                } else {
                    $exepLocation = $arExepIntersectLocation[0];
                }
            }
        }

        return $result;
    }

    //возвращает все доступные офферы с размерами для продуктов в корзине
    public function getAmountOffersByProductId($arProductsInCart)
    {
        global $CACHE_MANAGER;

        if (empty($arProductsInCart)) {
            return false;
        }

        if ($this->cache->initCache(86400, 'productOffersSizes', '/products')) {
            $arProductOffers = $this->cache->getVars()['arProductOffers'];
            $arOffersSizes = $this->cache->getVars()['arOffersSizes'];
        } elseif ($this->cache->StartDataCache()) {
            $CACHE_MANAGER->StartTagCache('/products');
            $CACHE_MANAGER->RegisterTag('catalogALL');

            $res = CIBlockElement::GetList(
                array("PROPERTY_SIZE" => "ASC"),
                array(
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => IBLOCK_OFFERS,
                ),
                false,
                false,
                array(
                    'ID',
                    'IBLOCK_ID',
                    'PROPERTY_SIZE',
                    'PROPERTY_CML2_LINK',
                )
            );

            while ($arItem = $res->Fetch()) {
                if (!empty($arItem['PROPERTY_SIZE_VALUE'])) {
                    $arProductOffers[$arItem['PROPERTY_CML2_LINK_VALUE']][] = $arItem['ID'];
                    $arOffersSizes[$arItem['ID']] = $arItem['PROPERTY_SIZE_VALUE'];
                }
            }
            if (!empty($arProductOffers) && !empty($arOffersSizes)) {
                $CACHE_MANAGER->EndTagCache();
                $this->cache->EndDataCache([
                    'arProductOffers' => $arProductOffers,
                    'arOffersSizes' => $arOffersSizes
                ]);
            } else {
                $this->cache->AbortDataCache();
            }
        }

        foreach ($arProductsInCart as $productId) {
            foreach ($arProductOffers[$productId] as $offerId) {
                $arOffersForCart[$productId][$offerId] = $arOffersSizes[$offerId];
                $arOffersForCheck[$offerId] = $offerId;
            }
        }

        $arRests = $this->getRests($arOffersForCheck, 1, true, false);

        foreach ($arProductsInCart as $productId) {
            $arOffersForCart[$productId] = array_intersect_key($arOffersForCart[$productId], $arRests);
        }

        return $arOffersForCart;
    }
}
