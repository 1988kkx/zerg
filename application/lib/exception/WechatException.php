<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 11:09
 */

namespace app\lib\exception;


class WechatException extends BaseException
{
    public $code = 400;
    public $msg = '微信服务器接口调用失败';
    public $errorCode = 999;
}