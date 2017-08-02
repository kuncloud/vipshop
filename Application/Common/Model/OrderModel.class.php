<?php
/**
 * author : panfeng
 * email : 89688563@qq.com
 * date : 2017-4-2
 * charset : UTF-8
 */
namespace Common\Model;

use Think\Log;
class OrderModel extends BaseModel {
	
	const DELAY_TIME = 900;			// 支付时间15分钟
	const CONFIRM_TIME = 604800;		// 自动确认时间7天
	const STATUS = array(
		'wait'=>'待付款',
		'send'=>'待发货',
		'sending'=>'已发货',
		'refund'=>'已退款',
		'cancel'=>'已取消',
		'ok'=>'已完成',
	);
	
	/* 自动验证规则 */
	protected $_validate = array();
	
	protected $_link = array(
		'_detail'=>array(
			'mapping_type'      => self::HAS_MANY,
// 			'mapping_name'      => 'Order_detail',
			'class_name'        => 'Order_detail',
			'foreign_key'       => 'oid',
			//'mapping_fields'    => '*',
		),
	);
	
	public function _initialize(){
		parent::_initialize();
		
		if (IS_POST){
		}
	}
	
	public function getStatus(){
		return array(
			'wait'=>'待付款',
			'send'=>'待发货',
			'sending'=>'已发货',
			'refund'=>'已退款',
			'cancel'=>'已取消',
			'ok'=>'已完成',
		);
	}
	
	public function setAdd(){
		$id = Param('id');
		$aid = Param('aid');
		if ($aid){
			$info = M('Address')->field('username, concat(province,city,district,detail) as address, phone, on_time')->find($aid);
			if (empty($info)){
				$this->error = '地址不存在';
				return -1;
			}
			$data = array(
				'consignee'=>serialize($info),
				'fare'=>get_config(CONFIG_FARE)['val']
			);
		} else {
			$info = array(
				'address'=>$_POST['address'],
				'sid'=>$_POST['sid'],
				'name'=>$_POST['name']
			);
			$data = array(
				'fare'=>0,
				'consignee'=>serialize($info)
			);
		}
		
		$data['status'] = 'wait';
		$data['delay_time'] = time() + self::DELAY_TIME;
		$res = $this->where(array('id'=>$id))->save($data);
		
		// 兑换券变为使用
		$this->lockGift($id);
		
		if ($res) {
			return 0;
		} else {
			return -1;
		}
	}
	
	/**
	 * 生成订单id
	 * @return string
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-24下午3:55:37
	 */
	public function generateID(){
		return 'JFDD'.CID.UID.time().rand(1000, 9999);
	}
	
	public function updateStatus($uid){
		// 删除无效订单
		$this->where(array('status'=>''))->delete();
		// 未付款订单
		$where = array('uid'=>$uid, 'status'=>'wait','delay_time'=>array('lt', time()));
		$this->where($where)->setField('status', 'cancel');
		// 取消了的订单返还兑换券
		$joinO = C('DB_PREFIX') . 'order o on o.id=od.oid';
		$joinOD = C('DB_PREFIX') . 'order_detail od on od.grid=gr.id';
		$where = array('o.uid'=>$uid, 'o.status'=>'cancel');
		$field = 'gr.id';
		$ids = M('Gift_record gr')->field($field)->join($joinOD)->join($joinO)->where($where)->select();
		if ($ids){
			foreach ($ids as $v){
				$temp .= ','.$v['id'];
			}
			$temp = trim($temp, ',');
			M('Gift_record')->where(array('id'=>array('in', $temp)))->setField('status', 0);
		}
		// 未确认订单
		$where = array('uid'=>$uid, 'status'=>'sending','confirm_time'=>array('lt', time()));
		$this->where($where)->setField('status', 'ok');
	}
	
	public function setStatus($id, $status){
		$info = $this->find($id);
		
		$data = array(
			'status'=>$status
		);
		switch ($status){
			case 'ok':
				$msg = '订单已完成';
				break;
			case 'cancel':
				$this->releaseGift($id);
				$msg = '订单已取消';
				break;
			case 'sending':
				$data['confirm_time'] = time() + self::CONFIRM_TIME;
				$msg = '订单已发货';
				break;
		}
		$r = $this->where(array('id'=>$id))->save($data);
		// 发送消息
		$openid = M('Member')->where(array('id'=>$info['uid']))->getField('openid');
		if ($openid) {
			dd_msg($id, $msg, $openid);
		}
		return $r > 0 ? 0 : -1;
	}
	
