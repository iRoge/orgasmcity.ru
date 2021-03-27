<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
/** @global CMain $APPLICATION */
/** @global array $aMenuLinks */

$aMenuLinks = array(
    Array(
        "Франчайзинг ",
        "/franchise/",
        Array(),
        Array("itemclass"=>"hideincart"),
        ""
    ),
    Array(
        "Оптовым клиентам",
        "/opt/",
        Array(),
        Array("itemclass"=>"hideincart"),
        ""
    ),
    Array(
        "Тендеры",
        "/tenders/",
        Array(),
        Array("itemclass"=>"hideincart"),
        ""
    ),
    Array(
        "Веб-мастерам",
        "/webmaster/",
        Array(),
        Array("itemclass"=>"hideincart"),
        ""
    )
);