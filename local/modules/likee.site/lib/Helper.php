<?php
/**
 * User: Azovcev Artem
 * Date: 05.03.17
 * Time: 0:26
 */
namespace Likee\Site;

use Bitrix\Main\Loader;

/**
 * Класс помощник. Содержит вспомогательные методы
 *
 * @package Likee\Site
 */
class Helper
{
    /**
     * Склонение слова
     *
     * @param $n
     * @param string $f1 строка для числа 1
     * @param string $f2 строка для числа 2
     * @param string $f5 строка для числа 5
     * @return string Результат функции
     */

    public static function strMorph($n, $f1, $f2, $f5)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20)
            return $f5;
        if ($n1 > 1 && $n1 < 5)
            return $f2;
        if ($n1 == 1)
            return $f1;
        return $f5;
    }

    /**
     * Определяем AJAX запрос
     *
     * @return bool True если запрос типа Ajax
     */
    public static function isAjax()
    {
        return ($_REQUEST['ajax'] == 'y' || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }


    /**
     *  Останавливает выполнение приложения
     */
    public static function stopApplication()
    {
        global $APPLICATION;
        $APPLICATION->FinalActions();
        exit;
    }

    /**
     * Возращает ссылку для редактирования элемента в инфоблоке
     *
     * @param integer $iElementId Id элемента
     * @param bool $bOnlyLink Возвратить только ссылку
     *
     * @return string ссылка на редактирование
     */
    public static function getEditUrl($iElementId, $bOnlyLink = false)
    {
        global $USER;

        if (!$USER->isAdmin())
            return '';

        $rsItem = \CIBlockElement::GetByID($iElementId);

        if ($arItem = $rsItem->Fetch()) {
            $sLink = '/bitrix/admin/' . \CIBlock::GetAdminElementEditLink($arItem['IBLOCK_ID'], $arItem['ID']);

            if ($bOnlyLink) {
                return $sLink;
            } else {
                return '<a target="_blank" href="' . $sLink . '">edit</a>';
            }
        }

        return '';
    }

    /**
     * Возвращает embed видео с youtube
     *
     * @param string $sVideoUrl Ссылка на видео в Youtube
     * @return array|bool
     */
    public static function getYouTubeOEmbed($sVideoUrl)
    {
        $sVideoUrl = trim($sVideoUrl);
        $arResult = false;

        $obCache = new \CPHPCache();
        $iCacheTime = 604800;
        $sCachePath = '/youtubeoembed';

        if ($obCache->InitCache($iCacheTime, $sVideoUrl, $sCachePath)) {
            $ar = $obCache->GetVars();
            $arResult = $ar['RESULT'];
        } elseif ($obCache->StartDataCache($iCacheTime, $sVideoUrl, $sCachePath)) {
            if (filter_var($sVideoUrl, FILTER_VALIDATE_URL)) {
                $json = file_get_contents('http://www.youtube.com/oembed?format=json&url=' . $sVideoUrl);
                $arResult = array_change_key_case(json_decode($json, true), CASE_UPPER);
                //добавляем js api
                $arResult['HTML'] = preg_replace('/(src\=\")(.+?)(\")/', '$1$2&enablejsapi=1$3', $arResult['HTML']);
                $arResult['URL'] = $sVideoUrl;
            }
            $obCache->EndDataCache(array('RESULT' => $arResult));
        }

        return $arResult;
    }

    /**
     * Возвращает полное имя сервера
     *
     * @return string Имя сервера
     */
    public static function getFullServerName()
    {
        $sServerName = 'http';

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $sServerName .= 's';
        }

        $sServerName .= '://' . $_SERVER['SERVER_NAME'];

        if ($_SERVER['SERVER_PORT'] != '80') {
            $sServerName .= ':' . $_SERVER['SERVER_PORT'];
        }

        return $sServerName;
    }

    /**
     * Возвращает путь до файла в котором объявлен класс.
     *
     * Полезно при работе с недокументированным API, например с bitrix 16
     *
     * @param string|object $obObject Строка с именем класса или экземпляр объекта класса
     * @return string Путь до файла
     */
    public static function getClassFile($obObject)
    {
        $obReflection = new \ReflectionClass($obObject);
        return $obReflection->getFileName();
    }

    /**
     * Вычесляет процент скидки
     *
     * @param int $iPrice Цена
     * @param int $iDiscountPrice Цена со скидкой
     * @return int Процент скидки
     *
     */
    public static function calculateDiscountPercent($iPrice, $iDiscountPrice)
    {
        return 100 - round($iDiscountPrice * 100 / $iPrice);
    }

    /**
     * Возвращает путь картинки с измененными размерами
     *
     * @param int|array $ID Id картинки или массив, результат метода \CFile::GetFileArray()
     * @param int $iWidth Новая ширина
     * @param int $iHeight Новая высота
     * @param bool $bExact Картинка по точным размерам, обрезая лишнее
     * @return bool|string Путь до картинки
     */
    public static function getResizePath($ID, $iWidth, $iHeight, $bExact = false)
    {
        if (empty($ID))
            return false;

        if ($bExact) {
            $iResizeType = BX_RESIZE_IMAGE_EXACT;
        } else {
            $iResizeType = BX_RESIZE_IMAGE_PROPORTIONAL_ALT;
        }

        $arResize = \CFile::ResizeImageGet($ID, array('width' => $iWidth, 'height' => $iHeight), $iResizeType, false);

        return $arResize['src'] ?: false;
    }

    /**
     * Добавляет класс к тэгу body
     *
     * @param string $NewClass Имя класса
     */
    public static function addBodyClass($NewClass = '')
    {
        global $APPLICATION;
        $sClass = $APPLICATION->GetPageProperty('BODY_CLASS');
        $APPLICATION->SetPageProperty('BODY_CLASS', trim($sClass . ' ' . $NewClass));
    }

    /**
     * Минимизирует HTML
     *
     * @param string $sHtml HTML код для минимизации
     * @return string Минимизированный код
     */
    public static function minHTML($sHtml = '')
    {
        $sHtml = preg_replace('/(?:(?<=\>)|(?<=\/\>))\s+(?=\<\/?)/', '', $sHtml);

        if (strpos($sHtml, '<pre') === false) {
            $sHtml = preg_replace('/\s+/', ' ', $sHtml);
        }

        $sHtml = preg_replace('/[\t\r]\s+/', ' ', $sHtml);
        $sHtml = preg_replace('/<!(--)([^\[|\|])^(<!-->.*<!--.*-->)/', '', $sHtml);
        $sHtml = preg_replace('/\/\*.*?\*\//', '', $sHtml);

        return $sHtml;
    }

    /**
     * Возвращает пустую картинку
     *
     * @param integer $iWidth Ширина картинки
     * @param integer $iHeight Высота картинки
     * @return string Путь к картине
     */
    public static function getEmptyImg($iWidth = 325, $iHeight = 325)
    {
        $sPath = SITE_TEMPLATE_PATH . '/images/caps';

        if (\CSite::InDir('/catalog/muzhskie/')) {
            $sPath .= '/man';
        } else {
            $sPath .= '/woman';
        }

        if ($iWidth == 650 && $iHeight == 650) {
            $sPath .= '/650_650.png';
        } else {
            $sPath .= '/325_325.png';
        }

        return $sPath;
        return "http://placehold.it/{$iWidth}x{$iHeight}";
    }

    /**
     * Перевод цвета из rgb в hex
     *
     * @param string $rgb Цвет в rgb через запятую
     * @return string Цвет hex
     */
    public static function rgb2hex($rgb)
    {
        $rgb = explode(',', $rgb);

        $hex = '#';
        $hex .= str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

        return $hex;
    }
}