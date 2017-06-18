<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class AddressModel extends BaseModel {
	
	const TIME = array(
		'周一至周五',
		'周末',
		'不限制',
	);
	
	public $_validate = array(
		array('username', 'require', '请输入收货人姓名', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('phone', 'require', '请输入电话', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('province', 'require', '请选择省', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('city', 'require', '请选择市', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('district', 'require', '请选择区', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('detail', 'require', '请输入详细地址', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
	);
	
	public function _initialize(){
		parent::_initialize();
	}
}