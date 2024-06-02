<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */
namespace Yangliuxin\Utils\Utils;


class ServiceConstant
{
    const CODE_SUCCESS = 1;
    const CODE_ERROR = -1;
    const CODE_EXCEPTION = 0;
    const MSG_SUCCESS = '操作成功';
    const MSG_ERROR = '操作失败';
    const MSG_TOKEN_ERROR = '不存在的登录信息';
    const MSG_EXCEPTION = '系统异常';
    const MSG_PARAM_ERROR = '网络参数错误';
    const MSG_SIGN_ERROR = '签名失败';
    const MSG_AUTH_VALID = '请先登录';
    const MSG_USER_VALID = '用户并未注册';
    const MSG_USER_NO_DATA = '不存在的用户信息';
    const MSG_INVALID_TOKEN = '无效的登录凭证';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_OPTIONS = 'OPTIONS';
    const HTTP_METHOD_PATCH = 'PATCH';


    public static function success($data = []): array
    {
        return [
            'code' => self::CODE_SUCCESS,
            'message' => self::MSG_SUCCESS,
            'data' => $data,
            'serverTime' => date('Y-m-d H:i:s'),
        ];
    }

    public static function result($status, $message = '', $data = []): array
    {
        return [
            'code' => $status,
            'message' => $message,
            'data' => $data,
            'serverTime' => date('Y-m-d H:i:s'),
        ];
    }

    public static function error($message): array
    {
        return [
            'code' => 0,
            'message' => $message,
            'data' => [],
            'serverTime' => date('Y-m-d H:i:s'),
        ];
    }

    public static function buildCouldHandleException($code, $message): \Exception
    {
        return new \Exception(json_encode(['message' => $message], 320), $code);
    }


}
