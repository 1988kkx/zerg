<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 10:17
 */

namespace app\api\model;


class User extends BaseModel
{
    //定义user和useraddress表之间的关联关系。
    public function address()
    {
        return $this->hasOne('UserAddress', 'user_id', 'id');
    }

    public static function getByOpenID($openid)
    {
        $user = self::where('openid', '=', $openid)
            ->find();
        return $user;
    }

}