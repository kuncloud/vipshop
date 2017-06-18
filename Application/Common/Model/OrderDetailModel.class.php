<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class OrderDetailModel extends BaseModel {
	
	/* 自动验证规则 */
	protected $_validate = array();
	
	public function _initialize(){
		parent::_initialize();
		
		if (IS_POST){
		}
	}
	
	
}