<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class AdModel extends BaseModel {
	
	public function _initialize(){
		parent::_initialize();
		
		if (IS_POST){
			$_POST['status'] = $_POST['status'] == 'on' ? 1 : 0;
		}
	}

	public function apiSlider(){
		$lists = $this->field('url, corver, type, iid')->where(array('cid'=>CID, 'status'=>1))->select();
		if ($lists){
			$lists = pre($lists, 'corver');
		}
		return $lists;
	}
	
	public function getSlider(){
		$lists = $this->where(array('cid'=>CID, 'status'=>1))->order('id desc')->select();
		if (empty($lists)) return [];
		foreach ($lists as $v){
			$url = '';
			if ($v['url']){
				$url = $v['url'];
			} else if(($type = $v['type']) && ($iid = $v['iid'])){
				$type = ucfirst($type);
				$url  = U("$type/info", "id=$iid&cid=".CID);
			}
			$data[] = array(
				'img'=>img_pre().$v['corver'],
				'url'=>$url
			);
		}
		return $data;
	}
}