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


function createTmpFile($count)
{
    $data = serialize(['count' => $count]);
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/upload/tmp/update_store_address.tmp', $data);
}

if ($_REQUEST['action'] == 'start') {
    $token = COption::GetOptionString('likee', 'dadata_token', '');
    $addressDD = new Dadata($token);
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
        []
    );
    $addressDD->init();
    $i = 0;
    while ($el = $array->Fetch()) {
        $ress = $addressDD->geolocate("address", $el['GPS_N'], $el['GPS_S']);
        $address = $addressDD->suggest("address", $ress[0]['value'])[0]['value'];
        if ($address) {
            if ($el['UF_ADDRESS_DADATA'] != $address) {
                $el['UF_ADDRESS_DADATA'] = $address;
                CCatalogStore::update($el['ID'], $el);
                global $DB, $USER_FIELD_MANAGER;
                $USER_FIELD_MANAGER->Update("CAT_STORE", $el['ID'], $el); // обновляем свойства
            }
        }
        $i++;
        createTmpFile($i);
    }
    $addressDD->close();
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
        'UF_ADDRESS_DADATA',
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
        <input type="submit" id="start_update" class="adm-btn-save" value="Обновить адреса складов">
    </div>

    <input type="hidden" name="lang" value="<?echo LANG?>">
    <script>
        var progressBar = new BX.UI.ProgressBar({
            color: BX.UI.ProgressBar.Color.PRIMARY,
            value: 0,
            maxValue: <?= $total ?>,
            statusType: BX.UI.ProgressBar.Status.COUNTER,
            textBefore: "Обновление адресов",
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
                BX.ajax.post('/local/admin/store_address_status.php', 'action=getStatus', function(response) {
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
