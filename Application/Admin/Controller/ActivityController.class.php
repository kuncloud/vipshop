<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class ActivityController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$this->assign('url', U('load'));
		$this->adminDisplay();
	}
	
	public function load(){
		parent::lists($this->model, true);
	}
	
	public function info(){
		parent::edit();
	}
	
	public function edit(){
		if(IS_POST){
			parent::edit();
		} else {
			$info = M($this->model)->find(Param('id'));
			$_GET['val'] = $info['logo'];
			$this->assign('info', $info);
			$this->adminDisplay();
		}
	}
	
}