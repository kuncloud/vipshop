<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-4
 * charset : UTF-8
 */
namespace Common\Model;

class VipModel extends BaseModel {
	public function setRights($post){
		$this->where(array('cid'=>CID))->setField('rights', '');
		if ($post){
			$sql = '';
			foreach ($post as $k=>$v){
				$keys = array_keys($v);
				$ids = implode(',', $keys);
				$sql .= $this->where(array('cid'=>CID, 'id'=>$k))->fetchSql(true)->setField('rights', $ids) . ';';
			}
			$res = $this->execute($sql);
		}
		return $res;
	}
}