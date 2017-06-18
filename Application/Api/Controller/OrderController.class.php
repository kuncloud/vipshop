<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-19
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class OrderController extends ApiBaseController{
	
	public function info(){
		$model = D($this->model);
		
		$info = $model->relation('_detail')->find(Param('id'));
		$this->response(array('err_code'=>0, 'info'=>$info));
	}
	
	public function generate(){
		$model = D($this->model);
		
		$id = $model->car2Order();
		$err_code = $id ? 0 : -1;
		$this->response(array('err_code'=>$err_code, 'id'=>$id, 'err_msg'=>$model->getError()));
	}
	
}