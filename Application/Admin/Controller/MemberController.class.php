<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-3-30
 * charset : UTF-8
 */
namespace Admin\Controller;
use Common\Controller\AdminBaseController;
use Common\Logic\ApiLogic;

class MemberController extends AdminBaseController {
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function index(){
		$username = Param('username');
		$this->assign('username', $username);
		$this->assign('url', U('load', "username=$username"));
		$this->adminDisplay();
	}
	
	public function load(){
		$username = Param('username');
		$where['pid'] = UID;
		$where['cid'] = CID;
		if ($username) {
			$username = urldecode($username);
			$where['_complex'] = array(
				'username' => array('like', "%$username%"),
				'nickname' => array('like', "%$username%"),
				'_logic' => 'or'
			);
		}
		$extend = array('where'=>$where);
		$lists = parent::lists($this->model, true, $extend);
	}
	
	public function info(){
		$id = Param('id');
		$info = D($this->model)->find($id);
		$this->assign('info', $info);
		
		$apiLogic = new ApiLogic();
		$employ = $apiLogic->getEmploy($info['pid']);
		$this->assign('employ', dv($employ['info']['user'], ''));
		
		$recordModel = M('Record');
		$in = $recordModel->where(array('uid'=>$id, 'jf'=>array('egt', 0)))->sum('jf');
		$out = $recordModel->where(array('uid'=>$id, 'jf'=>array('lt', 0)))->sum('jf');
		$this->assign('in', $in);
		$this->assign('out', $out);
		$this->adminDisplay();
	}
	
	public function add(){
		if (IS_POST){
			$model = D('Activity');
			$res = $model->activity();
			echo json_encode(array('status'=>$res, 'url'=>U('index'), 'msg'=>$model->getError()));die;
		} else {
			$where = array(
				'cid'=>CID,
				'status'=>1,
				'share'=>0
			);
			$lists = M('Activity')->where($where)->getField('id, name, max, min');
			$this->assign('lists', $lists);
			$this->assign('json', json_encode($lists));
			$this->adminDisplay();
		}
	}
	
	public function jf(){
		$uid = Param('uid');
		$type = Param('type');
		$this->assign('url', U('load_jf', "type=$type&uid=$uid"));
		$this->adminDisplay();
	}
	
	public function load_jf(){
		$uid = Param('uid');
		$type = Param('type');
		$where['uid'] = $uid;
		if ($type == 'in') $where['jf'] = array('egt', 0);
		if ($type == 'out') $where['jf'] = array('lt', 0);
		$extend = array('where'=>$where);
		$lists = parent::lists('Record', true, $extend, true);
		if ($lists){
			foreach ($lists as &$v){
				$v['create_time'] = timetostr($v['create_time']);
			}
		}
		echo json_encode(array('status'=>0, 'lists'=>$lists));die;
	}
	
}