<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/18
 * Time: 17:06
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'create_time'];

    public function img(){
        return $this->belongsTo('Image', 'topic_img_id', 'id');
    }
}