<?php
return array(
//*************************************附加设置***********************************
    'SHOW_PAGE_TRACE'        => false,                           // 是否显示调试面板
    'URL_CASE_INSENSITIVE'   => false,                           // url区分大小写
    'TAGLIB_BUILD_IN'        => 'Cx,Common\Tag\My',              // 加载自定义标签
    'LOAD_EXT_CONFIG'        => 'db',       // 加载网站设置文件
    'TMPL_PARSE_STRING'      => array(                           // 定义常用路径
        '__OSS__'            => OSS_URL,
        '__PUBLIC__'         => OSS_URL.__ROOT__.'/Public',
        '__UPLOAD__'         => OSS_URL.__ROOT__.'/Upload',
        '__HOME__'           => __ROOT__.trim(TMPL_PATH,'.').'Home',
        '__HOME_CSS__'       => __ROOT__.trim(TMPL_PATH,'.').'Home/Public/css',
        '__HOME_JS__'        => __ROOT__.trim(TMPL_PATH,'.').'Home/Public/js',
        '__HOME_IMAGES__'    => OSS_URL.__ROOT__.trim(TMPL_PATH,'.').'Home/Public/images',
        '__ADMIN__'          => __ROOT__.trim(TMPL_PATH,'.').'Admin',
        '__ADMIN_CSS__'      => __ROOT__.trim(TMPL_PATH,'.').'Admin/Public/css',
        '__ADMIN_JS__'       => __ROOT__.trim(TMPL_PATH,'.').'Admin/Public/js',
        '__ADMIN_IMAGES__'   => OSS_URL.__ROOT__.trim(TMPL_PATH,'.').'Admin/Public/images',
        '__ADMIN_ACEADMIN__' => OSS_URL.__ROOT__.'/Public/statics/aceadmin',
        '__PUBLIC_CSS__'     => __ROOT__.trim(TMPL_PATH,'.').'Public/css',
        '__PUBLIC_JS__'      => __ROOT__.trim(TMPL_PATH,'.').'Public/js',
        '__PUBLIC_IMAGES__'  => OSS_URL.__ROOT__.trim(TMPL_PATH,'.').'Public/images',
        '__QINIU_IMG__'      => 'http://onvjnemxr.bkt.clouddn.com',
    ),
//***********************************URL设置**************************************
    //'MODULE_ALLOW_LIST'      => array('Home','Admin','Api','User','App'), //允许访问列表
    'URL_HTML_SUFFIX'        => '',  // URL伪静态后缀设置
    'URL_MODEL'              => 1,  //启用rewrite
//***********************************SESSION设置**********************************
    'SESSION_OPTIONS'        => array(
        'name'               => 'JFSC',//设置session名
        'expire'             => 24*3600*15, //SESSION保存15天
        'use_trans_sid'      => 1,//跨页传递
        'use_only_cookies'   => 0,//是否只开启基于cookies的session的会话方式
    ),
//***********************************页面设置**************************************
    'TMPL_EXCEPTION_FILE'    => APP_DEBUG ? THINK_PATH.'Tpl/think_exception.tpl' : './Template/default/Home/Public/404.html',
    'TMPL_ACTION_ERROR'      => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'    => TMPL_PATH.'/Public/dispatch_jump.tpl', // 默认成功跳转对应的模板文件
//***********************************缓存设置**********************************
    'DATA_CACHE_TIME'        => 7200,        // 数据缓存有效期s
    'DATA_CACHE_PREFIX'      => 'jfsc_',      // 缓存前缀
    'DATA_CACHE_TYPE'        => 'file', // 数据缓存类型,
    'WEIXINPAY_CONFIG' => array (
		'APPID' 				=> 	'wx39046badac30d1b2', // 微信支付APPID
		'MCHID' 				=> 	'1460902602', // 微信支付MCHID 商户收款账号
		'KEY' 				=> 	'kuncloudsaasthewholechinaXYZ0729', // 微信支付KEY
		'APPSECRET' 			=> 	'7c2eb848bb697eadf83d476298633a12', // 公众帐号secert
		'NOTIFY_URL'			=> 	'http://baijunyao.com/Api/WeixPay/notify/order_number/'  // 接收支付状态的连接
	),
	'QINIU_CONFIG'			=>	array(
    		'ACCESSKEY'			=>	'zSQFUggxxzD2TsIyrIDJiKCuALsan7Sotap1BBLw',
    		'SECRETKEY'			=>	'9-TIHN8lAhf4d7yTBpRuXbme4m5cG_xa1jOCcHQm',
		'IMG_URL'			=>	'http://onvjnemxr.bkt.clouddn.com/',
    ),
);
