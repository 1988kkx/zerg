<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/11
 * Time: 8:54
 */

namespace app\api\model;


use think\Db;
use think\Exception;
use think\Model;


class Banner extends BaseModel
{
    protected $hidden = ['update_time','delete_time'];
    public function items()
    {
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }

    public  static function getBannerByID($id){
        $Banner = self::with(['items','items.img'])->find($id);
        return $Banner;
    }

}