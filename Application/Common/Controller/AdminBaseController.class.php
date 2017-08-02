<?php
namespace Common\Controller;
use Common\Controller\BaseController;
use Common\Logic\ApiLogic;
/**
 * admin 基类控制器
 */
class AdminBaseController extends BaseController{
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize ();
		$this->menu = $this->menu();
		$this->meta_title = dv($this->menu[CONTROLLER_NAME][ACTION_NAME], '后台管理');
	}
	
	public function checkUser(){
		$uid = session('a_uid');
// 		if (!$uid){
// 			$openid = Param('openid');
// 			if ($openid){
// 				$apiLogic = new ApiLogic();
// 				$res = $apiLogic->login($openid);
// 				$info = $res['info'];
// 				if ($info){
// 					$uid = $info['id'];
// 					session('a_uid', $uid);
// 					session('openid', $info['openid']);
// 					session('cid', $info['cid']);
// 				}
// 			}
// 		}
		if ($uid){
	    		define('UID', $uid);
	    		define('CID', session('cid'));
	    		define('OPENID', session('openid'));
		} else {
			redirect(U('Public/login', 'cid='.Param('cid')));
		}
	}
	
// 	public function lists($model=CONTROLLER_NAME, $ajax=false, $extend=array(), $return=false){
		
// 		$default = array(
// 			'where'=>array('cid'=>CID)
// 		);
// 		$default = array_merge($default, $extend);
// 		return parent::lists($model, $ajax, $default, $return);
// 	}
	
	public function menu(){
		return array(
			'Activity'=>array(
				'index'=>'活动管理',
				'info'=>'活动详情',
				'edit'=>'活动编辑',
			),
			'Ad'=>array(
				'index'=>'轮播图管理',
				'lists'=>'轮播图管理',
				'edit'=>'轮播图编辑',
			),
			'Classify'=>array(
				'index'=>'商品类别管理',
			),
			'Config'=>array(
				'edit'=>'商城配置'
			),
			'Feedback'=>array(
				'index'=>'用户反馈',
				'info'=>'反馈详情'
			),
			'Gift'=>array(
				'index'=>'礼品管理',
				'info'=>'礼品详情',
				'add'=>'添加礼品池',
				'edit'=>'礼品编辑'
			),
			'Giftrecord'=>array(
				'index'=>'中奖记录'
			),
			'Goods'=>array(
				'index'=>'商品管理',
				'edit'=>'商品编辑',
				'standard'=>'商品规格管理',
			),
			'Index'=>array(
				'index'=>'商城管理',
			),
			'Jf'=>array(
				'index'=>'积分记录',
			),
			'Member'=>array(
				'index'=>'会员列表',
				'info'=>'会员信息',
				'add'=>'增加积分',
				'edit'=>'会员编辑',
				'jf'=>'会员积分记录',
			),
			'Order'=>array(
				'index'=>'订单管理',
			),
			'Rights'=>array(
				'index'=>'特权管理',
				'edit'=>'特权编辑',
			),
			'Standard'=>array(
				'index'=>'商品规格管理',
			),
			'Vip'=>array(
				'index'=>'会员等级管理',
				'edit'=>'会员等级编辑',
			),
		);
	}
	
}

