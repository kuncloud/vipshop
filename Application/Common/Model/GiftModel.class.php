<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class GiftModel extends BaseModel {
	public $_validate = array();
	
	public function _initialize(){
		parent::_initialize();
	}
	
	public function open(){
		$r = 1;
		$pid = get_config(CONFIG_GIFT)['val'];
		
		if ($pid) {
			if ($this->has($pid)){
				$this->error = '您今天已经抽过奖了';
			} else {
				// 获取中奖的数组
				$gift = $this->award($pid);
				// 添加抽奖纪录
				$data = array(
					'cid'=>CID,
					'uid'=>UID,
					'pid'=>$pid,
					'gid'=>$gift['gid'],
					'create_time'=>time()
				);
				$res = M('Gift_record')->add($data);
				if ($res > 0){
					$r = $gift;
				}
			}
		} else {
			$this->error = '商家未设置抽奖';
		}
		return $r;
	}
	
	/**
	 * 抽奖
	 * @param string $type
	 * @return Ambigous <string>
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-8下午2:32:32
	 */
	private function award($pid){
		$where = array('cid'=>CID, 'pid'=>$pid);
		$total = $this->where($where)->sum('rate');
		$gift = $this->where($where)->getField('id, gid, rate');
		$gift[0] = array('id'=>'0', 'gid'=>'0', 'rate'=>''.(100-$total));
		
		foreach ($gift as $v){
			$temp[$v['id']] = $v['rate'];
		}
		$r = $this->getRand($temp);
		return $gift[$r];
	}
	
	/**
	 * 是否抽过奖了
	 * @param string $type
	 * @return boolean
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-8下午2:32:12
	 */
	private function has($pid){
		$border = time_border();
		$where = array(
			'uid'=>UID,
			'pid'=>$pid,
// 			'type'=>$type,
			'create_time'=>array(
				'between', array($border['min'], $border['max'])
			)
		);
		return !empty(M('Gift_record')->where($where)->find());
	}
	
	
	/**
	 * 随机抽奖
	 * @param array $proArr
	 * @return Ambigous <string, unknown>
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-8下午2:31:28
	 */
	private function getRand($proArr) {
		$result = '';
	
		//概率数组的总概率精度
		$proSum = array_sum($proArr);
	
		//概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset ($proArr);
	
		return $result;
	}
	
}