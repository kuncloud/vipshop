<?php
namespace Excute\Controller;

use Think\Controller;
use Common\Logic\DateLogic;

class IndexController extends Controller{
	public function index(){
		$this->birthday();
	}
	
	private function birthday(){
		$w1 = array(
			'birthtype'=>0,
			'birth'=>date('m-d', time())
		);
		$dateLogic = new DateLogic(time());
		$time = $dateLogic->getTime();
		$w2 = array(
			'birthtype'=>1,
			'birth'=>date('m-d', $time)
		);
		$where = array(
			$w1,
			$w2,
			'_logic'=>'or'
		);
		
		$model = M('Member');
		$member = $model->where($where)->order('cid desc')->select();
		
		if ($member){
			foreach ($member as $v){
				if ( strpos($v['rights'], '4') !== false ){
					// 发送模板消息
					if ($v['openid']){
						define('CID', $v['cid']);
						birth_msg($v['openid'], $v['nickname']);
					}
				}
			}
		}
	}
}