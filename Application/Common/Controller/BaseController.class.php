<?php
namespace Common\Controller;
use Think\Controller;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use Qiniu\json_decode;
use Common\Logic\WxJSApiLogic;
use Think\Log;
/**
 * Base基类控制器
 */
class BaseController extends Controller{
	
	var $menu;
	var $meta_title = '';
	var $model;
	var $qiniuConfig;
	var $accessKey = '';
	var $secretKey = '';
	var $pageSize = 10;
	
    /**
     * 初始化方法
     */
    public function _initialize(){
    		if ($openid = Param('openid')){
    			session('openid', $openid);
    		}
    		$this->checkUser();
        $this->model = CONTROLLER_NAME;
    }
    
    public function wxSign(){
	    	$jsLoigc = new WxJSApiLogic();
	    	$res = $jsLoigc->getSignPackage();
	    	$this->assign('sign', $res);
    }
    
	public function ajax_upload(){
		$this->qiniuConfig = C('QINIU_CONFIG');
		require 'ThinkPHP/Library/Vendor/Qiniu/autoload.php';
		
		$auth = new \Auth($this->qiniuConfig['ACCESSKEY'], $this->qiniuConfig['SECRETKEY']);
		// 空间名  https://developer.qiniu.io/kodo/manual/concepts
		$bucket = 'jfsc';
		// 生成上传Token
		$token = $auth->uploadToken($bucket);

		foreach ($_FILES as $v){
			$image = $v["tmp_name"];
			$fp = fopen($image, "r");
			$file = fread($fp, $v["size"]); //二进制数据流
			
			$uploadMgr = new UploadManager();
			list($ret, $err) = $uploadMgr->put($token, null, $file);
			fclose($fp);
		}
		
		if ($err !== null) {
	        $return = array('status'=>1, 'msg'=>'上传出错');
	        dump($err);die;
	    } else {
	        $return = array('status'=>0, 'name'=>$ret['key']);
	    }
	    echo json_encode($return);die;
	}
	
    public function adminDisplay($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = ''){
    		$this->assign('meta_title', $this->meta_title);
    		$this->display();
    }
    
    
    public function lists($model=CONTROLLER_NAME, $ajax=false, $extend=array(), $return=false){
    	
    		if (empty($model)) die('模型不能为空');
    	
		if (is_string($model)) {
			$model = D($model);
		}
		$page = Param('page', 1);
		$default = array(
// 			'page'=>array(
// 				'mult'=>array(Param('page', 1), $this->pageSize)
// 			),
			'page'=>"$page,{$this->pageSize}",
			'order'=>'id desc',
			'where'=>array('cid'=>CID),
			'field'=>'*'
		);
		
		$default = array_merge($default, $extend);
		
		foreach ($default as $k=>$v) {
			if (isset($v['mult']) && $v['mult']) {
				call_user_func_array(array($model, $k), $v['mult']);
			} else {
				call_user_func(array($model, $k), $v);
			}
		}
		$list = $model->select();
		
		if ($return) {
			return $list;
		} else {
			if ($ajax){
				echo json_encode(array('status'=>0,'lists'=>$list));die;
			}  {
				$this->assign ( 'lists', $list );
				$this->adminDisplay ();
			}
		}
	}
	
	public function edit($model = CONTROLLER_NAME, $uri='') {
		if (is_string($model)) {
			$model = D($model);
		} else {
			$model = D ( $this->model );
		}
		if (IS_POST) {
			$key = $model->getPk ();
			if ($model->create () !== false) {
	
				$id = I ( $key, '', 'int' );
				if ($id) {
					$w [$key] = $id;
					$model->where ( $w )->save ();
					$res = 1;
				} else {
					$res = $model->add ();
				}
				if ($res) {
					$arr = array (
							'status' => 0,
							'url'=>$uri ? $uri : U('index')
					);
				} else {
					$arr = array (
							'status' => 1,
							'msg' => $model->getError ()
					);
				}
			} else {
				$arr = array (
						'status' => 1,
						'msg' => $model->getError ()
				);
			}
			echo json_encode ( $arr ); die();
		} else {
			$id = Param('id');
			$info = $model->find ( $id );
			$this->assign ( 'info', $info );
			$this->adminDisplay ();
		}
	}
	
	public function del($model = CONTROLLER_NAME) {
		$id = Param('id');
		if (is_string ( $model )) {
			$model = D ( $model );
		} else {
			$model = D ( $this->model );
		}
		
		if ($id) {
			$key = $model->getPk ();
			if (strpos ( $id, ',' )) {
				$w [$key] = array (
					'in',
					$id 
				);
			} else {
				$w [$key] = $id;
			}
			$res = $model->where ( $w )->delete ();
			
			if ($res) {
				$arr ['status'] = 0;
				$arr ['msg'] = '删除成功';
			} else {
				$arr ['status'] = 1;
				$arr ['msg'] = '删除失败';
			}
		} else {
			$arr ['status'] = 1;
			$arr ['msg'] = '无效参数';
		}
		
		echo json_encode( $arr ); die();
	}
	
	public function status($model = CONTROLLER_NAME) {
		$s = Param ( 's' );
		$id = Param ( 'id' );
		$this->setField ( $model, $id, 'status', $s );
	}
	
	/**
	 * 设置字段值
	 * 
	 * @param string|object $model        	
	 * @param string $id        	
	 * @param string $field        	
	 * @param string $value        	
	 * @author : panfeng <89688563@qq.com>
	 *         time : 2016-9-7下午5:06:31
	 */
	public function setField($model = CONTROLLER_NAME, $id, $field, $value) {
		if (is_string ( $model )) {
			$model = D ( $model );
		} else {
			$model = D ( $this->model );
		}
		
		if ($id) {
			$key = $model->getPk ();
			if (strpos ( $id, ',' )) {
				$w [$key] = array (
						'in',
						$id 
				);
			} else {
				$w [$key] = $id;
			}
			$res = $model->where ( $w )->setField ( $field, $value );
			
			if ($res) {
				$arr ['status'] = 0;
				$arr ['msg'] = '操作成功';
			} else {
				$arr ['status'] = 1;
				$arr ['msg'] = '操作失败';
			}
		} else {
			$arr ['status'] = 1;
    			$arr ['msg'] = '无效参数';
    	}
    
    	echo json_encode ( $arr );
    	die ();
    }

}
