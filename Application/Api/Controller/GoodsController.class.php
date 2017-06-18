<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-17
 * charset : UTF-8
 */
namespace Api\Controller;
use Common\Controller\ApiBaseController;

class GoodsController extends ApiBaseController{
	
	public function lists(){
		$where = array('status'=>1);
		$extend = array('where'=>$where);
		$lists = parent::lists($this->model, true, $extend, true);
		$lists = pre($lists, 'corver');
		$this->response(array('err_code'=>0, 'lists'=>$lists));
	}
	
	public function info(){
		$id = Param('id');
		$info = D($this->model)->info($id);
		$info['logo'] = explode(',', $info['logo']);
		$info = pre($info, 'img,logo,corver');
		
		// 商品规格
		$standard = M('Standard')->where(array('cid'=>CID))->getField('id, pid, name');
		$data = array('err_code'=>0, 'info'=>$info, 'standard'=>$standard);
		$this->response($data);
	}
	
}