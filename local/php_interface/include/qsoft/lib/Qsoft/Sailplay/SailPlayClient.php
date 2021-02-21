<?php

namespace Qsoft\Sailplay;

use stdClass;

/**
 * Class SailPlayClient
 * @package Qsoft\SailPlay
 */
class SailPlayClient
{
    /**
     * SailPlay API url
     */
    const API_URL = 'https://sailplay.ru/api/v2/';

    /**
     * Default curl options
     */
    const CURL_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Cache-Control: no-cache",
        ],
    ];

    /**
     * Auth params for SailPlay
     */
    const AUTH_PARAMS = [
        'token' => '7d9cc7fe87eccbbb96a1c8152d626e2b999aced5',
        'store_department_id' => 4943
    ];

    const LOG_PATH = '/local/logs/sailplay/curl/';

    /**
     * @param string $method
     * @param array $params
     */
    public static function makeRequest(string $method, array $params)
    {
        $options = self::CURL_OPTIONS;
        $options[CURLOPT_URL] = self::getRequestUrl($method, $params);

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return self::parseResponce($response, $err);
    }

    /**
     * @param string $method
     * @param array $params
     * @return string
     */
    protected static function getRequestUrl(string $method, array $params)
    {
        $url = self::API_URL . $method . '/?';
        $url = $url . http_build_query($params);
        self::log("Request URL : {$url}");

        return $url;
    }


    /**
     * @param $response
     * @param $err
     * @return mixed|stdClass
     */
    protected static function parseResponce($response, $err)
    {
        if ($err) {
            $result = new stdClass();
            $response->status = 'error';
            $response->message = 'cURL Error #:' . $err;

            self::log("Error. " . $response->message);

            return $result;
        }

        self::log("Response: \n\r{$response}");

        return json_decode($response);
    }

    protected static function log($message)
    {
        $file = date('Y.m.d') . '.log';
        qsoft_logger($message, $file, self::LOG_PATH);
    }
}
