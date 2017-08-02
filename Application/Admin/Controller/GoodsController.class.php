<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-29
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class GoodsController extends AdminBaseController {
	
	public function _initialize() {
		parent::_initialize();
	}
	
	public function index() {
		$classify = M('Classify')->where(array('cid'=>CID))->select();
		$this->assign('classify', $classify);
		$this->assign('url', U('load', 'st='.Param('st')));
		$this->adminDisplay();
	}
	
	public function load(){
		$where = array('cid'=>CID);
		switch (Param('st')){
			default:
			case 1:
				$where = array(
					'status'=>1,
					'remain'=>array('gt', 0)
				);
				break;
			case 2:
				$where = array(
					'status'=>1,
					'remain'=>array('elt', 0)
				);
				break;
			case 3:
				$where = array(
					'status'=>0,
				);
				break;
			case 4:
				if ($c = Param('cl')){
					$where = array(
						'classify'=>$c,
					);
				}
				break;
		}
		$name = Param('name');
		if ($name){
			$where['name'] = array('like', "%$name%");
		}
		$where['cid'] = CID;
		$extend = array('where'=>$where);
		parent::lists(CONTROLLER_NAME, true, $extend);
	}
	
	public function info() {
		$id = Param('id');
		$info = D($this->model)->info($id);
		$info['img'] = explode(',', $info['img']);
		$info = pre($info, 'img');
		$praised = M('Praise')->where(array('gid'=>$id, 'uid'=>UID))->count();
		
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
		$this->assign('data', $data);
		$this->assign('info', $info);
		$this->assign('praised', $praised);
		$this->adminDisplay();
	}
	
	public function standard(){
		$data = parent::lists('Standard', false, array(), true);
		$lists = \Org\Nx\Data::channelLevel($data, 0, '', 'id', 'pid');
		echo json_encode(array('lists'=>$lists));die;
	}
	
	public function edit() {
		if (IS_POST){
			$model = D($this->model);
			if ($model->create() !== false){
				$id = Param('id');
				if ($id){
					$where['id'] = $id;
					M('Goods_standard')->where(array('gid'=>$id))->delete();
					$model->where($where)->relation('_standard')->save($_POST);
					$r = 1;
				} else {
					$_POST['cid'] = CID;
					$_POST['uid'] = UID;
					$_POST['create_time'] = time();
					$r = $model->relation('_standard')->add($_POST);
				}
			}
			$res = $r ? 0 : -1;
			echo json_encode(array('status'=>$res, 'msg'=>$model->getError(), 'url'=>U('index')));die;
// 			parent::edit();
		} else {
			$where = array(
				'cid'=>CID
			);
			$classify = M('Classify')->where($where)->select();
			
			$standard = parent::lists('Standard', false, array(), true);
			$standard = \Org\Nx\Data::channelLevel($standard, 0, '', 'id', 'pid');
			
			$id = Param('id');
			if ($id){
				$info = D($this->model)->relation('_standard')->find($id);
				$_GET['val'] = $info['logo'];
				$_GET['img'] = $info['img'];
// 				$info['standard'] = explode(',', $info['standard']);
				$temp = $info['standard'];
				$temp = explode(',', $temp);
				foreach ($temp as &$v){
					$v = explode(':', $v);
				}
				unset($v);
				$info['standard'] = $temp;
// 				dump($info);die;
				$this->assign('info', $info);
// 				$this->assign('stan', explode(',', $info['standard']));
			}
			
			$this->assign('classify', $classify);
			$this->assign('standard', $standard);
			$this->adminDisplay();
		}
	}
	
	
	
}