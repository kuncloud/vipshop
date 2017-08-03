<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class GiftController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$where = array('cid'=>CID, 'pid'=>0);
		$lists = M($this->model)->where($where)->select();
		$lists = pre($lists, 'logo');
		$this->assign('lists', $lists);
		$this->adminDisplay();
	}
	
	public function info(){
		$pid = Param('pid');
		$where = array(
			'g.cid'=>CID,
			'g.pid'=>$pid
		);
		$join = 'sys_goods go on go.id = g.gid';
		$field = 'g.id, g.pid, g.rate, g.type, go.name, go.corver, go.stock, go.sales, go.price';
		$lists = M('Gift g')->where($where)->join($join)->field($field)->select();
		$this->assign('lists', $lists);
		$this->adminDisplay();
	}
	
	public function add(){
		parent::edit();
	}
	
	public function edit(){
		$url = U('index');
		if (!IS_POST){
			$where = array(
				'cid'=>CID,
				'status'=>1
			);
			$lists = M('Goods')->where($where)->select();
			$this->assign('lists', $lists);
			$this->assign('url', $url);
		} else {
			$id = Param('id');
			$pid = Param('pid');
			$rate = Param('rate', 0, 'int');
			$where = [ 'pid' => $pid ];
			$id and $where['id'] = ['neq', $id];
			$total = M(CONTROLLER_NAME)->where($where)->sum('rate');
			if (intval($total) + intval($rate) > 100) {
				$res = [ 'status'=>-1, 'msg'=>"概率超过100%" ];
				echo json_encode($res);die;
			}
		}
		parent::edit(CONTROLLER_NAME);
	}
	
}