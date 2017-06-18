<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class FeedbackController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$this->adminDisplay();
	}
	
	public function load(){
		$extend = array('relation'=>'_user');
		$lists = parent::lists($this->model, true, $extend, true);
		if ($lists){
			foreach ($lists as &$v){
				$v['create_time'] = timetostr($v['create_time']);
			}
		}
		echo json_encode(array('status'=>0, 'lists'=>$lists));die;
	}
	
	public function info(){
		$id = Param('id');
		$info = D($this->model)->relation('_user')->find($id);
		$this->assign('info', $info);
		$this->adminDisplay();
	}
	
}