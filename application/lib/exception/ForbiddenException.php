<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/29
 * Time: 23:40
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;

}