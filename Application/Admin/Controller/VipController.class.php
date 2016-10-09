<?php
namespace Admin\Controller;
/**
 * 分销佣金以及重复购买设置
 * @author Administrator
 *
 */
class VipController extends PublicController{
	//会员费金额设置
	public function index () {
		$this->list = M('vipMoney')->where(array('id'=>1))->find();
		$this->list_vip = M('vipMoney')->where(array('id'=>2))->find();
		$this->display();
	}
	//会员费进行修改
	public function indexProcess(){
		$vipMoney = M('vipMoney');
		$one = I('one');
		$two = I('two');
		if($vipMoney->where(array('id'=>1))->save(array('vName'=>$one))){
			if($vipMoney->where(array('id'=>2))->save(array('vName'=>$two))){
				$this->success('修改成功!',U('Admin/Vip/index'));
			}else{
				$this->success('修改成功!',U('Admin/Vip/index'));
			}
		}else{
			if($vipMoney->where(array('id'=>2))->save(array('vName'=>$two))){
				$this->success('修改成功!',U('Admin/Vip/index'));
			}else{
				$this->success('修改失败!',U('Admin/Vip/index'));
			}
		}
	}

	//一级会员成为二级会员显示
	public function vip_one(){
		$vip = M('vipMoney');
		$this->list = M('vip_one')->where('id=1')->find();
		$this->vip_one = $vip->where('id=1')->find();
		$this->vip_two = $vip->where('id=2')->find();
		$this->display();
	}
	//一级会员成为二级会员显示修改
	public function vipProcess(){
		$vip_one = M('vip_one');
		if($vip_one->create()){
			if($vip_one->save()){
				$this->success('修改成功!',U('Admin/Vip/vip_one'));
			}else{
				$this->success('修改失败!',U('Admin/Vip/vip_one'));
			}
		}else{
			exit($User->getError());
		}
	}
}