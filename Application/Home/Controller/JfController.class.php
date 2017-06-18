<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-13
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class JfController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$info = M('Member')->field('jf,phone')->find(UID);
		$this->assign('info', $info);
	}
	
	public function help() {
		$day = D('Punch')->info();
		$this->assign('day', $day);
		$lists = parent::lists('Vip', false, array('order'=>'jf asc'), true);
		$this->assign('lists', $lists);
		$this->adminDisplay();
	}
	
	public function gain() {
		// 获取打卡状态
		$day = D('Punch')->info();
		$this->assign('day', $day);
		$this->assign('config', get_config(CONFIG_PUNCH));
		$this->adminDisplay();
	}
	
	public function expend() {
		$this->adminDisplay();
	}
	
}