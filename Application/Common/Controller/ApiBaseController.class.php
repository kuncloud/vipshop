<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-17
 * charset : UTF-8
 */
namespace Common\Controller;
use Think\Controller\RestController;

class ApiBaseController extends RestController{
	var $model;
	
	public function _initialize(){
		$this->model = CONTROLLER_NAME;
		define('CID', 1);
		define('UID', 1);
	}
	
	protected function response($data, $type='json', $code=200) {
		parent::response($data, $type, $code);
	}
	
	public function lists($model=CONTROLLER_NAME, $ajax=true, $extend=array(), $return=false){
		 
		if (empty($model)) die('模型不能为空');
		 
		if (is_string($model)) {
			$model = D($model);
		}
		$default = array(
			'page'=>array(
				'mult'=>array(Param('page', 1), $this->pageSize)
			),
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
				echo json_encode(array('err_code'=>0,'lists'=>$list));die;
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
						'err_code' => 0,
						'url'=>$uri ? $uri : U('index')
					);
				} else {
					$arr = array (
						'err_code' => 1,
						'err_msg' => $model->getError ()
					);
				}
			} else {
				$arr = array (
					'err_code' => 1,
					'err_msg' => $model->getError ()
				);
			}
			$this->response($arr);
		} else {
			$id = Param('id');
			$info = $model->find ( $id );
			$this->response(array('err_code'=>0, 'info'=>$info));
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
				$arr ['err_code'] = 0;
				$arr ['err_msg'] = '删除成功';
			} else {
				$arr ['err_code'] = 1;
				$arr ['err_msg'] = '删除失败';
			}
		} else {
			$arr ['err_code'] = 1;
			$arr ['err_msg'] = '无效参数';
		}
		$this->response($arr);
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
				$arr ['err_code'] = 0;
				$arr ['err_msg'] = '操作成功';
			} else {
				$arr ['err_code'] = 1;
				$arr ['err_msg'] = '操作失败';
			}
		} else {
			$arr ['err_code'] = 1;
    			$arr ['err_msg'] = '无效参数';
    	}
    		$this->response($arr);
    }
	
}