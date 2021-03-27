<?php
/**
 * Project: respect
 * Date: 12.01.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */
namespace Likee\Exchange;
/**
 * Класс для работы с XML. Содержит методы для конвертации xml в массив и массива в xml
 *
 * @package Likee\Exchange
 */
class Helper
{
    /**
     * Конвертирует массив в xml
     *
     * @param array $array Массив для конвертации
     * @param \SimpleXMLElement $xml_user_info Итоговый xml
     * @param string|bool $parent Родительский элемент
     *
     */
    public static function array2xml($array, &$xml_user_info, $parent = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml_user_info->addChild("$key");
                    self::array2xml($value, $subnode, $key);
                } else {
                    if ($parent == 'products')
                        $subnode = $xml_user_info->addChild("product");
                    else if ($parent == 'discounts')
                        $subnode = $xml_user_info->addChild("discount");
                    else
                        $subnode = $xml_user_info->addChild("item$key");

                    self::array2xml($value, $subnode, $key);
                }
            } else {
                $xml_user_info->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

    /**
     * Конвертирует xml в массив
     *
     * @param \SimpleXMLElement $xmlObject Исходный xml
     * @param array $out Массив, который будет содержать результат
     * @return array Полученный массив
     *
     */
    static function xml2array($xmlObject, $out = [])
    {
        foreach ((array)$xmlObject as $index => $node) {
            $out[$index] = (is_object($node) || is_array($node)) ? self::xml2array($node) : $node;
            unset($xmlObject[$index]);
        }
        return $out;
    }
}