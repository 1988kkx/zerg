<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/18
 * Time: 16:32
 */

namespace app\lib\exception;


class ProductException extends BaseException
{
    public $code = 404;
    public $msg = '指定的商品不存在，请检查参数';
    public $errorCode = 20000;
}