<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/28
 * Time: 16:27
 */

namespace app\api\model;


class UserAddress extends BaseModel
{
    protected $hidden =['id', 'delete_time', 'user_id'];
}