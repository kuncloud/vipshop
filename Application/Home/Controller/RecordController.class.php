<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-16
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class RecordController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$jf = M('Member')->where(array('id'=>UID))->getField('jf');
		$this->assign('jf', $jf);
		$this->assign('load', U('load'));
		$this->adminDisplay();
	}
	
	public function load(){
		$where = array('uid'=>UID);
		$extend = array('where'=>$where);
		$lists = parent::lists($this->model, false, $extend, true);
		if ($lists){
			foreach ($lists as &$v){
				$v['create_time'] = timetostr($v['create_time']);
			}
		}
		unset($v);
		echo json_encode(array('status'=>0, 'lists'=>$lists));die;
	}
	
}
