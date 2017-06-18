<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class GiftrecordController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
		$this->model = 'Gift_record';
	}
	
	public function index(){
		$this->assign('url', U('load'));
		$this->adminDisplay();
	}
	
	public function load(){
		$where = array('gr.gid'=>array('gt', 0));
		$join = 'INNER JOIN '.C('DB_PREFIX').'goods g on g.id=gr.gid INNER JOIN '.C('DB_PREFIX').'member m on gr.uid=m.id';
		$field = 'gr.*, g.name,m.username';
		$order = 'gr.create_time desc';
		$extend = array('where'=>$where, 'join'=>$join, 'field'=>$field, 'order'=>$order);
		$lists = parent::lists('Gift_record gr', false, $extend, true);
		if ($lists){
			foreach($lists as &$v){
				$v['create_time'] = timetostr($v['create_time']);
			}
			unset($v);
		}
		echo json_encode(array('status'=>0, 'lists'=>$lists));die;
	}
	
}