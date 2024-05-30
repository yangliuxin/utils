<?php
function nl2p($str): string
{
    if (!$str) {
        return '<p></p>';
    }
    $result = '';
    foreach (explode(PHP_EOL, $str) as $key => $val) {
        $result .= '<p>' . $val . '</p>';
    }
    return $result;
}

function formatMobile($mobile): string
{
    return substr($mobile, 0, 3) . '****' . substr($mobile, -4);
}

function formatBankCardNo($no): string
{
    return substr($no, 0, 3) . '****' . substr($no, -4);
}

function str_random(int $length = 16): string
{
    $string = '';

    while (($len = strlen($string)) < $length) {
        $size = $length - $len;
        $bytes = random_bytes($size);

        $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
    }

    return $string;
}

function getIpInfo($ip){
    $get_city = 'http://whois.pconline.com.cn/ipJson.jsp?json=true&ip=' . $ip;
    try {
        $content = file_get_contents($get_city);
        $data = json_decode($content, true);
        if($data && isset($data['pro']) && isset($data['city'])){
            return ['province' => $data['pro'], 'city' => $data['city']];
        }
        return ['province' => '', 'city' => ''];
    } catch (\Exception $e) {
        return ['province' => '', 'city' => ''];
    }

}