<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-16
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class ActivityController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->assign('cid', CID);
	}
	
	public function index() {
		$extend = array(
			'where' => array('cid'=>CID, 'status'=>1, 'share'=>0)
		);
		parent::lists(CONTROLLER_NAME, false, $extend);
	}
	
	public function info() {
		$info = M(CONTROLLER_NAME)->find(Param('id'));
		$this->assign('info', $info);
		$this->adminDisplay();
	}
	
}