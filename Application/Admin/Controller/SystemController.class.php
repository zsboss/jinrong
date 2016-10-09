<?php
namespace Admin\Controller;
/**
 * 分销佣金以及重复购买设置
 * @author Administrator
 *
 */
class SystemController extends PublicController{
	//分销返佣百分比设置
	public function index () {
		$vip = M('vipMoney');
		$this->vip_one = $vip->where('id=1')->find();
		$this->vip_two = $vip->where('id=2')->find();
		$this->list = M('system')->where("id=1")->find();
		$this->display();
	}
	//修改分销返佣百分比操作
	public function indexProcess () {
		$system = D('system');
		if($system->create()){
			if($system->where("id=1")->save()){
				$this->success("更新成功！",'index');
			}else{
				$this->error("更新失败！");	
			}
		}else{
			exit($User->getError());
		}
	}
	//会员购买折扣
	public function discount () {
		$vip = M('vipMoney');
		$this->vip_one = $vip->where('id=1')->find();
		$this->vip_two = $vip->where('id=2')->find();
		$this->list = M('system')->where("id=1")->find();
		$this->display();
	}
	//修改会员折扣操作
	public function discountProcess () {
		$system = D('system');
		if($system->create()){
			if($system->where("id=1")->save()){
				$this->success("更新成功！",'discount');
			}else{
				$this->error("更新失败！");	
			}
		}else{
			exit($User->getError());
		}
	}
	//会员体现最低值
	public function cash () {
		$vip = M('vipMoney');
		$this->vip_one = $vip->where('id=1')->find();
		$this->vip_two = $vip->where('id=2')->find();
		$this->list = M('system')->where("id=1")->find();
		$this->display();
	}
	//会员体现最低值操作
	public function cashProcess () {
		$system = D('system');
		if($system->create()){
			if($system->where("id=1")->save()){
				$this->success("更新成功！",'cash');
			}else{
				$this->error("更新失败！");	
			}
		}else{
			exit($User->getError());
		}
	}
	//培育奖设置
	public function foster () {
		$vip = M('vipMoney');
		$this->vip_one = $vip->where('id=1')->find();
		$this->vip_two = $vip->where('id=2')->find();
		$this->list = M('system')->where("id=1")->find();
		$this->display();
	}
	//培育奖设置处理
	public function fosterProcess () {
		$system = D('system');
		if($system->create()){
			if($system->where("id=1")->save()){
				$this->success("更新成功！",'foster');
			}else{
				$this->error("更新失败！");	
			}
		}else{
			exit($User->getError());
		}
	}
}