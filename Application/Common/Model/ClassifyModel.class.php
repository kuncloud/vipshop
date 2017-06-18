<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class ClassifyModel extends BaseModel {
	
	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', '1,5', '名称长度不能超过5个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
	);
	
	/* 自动完成规则 */
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_BOTH, 'function'),
		array('cid', CID, self::MODEL_BOTH),
		array('uid', UID, self::MODEL_BOTH),
	);
}