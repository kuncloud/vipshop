<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-18
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class CarController extends ApiBaseController{
	
	public function index(){
		$extend = array(
			'field'=>'c.id, c.standard, c.count, g.name, g.corver, g.id gid, g.price',
			'join'=>'sys_goods g on g.id=c.gid',
			'order'=>'c.create_time desc',
			'where'=>array('c.cid'=>CID, 'c.uid'=>UID),
			'page'=>[]
		);
		$lists = parent::lists('Car c', false, $extend, true);
		$lists = pre($lists, 'corver');
		$this->response(array('err_code'=>0, 'lists'=>$lists));
	}
	
	public function add(){
		$model = D($this->model);
		if ($model->create() !== false){
			$res = $model->add();
		}
		if ($res) {
			$data = array('err_code'=>0);
		} else {
			$data = array('err_code'=>-1, 'err_msg'=>$model->getError());
		}
		$this->response($data);
	}
	
}