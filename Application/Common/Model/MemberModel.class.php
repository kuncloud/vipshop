<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-24
 * charset : UTF-8
 */
namespace Common\Model;

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
		$_POST['rand'] = $rand = mt_rand(100000, 999999);
		$_POST['psw'] = encrypt(dv($_POST['psw'], 123456), $rand);
		
		$openid = Param('openid');
		$cid = Param('cid');
		define('CID', $cid);
		
		$config = get_config(CONFIG_REG);
		$jf = dv($config['val'], 0);
		$_POST['jf'] = $jf;
		if ($this->create() !== false){
			$id = $this->add();
			if ($id) {
				$title = '注册送积分';
				$data = array(
					'uid'=>$id,
					'cid'=>$cid,
					'title'=>$title,
					'jf'=>$jf,
					'create_time'=>time()
				);
				$rR = M('Record')->add($data);
			}
		} else {
		    $id = 0;
		}
		return $id;
	}
	
	// 登陆
	public function login($user, $psw=''){
	    $err = 0;
	    $msg = '';
	    // 		$field = 'id, cid, sid, pid, nickname, username, avatar, gender, birthday, tagids, remark, mobile, phone, jf, province, city, district, address';
	    $info = $this->where(array('openid'=>$user))->find();
	    if (!$info) {
	        $where = array(
	            'phone'=>$user,
	            'id'=>$user,
	            '_logic'=>'or'
	        );
	        $info = $this->where($where)->find();
	        
	        if ($info){
	            if (encrypt($psw, $info['rand']) !== $info['psw']){
	                $err = -1;
	                $msg = '账号密码错误';
	            }
	        } else {
	            $err = -1;
	            $msg = '用户不存在';
	        }
	    }
	    
	    $return = array(
	        'err_code'=>$err,
	        'err_msg'=>dv($msg, $this->getError())
	    );
	    if (isset($info['psw'])) unset($info['psw']);
	    if (isset($info['rand'])) unset($info['rand']);
	    if (isset($info['create_time'])) unset($info['create_time']);
	    
	    $err == 0 and $return['info'] = $info;
	    return $return;
	}
	
	/**
	 * 转移会员
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-31上午11:08:27
	 */
	public function trans($data = array()){
	    $data = dv($data, array_merge($_POST, $_GET));
	    extract($data);
	    
	    if (!$from || !$to){
	        $this->error = '参数出错';
	        return -1;
	    }
	    
	    $where = array(
	        'pid'=>$from
	    );
	    $tagid and $where['_string'] = "FIND_IN_SET('$tagid', `tagids`)";
	    $id and $where['id'] = $id;
	    
	    $res = $this->where($where)->setField('pid', $to);
	    
	    $add = array(
	        'origin'=>$to,
	        'content'=>"转移会员, from: $from, to: $to, id: $id, tagid: $tagid",
	        'create_time'=>time()
	    );
	    M('Log')->add($data);
	    
	    return $res > 0 ? 0 : -1;
	}
	
}