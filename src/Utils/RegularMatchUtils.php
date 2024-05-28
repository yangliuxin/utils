<?php
/**
 * Created by PhpStorm.
 * User: yangliuxin
 * Date: 2020/3/2
 * Time: 下午6:36
 */
namespace Yangliuxin\Utils\Utils;

class RegularMatchUtils
{
    public const REGEX_CN = '[\u4e00-\u9fa5]'; // 匹配中文字符的正则表达式

    public const REGEX_CN_EN_NUM = '^[\u4E00-\u9FA5A-Za-z0-9]+$';

    public const REGEX_CN_EN_NUM_2 = '^[\u4E00-\u9FA5A-Za-z0-9]{2,20}$'; // 中文、英文、数字但不包括下划线等符号

    public const REGEX_USERNAME = '^[a-zA-Z][a-zA-Z0-9_]{4,15}$'; // 帐号是否合法(字母开头，允许5-16字节，允许字母数字下划线)

    public const REGEX_DOUBLE_BYTE = '[^\x00-\xff]'; // 匹配双字节字符(包括汉字在内)

    public const REGEX_EMPTY_LINE = '\n[\s| ]*\r'; // 匹配空行的正则表达式

    public const REGEX_HTML = '/<(.*)>.*<\/\1>|<(.*) \/>/'; // 匹配HTML标记的正则表达式

    public const REGEX_BLANK_TRIM = '(^\s*)|(\s*$)'; // 匹配首尾空格的正则表达式

    public const REGEX_IP = '/(\d+)\.(\d+)\.(\d+)\.(\d+)/g'; // 匹配IP地址的正则表达式

    public const REGEX_EMAIL = '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/'; // 匹配Email地址的正则表达式

    public const REGEX_MOBILE = '/^1([2-9])\d{9}$/'; // 手机号

    public const REGEX_IDENTIFY = '/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/'; // 18位身份证号

    public const REGEX_IDENTIFY_15 = '/^([1-6][1-9]|50)\d{4}\d{2}((0[1-9])|10|11|12)(([0-2][1-9])|10|20|30|31)\d{3}$/'; // 15位身份证号
}
