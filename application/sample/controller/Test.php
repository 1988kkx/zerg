<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 2018/1/9
 * Time: 17:05
 */

namespace app\sample\controller;

use think\Request;

class Test
{
    //http://localhost/zerg/public/index.php/sample/test/hello
    //用虚拟域名来替换：http://z.cn/sample/test/hello
    //修改文件E:\software\server\xampp\apache\conf\extra\httpd-vhosts.conf
    //还有C:\Windows\System32\drivers\etc\hosts

    //id方式获取路由参数
//    public function  hello($id,$name,$age)
//    {
//        echo $id;
//        echo '|';
//        echo $name;
//        echo '|';
//        echo $age;
//        //return 'hello,chengzizhen';
//    }


     //request 方式获取参数
    public function hello(Request $request)
    {
        //获取实例（静态）
//        $id = Request::instance()->param('id');
//        $name = Request::instance()->param('name');
//        $age = Request::instance()->param('age');
        //$all = input('param.');

        //$all = Request::instance()->param();
        //var_dump($all);

        //依赖注入(动态)
        $all = $request->param();
//        echo $id;
//        echo '|';
//        echo $name;
//        echo '|';
//        echo $age;
    }

}