<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/18
 * Time: 17:14
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定类目不存在，请检查参数';
    public $errorCode = 50000;
}