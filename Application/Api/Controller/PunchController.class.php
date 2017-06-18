<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-19
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class PunchController extends ApiBaseController{
	
	public function punch(){
		$model = D($this->model);
		
		switch ($this->_method){
			case 'get':
				$info = $model->info();
				$data = array('err_code'=>0, 'punched'=>$info);
				break;
			case 'post':
				$res = $model->punch();
				$data = array('err_code'=>$res, 'err_msg'=>$model->getError());
				break;
		}
		$this->response($data);
	}
	
}