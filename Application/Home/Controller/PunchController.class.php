<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class PunchController extends HomeBaseController {
	
	public function index() {
		$jf = M('Member')->where(array('id'=>UID))->getField('jf');
		$this->assign('jf', $jf);
		// 获取打卡配置
		$this->assign('config', get_config(CONFIG_PUNCH));
		// 获取打卡状态
		$model = D(CONTROLLER_NAME);
		$day = $model->info();
		!$day and $day = $model->info(time() - 86400);
		$this->assign('day', $day);
		$this->adminDisplay();
	}
	
	public function punch(){
		$model = D(CONTROLLER_NAME);
		$res = $model->punch();
		echo json_encode(array('status'=>$res, 'msg'=>$model->getError()));die;
	}
	
}