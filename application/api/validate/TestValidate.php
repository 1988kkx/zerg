<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/10
 * Time: 13:07
 */

namespace app\api\validate;

use think\Validate;

class TestValidate extends  Validate
{
    //定义验证器示例
    protected $rule = [
        'name'=>'require|max:10',
        'email'=>'email'
    ];


}