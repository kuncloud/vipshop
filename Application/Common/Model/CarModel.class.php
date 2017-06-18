<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class CarModel extends BaseModel {
	
	/* 自动验证规则 */
	protected $_validate = array();
	
	/* 自动完成规则 */
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_BOTH, 'function'),
		array('cid', CID, self::MODEL_BOTH),
		array('uid', UID, self::MODEL_BOTH),
		array('status', 'wait', self::MODEL_INSERT),
	);
	
	public function _initialize(){
		parent::_initialize();
	}
	
}