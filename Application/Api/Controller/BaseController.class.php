<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-17
 * charset : UTF-8
 */

namespace Api\Controller;
use Think\Controller;

// use Think\Controller\RestController;

class BaseController extends Controller {
    
    private function parentLists($model=CONTROLLER_NAME, $ajax=true, $extend=array(), $return=false){
        
        if (empty($model)) die('模型不能为空');
        
        if (is_string($model)) {
            $model = D($model);
        }
        $default = array(
            'page'=>array(
                'mult'=>array(Param('page', 1), $this->pageSize)
            ),
            'order'=>'id desc',
            'where'=>array(),
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
    
    protected function response($data, $type='json', $code=200) {
        echo json_encode($data);die;
//         parent::response($data, $type, $code);
    }
    
//     protected function response($data, $type='json', $code=200) {
//         parent::response($data, $type, $code);
//     }
}