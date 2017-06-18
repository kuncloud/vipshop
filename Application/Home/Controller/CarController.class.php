<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class CarController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		
		$extend = array(
			'field'=>'c.id, c.standard, c.count, c.grid, g.name, g.corver, g.id gid, g.price',
			'join'=>'sys_goods g on g.id=c.gid',
			'order'=>'c.create_time desc',
			'where'=>array('c.cid'=>CID, 'c.uid'=>UID)
		);
		parent::lists('Car c', false, $extend);
	}
	
	public function idecr(){
		$id = Param('id');
		$count = Param('count');
		$res = M($this->model)->where(array('id'=>$id))->setField('count', $count);
		echo json_encode(array('status'=> $res ? 0 : -1));die;
	}
	
	public function add(){
		$model = D($this->model);
		$res = $model->add();
		
		$return = array('status'=>$res, 'msg'=>$model->getError());
		echo json_encode($return);die;
	}
	
	public function edit(){
		if (IS_POST){
			$where = array(
				'uid'=>UID,
				'sids'=>$_POST['sids'],
			);
			$model = M('Car');
			if ($model->where($where)->find()){
				$res = $model->where($where)->setInc('count', $_POST['count']);
				echo json_encode(array('status'=>$res ? 0 : -1, 'msg'=>$model->getError()));die;
			} else {
				parent::edit();
			}
		}
	}
	
}