	/**
	 * 锁定兑换券
	 * @param unknown $id
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-10下午7:27:17
	 */
	private function lockGift($id){
		$grid = M('Order_detail')->where(array('oid'=>$id))->field('grid')->select();
		if ($grid){
			foreach ($grid as $v){
				$temp .= ','.$v['grid'];
			}
			$temp = trim($temp, ',');
			
			$where = array('id'=>array('in', $temp));
			M('Gift_record')->where($where)->setField('status', 1);
		}
	}
	
	/**
	 * 释放兑换券
	 * @param unknown $id
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-5-10下午7:27:44
	 */
	private function releaseGift($id){
		$grid = M('Order_detail')->where(array('oid'=>$id))->field('grid')->select();
		if ($grid){
			foreach ($grid as $v){
				$temp .= ','.$v['grid'];
			}
			$temp = trim($temp, ',');
			
			$where = array('id'=>array('in', $temp));
			M('Gift_record')->where($where)->setField('status', 0);
		}
	}
	
	public function gift($grid){
		$id = $this->generateID();
		
		$join = C('DB_PREFIX').'gift_record gr on g.id=gr.gid';
		$where = array(
			'gr.id'=>$grid,
			'g.status'=>1
		);
		$field = 'g.id, g.name, g.corver, g.remain, gr.status';
		$goodsInfo = M('Goods g')->where($where)->field($field)->join($join)->find();
		
		if (empty($goodsInfo)){
			$this->error = '商品已下架';
			return -1;
		}
		if ($goodsInfo['status'] == 1){
			$this->error = '兑换券已经使用过了';
			return -1;
		}
		if ($goodsInfo['remain'] <= 0){
			$this->error = '商品库存不足';
			return -1;
		}
		
		$data = array(
			'cid'=>CID,
			'uid'=>UID,
			'id'=>$id,
			'count'=>1,
			'create_time'=>time(),
			'_detail'=>array(
				array(
					'oid'=>$id,
					'grid'=>$grid,
					'gid'=>$goodsInfo['id'],
					'standard'=>'随机',
					'name'=>$goodsInfo['name'],
					'corver'=>$goodsInfo['corver'],
					'count'=>1,
					'create_time'=>time(),
				)
			)
		);
		
		$res = $this->relation('_detail')->add($data);
		
		return $res ? $id : -1;
	}
	
	/**
	 * 购物车的商品加入订单
	 * @return string
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-24下午3:55:46
	 */
	public function car2Order(){
		$modelCar = D('Car c');
		$total = 0;
		$count = 0;
		$jf = 0;
		$rebate = 0;
		$_detail = [];
		
		$car = $modelCar->field('g.price, g.corver, g.name, g.jf, g.rebate, c.*')->join(C('DB_PREFIX').'goods g on g.id = c.gid')->where(array('c.uid'=>UID))->select();
		
		$id = $this->generateID();
		$this->startTrans();
		
		if ($car){
			foreach ($car as &$v){
				$c = intval($v['count']);
				$count += $c;
				if (!$v['grid']){
					$total += $c * floatval($v['price']);
					$jf += $c * intval($v['jf']);
					$rebate += $c * intval($v['rebate']);
				} else {
					$rG = M('Gift_record')->where(array('id'=>$v['grid']))->setField('status', 1);
					if (!$rG) {
						$this->rollback();
						$this->error = '兑换券已被使用过';
						return 0;
					}
				}
				
				$v['oid'] = $id;
				unset($v['id']);
				$v['create_time'] = time();
			}
			unset($v);
			$data = array(
				'id'=>$id,
				'cid'=>CID,
				'uid'=>UID,
				'total'=>$total,
				'jf'=>$jf,
				'rebate'=>$rebate,
				'count'=>$count,
				'status'=>'',
				'create_time'=>time(),
// 				'delay_time'=>time() + self::DELAY_TIME,	// 15分钟过期时间
				'_detail'=>$car
			);
		}
		
		$res = $this->relation('_detail')->add($data);
		if ($res) {
			$err_code = 0;
			M('Car')->where(array('uid'=>UID))->delete();
			$this->commit();
		}
		$this->rollback();
		return $id;
	}
	
