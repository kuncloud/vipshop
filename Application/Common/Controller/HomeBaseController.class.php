<?php
namespace Common\Controller;
use Common\Controller\BaseController;
/**
 * Home基类控制器
 */
class HomeBaseController extends BaseController{
	
	/**
	 * 初始化方法
	 */
	public function _initialize(){
		parent::_initialize();
		$this->menu = $this->menu();
		$this->meta_title = dv($this->menu[CONTROLLER_NAME][ACTION_NAME], '积分商城');
	}
	
	public function checkUser(){
		$uid = session('uid');
		if ($cid = Param('cid')) session('cid', $cid);
		// 有传openid，表示微信登录，直接获取用户信息
		$openid = Param('openid');
		if ($openid){
			session('openid', $openid);
			$info = M('Member')->where(array('openid'=>$openid))->find();
			if ($info){
				$uid = $info['id'];
				session('uid', $uid);
				session('openid', $info['openid']);
				session('cid', $info['cid']);
			}
		}
		
		if ($uid){
			// 防止同一个用户进入两个商城时session冲突
			$cid = session('cid');
			if (M('Member')->where(array('cid'=>$cid, 'id'=>$uid))->find()) {
		    		define('UID', $uid);
		    		define('CID', $cid);
		    		define('OPENID', session('openid'));
			} else {
				session('uid', null);
				redirect(U('Public/login', 'cid='.Param('cid')));
			}
		} else {
			redirect(U('Public/login', 'cid='.Param('cid')));
		}
	}

	public function load_goods() {
		$page = Param('page');
		if (IS_POST) {
			$extend = array(
				'where'=>array(
					'cid'=>CID,
					'status'=>1,
					'remain'=>array('gt', 0)
				),
			);
			$data = parent::lists('Goods', false, $extend, true);
			$html = '';
			foreach ($data as $k=>$v){
				$img = img_pre() . $v['corver'];
				$url = U('Goods/info', 'id='.$v['id']);
				if ($k % 2 == 0){
					$html .="<div class='weui-cell list'>
							<a href='$url' class='weui-cell__bd'>";
				} else {
					$html .= "<a href='$url' class='weui-cell__bd second'>";
				}
				$price = '';
				$v['jf'] and $price = $v['jf'] . '积分';
				$v['price'] and $price ? $price .= ' + ¥' . $v['price'] : $price = '¥' . $v['price'];
				$html .= "
							<div>
								<img src='$img'>
							</div>
							<div class='name'>{$v['name']}</div>
							<div class='price'>$price</div>
							<div class='detail'>{$v['detail']} 剩余 <font>{$v['remain']}</font> 份</div>
						</a>";
				if ($k % 2 == 1){
					$html .= "</div>";
				}
			}
			$return['html'] = $html;
			echo json_encode($return);die;
		} else {
			$this->display();
		}
	}
	
	public function menu(){
		return array(
			'Activity'=>array(
				'index'=>'活动',
				'info'=>'活动详情',
			),
			'Address'=>array(
				'index'=>'我的地址',
				'edit'=>'修改地址',
			),
			'Car'=>array(
				'index'=>'我的购物车',
			),
			'Feedback'=>array(),
			'Goods'=>array(
				'info'=>'商品详情'
			),
			'Index'=>array(
				'index'=>'积分商城',
				'czd'=>'超值兑',
			),
			'Jf'=>array(
				'expend'=>'花积分',
				'gain'=>'赚积分',
				'help'=>'怎么赚积分',
			),
			'Order'=>array(
				'confirm'=>'确认订单',
				'detail'=>'订单详情',
				'index'=>'我的订单',
				'wpay'=>'微信支付'
			),
			'Punch'=>array(
				'index'=>'打卡',
			),
			'Record'=>array(
				'index'=>'积分记录',
			),
			'Red'=>array(
				'index'=>'抢红包',
				'record'=>'红包记录',
			),
			'Share'=>array(
				'index'=>'分享赚积分',
				'info'=>'分享赚积分',
			),
			'User'=>array(
				'index'=>'我的',
				'info'=>'个人信息',
				'about'=>'关于',
				'reset'=>'修改密码',
			),
		);
	}
}

