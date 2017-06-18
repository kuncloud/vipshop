<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-18
 * charset : UTF-8
 */

namespace Api\Controller;
use Common\Controller\ApiBaseController;

class JfController extends ApiBaseController{
	
	public function gain(){
		$model = D('Record');
		
		$res = $model->addByEmploy();
		$this->response(array('err_code'=>0, 'err_msg'=>$model->getError()));
	}
}