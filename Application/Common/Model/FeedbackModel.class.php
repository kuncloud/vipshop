<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class FeedbackModel extends BaseModel {
	
	protected $_link = array(
		'_user'=>array(
			'mapping_type'      => self::BELONGS_TO,
			'class_name'        => 'Member',
			'foreign_key'       => 'uid',
			'mapping_fields'    => 'username',
			'as_fields'		    => 'username',
		),
	);
	
	public $_validate = array(
		array('content', 'require', '请输入内容', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	
	public function _initialize(){
		parent::_initialize();
	}
}