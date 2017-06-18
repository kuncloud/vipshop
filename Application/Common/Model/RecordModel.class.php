<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class RecordModel extends BaseModel {
	
	public $_validate = array();
	public $memberModel;
	
	public function _initialize(){
		parent::_initialize();
		$this->memberModel = D('Member');
	}
	
	public function punch($day){
		$config = get_config(CONFIG_PUNCH);
		$jf = $config['val'][0];
		$max = $config['val'][1];
		$scale = $config['val'][2];
		
		if ($day == $max){
			$jf *= $scale;
		}
		
		$data = array(
			'jf'=>$jf,
			'title'=>'打卡送积分'
		);
		if ($this->create($data) !== false){
			$res = $this->add();
		}
		// 更新用户积分
		$this->memberModel->where(array('uid'=>UID))->setInc('jf', $jf);
	}
	
	public function share($id){
		if ($info = M('Activity')->find($id)){
			$modelShare = M('Activity_share');
			if ( !$modelShare->where(array('uid'=>UID, 'aid'=>$id))->find()) {
				
				$this->startTrans();
				
				$data = array(
					'title'=>"分享送积分 - {$info['name']}",
					'jf'=>$info['jf']
				);
				
				if ($this->create($data) !== false){
					$r = $this->add();
				}
				
				$data = array(
					'uid'=>UID,
					'cid'=>CID,
					'aid'=>$id,
					'create_time'=>time()
				);
				
				$rs = $modelShare->add($data);
				
				// 更新用户积分
				$this->memberModel->where(array('uid'=>$uid))->setInc('jf', $info['jf']);
				
				if ($r && $rs){
					$res = 0;
					$this->commit();
				} else {
					$res = 1;
					$this->rollback();
				}
			} else {
				$res = 1;
			}
			
		} else {
			$res = -1;
			$this->error = '参数错误';
		}
		return $res;
	}
	
	// 店员增加
	public function addByEmploy(){
		if ($this->create() !== false){
			$res = $this->add();
		}
		// 更新用户积分
		return $res;
	}
	
}