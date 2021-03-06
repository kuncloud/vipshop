<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class UserController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->model = 'Member';
	}
	
	public function index() {
		$model = D($this->model);
		$uid = UID;
		$info = $model->field('username,jf,phone,vname')->find($uid);
		
		// 更新会员信息
		$model->updateVInfo($uid);
		
		$this->assign('info', $info);
		$this->adminDisplay();
	}
	
	public function about(){
		$config = get_config(CONFIG_ABOUT)['val'];
		$this->assign('content', $config);
		$this->adminDisplay();
	}
	
	public function info() {
		$model = D($this->model);
		$uid = UID;
		$info = $model->field('username,jf,phone,vname')->find($uid);
		$this->assign('info', $info);
		$this->adminDisplay();
	}
	
	public function reset() {
		$model = D($this->model);
		$uid = UID;
		if (IS_POST) {
			$res = $model->resetPsw();
			$result = array('status'=>$res, 'msg'=>$model->getError(), 'url'=>U('info'));
			echo json_encode($result);die;
		} else {
			$info = $model->field('username,jf,phone,vname')->find($uid);
			$this->assign('info', $info);
			$this->adminDisplay();
		}
	}
	
	public function address() {
		$this->adminDisplay();
	}
	
}