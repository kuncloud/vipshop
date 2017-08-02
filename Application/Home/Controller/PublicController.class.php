<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-5-4
 * charset : UTF-8
 */
namespace Home\Controller;
use Think\Controller;
use Common\Logic\ApiLogic;
use Common\Logic\WxLogic;

class PublicController extends Controller{
	
	public function _initialize(){
		$cid = dv(Param('cid', session('cid')));
		define('CID', $cid);
		$this->assign('cid', $cid);
	}
	
	public function login(){
		if (IS_POST){
			$user = Param('user');
			$psw = Param('psw');
			$where['phone'] = $user;
			$model = M('Member');
			
			if ($info = $model->where($where)->find()){
				if (encrypt($psw, $info['rand']) == $info['psw']){
					$status = 0;
					$msg = '登录成功';
					
					session('uid', $info['id']);
					session('cid', $info['cid']);
					session('openid', $info['openid']);
				} else {
					$status = -2;
					$msg = '用户名或密码错误';
				}
			} else {
				$status = -1;
				$msg = '用户不存在';
			}
			echo json_encode(array('status'=>$status, 'msg'=>$msg, 'url'=>U('Index/index')));die;
		} else {
			if (session('uid')) redirect(U('Index/index'));
			$this->display();
		}
	}
	
	public function reg(){
		if (IS_POST){
			$model = D('Member');
			$res = $model->reg();
			echo json_encode(array('status'=>$res>0 ? 0 : -1, 'msg'=>$model->getError(), 'url'=>U('login', 'cid='.Param('cid'))));die;
		} else{
			if ($openid = session('openid')){
				$wxLogic = new WxLogic();
				$info = $wxLogic->get_user_info($openid);
				$this->assign('wxinfo', $info);
			}
			
			$logic = new ApiLogic();
			$cid = Param('cid');
			$stores = $logic->getStoreByCid($cid);
			$this->assign('stores', $stores['lists']);
			$this->assign('mobile_type', mobile_type());
			$this->display();
		}
	}
	
	public function employs(){
		$sid = Param('sid');
		$logic = new ApiLogic();
		$employs = $logic->getEmploysBySid($sid);
		echo json_encode($employs);die;
	}
	
	public function logout(){
		session('uid', null);
// 		session('cid', null);
		session('openid', null);
		
		echo json_encode(array('status'=>0, 'msg'=>'已退出'));die;
	}
	
}