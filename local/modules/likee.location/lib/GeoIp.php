<?php

namespace Likee\Location;

class GeoIp
{
    public function getLocation($sIP)
    {
        //$sResponse = file_get_contents("http://ipgeobase.ru:7020/geo?ip={$sIP}&json=1");
        require_once("IPGeoBase.php");
        $gb = new IPGeoBase();
        $data = $gb->getRecord($sIP);
        if (defined('BX_UTF') && BX_UTF === true && is_array($data)) {
            foreach ($data as &$d) {
                $d = iconv('windows-1251', 'utf-8', $d);
            }
        }
        if (is_array($data)) {
            return $data;
        }
        return false;
    }
}
