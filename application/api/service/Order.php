<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/2/23
 * Time: 22:08
 */

namespace app\api\service;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\api\validate\OrderPlace;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    // 订单的商品列表，也就是客户端传递过来的products参数
    protected $oProducts;

    // 真实的商品信息（包括库存量）从数据库中查询的信息。
    protected $products;

    protected $uid;

    public function place($uid, $oProducts)
    {
        //oProducts和products 作对比
        // products从数据库中查询出来
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        //如果订单状态不通过，则返回['order_id'] = -1到客户端，表示订单创建失败了！！
        if (!$status['pass'])
        {
            $status['order_id'] = -1;
            return $status;
        }

        //如果订单状态监测通过，则开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }


    private function createOrder($snap)
    {

        Db::startTrans();
        try
        {
            $orderNo = $this->makeOrderNo();
            $order = new \app\api\model\Order();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);

            $order->save();

            //orderID是数据库表order_product中的主键
            $orderID = $order->id;
            $create_time = $order->create_time;//这个是订单创建时间

            foreach ($this->oProducts as &$p)
            {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        }
        catch (Exception $ex)
        {
            Db::rollback();
            throw $ex;
        }
    }


    //生成订单号的方法
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }


    // 生成订单快照
    private function snapOrder($status)
    {
        //快照的属性定义
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];

        //将getOrderStatus中获得的订单信息及用户信息赋值给快照
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        if (count($this->products) > 1)
        {
            $snap['snapName'] .= '等';
        }

        return $snap;
    }


    //获取用户地址方法
    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)
            ->find();
        if (!$userAddress)
        {
            throw new UserException(
                [
                    'msg' => '用户收货地址不存在，下单失败',
                    'errorCode' => 60001,
                ]);
        }
        return $userAddress->toArray();
    }

//为了在pay中可以调用库存量检测方法
    public function checkOrderStock($orderID)
    {
        $oProducts = OrderProduct::where('order_id','=',$orderID)
            ->select();
        $this->oProducts = $oProducts;

        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
        return $status;
    }

//获取订单状态，getOrderStatus包括获取订单信息及商品库存量的监测
    private function getOrderStatus()
    {
        //status 是订单信息的汇总
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];
//库存量检测，
        foreach ($this->oProducts as $oProduct)
        {
            $pStatus = $this->getProductStatus(
                $oProduct['product_id'], $oProduct['count'], $this->products
            );
            //对每一个商品的库存量判断
            if (!$pStatus['haveStock'])
            {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            $status['totalCount'] += $pStatus['counts'];

            array_push($status['pStatusArray'], $pStatus);
        }
        return $status;
    }


//获取商品信息
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        //pStatus的作用是显示历史订单中的商品信息
        $pStatus = [
            'id' => null,
            //库存量是否足够
            'haveStock' => false,
            'counts' => 0,
            'price' => 0,
            'name' => '',
            'totalPrice' => 0,
            'main_img_url' => null
        ];

        for ($i = 0; $i < count($products); $i++)
        {
            if ($oPID == $products[$i]['id'])
            {
                $pIndex = $i;
            }
        }

        if ($pIndex == -1)
        {
            // 客户端传递的product_id有可能根本不存在
            throw new OrderException(
                [
                    'msg' => 'id为' . $oPID . '的商品不存在，创建订单失败'
                ]);
        }
        else
        {
            //填写pStatus数组
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['counts'] = $oCount;
            $pStatus['price'] = $product['price'];
            $pStatus['main_img_url'] = $product['main_img_url'];
            $pStatus['totalPrice'] = $product['price'] * $oCount;

            if ($product['stock'] - $oCount >= 0)
            {
                $pStatus['haveStock'] = true;
            }
        }

        return $pStatus;
    }

    // 根据订单信息查找真实的商品信息（数据库中的商品信息）
    private function getProductsByOrder($oProducts)
    {
        //        foreach ($oProducts as $oProduct){
        //            //循环的查询数据库
        //        }
        $oPIDs = [];
        foreach ($oProducts as $item)
        {
            array_push($oPIDs, $item['product_id']);
        }
        $products = Product::all($oPIDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }

}