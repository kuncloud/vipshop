<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-24
 * charset : UTF-8
 */
namespace Api\Controller;

class MemberController extends BaseController {
    
    public $model = 'Member';
	
	// 登陆
	public function login(){
	    $user = Param('user');
	    $psw = Param('psw');
	    
	    if (empty($user)) $this->response(array('err'=>1, 'msg'=>'用户名不能为空'));
	    
	    $model = D($this->model);
	    $data = $model->login($user, $psw);
	    $this->response($data);
	}
	
	// 扫码注册
	public function regist() {
	    $model = D('Member');
	    
	    $cid = Param('cid');
	    if (!$cid) {
	        $data = array('err_code'=>-1, 'err_msg'=>'参数错误');
	        $this->response($data);
	    }
	    
	    $openid = Param('openid');
	    $field = 'id, cid, sid, pid, openid, nickname, username, avatar, gender, birthday, birthtype, tagids, remark, mobile, phone, jf, province, city, district, address';
	    $info = $model->field($field)->where(array('openid'=>$openid))->find();
	    if ($info) {
	        $data = array('err_code'=>1, 'info'=>$info);
	        $this->response($data);
	    } else {
//     	    $_POST = array_merge($_POST, $_GET);
    	    $id = $model->reg();
    	    $info = $id ? $model->field($field)->find($id) : [];
    	    $data = array(
    	        'err_code'=>$id > 0 ? 0 : -1,
    	        'err_msg'=>$model->getError(),
    	        'info'=>$info
    	    );
    	    $this->response($data);
	    }
	}
	
	// 获取用户信息
	public function info(){
	    $id = Param('id');
	    $openid = Param('openid');
	    $cid = Param('cid');
	    $field = 'id, cid, sid, pid, openid, nickname, username, avatar, gender, birthday, birthtype, tagids, remark, mobile, phone, jf, province, city, district, address';
	    $model = M('Member');
	    if ($id) {
	        $info = $model->field($field)->find($id);
	    } elseif($openid && $cid) {
	        $where = array(
	            'openid'=>$openid,
	            'cid'=>$cid
	        );
	        $info = $model->where(array('openid'=>$openid))->field($field)->find();
	    }
	    $this->response(array('err_code'=>0, 'err_msg'=>$model->getError(), 'info'=>$info));
	}
	
	// 转消费
	public function cons(){
		$cid = Param('cid');
		$phone = Param('phone');
		$cons = Param('cons');
		$model = D('Member');
		define('CID', $cid);
		
		$where = array(
			'phone'=>$phone
		);
		$scale = get_config(CONFIG_SCALE);
		$jf = intval(floatval($cons) / intval($scale['val'][1]) * intval($scale['val'][0]));
		$res = -1;
		
		$info = $model->where($where)->find();
		if (!$info){
			$_POST['psw'] = 123456;
			$res = $model->reg();
			
			if ($res == 0){
				$info = $model->where($where)->find();
			} 
		}
		if ($info){
			$res = 0;
			$model->where(array('id'=>$info['id']))->setInc('jf', $jf);
			$uid = $info['id'];
			$title = '屏碎险转消费';
			$openid = $info['openid'];
			// 积分记录
			$data = array(
				'cid'=>$cid,
				'pid'=>$_POST['pid'],
				'uid'=>$uid,
				'jf'=>$jf,
				'title'=>$title,
				'create_time'=>time()
			);
			M('Record')->add($data);
			// 模板消息
			$openid and jf_msg($uid, $openid, $jf, $title);
		}
		
		$data = array(
			'err_code'=>$res,
			'err_msg'=>$model->getError()
		);
		$this->response($data);
	}
	
	/**
	 * 查积分
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-31上午10:59:40
	 */
	public function jf(){
	    $openid = Param('openid');
	    
	    if (!$openid) $this->response(array('err_code'=>-1, 'err_msg'=>'参数出错'));
	    $where = array(
	        'openid'=>$openid
	    );
	    $model = D($this->model);
	    $jf = $model->where($where)->getField('jf');
	    $data = array('err_code'=>0, 'err_msg'=>$model->getError(), 'jf'=>$jf);
	    $this->response($data);
	}
	
	/**
	 * 转移会员
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-31上午11:04:35
	 */
	public function trans(){
	    $model = D($this->model);
	    $res = $model->trans();
	    
	    $data = array('err_code'=>$res, 'err_msg'=>$model->getError());
	    $this->response($data);
	}
	
	// 会员列表
	public function lists(){
	    $pid = Param('pid');
	    $where = array('pid'=>$pid);
	    $extend = array('where'=>$where);
	    $field = 'id, cid, sid, pid, nickname, username, avatar, gender, birthday, birthtype, tagids, remark, mobile, phone, jf, province, city, district, address';
	    $extend['field'] = $field;
	    $lists = $this->parentLists($this->model, false, $extend, true);
	    
	    $this->response(array('err_code'=>0, 'lists'=>$lists));
	}
	
	/**
	 * 搜索会员
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-2下午11:41:48
	 */
	public function search(){
	    $sid = Param('sid');
	    $cid = Param('cid');
	    $uid = Param('uid');
	    $pid = Param('pid');
	    $tag = Param('tag');
	    $key = Param('key');
	    $from = Param('from');
	    $to = Param('to');
	    $rt = Param('rt');
	    $where = array();
	    $model = M($this->model);
	    
	    if (!$cid){
	        $this->response(array('err_code'=>-1, 'err_msg'=>'公司id必须'));
	    }
	    
	    $where['cid'] = $cid;
	    $pid and $where['pid'] = $pid;
	    $sid and $where['sid'] = $sid;
	    $uid and $where['id'] = $uid;
	    if ($from && $to) {
	        $where['create_time'] = array('between', array(strtotime($from), strtotime($to)));
	    } else {
	        $from and $where['create_time'] = array('egt', strtotime($from));
	        $to and $where['create_time'] = array('elt', strtotime($to));
	    }
	    
	    if ($tag) {
	        if ($tag == -1){
	            $where['tagids'] = '';
	        } elseif($tag != 'all' && $tag !== 'index.php') {
	            $where['_string'] = "FIND_IN_SET('$tag', `tagids`)";
	        }
	    }
	    
	    $key and $where['_complex'] = array(
	        'nickname'=>array('like', "%$key%"),
	        'username'=>array('like', "%$key%"),
	        'phone'=>array('like', "%$key%"),
	        '_logic'=>'or',
	    );
	    
	    $field = 'id, nickname, username, avatar, index';
	    if ($rt) {
	        $count = $model->where($where)->count();
	        $data = array('err_code'=>0, 'count'=>$count);
	    } else {
	        $lists = $model->where($where)->order('`index` asc')->field($field)->select();
	        $data = array('err_code'=>0, 'lists'=>$lists);
	    }
	    
	    $this->response($data);
	}
	
	// 更新
	public function update(){
	    $model = D($this->model);
	    $id = Param('id');
	    $info = $model->find($id);
	    if ($info){
// 	        $_POST = array_merge($_GET, $_POST);
	        if (isset($_POST['psw']) && $_POST['psw']) {
	            $_POST['psw'] = encrypt($_POST['psw'], $info['rand']);
	        }
	        if ($model->create() !== false){
	            $r = $model->save();
	            $err = 0;
	        } else {
	            $err = -1;
	        }
	        $msg = $model->getError();
	    } else {
	        $err = -1;
	        $msg = '用户不存在';
	    }
	    $this->response(array('err_code'=>$err, 'err_msg'=>$msg));
	}
	
}