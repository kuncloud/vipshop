<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class RightsController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		
		if (IS_POST){
// 			dump($_POST);die;
			$post = $_POST;
			$model = D('Vip');
			$res = $model->setRights($post);
			
			if ($model->getError()){
				$return = array('status'=>1, 'msg'=>$model->getError());
			} else {
				$return = array('status'=>0, 'url'=>__SELF__);
			}
			echo json_encode($return);die;
		} else {
			$vips = parent::lists('Vip', false, array('order'=>'jf asc'), true);
			$rights = M($this->model)->select();
// 			$rights = parent::lists(CONTROLLER_NAME, false, array(), true);
			
			$this->assign('vips', $vips);
			$this->assign('rights', $rights);
			$this->adminDisplay();
		}
	}
	
}