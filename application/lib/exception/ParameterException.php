<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/15
 * Time: 16:28
 */

namespace app\lib\exception;


class ParameterException extends BaseException
{
    public $code = 400;
    public $msg = '参数错误';
    public $errorCode = 10000;

}