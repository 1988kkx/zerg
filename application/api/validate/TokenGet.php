<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 10:07
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code'=> 'require|isNotEmpty'
    ];

    protected $message = [
        'code'=> '没有code,无法获取Token'
    ];
}