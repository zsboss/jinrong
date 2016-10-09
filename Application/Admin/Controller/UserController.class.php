<?php
namespace Admin\Controller;
use Org\Util\UploadFile;
/**
 * 后台用户管理控制器
 * @author Administrator
 *
 */
class UserController extends PublicController{
	//游客列表
	public function index () {
		$User = M('user'); 
		$count = $User->where("vipid=0")->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $User->where("vipid=0")->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//推广代理商列表
	public function index2 () {
		$User = M('user');
		$count = $User->where("vipid=1")->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $User->where("vipid=1")->join("left join mdb_invite on mdb_invite.unactive=mdb_user.openid")->field("mdb_user.*,mdb_invite.active")->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>&$v){
			$list[$k]['number'] = $list[$k]['one'] + $list[$k]['two'] + $list[$k]['three'] + $list[$k]['one2'] + $list[$k]['two2'] + $list[$k]['three2'];
			if($v['active']){
				$openid = $v['active'];
				$info = $User->where("openid='$openid'")->find();
				$v['active'] = $info['nickname'];
			}else{
				$v['active'] = "直接关注";
			}
			
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//VIP会员列表
	public function index3(){
		$User = M('user'); 
		$count = $User->where("vipid=2")->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $User->where("vipid=2")->join("left join mdb_invite on mdb_invite.unactive=mdb_user.openid")->field("mdb_user.*,mdb_invite.active")->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>&$v){
			$list[$k]['number'] = $list[$k]['one'] + $list[$k]['two'] + $list[$k]['three'] + $list[$k]['one2'] + $list[$k]['two2'] + $list[$k]['three2'];
			if($v['active']){
				$openid = $v['active'];
				$info = $User->where("openid='$openid'")->find();
				$v['active'] = $info['nickname'];
			}else{
				$v['active'] = "直接关注";
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//白金会员列表
	public function index4(){
		$User = M('user'); 
		$count = $User->where("vipid=3")->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $User->where("vipid=3")->join("left join mdb_invite on mdb_invite.unactive=mdb_user.openid")->field("mdb_user.*,mdb_invite.active")->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>&$v){
			$list[$k]['number'] = $list[$k]['one'] + $list[$k]['two'] + $list[$k]['three'] + $list[$k]['one2'] + $list[$k]['two2'] + $list[$k]['three2'];
			if($v['active']){
				$openid = $v['active'];
				$info = $User->where("openid='$openid'")->find();
				$v['active'] = $info['nickname'];
			}else{
				$v['active'] = "直接关注";
			}
		}
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//会员充值记录
	public function user_pay_list(){
		$user_pay = M('user_pay');
		$paytime = $this->time_sou(I('paytime_sta'),I('paytime_end'));
		if($paytime){
				$where['mdb_user_pay.time'] = $paytime;
 		}
		if(I('name')){
			$where['mdb_user_pay.name'] = array('like','%'.I('name').'%');
		}
		$where['mdb_user_pay.status'] = array('gt',0);
		$count = $user_pay->where($where)->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $user_pay->where($where)->join('mdb_vip_money on mdb_user_pay.vipid = mdb_vip_money.id')->field('mdb_user_pay.*,mdb_vip_money.vname as money')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//提现记录
	public function user_reflect_list(){
		$user_reflect = M('user_reflect');
		$paytime = $this->time_sou(I('paytime_sta'),I('paytime_end'));
		if($paytime){
				$where['time'] = $paytime;
 		}
		if(I('name')){
			$where['name'] = array('like','%'.I('name').'%');
		}
		$where['status'] = array('eq',2);
		$count = $user_reflect->where($where)->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $user_reflect->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
		
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//会员提现记录表
	public function user_reflect(){
		$user_reflect = M('user_reflect');
		$paytime = $this->time_sou(I('paytime_sta'),I('paytime_end'));
		if($paytime){
				$where['time'] = $paytime;
 		}
		if(I('name')){
			$where['name'] = array('like','%'.I('name').'%');
		}
		$where['mdb_user_reflect.status'] = array('in','1,3');
		$count = $user_reflect->where($where)->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $user_reflect->where($where)->join('mdb_user on mdb_user_reflect.userid=mdb_user.id')->field('mdb_user.openid,mdb_user_reflect.*')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	
	//会员提现不同意
	public function tixian(){
		$id = I('id');
		$user_reflect = M('user_reflect');
		if($user_reflect->where(array('id'=>$id))->save(array('status'=>3))){
			$this->success("操作成功!",U('Admin/User/user_reflect'));
		}else{
			$this->error("操作失败!",U('Admin/User/user_reflect'));
		}
	}

	//查看推广会员
	public function inviteList () {
		$user = M('user');
		$invite = M('invite');
		$openid = $_GET['openid'];
		$this->nickname = $_GET['nickname'];

		$oneOpenid = array();
		$twoOpenid = array();
		$threeOpenid = array();
		$res = array();
		$oneList = $invite->where("active='$openid'")->field("unactive")->select();
		$twoList = array();
		$threeList = array();
		foreach($oneList as $v){
			$oneOpenid[] = $v['unactive']; 
			$two = $v['unactive']; 
			$userInfo = $user->where("openid='$two'")->field("id,nickname,sex,province,vipid,city,headimgurl")->find();
			if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
				$userInfo['grade'] = "一级";
				$res[] = $userInfo;
			}
			
			$two = $invite->where("active='$two'")->field('unactive')->select();
			if($two){
				$twoList[] = $two;
			} 
		}
		for($i=0;$i<count($twoList);$i++){
			$twoRes = $twoList[$i];
			foreach($twoRes as $v){
				$twoOpenid[] = $v['unactive']; 
				$three = $v['unactive']; 
				$userInfo = $user->where("openid='$three'")->field("id,vipid,nickname,sex,province,city,headimgurl")->find();
				if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
					$userInfo['grade'] = "二级";
					$res[] = $userInfo;
				}
				$three = $invite->where("active='$three'")->field('unactive')->select();
				if($three){
					$threeList[] = $three;
				} 
			}
		}
		for($i=0;$i<count($threeList);$i++){
			$threeRes = $threeList[$i];
			foreach($threeRes as $v){
				$threeOpenid[] = $v['unactive']; 
				$three = $v['unactive']; 
				$userInfo = $user->where("openid='$three'")->field("id,vipid,nickname,sex,province,city,headimgurl")->find();
				if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
					$userInfo['grade'] = "三级";
					$res[] = $userInfo;
				}
			}
		}
		$this->list = $res;
		$this->display();
	}
	//查看推广会员
	public function inviteList2 () {
		$user = M('user');
		$invite = M('invite');
		$openid = $_GET['openid'];
		$this->nickname = $_GET['nickname'];

		$oneOpenid = array();
		$twoOpenid = array();
		$threeOpenid = array();
		$res = array();
		$oneList = $invite->where("active='$openid'")->field("unactive")->select();
		$twoList = array();
		$threeList = array();
		foreach($oneList as $v){
			$oneOpenid[] = $v['unactive']; 
			$two = $v['unactive']; 
			$userInfo = $user->where("openid='$two'")->field("id,nickname,sex,province,vipid,city,headimgurl")->find();
			if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
				$userInfo['grade'] = "一级";
				$res[] = $userInfo;
			}
			
			$two = $invite->where("active='$two'")->field('unactive')->select();
			if($two){
				$twoList[] = $two;
			} 
		}
		for($i=0;$i<count($twoList);$i++){
			$twoRes = $twoList[$i];
			foreach($twoRes as $v){
				$twoOpenid[] = $v['unactive']; 
				$three = $v['unactive']; 
				$userInfo = $user->where("openid='$three'")->field("id,vipid,nickname,sex,province,city,headimgurl")->find();
				if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
					$userInfo['grade'] = "二级";
					$res[] = $userInfo;
				}
				$three = $invite->where("active='$three'")->field('unactive')->select();
				if($three){
					$threeList[] = $three;
				} 
			}
		}
		for($i=0;$i<count($threeList);$i++){
			$threeRes = $threeList[$i];
			foreach($threeRes as $v){
				$threeOpenid[] = $v['unactive']; 
				$three = $v['unactive']; 
				$userInfo = $user->where("openid='$three'")->field("id,vipid,nickname,sex,province,city,headimgurl")->find();
				if($userInfo['vipid'] == 1 || $userInfo['vipid'] == 2){
					$userInfo['grade'] = "三级";
					$res[] = $userInfo;
				}
			}
		}
		$this->list = $res;
		$this->display();
	}
	
}