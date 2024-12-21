<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */
namespace Yangliuxin\Utils\Utils;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;

class JwtUtils
{

    public static function makeToken($uid,$jwtKey,$iss, $aud, $expireDays = '+240 hour'): string
    {
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $config = self::createJwt($jwtKey);
        $now = new DateTimeImmutable();
        $token = $tokenBuilder
            ->issuedBy($iss)
            ->permittedFor($aud)
            ->relatedTo('api')
            ->identifiedBy('token_' . time())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now->modify('+0 second'))
            ->expiresAt($now->modify($expireDays))
            ->withClaim('uid', $uid)
            ->withHeader('foo', 'bar')
            ->getToken($config->signer(), $config->signingKey());

        return $token->toString();
    }

    public static function parseToken($token, $jwtKey, $iss, $aud)
    {
        $tokenStr = $token;
        $config = self::createJwt($jwtKey);
        try {
            $token = $config->parser()->parse($token);
            assert($token instanceof UnencryptedToken);
        } catch (Exception $e) {
            return false;
        }

        // 验证签发端是否匹配
        $validate_issued = new IssuedBy($iss);
        $config->setValidationConstraints($validate_issued);
        $constraints = $config->validationConstraints();
        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return false;
        }

        // 验证客户端是否匹配
        $validate_permitted_for = new PermittedFor($aud);
        $config->setValidationConstraints($validate_permitted_for);
        $constraints = $config->validationConstraints();
        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return false;
        }

        // 验证是否过期
        $timezone = new DateTimeZone('Asia/Shanghai');
        $time = new SystemClock($timezone);
        $validate_exp = new StrictValidAt($time);
        $config->setValidationConstraints($validate_exp);
        $constraints = $config->validationConstraints();
        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return false;
        }

        // 验证令牌是否已使用预期的签名者和密钥签名
        $validate_signed = new SignedWith(new Sha256(), InMemory::base64Encoded(base64_encode($jwtKey)));
        $config->setValidationConstraints($validate_signed);
        $constraints = $config->validationConstraints();
        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $e) {
            return false;
        }

        try {
            $decode_token = $config->parser()->parse($tokenStr);
            $claims = json_decode(base64_decode($decode_token->claims()->toString()), true);
        } catch (Exception $e) {
            return false;
        }

        return $claims['uid'] ?? false;
    }

    private static function createJwt($jwtKey): Configuration
    {
        return Configuration::forSymmetricSigner(new Sha256(), InMemory::base64Encoded(base64_encode($jwtKey)));
    }
}
