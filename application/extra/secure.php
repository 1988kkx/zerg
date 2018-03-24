<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 12:07
 */

return [
    'token_salt' => 'C0zz2k0k1x',
    //如果代码部署在云上，和route中的地址相同；如在本地，则需要用反向代理，并且要改一下地址：http://新域名/zerg/public/index.php/api/v1/pay/notify
    'pay_back_url' => 'http://testone.zzerg.cn/zerg/public/index.php/api/v1/pay/notify'
//    http://testone.zzerg.cn
    //Ngrok
];