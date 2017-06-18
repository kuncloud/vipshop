<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;

class GoodsController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$this->model = CONTROLLER_NAME;
	}
	
	public function info() {
		$id = Param('id');
		$info = D($this->model)->info($id);
		$info['img'] = explode(',', $info['img']);
		$info = pre($info, 'img');
		$praised = M('Praise')->where(array('gid'=>$id, 'uid'=>UID))->count();
		// 兑换券
		$gift = M('Gift_record')->where(array('gid'=>$id,'uid'=>UID,'status'=>0))->find();
		// 轮播图
		$this->assign('gift', $gift);
		$logo = $info['logo'];
		$data = array();
		if ($logo){
			foreach (explode(',', $logo) as $v){
				$data[] = array('img'=>img_pre() . $v);
			}
		}
		// 商品规格
		$standard = M('Standard')->where(array('cid'=>CID))->getField('id, pid, name');
		$this->assign('standard', $standard);
		// 购物车
		$count = M('Car')->where(array('uid'=>UID))->sum('count');
		$this->assign('data', $data);
		$this->assign('count', intval($count));
		$this->assign('info', $info);
		$this->assign('praised', $praised);
		$this->adminDisplay();
	}
	
	public function praise(){
		$id = Param('id');
		$uid = UID;
		$model = D($this->model);
		$res = $model->praise($id, $uid);
		echo json_encode(array('status'=>0, 'msg'=>$model->getError()));die;
	}
}