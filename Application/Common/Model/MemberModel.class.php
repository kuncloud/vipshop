<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-24
 * charset : UTF-8
 */
namespace Common\Model;

use Common\Logic\WxLogic;
class MemberModel extends BaseModel{
	
	public $_validate = array(
// 		array('username', 'require', '用户名必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
// 		array('openid', 'require', 'openid必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('pid', 'require', '导购必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('cid', 'require', '公司必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
// 		array('sid', 'require', '门店必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('phone', 'require', '手机号必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('psw', 'require', '密码必须', self::MUST_VALIDATE, 'regex', self::MODEL_INSERT),
		array('phone', '', '该手机号已注册', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),
		array('openid', '', '该微信已绑定', self::VALUE_VALIDATE, 'unique', self::MODEL_INSERT),
	);
	
	public $_auto = array(
		array('create_time', 'time', self::MODEL_BOTH, 'function'),
		array('birthday', 'strtotime', self::MODEL_BOTH, 'function'),
		array('birth', 'birth', self::MODEL_BOTH, 'function'),
		array('index', 'generate_index', self::MODEL_BOTH, 'function'),
	);
	
	public function updateVInfo($id){
		$info = $this->find($id);

		$vips = M('Vip')->where(array('cid'=>$info['cid']))->select();
		if ($vips){
			foreach ($vips as $v){
				if ($info['jf'] >= $v['jf']){
					$name = $v['name'];
					$rights = $v['rights'];
					break;
				}
			}
			$data = array(
				'vname'=>$name,
				'rights'=>$rights
			);
			$this->where(array('id'=>$id))->save($data);
		}
	}
	
	public function reg(){
		$res = -1;
		
		$_POST['rand'] = $rand = rand(100000, 999999);
		$_POST['psw'] = encrypt($_POST['psw'], $rand);
		
		if ($openid = $_POST['openid']){
			$logic = new WxLogic();
			$info = $logic->get_user_info($openid);
			$info['headimgurl'] and $_POST['avatar'] = dv($_POST['avatar'], $info['headimgurl']);
			$info['nickname'] and $_POST['nickname'] = dv($_POST['nickname'], $info['nickname']);
		}
		
		$this->startTrans();
		if ($this->create() !== false){
			$id = $this->add();
			
			if ($id){
				$config = get_config(CONFIG_REG);
				$jf = dv($config['val'], 0);
				$title = '注册送积分';
				$data = array(
					'uid'=>$id,
					'cid'=>CID,
					'title'=>$title,
					'jf'=>$jf,
					'create_time'=>time()
				);
				$rR = M('Record')->add($data);
				// 增加积分
				$rM = $this->where(array('id'=>$id))->setField('jf', $jf);
				
				if ($rR && $rM){
					$this->commit();
					// 发送模板消息
					jf_msg($uid, $_POST['openid'], $jf, $title);
					$res = 0;
				} else {
					$this->rollback();
				}
			}
		}
		return $res;
	}
	
}