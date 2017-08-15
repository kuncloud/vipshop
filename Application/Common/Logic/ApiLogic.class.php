<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-18
 * charset : UTF-8
 */
namespace Common\Logic;

use Qiniu\json_decode;
class ApiLogic{
	public $pxUrl = 'http://sp.kuncloud.cn/htdocs/api.php/api/';
	
	public function login($user, $psw=''){
		$url = $this->pxUrl . 'login';
		$data = array('user'=>$user, 'psw'=>$psw);
		
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	public function getEmploy($uid){
		$url = $this->pxUrl . 'uinfo';
		$data = array('id'=>$uid);
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	public function getEids($id, $type='manager', $sid=0){
		$url = $this->pxUrl . 'eids';
		$data = array('id'=>$id, 'type'=>$type, 'sid'=>$sid);
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	public function getStoreById($id){
		$url = $this->pxUrl . 'store';
		$data['id'] = $id;
		
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	public function getStoreByCid($cid){
		$url = $this->pxUrl . 'stores';
		$data['cid'] = $cid;
		
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	public function getEmploysBySid($sid){
		$url = $this->pxUrl . 'employs';
		$data['sid'] = $sid;
		$data['all'] = 1;
		$res = $this->https_request($url, $data);
		return json_decode($res, true);
	}
	
	//https请求（支持GET和POST）
	protected function https_request($url, $data = null)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($curl, CURLOPT_URL, $url);
		if (!empty($data)){
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$output = curl_exec($curl);
		curl_close($curl);
		return $output;
	}
}