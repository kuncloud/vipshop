<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-17
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class AdController extends ApiBaseController{
	
	public function slider(){
		$model = D($this->model);
		
		$lists = $model->apiSlider();
		$data = array('err_code'=>0, 'lists'=>$lists);
		$this->response($data);
	}
	
	
}