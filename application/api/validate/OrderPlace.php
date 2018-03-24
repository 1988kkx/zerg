<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/2/23
 * Time: 21:46
 */

namespace app\api\validate;
use app\lib\exception\ParameterException;

class OrderPlace extends BaseValidate
{
    //客户端传过来的订单商品数据
    protected $oProducts = [
        [
            'product_id' => 1,
            'count' => 3
        ],
        [
            'product_id' => 2,
            'count' => 3
        ],
        [
            'product_id' => 3,
            'count' => 3
        ]
    ];
//数据库中的商品数据信息
    protected $products = [
        [
            'product_id' => 1,
            'count' => 2
        ],
        [
            'product_id' => 2,
            'count' => 3
        ],
        [
            'product_id' => 3,
            'count' => 3
        ]
    ];

    protected $rule = [
        'products' => 'checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger',
    ];

//checkProducts判断方法定义
    protected function checkProducts($values)
    {
        if (!is_array($values))
        {
            throw new ParameterException(
                [
                    'msg' => '商品参数不正确'
                ]);
        }

        if (empty($values))
        {
            throw new ParameterException(
                [
                    'msg' => '商品列表不能为空'
                ]);
        }

        foreach ($values as $value)
        {
            $this->checkProduct($value);
        }
        return true;
    }
//checkProduct判断方法定义
    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if(!$result){
            throw new ParameterException([
                'msg' => '商品列表参数错误',
            ]);
        }
    }
}