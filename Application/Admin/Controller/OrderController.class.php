<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class OrderController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
		$model = D($this->model);
		$this->assign('status', $model::STATUS);
		$this->assign('json_status', json_encode($model::STATUS));
	}
	
	public function index(){
		$st = Param('st');
		$this->assign('st', $st);
		$this->assign('url', U('load', "st=$st"));
		$this->adminDisplay();
	}
	
	public function load(){
		$st = Param('st');
		$consignee = Param('consignee');
		$where = array('cid'=>CID);
		$st and $where['status'] = $st;
		$consignee and $where['consignee'] = array('like', "%$consignee%");
		
		$order = 'create_time desc';
		$extend = array('where'=>$where, 'order'=>$order, 'relation'=>true);
		parent::lists($this->model, true, $extend);
	}
	
	public function status(){
		$id = Param('id');
		$status = Param('s');
		$model = D($this->model);
		$res = $model->setStatus($id, $status);
		echo json_encode(array('status'=>$res, 'msg'=>$model->getError()));die;
	}
	
}