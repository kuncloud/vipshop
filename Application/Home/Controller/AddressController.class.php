<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class AddressController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->model = CONTROLLER_NAME;
	}
	
	public function index() {
		$lists = D($this->model)->where(array('uid'=>UID))->select();
		$this->assign('lists', $lists);
		$this->adminDisplay();
	}
	
	public function edit(){
		if (!IS_POST){
			$this->assign('default', get_config(CONFIG_ADDRESS));
		}
		$oid = Param('oid');
		$url = $oid ? U('Order/confirm', 'id='.$oid) : '';
		parent::edit($this->model, $url);
	}
	
}