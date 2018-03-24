<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/2/25
 * Time: 11:04
 */

namespace app\lib\enum;


class OrderStatusEnum
{
    // 待支付
    const UNPAID = 1;

    // 已支付
    const PAID = 2;

    // 已发货
    const DELIVERED = 3;

    // 已支付，但库存不足
    const PAID_BUT_OUT_OF = 4;

}