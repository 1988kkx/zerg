<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/21
 * Time: 10:35
 */

return[
    'app_id' => 'wxb793a42143243085',
    'app_secret' => '7413881ebb48e7a32d4e8c90994b3a33',
    'login_url'=> "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",
//    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
//        "grant_type=client_credential&appid=%s&secret=%s",
];