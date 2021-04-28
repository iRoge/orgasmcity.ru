<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

class Dadata
{

    private $base_url = "https://suggestions.dadata.ru/suggestions/api/4_1/rs/";
    private $token;
    private $handle;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function init()
    {
        $this->handle = curl_init();
        curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Accept: application/json",
            "Authorization: Token " . $this->token,
        ));
        curl_setopt($this->handle, CURLOPT_POST, 1);
    }

    public function close()
    {
        curl_close($this->handle);
    }

    private function executeRequest($url, $data)
    {
        curl_setopt($this->handle, CURLOPT_URL, $url);
        if ($data != null) {
            curl_setopt($this->handle, CURLOPT_POST, 1);
            curl_setopt($this->handle, CURLOPT_POSTFIELDS, json_encode($data));
        } else {
            curl_setopt($this->handle, CURLOPT_POST, 0);
        }
        $result = $this->exec();
        $result = json_decode($result, true);
        return $result;
    }

    private function exec()
    {
        $result = curl_exec($this->handle);
        $info = curl_getinfo($this->handle);
        if ($info['http_code'] == 429) {
            throw new TooManyRequests();
        } elseif ($info['http_code'] != 200) {
            throw new Exception('Request failed with http code ' . $info['http_code'] . ': ' . $result);
        }
        return $result;
    }


    public function geolocate($name, $lat, $lon, $radiusMeters = 100)
    {
        $url = $this->base_url . "geolocate/$name";
        $data = array(
            "lat" => $lat,
            "lon" => $lon,
            "radius_meters" => $radiusMeters,
            'count' => 1
        );
        $response = $this->executeRequest($url, $data);
        return $response["suggestions"];
    }

    public function suggest($name, $query)
    {
        $url = $this->base_url . "suggest/$name";
        $response = $this->executeRequest($url, ['query' => $query, 'count' => 1]);
        return $response["suggestions"];
    }
}

function translit($str, $lang, $params = array())
{
    static $search = array();

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

    $len = mb_strlen($str);
    $str_new = '';
    $last_chr_new = '';

    for ($i = 0; $i < $len; $i++) {
        $chr = mb_substr($str, $i, 1);

        if (preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || mb_strpos($params["safe_chars"], $chr)!==false) {
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

        if (mb_strlen($chr_new)) {
            if ($params["change_case"] == "L" || $params["change_case"] == "l") {
                $chr_new = ToLower($chr_new);
            } elseif ($params["change_case"] == "U" || $params["change_case"] == "u") {
                $chr_new = ToUpper($chr_new);
            }

            $str_new .= $chr_new;
            $last_chr_new = $chr_new;
        }

        if (mb_strlen($str_new) >= $params["max_len"]) {
            break;
        }
    }

    return $str_new;
}


function createTmpFile($count)
{
    $data = serialize(['count' => $count]);
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/update_store_metro.tmp', $data);
}

if ($_REQUEST['action'] == 'start') {
    $token = COption::GetOptionString('likee', 'dadata_token', '');
    $address = new Dadata($token);
    $arFilter = [];
    $arFilter['ACTIVE'] = 'Y';
    $arFilter[] = ['LOGIC' => 'OR',
        ['!GPS_N' => false],
        ['!GPS_S' => false],
        ];
    $array = CCatalogStore::GetList(
        ['SORT' => 'ASC'],
        $arFilter,
        false,
        false,
        [
                'UF_METRO_DADATA',
                'UF_METRO_CODE',
            ]
    );
    $address->init();
    $i = 0;
    while ($el = $array->Fetch()) {
        $ress = $address->geolocate("address", $el['GPS_N'], $el['GPS_S']);
        $metro = $address->suggest("address", $ress[0]['value'])[0]['data']['metro'];
        if ($metro) {
            // цвет ветки метро
            foreach ($metro as $key => $item) {
                $metro[$key]['color'] = $address->suggest("metro", $item['name'])[0]['data']['color'];
            }
            $metroCode = translit($item['name'], 'ru', []);
            $metro = json_encode($metro); // подготовка массива
            if ($el['UF_METRO_DADATA'] != $metro || $el['UF_METRO_CODE'] != $metroCode) {
                $el['UF_METRO_CODE'] = $metroCode;
                $el['UF_METRO_DADATA'] = $metro;
                CCatalogStore::update($el['ID'], $el);
                global $DB, $USER_FIELD_MANAGER;
                $USER_FIELD_MANAGER->Update("CAT_STORE", $el['ID'], $el); // обновляем свойства
            }
        }
        $i++;
        createTmpFile($i);
    }
    $address->close();
    createTmpFile(0);
    exit;
}

?>
<?require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");?>
<?
$arFilter['ACTIVE'] = 'Y';
$arFilter[] = ['LOGIC' => 'OR',
    ['!GPS_N' => false],
    ['!GPS_S' => false],
];
$array = CCatalogStore::GetList(
    ['SORT' => 'ASC'],
    $arFilter,
    false,
    false,
    [
        'NAME',
        'UF_METRO_DADATA',
        'UF_METRO_CODE',
    ]
);
$total = $array->result->num_rows;
\Bitrix\Main\UI\Extension::load("ui.progressbar");
?>


    <div class="result">
        <div class="update-process">
            <div id="update-progress"></div>
        </div>
    </div>
    <div class="result-count"></div>
    <div class="adm-detail-content-item-block">
        <input type="submit" id="start_update" class="adm-btn-save" value="Обновить станции метро для складов">
    </div>

    <input type="hidden" name="lang" value="<?echo LANG?>">
    <script>
        var progressBar = new BX.UI.ProgressBar({
            color: BX.UI.ProgressBar.Color.PRIMARY,
            value: 0,
            maxValue: <?= $total ?>,
            statusType: BX.UI.ProgressBar.Status.COUNTER,
            textBefore: "Обновление станций",
            fill: true,
            column: true
        });

        $(function(){
            let progress = $('#update-progress');
            let itemsCount = <?= $total ?>;

            if(itemsCount) {
                progress.append(progressBar.getContainer());
            }

            let button = $('#start_update');

            button.on('click', function (e) {
                $('.preview').remove();
                $('.result').css('display', 'block');
                e.preventDefault();
                button.remove();
                BX.ajax.post($(location).attr('href'), 'action=start', function(response) {
                });
                processChunk();
            });

            var processChunk = function () {
                BX.ajax.post('/local/admin/store_metro_status.php', 'action=getStatus', function(response) {
                    response = JSON.parse(response);
                    if(response.count !== 0){
                        progressBar.update(Number(response.count));
                        processChunk();
                    } else {
                        progressBar.update(<?= $total ?>);
                        $('.result-count').html('<h2>Обновление завершено</h2>');
                    }
                });
            }
        });
    </script>
<? require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_admin.php");
