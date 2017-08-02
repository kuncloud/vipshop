<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

class GoodsModel extends BaseModel {
	
	protected $_link = array(
		'_standard'=>array(
			'mapping_type'      => self::HAS_MANY,
			'class_name'        => 'Goods_standard',
			'foreign_key'       => 'gid',
		),
	);
	
	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', '1,8', '名称长度不能超过8个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
	);
	
	/* 自动完成规则 */
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_BOTH, 'function'),
		array('cid', CID, self::MODEL_BOTH),
		array('uid', UID, self::MODEL_BOTH),
	);
	
	public function _initialize(){
		parent::_initialize();
		
		if (IS_POST){
			$standard = $_POST['_standard'];
			if ($standard){
				$str = '';
				$temp = array();
				foreach ($standard as $k=>$v){
					$i = $k / 2;
					$temp[$i][] = $k;
				}
				foreach ($standard as $k=>$v){
					$i = $k / 2;
					switch ($k % 2){
						case 0:
							$temp[$i]['pid'] = $v;
							break;
						case 1:
							$temp[$i]['sid'] = $v;
							break;
// 						case 2:
// 							$temp[$i]['price'] = $v;
// 							break;
// 						case 3:
// 							$temp[$i]['count'] = $v;
// 							break;
					}
// 					if ($k % 2 == 0){
// 						$str .= ','.$v;
// 					} else {
// 						$str .= ':'.$v;
// 					}
				}
// 				dump($temp);
// 				dump(array_flip(array_flip($temp)));
// 				dump(array_unique($temp));die;
				$_POST['_standard'] = $temp;
				
// 				$_POST['standard'] = trim($str, ',');
			}
			$logo = $_POST['logo'];
			if ($logo){
				$arr = explode(',', $logo);
				$_POST['corver'] = $arr[0];
			}
			$_POST['status'] = $_POST['status'] == 'on' ? 1 : 0;
		}
	}
	
	public function info($id){
		$info = $this->relation('_standard')->find($id);
		if ($info){
			if ($standard = $info['_standard']){
				$temp = array();
				foreach ($standard as $v){
					$temp[$v['pid']][] = $v;
				}
				$info['_standard'] = $temp;
			}
// 			if ($standard = $info['standard']){
				
// 				$temp = array();
// 				foreach (explode(',', $standard) as $v){
// 					$a = explode(':', $v);
// 					$temp[$a[0]][] = $a[1];
// 				}
// 				$info['_standard'] = $temp;
// 			}
		}
		return $info;
	}
	
	public function praise($id, $uid){
		$praiseModel = D('Praise');
		
		$wPraise = array('uid'=>$uid, 'gid'=>$id);
		$wGoods = array('id'=>$id);
		if ($praiseModel->where($wPraise)->find()){
			$this->where($wGoods)->setDec('praise');
			$praiseModel->where($wPraise)->delete();
		} else {
			$this->where($wGoods)->setInc('praise');
			$data = array(
				'cid'=>CID,
				'gid'=>$id,
				'uid'=>$uid,
				'create_time'=>time(),
			);
			$praiseModel->add($data);
		}
		return 0;
	}
	
	
	
}