<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/10
 * Time: 12:34
 */

namespace app\api\controller\v2;

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
        return 'This is v2 version';

    }
}