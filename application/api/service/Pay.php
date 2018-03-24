<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/2/25
 * Time: 9:48
 */

namespace app\api\service;
use app\api\model\Order as OrderModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use think\Log;


//   extend/WxPay/WxPay.Api.php
Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pay
{
    private $orderID;
    private $orderNO;

    function __construct($orderID)
    {
        if (!$orderID)
        {
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    //处理支付的主方法，类似于order中的place

    // 调用我们的支付接口，进行支付
    // 还需要再次进行库存量检测（调用的order中的getorderstatus，通过checkOrderStock调用）
    // 服务器这边就可以调用微信的支付接口进行支付
    // 小程序根据服务器返回的结果拉起微信支付
    // 微信会返回给我们一个支付的结果（异步）
    // 成功：也需要进行库存量的检查
    // 成功：进行库存量的扣除

    //处理支付的主体方法
    public function pay()
    {
        //订单号可能根本就不存在
        //订单号确实是存在的，但是，订单号和当前用户是不匹配的
        //订单有可能已经被支付过
        //进行库存量检测

        //对请求支付的订单进行业务的合法性监测（即前三点提到的）
        $this->checkOrderValid();
        //库存量监测
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($this->orderID);
        //如果库存量监测未通过
        if (!$status['pass'])
        {
            return $status;
        }
        //如果库存量监测通过，则调动微信的预订单接口，来获取一组参数。
        return $this->makeWxPreOrder($status['orderPrice']);
    }


    private function makeWxPreOrder($totalPrice)
    {
        //openid（先拿到当前用户的openid）
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid)
        {
            throw new TokenException();
        }
        //开始调用微信预订单接口。这里我们用了微信的sdk,并且对这个sdk进行了修改（四个参数的配置）。将它下载，并放入到项目文件中。
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNO);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('便利Mall');
        $wxOrderData->SetOpenid($openid);
        //这个参数很重要，url地址用于接收微信的回调通知
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

//调用预订单接口，获取签名信息。
    private function getPaySignature($wxOrderData)
    {
        //调用\WxPayApi::unifiedOrder微信接口，获取微信返回结果，用来判断请求到的预支付订单的状态
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' ||
            $wxOrder['result_code'] != 'SUCCESS'
        )
        {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
        }

        //prepay_id
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

//为了生成客户端wx.requestPayment(OBJECT)方法所需要的一组参数，主要用了这个方法$jsApiPayData = new \WxPayJsApiPay()。
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());

        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);

        $jsApiPayData->SetPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');

        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;

        unset($rawValues['appId']);

        return $rawValues;
    }


    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id', '=', $this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }


//对订单的(pay中的)前三点进行检测
    private function checkOrderValid()
    {
        $order = OrderModel::where('id', '=', $this->orderID)
            ->find();
        if (!$order)
        {
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id))
        {
            throw new TokenException(
                ['msg' => '订单与用户不匹配',
                    'errorCode' => 10003
                ]);
        }
        if ($order->status != OrderStatusEnum::UNPAID)
        {
            throw new OrderException(
                [
                    'msg' => '订单已支付过啦',
                    'errorCode' => 80003,
                    'code' => 400
                ]);
        }
        $this->orderNO = $order->order_no;
        return true;
    }

}