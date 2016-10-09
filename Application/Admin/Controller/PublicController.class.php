<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 公用控制类
 * @author Administrator
 *
 */
class PublicController extends Controller {
	
	//检测登录状态
	public function _initialize(){
		// 自动运行方法
		$username = session("username");
		$villageId = $_SESSION['villageId'];
		if(!empty($villageId)){
			unset($_SESSION['villageId']);
			$this->error("请先登录",U('Admin/Login/index'));
		}
		if(!isset($username)){
			$this->error("请先登录",U('Admin/Login/index'));
		}
	}
	
	//开始时间与结束时间的搜索
	public function time_sou($strate_time,$end_time){
		if($strate_time)
		{
			$strate_time = $strate_time.' 00:00:00';
			$send_time = array('egt',$strate_time);
			return $send_time;
		}
		if($end_time)
		{
			$end_time = $end_time.' 00:00:00';
			$send_time = array('elt',$end_time);
			return $send_time;
		}
		if($strate_time && $end_time)
		{
			$strate_time = $strate_time.' 00:00:00';
			$end_time = $end_time.' 00:00:00';
			$send_time =  array('between',array($strate_time,$end_time));
			return $send_time;
		}
	}
	//搜索方法
		public function search($strate_time,$end_time){	
			//订单编号
			if(I('title')){
				$where['mdb_order.title'] = array('like','%'.I('title').'%');
			}
			//下单时间
			$paytime = $this->time_sou($strate_time,$end_time);
			if($paytime){
				$where['paytime'] = $paytime;
 			}
			//地区
			if(I('address')){
				$where['mdb_order.address'] = array('like','%'.I('address').'%');
			}
			//收货人姓名
			if(I('name')){
				$where['mdb_order.name'] = I('name');
			}
			//订单状态
			if(I('status')){
				$where['mdb_order.status'] = I('status');
			}
			//付款方式
			if(I('payment')){
				$where['mdb_order.payment'] = I('payment');
			}
			//收货人姓名
			if(I('tel')){
				$where['mdb_order.tel'] = I('tel');
			}
			return $where;
		}
    
}