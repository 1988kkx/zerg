<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 16:46
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id', 'delete_time', 'product_id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}