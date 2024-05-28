<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */

namespace Yangliuxin\Utils\Utils;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use OSS\Core\OssException;
use OSS\Http\RequestCore_Exception;
use OSS\OssClient;

class AliUtils
{
    /**
     * @throws ClientException|ServerException
     */
    private static function sendSms($aliAccessKeyId, $aliAccessKeySecret, $signName, $regionId, $templateCode, $mobile, $verifyCode): bool
    {
        AlibabaCloud::accessKeyClient($aliAccessKeyId, $aliAccessKeySecret)
            ->regionId($regionId)
            ->asDefaultClient();

        $code = [
            'code' => $verifyCode,
        ];

        $result = AlibabaCloud::rpc()
            ->product('Dysmsapi')
            ->scheme('https')// https | http
            ->version('2017-05-25')
            ->action('SendSms')
            ->method('POST')
            ->host('dysmsapi.aliyuncs.com')
            ->options([
                'query' => [
                    'RegionId' => $regionId,
                    'PhoneNumbers' => $mobile,
                    'SignName' => $signName,
                    'TemplateCode' => $templateCode,
                    'TemplateParam' => json_encode($code),
                ],
            ])
            ->request();
        $result = $result->toArray();
        if (strtolower($result['Code']) == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $aliAccessKeyId
     * @param $aliAccessKeySecret
     * @param $signName
     * @param $regionId
     * @param        $mobile
     * @param $verifyCode
     * @param string $template
     *
     * @return bool
     * @throws ClientException
     * @throws ServerException
     */
    public static function sendCode($aliAccessKeyId, $aliAccessKeySecret, $signName, $regionId, $mobile, $verifyCode, string $template): bool
    {
        if (!preg_match(RegularMatchUtils::REGEX_MOBILE, $mobile)) {
            return false;
        }

        if (!$verifyCode) {
            return false;
        }

        return self::sendSms($aliAccessKeyId, $aliAccessKeySecret, $signName, $regionId, $template, $mobile, $verifyCode);
    }


    /**
     * @throws RequestCore_Exception
     * @throws OssException
     */
    public static function putFileToAliOssByContent($aliAccessKeyId, $aliAccessKeySecret, $endPoint, $bucket, $content, $path): bool
    {
        $accessKeyId = $aliAccessKeyId;
        $accessKeySecret = $aliAccessKeySecret;
        $endpoint = $endPoint;
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $ossClient->putObject($bucket, ltrim($path, "/"), $content);
        return true;
    }

}