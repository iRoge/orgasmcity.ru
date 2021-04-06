<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?>


<div class="column-8 column-center">
    <div class="auth-div-full2" style="display: block;">
        <? $APPLICATION->IncludeComponent(
            "bitrix:main.register",
            "registr-desk",
            array(
                "AUTH" => "Y",
                "REQUIRED_FIELDS" => array("EMAIL", "NAME", "PERSONAL_PHONE"),
                "SET_TITLE" => "Y",
                "SHOW_FIELDS" => array(
                    0 => "NAME",
                    1 => "EMAIL",
                    2 => "PERSONAL_PHONE",
                    3 => 'PASSWORD',
                    4 => 'CONFIRM_PASSWORD',
                    5 => 'LOGIN'
                ),
                "SUCCESS_PAGE" => "",
                "USER_PROPERTY" => array(),
                "USE_BACKURL" => "Y",
                "SUBSCRIBE" => "N",
            )
        ); ?>
    </div>
</div>
