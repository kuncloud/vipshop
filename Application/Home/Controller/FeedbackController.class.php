<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class FeedbackController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function edit(){
		parent::edit($this->model, U('User/index'));
	}
	
}