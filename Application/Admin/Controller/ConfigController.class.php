<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class ConfigController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
		$this->model = D(CONTROLLER_NAME);
	}
	
	public function index(){
// 		parent::lists(CONTROLLER_NAME, false, array('order'=>'jf asc'));
		$this->adminDisplay();
	}
	
	public function edit(){
		if (IS_POST){
			$this->model->setConfig($_POST);
			if ($this->model->getError()){
				$return = array('status'=>1, 'msg'=>$this->model->getError());
			} else {
				$return = array('status'=>0, 'url'=>__SELF__);
			}
			echo json_encode($return);die;
		} else {
			$gift = M('Gift')->where(array('cid'=>CID, 'pid'=>0))->select();
			$config = $this->model->getConfig();
			$this->assign('info', $config);
			$this->assign('gift', $gift);
			$this->adminDisplay();
		}
	}
	
}