<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class AdController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		parent::lists();
	}
	
	public function edit(){
		if (IS_POST){
			parent::edit();
		} else {
			$id = Param('id');
			$info = M('Ad')->find($id);
			$_GET['val'] = $info['corver'];
			if ($iid = $info['iid']) {
				$type = $info['type'];
				$model = M(ucfirst($type));
				$info['gname'] = $model->where(array('id'=>$iid))->getField('name');
			}
			
			$info['iid'] = dv(Param('iid'), $info['iid']);
			$info['gname'] = dv(Param('gname'), $info['gname']);
			$info['type'] = dv(Param('type'), $info['type']);
			
			$this->assign('info', $info);
			$this->adminDisplay();
		}
	}
	
	public function lists(){
		$name = Param('name');
		$id = Param('id');
		$this->assign('id', $id);
		
		$where = array('cid'=>CID);
		$name and $where['name'] = array('like', "%$name%");
		$extend = array('where'=>$where);
		switch (Param('st')){
			case 2:
				parent::lists('Activity', false, $extend);
				break;
			case 1:
			default:
				parent::lists('Goods', false, $extend);
				break;
		}
	}
	
}