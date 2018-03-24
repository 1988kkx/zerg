<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/10
 * Time: 15:48
 */

namespace app\api\validate;


use think\Validate;

class IDMustBePostiveInt extends BaseValidate
{
    protected  $rule = [
        'id' => 'require|isPositiveInteger'
    ];

    protected $message=[
        'id' => 'id必须是整数'
    ];



}