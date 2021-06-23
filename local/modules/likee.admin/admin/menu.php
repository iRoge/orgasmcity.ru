<?
global $APPLICATION;
global $USER;
$access = false;
$dbGroup = \Bitrix\Main\GroupTable::getList(array(
    'filter' => array("STRING_ID" => ['seo', 'modules_sa'])
));
while ($arGroup = $dbGroup->Fetch()) {
    if (in_array($arGroup['ID'], $USER->GetUserGroupArray())) {
        $access = true;
        break;
    }
}
//if ($access) {
//    $aMenu = array(
//        "parent_menu" => "global_menu_services",
//        "section" => "Respect",
//        "sort" => 50,
//        "text" => "Respect",
//        "icon" => "sys_menu_icon",
//        "page_icon" => "sys_page_icon",
//        "items_id" => "likee_respect",
//        "items" => array(
//            array(
//                "text" => "Уникальные витрины без учета работы складов",
//                "url" => "unique_showcasesWO.php?lang=" . LANGUAGE_ID,
//            ),
//            array(
//                "text" => "Уникальные витрины с учетом работы складов",
//                "url" => "unique_showcases.php?lang=" . LANGUAGE_ID,
//            ),
//        )
//    );
//    return $aMenu;
//}

if ($APPLICATION->GetGroupRight("likee.admin") != "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_services",
        "section" => "Orgasmcity",
        "sort" => 50,
        "text" => "Orgasmcity",
        "icon" => "sys_menu_icon",
        "page_icon" => "sys_page_icon",
        "items_id" => "likee_respect",
        "items" => array(
            array(
                "text" => "Настройки",
                "url" => "likee_options.php?lang=" . LANGUAGE_ID,
            ),
            array(
                "text" => "Кеш каталога",
                "url" => "catalog_cache.php?lang=" . LANGUAGE_ID,
            ),
//            array(
//                "text" => "Филиалы и склады",
//                "items" => array(
//                    array(
//                        "text" => "Настройки",
//                        "url" => "branches_and_storages.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "(вер 3) Привязка местоположений к филиалам",
//                        "url" => "location2branch_new.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "(вер 2) Привязка местоположений к филиалам",
//                        "url" => "location2branch.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Тестовый каталог",
//                        "url" => "test_catalog.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Местоположения, которые не используют филиальную цену",
//                        "url" => "branchless_locations.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Удаление местоположений",
//                        "url" => "delete_locations.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Скидки по бонусной программе",
//                        "url" => "discounts_rules.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Уникальные витрины без учета работы складов",
//                        "url" => "unique_showcasesWO.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Уникальные витрины с учетом работы складов",
//                        "url" => "unique_showcases.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Импорт линейных размеров и описания",
//                "url" => "product_size_import.php?lang=" . LANGUAGE_ID,
//            ),
//            array(
//                "text" => "Онлайн примерочная",
//                "items" => array(
//                    array(
//                        "text" => "Преобразовать артикулы в CSV-файл",
//                        "url" => "vendor_code_to_csv.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Установка свойства «Онлайн примерочная»",
//                        "url" => "online_try_on_set.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Сброс свойства «Онлайн примерочная»",
//                        "url" => "online_try_on_reset.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Управление промокодами",
//                "items" => array(
//                    array(
//                        "text" => "Загрузка промокодов",
//                        "url" => "coupons_upload.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Удаление промокодов",
//                        "url" => "coupons_delete.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Проверка промокодов",
//                        "url" => "coupons_check.php?lang=" . LANGUAGE_ID,
//                    ),
//                )
//            ),
//            array(
//                "text" => "Импорт roistat",
//                "url" => "roistat_import.php?lang=" . LANGUAGE_ID,
//            ),
//            array(
//                "text" => "Отправка заказов Sailplay",
//                "url" => "sailplay_orders_send.php?lang=" . LANGUAGE_ID,
//            ),
//            array(
//                "text" => "Отчёты",
//                "items" => array(
//                    array(
//                        "text" => "Отчёт по товарам",
//                        "url" => "likee_report.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Отчёт по подписчикам",
//                        "url" => "likee_subscribe.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Сортировка",
//                "items" => array(
//                    array(
//                        "text" => "Сортировка сортировок",
//                        "url" => "sorting_setup.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Импорт сортировки",
//                        "url" => "likee_sort_import.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Сброс сортировки",
//                        "url" => "likee_sort_reset.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Управление ценовыми акциями",
//                "items" => array(
//                    array(
//                        "text" => "Лист акций",
//                        "url" => "qsoft_price_share_list.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Добавление ценовой акции",
//                        "url" => "qsoft_price_share_add.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Резервирование, доставка и предзаказ",
//                "items" => array(
//                    array(
//                        "text" => "Импорт запрета резервирования",
//                        "url" => "likee_reserve.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Сброс запрета резервирования",
//                        "url" => "likee_reserve_reset.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Импорт запрета доставки",
//                        "url" => "likee_delivery.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Сброс запрета доставки",
//                        "url" => "likee_delivery_reset.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Импорт предзаказа",
//                        "url" => "rdevs_preorder.php?lang=" . LANGUAGE_ID,
//                    ),
//                    array(
//                        "text" => "Сброс предзаказа",
//                        "url" => "rdevs_preorder_reset.php?lang=" . LANGUAGE_ID,
//                    ),
//                ),
//            ),
//            array(
//                "text" => "Управление подписчиками",
//                "url" => "qsoft_subscription_list.php?lang=" . LANGUAGE_ID,
//            ),
//            array(
//                "text" => "Управление конкурсом",
//                "url" => "likee_contest.php?lang=" . LANGUAGE_ID,
//            ),
            array(
                "text" => "Способы доставки",
                "url" => "delivery_ways.php?lang=" . LANGUAGE_ID,
            ),

            array(
                "text" => "Способы оплаты",
                "url" => "payment_ways.php?lang=" . LANGUAGE_ID,
            ),

//            array(
//                "text" => "Обновить станции метро",
//                "url" => "store_metro.php?lang=" . LANGUAGE_ID,
//            ),
//
//            array(
//                "text" => "Обновить адреса магазинов",
//                "url" => "store_address.php?lang=" . LANGUAGE_ID,
//            ),
        )
    );

    return $aMenu;
}
return false;
