<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-29
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class ClassifyController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		parent::lists();
	}
	
}