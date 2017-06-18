<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-4
 * charset : UTF-8
 */
namespace Common\Model;

class ConfigModel extends BaseModel {
	
	public function setConfig($data){
		//dump($data);die;
		$res = 0;
		if ($data){
			$sql = '';
			$where = [];
			$type = '';
			foreach ($data as $k=>$v){
				if(is_array($v)){
					$v = implode(',', $v);
					$type = 'exp';
				}else{
					$type = 'value';
				}
				$where = array('cid'=>CID, 'name'=>$k);
				if ($this->where($where)->find()){
					$sql .= $this->where($where)->fetchSql(true)->setField('val', $v) . ';';
				} else {
					$temp = array(
						'name'=>$k,
						'val'=>$v,
						'type'=>$type
					);
					if ($this->create($temp)){
						$sql .= $this->fetchSql(true)->add() . ';';
					}
				}
			}
// 			dump($sql);die;
			$this->execute($sql);
			S('config_'.CID, null);
		}
	}
	
	public function getConfig($name=''){
		$where = array(
			'cid'=>CID
		);
		$name and $where['name'] = $name;
		$config = $this->where($where)->getField('name,id,type,val');
		
		if ($config){
			foreach ($config as &$v){
				switch ($v['type']){
					case 'exp':
						$v['val'] = explode(',', $v['val']);
						break;
					case 'value':
					default:
						break;
				}
			}
			unset($v);
		}
		
		return $name ? $config[$name] : $config;
	}
	
	
}