<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/2/25
 * Time: 9:35
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;
use think\Exception;
use think\Log;

class Pay extends BaseController
{
    //预订单只有用户可以访问，管理员不可以访问的。
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];
    //请求预订单信息。预订单是API到微信服务器去生成一个微信要求的订单，而非自己可以管理的自由的订单。
    public function getPreOrder($id = '')
    {
        (new IDMustBePostiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    //断点调试微信支付回调用
    public function redirectNotify()
    {
        //通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒

        //1. 检查库存量，超卖
        //2. 更新这个订单的status状态
        //3. 减库存
        // 如果成功处理，我们返回微信成功处理的信息。否则，我们需要返回没有成功处理。

        //特点：post；xml格式；不会携带参数
        $notify = new WxNotify();
        $notify->Handle();
    }

    public function receiveNotify()
    {
        //微信的通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒

        //1. 检查库存量，超卖
        //2. 更新这个订单的status状态
        //3. 减库存
        // 如果成功处理，我们返回微信成功处理的信息。否则，我们需要返回没有成功处理。

        //首先要明白微信调用开发者服务器接口的参数和特点
        //特点：post(微信会通过post方式调用服务器接口)；微信携带的参数是xml格式；url路由地址不携带查询参数。
//        Log::('aaaaaaaaaaaaaaaaaaaaaa');
//        throw new Exception('asdfasdfasdfsdf');
//        $notify = new WxNotify();
//        $notify->Handle();

        $xmlData = file_get_contents('php://input');
        $result = curl_post_raw('http:/z.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=9000',
            $xmlData);

    }

}