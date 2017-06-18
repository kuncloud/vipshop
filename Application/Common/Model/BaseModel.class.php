<?php
namespace Common\Model;
use Think\Model;
use Think\Model\RelationModel;
/**
 * 基础model
 */
class BaseModel extends RelationModel{
	
	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
// 		array('name', '1,8', '名称长度不能超过8个字符', self::MUST_VALIDATE, 'length', self::MODEL_BOTH),
	);
	
	/* 自动完成规则 */
	protected $_auto = array(
		array('create_time', 'time', self::MODEL_BOTH, 'function'),
		array('cid', CID, self::MODEL_BOTH),
		array('uid', UID, self::MODEL_BOTH),
	);

    /**
     * 获取全部数据
     * @param  string $type  tree获取树形结构 level获取层级结构
     * @param  string $order 排序方式   
     * @return array         结构数据
     */
    public function getTreeData($type='tree',$order='',$name='name',$child='id',$parent='pid'){
        // 判断是否需要排序
        if(empty($order)){
            $data=$this->select();
        }else{
            $data=$this->order($order.' is null,'.$order)->select();
        }
        // 获取树形或者结构数据
        if($type=='tree'){
            $data=\Org\Nx\Data::tree($data,$name,$child,$parent);
        }elseif($type="level"){
            $data=\Org\Nx\Data::channelLevel($data,0,'&nbsp;',$child);
        }
        return $data;
    }
}
