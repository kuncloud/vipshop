<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-24
 * charset : UTF-8
 */
namespace Api\Controller;
use Think\Controller\RestController;

class AccountController extends RestController{
	
	public function reg(){
		$model = D('Member');
		define('CID', $_POST['cid']);
		$res = $model->reg();
		$data = array(
			'err_code'=>$res,
			'err_msg'=>$model->getError()
		);
		$this->response($data);
	}
	
	// 转消费
	public function cons(){
		$cid = Param('cid');
		$phone = Param('phone');
		$cons = Param('cons');
		$model = D('Member');
		define('CID', $cid);
		
		$where = array(
			'phone'=>$phone
		);
		$scale = get_config(CONFIG_SCALE);
		$jf = intval(floatval($cons) / intval($scale['val'][1]) * intval($scale['val'][0]));
		$res = -1;
		
		$info = $model->where($where)->find();
		if (!$info){
			$_POST['psw'] = 123456;
			$res = $model->reg();
			
			if ($res == 0){
				$info = $model->where($where)->find();
			} 
		}
		if ($info){
			$res = 0;
			$model->where(array('id'=>$info['id']))->setInc('jf', $jf);
			$uid = $info['id'];
			$title = '屏碎险转消费';
			$openid = $info['openid'];
			// 积分记录
			$data = array(
				'cid'=>$cid,
				'pid'=>$_POST['pid'],
				'uid'=>$uid,
				'jf'=>$jf,
				'title'=>$title,
				'create_time'=>time()
			);
			M('Record')->add($data);
			// 模板消息
			$openid and jf_msg($uid, $openid, $jf, $title);
		}
		
		$data = array(
			'err_code'=>$res,
			'err_msg'=>$model->getError()
		);
		$this->response($data);
	}
	
	protected function response($data, $type='json', $code=200) {
		parent::response($data, $type, $code);
	}
	
}