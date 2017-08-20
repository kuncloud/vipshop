<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class PunchModel extends BaseModel {
	
	public $_validate = array();
	public $_auto = array(
		array('punch_time', 'time', self::MODEL_BOTH, 'function'),
		array('cid', CID, self::MODEL_BOTH),
		array('uid', UID, self::MODEL_BOTH),
	);
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function info($time=0){
		return $this->getDay($time);
	}
	
	public function punch(){
		$day = $this->getDay();
		$uid = UID;
		$cid = CID;
		if ($day){
			$this->error = '今天已经打过卡了';
			$res = 1;
		} else {
			
			$this->startTrans();
			
			$day = $this->getDay(time() - 86400);
			$r = 0;
			// 读取配置
			$config = get_config(CONFIG_PUNCH);
			$jf = intval($config['val'][0]);
			$max = intval($config['val'][1]);
			$scale = intval($config['val'][2]);
			// 当打卡到达翻倍天数时重置
			if ($day == $max) {
				$this->where(array('uid'=>$uid))->setField('status', 0);
				$day = 0;
			}
			// 当本次打卡达到翻倍天数
			if ($day + 1 >= $max) {
				$jf *= $scale;
			}
			if ($day){
				$where = array('uid'=>$uid, 'status'=>1);
				$data = array(
					'day'=>$day + 1,
					'punch_time'=>time()
				);
				$r = $this->where($where)->save($data);
			} else {
				$day = 0;
				$r = 0;
				$this->where(array('uid'=>$uid))->setField('status', 0);
				$data = array(
					'uid'=>UID,
					'day'=>1,
					'status'=>1
				);
				if ($this->create($data) !== false){
					$r = $this->add();
				}
			}
			// 增加积分
			$rM = D('Member')->where(array('id'=>$uid))->setInc('jf', $jf);
			// 增加记录
			$title = '打卡送积分';
			$data = array(
				'cid'=>$cid,
				'uid'=>$uid,
				'jf'=>$jf,
				'title'=>$title,
				'create_time'=>time(),
			);
			$rR = D('Record')->add($data);
			
			if ($r && $rM && $rR) {
				$res = 0;
				$this->error = '已打卡';
				$this->commit();
				
				// 发送模板消息
				jf_msg($uid, session('openid'), $jf, $title);
			} else {
				$this->rollback();
			}
		}
		return $res;
	}
	
	private function getDay($time=0){
		$time = dv($time, time());
		$border = time_border($time);
		$where = array('uid'=>UID, 'status'=>1, 'punch_time'=>array('between', array($border['min'], $border['max'])));
		$day = $this->where($where)->getField('day');
		return $day;
	}
	
	
	
	
	
}