<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-20
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class AddressController extends ApiBaseController{
	
	public function index(){
		$lists = D($this->model)->where(array('uid'=>UID))->select();
		$this->response(array('err_code'=>0, 'lists'=>$lists));
	}
	
	public function edit(){
		if (IS_POST){
			parent::edit();
		} else {
			$model = D($this->model);
			$info = $model->find(Param('id'));
			$on_time = $model::TIME;
			
			$data = array('err_code'=>0, 'info'=>$info, 'on_time'=>$on_time);
			$this->response($data);
		}
	}
	
}