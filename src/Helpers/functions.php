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