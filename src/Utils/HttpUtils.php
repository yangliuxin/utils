<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */

namespace Yangliuxin\Utils\Utils;


use GuzzleHttp\Exception\GuzzleException;

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/12/30
 * Time: 2:33 PM
 */
class HttpUtils
{

    /**
     * @throws GuzzleException
     */
    public static function request($url, $requestData = [], $httpMethod = 'POST', $contentType = '', $header = [], $time_out = 20, $retry_times = 2, $async = false)
    {
        $client = new \GuzzleHttp\Client();
        $request_body = ['headers' => $header, 'time_out' => $time_out];
        if ($httpMethod == 'GET' && $requestData) {
            $request_body['query'] = $requestData;
        } elseif ($httpMethod == 'POST') {
            if ($contentType == 'json') {
                $request_body['json'] = $requestData;
            } else {
                $request_body['form_params'] = $requestData;
            }

        }
        $response = false;
        while (($response === false) && (--$retry_times >= 0)) {
            $response = $client->request($httpMethod, $url, $request_body);
        }
        if (!$response) {
            return false;
        }
        if ($response->getStatusCode() != 200) {
            return false;
        }
        $response_body = $response->getBody();
        return json_decode($response_body->getContents(), true);
    }

}