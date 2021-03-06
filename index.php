<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
// 检测是否是新安装
if(file_exists("./Public/install") && !file_exists("./Public/install/install.lock")){
    // 组装安装url
    $url=$_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'index.php').'Public/install/index.php';
    // 使用http://域名方式访问；避免./Public/install 路径方式的兼容性和其他出错问题
    header("Location:http://$url");
    die;
}
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);

// 定义应用目录
define('APP_PATH','./Application/');

// 定义缓存目录
define('RUNTIME_PATH','./Runtime/');

// 定义模板文件默认目录
define("TMPL_PATH","./tpl/");

// 定义oss的url
define("OSS_URL","");

define('HTML_PATH', './HTML/');//生成静态页面的文件位置

// 配置名
define('CONFIG_KF', 'kf');
define('CONFIG_ABOUT', 'about');
define('CONFIG_GIFT', 'gift');
define('CONFIG_REG', 'reg');
define('CONFIG_PUNCH', 'punch');
define('CONFIG_SCALE', 'scale');
define('CONFIG_ADDRESS', 'address');
define('CONFIG_FARE', 'fare');
define('CONFIG_SHARE_EXPLAIN', 'share_explain');

// 微信相关
define('CONFIG_APPID', 'appid');
define('CONFIG_MCHID', 'mchid');
define('CONFIG_APPKEY', 'appkey');
define('CONFIG_APPSECRET', 'appsecret');
// 订单支付
define('CONFIG_MODULE_ORDER_PAY', 'orderpay');
// 会员加入
define('CONFIG_MODULE_MEMBER_IN', 'memberin');
// 订单信息提醒
define('CONFIG_MODULE_ORDER_CHANGE', 'orderchange');
// 积分变动
define('CONFIG_MODULE_JF_CHANGE', 'jfchange');

// 引入ThinkPHP入口文件
require './ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单


