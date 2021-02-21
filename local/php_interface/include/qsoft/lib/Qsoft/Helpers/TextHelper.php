<?php

namespace Qsoft\Helpers;

class TextHelper
{
    public function restoreText($str)
    {
        $str = mb_strtolower($str);
        $keyWrongMap = array(
            "q" => "й",
            "w" => "ц",
            "e" => "у",
            "r" => "к",
            "t" => "е",
            "y" => "н",
            "u" => "г",
            "i" => "ш",
            "o" => "щ",
            "p" => "з",
            "[" => "х",
            "{" => "х",
            "]" => "ъ",
            "}" => "ъ",
            "a" => "ф",
            "s" => "ы",
            "d" => "в",
            "f" => "а",
            "g" => "п",
            "h" => "р",
            "j" => "о",
            "k" => "л",
            "l" => "д",
            ";" => "ж",
            ":" => "ж",
            "'" => "э",
            "\"" => "э",
            "z" => "я",
            "x" => "ч",
            "c" => "с",
            "v" => "м",
            "b" => "и",
            "n" => "т",
            "m" => "ь",
            "," => "б",
            "<" => "б",
            ">" => "ю",
            "." => "ю",
        );
        return strtr($str, $keyWrongMap);
    }

    public function myTranslit($str, $lang, $params = array())
    {
        $search = array();

        if (!isset($search[$lang])) {
            $mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/js_core_translit.php", $lang, true);
            $trans_from = explode(",", $mess["TRANS_FROM"]);
            $trans_to = explode(",", $mess["TRANS_TO"]);
            foreach ($trans_from as $i => $from) {
                $search[$lang][$from] = $trans_to[$i];
            }
        }

        $defaultParams = array(
            "max_len" => 100,
            "change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
            "replace_space" => '_',
            "replace_other" => '_',
            "delete_repeat_replace" => true,
            "safe_chars" => '',
        );
        foreach ($defaultParams as $key => $value) {
            if (!array_key_exists($key, $params)) {
                $params[$key] = $value;
            }
        }

        $len = mb_strlen($str, 'utf-8');
        $str_new = '';
        $last_chr_new = '';

        for ($i = 0; $i < $len; $i++) {
            $chr = mb_substr($str, $i, 1, 'utf-8');

            if (preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || strpos($params["safe_chars"], $chr)!==false) {
                $chr_new = $chr;
            } elseif (preg_match("/\\s/".BX_UTF_PCRE_MODIFIER, $chr)) {
                if (!$params["delete_repeat_replace"]
                    ||
                    ($i > 0 && $last_chr_new != $params["replace_space"])
                ) {
                    $chr_new = $params["replace_space"];
                } else {
                    $chr_new = '';
                }
            } else {
                if (array_key_exists($chr, $search[$lang])) {
                    $chr_new = $search[$lang][$chr];
                } else {
                    if (!$params["delete_repeat_replace"]
                        ||
                        ($i > 0 && $i != $len-1 && $last_chr_new != $params["replace_other"])
                    ) {
                        $chr_new = $params["replace_other"];
                    } else {
                        $chr_new = '';
                    }
                }
            }

            if (strlen($chr_new)) {
                if ($params["change_case"] == "L" || $params["change_case"] == "l") {
                    $chr_new = ToLower($chr_new);
                } elseif ($params["change_case"] == "U" || $params["change_case"] == "u") {
                    $chr_new = ToUpper($chr_new);
                }

                $str_new .= $chr_new;
                $last_chr_new = $chr_new;
            }

            if (strlen($str_new) >= $params["max_len"]) {
                break;
            }
        }

        return $str_new;
    }
}
