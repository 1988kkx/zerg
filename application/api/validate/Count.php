<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/18
 * Time: 16:01
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];
}