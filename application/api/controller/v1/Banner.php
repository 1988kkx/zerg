<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/10
 * Time: 12:34
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
//use app\api\validate\TestValidate;
//use think\Validate;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use think\Exception;

class Banner
{
    /**
     * 获取指定id的banner信息
     * @url /banner/:id
     * @http GET
     * @id banner的id号
     *
     */

    public function getBanner($id)
    {
        //AOP面向切面编程
        (new IDMustBePostiveInt())->goCheck();
        $Banner = BannerModel::getBannerByID($id);
        if(!$Banner){
        throw new BannerMissException();
    }
        return $Banner;
    }
}