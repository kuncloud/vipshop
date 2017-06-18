<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-14
 * charset : UTF-8
 */
namespace Home\Controller;
use Common\Controller\HomeBaseController;
use Qiniu\json_decode;
use OSS\Model\UploadInfo;
use Common\Logic\ApiLogic;

class OrderController extends HomeBaseController {
	
	public function _initialize() {
		parent::_initialize();
		$model = D($this->model);
		$this->assign('json_status', json_encode($model::STATUS));
		$this->assign('status', $model::STATUS);
	}
	
	public function index() {
		// 设置订单状态
		$model = D($this->model);
		$model->updateStatus(UID);
		
		parent::wxSign();
		
		$this->assign('st', Param('st'));
		$extend = array('relation'=>true);
		$st = Param('st');
		$where['uid'] = UID;
		$st and $where['status'] = $st;
		$extend['where'] = $where;
		parent::lists($this->model, false, $extend);
	}
	
	public function load(){
		$extend = array('relation'=>true);
		$st = Param('st');
		$where['uid'] = UID;
		$st and $where['status'] = $st;
		$extend['where'] = $where;
		parent::lists($this->model, true, $extend);
	}
	
	public function detail() {
		// 设置订单状态
		$model = D($this->model);
		$model->updateStatus(UID);

		parent::wxSign();
		
		$id = Param('id');
		$info = D($this->model)->relation(true)->find($id);
		$this->assign('info', $info);
		$this->adminDisplay();
	}
	
	public function confirm(){
		
		$id = Param('id');
		$info = D($this->model)->find($id);
		$this->assign('info', $info);
		
		parent::wxSign();
		
		// 店铺信息
		$logic = new ApiLogic();
		$stores = $logic->getStoreByCid(CID);
		$this->assign('stores', $stores['lists']);
		
		$aid = Param('aid');
		// 获取收货地址
		$where = array('uid'=>UID);
		$aid and $where['id'] = $aid;
		$address = M('Address')->where($where)->find();
		$fare = get_config(CONFIG_FARE);
		$scale = get_config(CONFIG_SCALE);
		$scale = intval($scale['val'][1]) / intval($scale['val'][0]);
		$this->assign('fare', $fare);
		$this->assign('scale', $scale);
		$this->assign('address', $address);
		$this->assign('aid', dv($aid, $address['id']));
		$this->adminDisplay();
	}
	
	public function ok(){
		$id = Param('id');
		$model = D($this->model);
		$r = $model->where(array('id'=>$id))->setField('status', 'ok');
		echo json_encode(array('status'=>$r ? 0 : -1, 'msg'=>$model->getError()));die;
	}
	
	public function status(){
		$id = Param('id');
		$status = Param('s');
		$model = D($this->model);
		$res = $model->setStatus($id, $status);
		echo json_encode(array('status'=>$res, 'msg'=>$model->getError()));die;
	}
	
	public function setAdd(){
		$id = Param('id');
		$model = D($this->model);
		
		$res = $model->setAdd();
		if ($res==0){
			$info = $model->find($id);
			$return = array('status'=>0);
			if ($info['total'] == 0 && $info['fare'] == 0){
				// 无需支付，直接设置状态
				$where = array('id'=>$id);
				$model->where($where)->setField('status', 'send');
				$return['url'] = U('detail', 'id='.$id);
			}
		} else {
			$return = array('status'=>1, 'msg'=>$model->getError());
		}
		echo json_encode($return);die;
	}
	
	public function wpay(){
		$model = D($this->model);
		$res = $model->wpay(Param('id'));
		if ($res == -1){
			echo json_encode(array('status'=>$res, 'msg'=>$model->getError()));die;
		} else {
			echo $res;die;
		}
	}
	
	public function payed(){
		$id = Param('id');
		$model = D($this->model);
		$res = $model->payed($id);
		echo json_encode(array('status'=>$res, 'msg'=>$model->getError(), 'url'=>U('detail', 'id='.$id)));die;
	}
	
	public function car() {
		$model = D($this->model);
		$id = $model->car2Order();
		$return = array('status'=>$id ? 0 : -1, 'id'=>$id, 'msg'=>$model->getError());
		echo json_encode($return);die;
	}
	
}