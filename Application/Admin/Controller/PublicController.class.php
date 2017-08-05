<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-5-4
 * charset : UTF-8
 */
namespace Admin\Controller;
use Think\Controller;
use Common\Logic\ApiLogic;
use Common\Logic\WxLogic;

class PublicController extends Controller{
	
	public function _initialize(){
	}
	
	public function login(){
		if (IS_POST){
			$user = Param('user');
			$psw = Param('psw');
			$apiLogic = new ApiLogic();
			$res = $apiLogic->login($user, $psw);
			$info = $res['info'];
			
			if ($info){
				$status = 0;
				$msg = '登录成功';
				
				session('a_uid', $info['id']);
				session('a_type', $info['type']);
				session('cid', $info['cid']);
				session('openid', $info['openid']);
				session('ainfo', $info);
			} else {
				$status = -1;
				$msg = $res['err_msg'];
			}
			echo json_encode(array('status'=>$status, 'msg'=>$msg, 'url'=>U('Index/index')));die;
		} else {
			if (session('a_uid')) redirect(U('Index/index'));
			$this->display();
		}
	}
	
	public function logout(){
		session('a_uid', null);
// 		session('cid', null);
		session('openid', null);
		session('a_type', null);
		
		echo json_encode(array('status'=>0, 'msg'=>'已退出'));die;
	}
	
}