<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */

namespace Yangliuxin\Utils\Utils;

use EasyWeChat\Kernel\Exceptions\BadResponseException;
use EasyWeChat\Kernel\Exceptions\InvalidArgumentException;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WeChatUtils
{
    /**
     * @throws InvalidArgumentException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function sendMiniMessage($config, $data): bool
    {
        $app = new \EasyWeChat\MiniApp\Application($config);
        $api = $app->getClient();
        $accessToken = $app->getAccessToken();
        $accessToken = $accessToken->getToken();
        $result = $api->postJson('/cgi-bin/message/subscribe/send?access_token=' . $accessToken, $data);
        if ($result->isSuccessful()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function createOrderForJsApi($config, $merchantId, $appid, $description, $notifyUrl, $outTradeNo, $amount, $payer = '')
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->postJson('v3/pay/transactions/jsapi', [
            "mchid" => $merchantId,
            "out_trade_no" => $outTradeNo,
            "appid" => $appid,
            "description" => $description,
            "notify_url" => $notifyUrl,
            "amount" => [
                "total" => intval($amount),
                "currency" => "CNY"
            ],
            "payer" => [
                "openid" => $payer
            ]
        ]);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function createOrderForApp($config, $merchantId, $description, $appid, $outTradeNo, $notifyUrl, $amount)
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->postJson('v3/pay/transactions/app', [
            "mchid" => $merchantId,
            "out_trade_no" => $outTradeNo,
            "appid" => $appid,
            "description" => $description,
            "notify_url" => $notifyUrl,
            "amount" => [
                "total" => intval($amount),
                "currency" => "CNY"
            ]
        ]);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function queryOrder($config, $outTradeNo)
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->get("v3/pay/transactions/out-trade-no/{$outTradeNo}", [
            'query' => [
                'mchid' => $app->getMerchant()->getMerchantId()
            ]
        ]);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function closeOrder($config, $outTradeNo)
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->postJson("v3/pay/transactions/out-trade-no/{$outTradeNo}/close", [
            'mchid' => $app->getMerchant()->getMerchantId()
        ]);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function refund($config, $outTradeNo, $notifyRefundUrl, $outRefundNo, $amount)
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->postJson("v3/refund/domestic/refunds", [
            'out_trade_no' => $outTradeNo,
            'out_refund_no' => $outRefundNo,
            'notify_url' => $notifyRefundUrl,
            'amount' => [
                'refund' => intval($amount * 100),
                'total' => intval($amount * 100),
                'currency' => "CNY"
            ]
        ]);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws InvalidConfigException
     */
    public static function queryRefund($config, $outRefundNo)
    {
        $app = new \EasyWeChat\Pay\Application($config);
        $api = $app->getClient();
        $result = $api->get("v3/refund/domestic/refunds/{$outRefundNo}", []);
        return $result->toArray();
    }

    /**
     * @throws InvalidArgumentException
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws BadResponseException
     */
    public static function securityContent($config, $openid, $content)
    {
        $app = new \EasyWeChat\MiniApp\Application($config);

        $api = $app->getClient();
        $accessToken = $app->getAccessToken();
        $accessToken = $accessToken->getToken();
        $result = $api->postJson("wxa/msg_sec_check?access_token={$accessToken}", [
            'content' => $content,
            'version' => 2,
            'scene' => 2,
            'openid' => $openid,
        ]);
        return $result->toArray();
    }


}