	public function wpay($id){
		$info = $this->find($id);
		
		$jf = D('Member')->where(array('id'=>UID))->getField('jf');
		$d = $info['jf'] - $jf;
		if ($d > 0){
			$this->error = "积分不足，还缺少{$d}积分";
			return -1;
		}
		
		$openId = OPENID;
// 		$openId = 'oLfd9wIPRv6o1Tu8IcqYCTEA7eHM';
		
		vendor('Weixinpay.Api');
		$input = new \WxPayUnifiedOrder();
		$tools = new \JsApiPay();
// 		$openId = $tools->GetOpenid();
		
		$input->SetBody("订单：$id");
		$input->SetAttach("none");	// 自定义数据
		$input->SetOut_trade_no($id);
// 		$input->SetOut_trade_no($id.'_'.time());
// 		$input->SetTotal_fee("1");
		$price = ($info['total'] + $info['fare']) * 100;
		$input->SetTotal_fee("$price");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("");
		$input->SetNotify_url("https://portshock.cn/jfsc/index.php/Api/Wx/notify");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		
		$order = \WxPayApi::unifiedOrder($input);
		Log::write(json_encode($order), Log::NOTICE);
		$result = $tools->GetJsApiParameters($order);
		
		return $result;
	}
	
	/**
	 * 支付完成
	 * @param unknown $id
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-24下午5:24:32
	 */
	public function payed($id){
		$res = -1;
		
		$rO = $this->where(array('id'=>$id))->setField('status', 'send');
		
		$join = C('DB_PREFIX') . 'goods g on od.gid = g.id';
		$where = array('od.oid'=>$id);
		$field = 'od.jf, od.rebate, od.gid, od.count, od.grid, g.name';
		$detail = M('Order_detail od')->join($join)->where($where)->field($field)->select();
		if ($detail){
			$totalJf = 0;
			$totalRebate = 0;
			$uid = UID;
			$cid = CID;
			$goodsModel = M('Goods');
			foreach ($detail as $v){
				$count = intval($v['count']);
				if (!$v['grid']){
					$jf = intval($v['jf']) * $count;
					$rebate = intval($v['rebate']) * $count;
					$name = $v['name'];
					
					$totalJf += $jf;
					$totalRebate += $rebate;
					$data[] = array(
						'cid'=>$cid,
						'uid'=>$uid,
						'jf'=>-$jf,
						'title'=>"购买商品：$name 消费积分",
						'create_time'=>time(),
					);
					$data[] = array(
						'cid'=>$cid,
						'uid'=>$uid,
						'jf'=>$rebate,
						'title'=>"购买商品：$name 返还积分",
						'create_time'=>time(),
					);
				}
				
				$sql .= $goodsModel->where(array('id'=>$v['gid']))->fetchSql(true)->setDec('remain', $count) . ';';
				$sql .= $goodsModel->where(array('id'=>$v['gid']))->fetchSql(true)->setInc('sales', $count) . ';';
			}
			
			// 积分记录
			if ($data){
				$rR = M('Record')->addAll($data);
				$inc = $totalRebate - $totalJf;
				$rM = D('Member')->where(array('id'=>$uid))->setInc('jf', $inc);
			} else {
				$rR = 1;
				$rM = 1;
			}
			
			// 商品记录
			$rG = $goodsModel->execute($sql);
			
			if ($rO && $rR && $rG && $rM){
				$res = 0;
				$this->commit();
				$openid = session('openid');
				// 发送模板消息
				jf_msg($uid, $openid, $inc, '购买商品');
				
				// 订单变化通知
				dd_msg($id, '支付完成，待发货', $openid);
			} else {
				$this->rollback();
			}
		}
		$this->error = '操作异常';
		return $res;
	}
	
	/**
	 * 购物车生成订单 废弃
	 * @author : panfeng <89688563@qq.com>
	 * time : 2017-4-11下午12:59:18
	 */
	public function car(){
		$postData = $_POST['data'];
		if (empty($postData)){
			$this->error = '非法数据';
			return -1;
		}
		$id = $this->generateID();
		$total = 0;
		$count = 0;
		$modelCar = D('Car c');
		
		// 开启事务
		$this->startTrans();
		foreach ($postData as $v){
			$car = $modelCar->field('g.price, g.corver, g.name, c.*')->join(C('DB_PREFIX').'goods g on g.id = c.gid')->where(array('c.id'=>$v['id']))->find();
			$total += intval($v['count']) * floatval($car['price']);
			$count += intval($v['count']);
			$car['count'] = intval($v['count']);
			unset($car['id']);
			$detailData[] = array_merge($car, array('oid'=>$id));
			M('Car')->delete($v['id']);
		}
		$data = array(
			'id'=>$id,
			'total'=>$total,
			'count'=>$count,
			'status'=>'wait',
		);
		
		// 增加订单
		if ($this->create($data) !== false){
			$orderRes = $this->add();
		}
		
		// 增加订单详情
		$detailRes = M('Order_detail')->addAll($detailData);
		
		if ($detailRes && $orderRes){
			$this->commit();
			return $id;
		} else {
			$this->rollback();
			$this->error = '生成订单异常';
			return -1;
		}
	}
	
	
	
	
	
}