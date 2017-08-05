<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-19
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Common\Logic\WxJSApiLogic;
use Think\Log;

class ShareController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->model = 'Activity';
		$this->assign('cid', CID);
	}
	
	public function index() {
		$extend = array(
			'where'=>array(
				'status' => 1,
				'share' => 1,
				'cid' => CID
			),
		);
		$this->assign('share_explain', get_config(CONFIG_SHARE_EXPLAIN));
		parent::lists($this->model, false, $extend);
	}
	
	public function info() {
		$info = M($this->model)->find(Param('id'));
		$this->assign('info', $info);
		
		$jsLoigc = new WxJSApiLogic();
		$res = $jsLoigc->getSignPackage();
		Log::write("wx-debug: ".json_encode($res));
		$this->assign('sign', $res);
		$this->adminDisplay();
	}
	
	public function share(){
		$id = Param('id');
		$model = D($this->model);
		$res = $model->share($id);
		
		echo json_encode(array('status'=>$res, 'msg'=>$model->getError()));die;
	}
	
}
