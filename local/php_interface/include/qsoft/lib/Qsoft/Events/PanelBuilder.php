<?php

namespace Qsoft\Events;


class PanelBuilder
{
    public static function handleEvent()
    {
        global $APPLICATION, $USER;

        $arUserGroups = $USER->GetUserGroupArray();

        if (!$USER->IsAdmin()) {
            unset($APPLICATION->arPanelButtons['seo']);
        }

        if ($USER->IsAdmin() || in_array(SEO_USER_GROUP_ID, $arUserGroups)) {
            $APPLICATION->AddPanelButton(array(
                "HREF" => "javascript:$.ajax({type: 'POST', url: '/local/ajax/clear_seo_cache.php', success: function () {location.reload()}});",
                // ссылка на кнопке
                "ICON" => "bx-panel-clear-cache-icon",
                "ALT" => "Сбросить кеш SEO",
                "MAIN_SORT" => 500,
                "SORT" => 10,
                "TEXT" => 'Сбросить <br> кеш SEO',
                "TYPE" => 'BIG',
            ));
        }

        if (in_array(\Functions::getEnvKey('SELLERS_GROUP_ID'), $arUserGroups)) {
            $url = "javascript:$.ajax({
                type: 'GET',
                url: '/local/ajax/find_buyer.php?SHOW_POPUP=Y',
                success: function (data) {
                Popup.show(data);
                }
            });";

            $APPLICATION->AddPanelButton([
                "HREF"      => $url,
                "ICON"      => "bx-panel-site-wizard-icon",
                "ALT"       => "Войти в аккаунт покупателя",
                "MAIN_SORT" => 500,
                "SORT"      => 10,
                "TEXT" => 'Войти в аккаунт <br> покупателя',
                "TYPE"=>'BIG',
            ]);
        }
    }
}
