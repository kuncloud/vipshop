<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class StandardController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$data = D('Standard')->where(array('cid'=>CID))->select();
// 		$data = parent::lists(CONTROLLER_NAME, false, array(), true);
		$lists = \Org\Nx\Data::channelLevel($data, 0, '', 'id', 'pid');
		$this->assign('lists', $lists);
// 		dump($lists);die;
		$this->adminDisplay();
	}
	
}