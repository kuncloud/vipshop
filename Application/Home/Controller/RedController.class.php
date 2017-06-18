<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-13
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class RedController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		
		$this->adminDisplay();
	}
	
	public function record() {
		$extend = array(
			'where'=>array(
				'gf.uid'=>UID,
				'gf.gid'=>array('gt', 0)
			),
			'page'=>'',
			'join'=>'sys_goods g on g.id=gf.gid',
			'field'=>'gf.id, gf.create_time, g.name, gf.status',
			'order'=>'gf.create_time desc'
		);
		parent::lists('Gift_record gf', false, $extend);
	}
	
	public function exchange(){
		$id = Param('id');
		$model = D('Order');
		$res = $model->gift($id);
		
		$err = -1;
		$msg = '';
		$url = '';
		if ($res!=-1){
			$url = U('Order/confirm', 'id='.$res);
			$err = 0;
		} else {
			$msg = $model->getError();
		}
		echo json_encode(array('status'=>$err, 'msg'=>$msg, 'url'=>$url));die;
	}
	
	public function open(){
		$model = D('Gift');
		$gift = $model->open();
		if ($gift == 1){
			$return = array('status'=>1, 'msg'=>$model->getError());
		} else {
			$info = M('Goods')->find($gift['gid']);
			$return = array('status'=>0, 'info'=>$info);
		}
		echo json_encode($return);die;
	}
	
}