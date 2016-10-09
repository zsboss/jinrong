<?php
namespace Wxpay\Controller;
//微信支付公用类
class LiziController extends PublicController 
{
	
public function index(){
		$openid = 'o4nJrwKmsOKHSCxFZGeC-dyV2LE0';
			$invite = M('invite');
			$user = M('user');
			$invite_list = $invite->where(array('unactive'=>$openid))->find();
			$user_list = $user->where(array('openid'=>$invite_list['active']))->find();
			$this->vip_two($user_list,$invite);
				//此处应该更新一下订单状态，商户自行增删操作
				//$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");

	}


	//一级会员成为二级会员条件
	public function vip_two($userInfo,$invite){
		$where['mdb_invite.active'] = $userInfo['openid'];
		$where['vipid'] = 2;
		$list = $invite->where($where)->join('mdb_user on mdb_user.openid=mdb_invite.unactive')->field('mdb_invite.*,mdb_user.vipid,mdb_user.id as userid')->select();
	
		if($userInfo['vipid'] == 1){
			$one = $userInfo['one'];
			$two = $userInfo['one2'];
			$three = 0;
			$four = 0;
			$userid = '';
			$ym = date('Y-m-d H:i:s',time());
			$time = $this->getlastMonthDays($ym);
			$start_time = $time;
			
			$end_time = date('Y-m-d H:i:s',time());
			foreach($list as $k=>$v){
				$userid = $userid.','.$v['userid'];
			}
			$where_pay['userid'] = array('in',$userid);
			$where_pay['paytime'] = array('BETWEEN',array($start_time,$end_time));
			$user_pay = M('user_pay');
			$user_pay_list = $user_pay->where($where_pay)->select();
			foreach($user_pay_list as $k=>$v){
				if($user_pay_list[$k]['vipid'] == 1){
					$three++;
				}else if($user_pay_list[$k]['vipid'] == 2){
					$four++;
				}
			}
		
			$vip_one = M('vip_one')->where(array('id'=>1))->find();
			if($four >= $vip_one['one']){
				M('user')->where(array('id'=>$userInfo['id']))->save(array('vipid'=>2));
			}
			if($three >= $vip_one['two']){
				M('user')->where(array('id'=>$userInfo['id']))->save(array('vipid'=>2));
			}
			if($one >= $vip_one['three']){
				M('user')->where(array('id'=>$userInfo['id']))->save(array('vipid'=>2));
			}
			if($two >= $vip_one['four']){
				M('user')->where(array('id'=>$userInfo['id']))->save(array('vipid'=>2));
			}
		}
	}

	function getlastMonthDays($date){
     //$timestamp=strtotime($date);
     //$firstday=date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
     $lastday=date('Y-m-d H:i:s',strtotime("$date -1 month"));
     return $lastday;
 }
//业绩突出
public function tu_chu($user,$userInfo,$invite,$rebate){
	$openid = $userInfo['openid'];
	$invite_one = $invite->where(array('unactive'=>$openid))->find();
	$invite_list = $invite->where(array('active'=>$invite_one['active']))->find();
	$count = count($invite_list);
	$user_one = $user->where(array('openid'=>$invite_one['active']))->find();
	$invite_openid = '';
	if($count >=3){
		foreach($invite_list as $k=>$v){
			$invite_openid = $v['unactive'].','.$invite_openid;
		}
		$invite_openid = substr($invite_openid,0,-1);
		$number = 0;
		$tu_list = array();
		$where['openid'] = array('in',$invite_openid);
		$user_list = $user->where($where)->select();
		foreach($user_list as $k=>$v){
			$number = $user_list[$k]['one'] + $user_list[$k]['two'] + $user_list[$k]['three'] + ($user_list[$k]['one2'] + $user_list[$k]['two2'] + $user_list[$k]['three2'])*10;
			if($number>=1000){
				$tu_list[$k]['id'] = $user_list[$k]['id'];
			}
			$number = 0;
		}
		$tu_number = count($tu_list);
		$tu_num = $tu_number - 2;
		if($tu_num > $user_one['tu_number']){
			if($tu_num == 1){
				$user->where(array('openid'=>$user_one['openid']))->setInc('integral',10000);
				$user->where(array('openid'=>$user_one['openid']))->setInc('tu_number',1);
				$add['money'] = 10000;
				$add['status'] = 3;
				$add['time'] = date('Y-m-d H:i:s',time());
				$add['userid'] = $user_one['id'];
				$add['name'] = $user_one['nickname'];
				$rebate->add($add);
			}else{
				$tu_count = $tu_num - $user_one['tu_number'];
				if($tu_count == 1){
					$user->where(array('openid'=>$user_one['openid']))->setInc('integral',5000);
					$user->where(array('openid'=>$user_one['openid']))->setInc('tu_number',1);
					$add['money'] = 5000;
					$add['status'] = 3;
					$add['time'] = date('Y-m-d H:i:s',time());
					$add['userid'] = $user_one['id'];
					$add['name'] = $user_one['nickname'];
					$rebate->add($add);
				}elseif($tu_count >1){
					$user->where(array('openid'=>$user_one['openid']))->setInc('integral',$tu_count*5000);
					$user->where(array('openid'=>$user_one['openid']))->setInc('tu_number',$tu_count);
					$add['money'] = $tu_count*5000;
					$add['status'] = 3;
					$add['time'] = date('Y-m-d H:i:s',time());
					$add['userid'] = $user_one['id'];
					$add['name'] = $user_one['nickname'];
					$rebate->add($add);
				}
				
			}
			
		}
	}
}
}
?>