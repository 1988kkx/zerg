<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/3/1
 * Time: 23:19
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    //微信官方示例
    //<xml>
    //<appid><![CDATA[wx2421b1c4370ec43b]]></appid>
    //<attach><![CDATA[支付测试]]></attach>
    //<bank_type><![CDATA[CFT]]></bank_type>
    //<fee_type><![CDATA[CNY]]></fee_type>
    //<is_subscribe><![CDATA[Y]]></is_subscribe>
    //<mch_id><![CDATA[10000100]]></mch_id>
    //<nonce_str><![CDATA[5d2b6c2a8db53831f7eda20af46e531c]]></nonce_str>
    //<openid><![CDATA[oUpF8uMEb4qRXf22hE3X68TekukE]]></openid>
    //<out_trade_no><![CDATA[1409811653]]></out_trade_no>
    //<result_code><![CDATA[SUCCESS]]></result_code>
    //<return_code><![CDATA[SUCCESS]]></return_code>
    //<sign><![CDATA[B552ED6B279343CB493C5DD0D78AB241]]></sign>
    //<sub_mch_id><![CDATA[10000100]]></sub_mch_id>
    //<time_end><![CDATA[20140903131540]]></time_end>
    //<total_fee>1</total_fee>
    //<trade_type><![CDATA[JSAPI]]></trade_type>
    //<transaction_id><![CDATA[1004400740201409030005092168]]></transaction_id>
    //</xml>

    //微信支付回调通知处理方法逻辑
    public function NotifyProcess($data, &$msg)
    {
        //首先判断支付成功的情况
        if ($data['result_code'] == 'SUCCESS')
            //做库存量检测
        {
            $orderNo = $data['out_trade_no'];
            Db::startTrans();
            try
            {
                $order = OrderModel::where('order_no', '=', $orderNo)
                    ->lock(true)
                    ->find();
                //当订单状态为未支付时，进行库存量检测，调用checkOrderStock（）方法进行库存量检测。
                if ($order->status == 1)
                {
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    //如果库存量监测通过，则更新订单状态为“已支付，并减库存。（调用相应的方法）
                    if ($stockStatus['pass'])
                    {
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($stockStatus);
                    }
                    //如果库存量监测未通过，则更新订单状态为“已支付，但库存不足”
                    else
                    {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            }
            catch (Exception $ex)
            {
                Db::rollback();
                Log::error($ex);
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    //减库存
    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $singlePStatus)
        {
            //$singlePStatus['count']当前用户所购买商品的数量。
            //可使用模型setDec直接完成减库存操作。
            Product::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['counts']);
        }
    }

    //更新订单状态
    private function updateOrderStatus($orderID, $success)
    {
        $status = $success ?
            OrderStatusEnum::PAID :
            OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', '=', $orderID)
            ->update(['status' => $status]);
    }

}