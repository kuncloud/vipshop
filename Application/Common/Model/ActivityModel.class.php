<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class ActivityModel extends BaseModel {
	
	public function _initialize(){
		parent::_initialize();
		
		if (IS_POST){
			$_POST['status'] = $_POST['status'] == 'on' ? 1 : 0;
			$_POST['share'] = $_POST['share'] == 'on' ? 1 : 0;
			$_POST['start_time'] = strtotime($_POST['start_time']);
			$_POST['end_time'] = strtotime($_POST['end_time']);
		}
	}
	
	public function activity(){
		$this->startTrans();
	
		$jf = $_POST['jf'];
		$rm = M('Member')->where(array('id'=>$_POST['uid']))->setInc('jf', $jf);
		
		$modelRecord = D('Record');
		if ($modelRecord->create()){
			$rr = $modelRecord->add();
		}
		
		if ($rm && $rr){
			$this->commit();
			
			// 发送模板消息
			jf_msg($_POST['uid'], session('openid'), $_POST['jf'], $_POST['title']);
			return 0;
		} else {
			$this->rollback();
			return -1;
		}
	}
	
	public function share($id){
		$res = 1;
		if ($info = $this->find($id)){
			$uid = UID;
			$cid = CID;
			
			$modelShare = M('Activity_share');
			if ( !$modelShare->where(array('uid'=>UID, 'aid'=>$id))->find()) {
				$this->startTrans();
			
				$min = $info['min'];
				$max = $info['max'];
				if ($max && $min){
					$jf = rand($min, $max);
				} else {
					$jf = 1;
					$max and $jf = $max;
					$min and $jf = $min;
				}
				$name = $info['name'];
				// 更新用户积分
				$rM = M('Member')->where(array('id'=>$uid))->setInc('jf', $jf);
				$title = "分享送积分 - $name";
				// 积分记录
				$data = array(
					'uid'=>$uid,
					'cid'=>$cid,
					'title'=>$title,
					'jf'=>$jf,
					'create_time'=>time()
				);
				$r = M('Record')->add($data);
			
				// 分享记录
				$data = array(
					'uid'=>UID,
					'cid'=>CID,
					'aid'=>$id,
					'create_time'=>time()
				);
				$rs = $modelShare->add($data);
			
				if ($rM && $r && $rs){
					$res = 0;
					$this->commit();
					
					// 发送模板消息
					jf_msg($uid, session('openid'), $jf, $title);
				} else {
					$this->rollback();
				}
			}
		} else {
			$res = -1;
			$this->error = '参数错误';
		}
		return $res;
	}
}