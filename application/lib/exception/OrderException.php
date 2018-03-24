<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/3/3
 * Time: 16:17
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 80000;
}