<?php
namespace Wxpay\Controller;
//微信支付公用类
class JsApiCallController extends PublicController 
{
/**
     * 初始化
     */
    public function _initialize()
    {
        //引入WxPayPubHelper
        vendor('WxPayPubHelper.WxPayPubHelper');
    }
	/**
	 * JS_API支付demo
	 * ====================================================
	 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
	 * 成功调起支付需要三个步骤：
	 * 步骤1：网页授权获取用户openid
	 * 步骤2：使用统一支付接口，获取prepay_id
	 * 步骤3：使用jsapi调起支付
	 */
public function index(){
		$id = $_GET['id'];
		$money =  $_GET['money'];
		$status =  $_GET['status'];
		$JS_API_CALL_URL = \WxPayConf_pub::JS_API_CALL_URL."?total_fee=".$id."x".$money."y".$status;
		//使用jsapi接口
		$jsApi = new \JsApi_pub();
		//=========步骤1：网页授权获取用户openid============
		//通过code获得openid
		if (!isset($_GET['code']))
		{
			//触发微信返回code码
			$url = $jsApi->createOauthUrlForCode($JS_API_CALL_URL);
			Header("Location:$url");
			die();
		}else{
			// 获取code码，以获取openid
			$code = $_GET['code'];
			$jsApi->setCode($code);
			$openid = $jsApi->getOpenId();
			//echo $openid;die();
			$total_fee = $_GET['total_fee'];
			$res = explode("x",$total_fee);
			$rest = explode("y",$res[1]);
			$id = $res[0];
			//echo $total_fee;die();			
			$money= $rest[0];
			$status = $rest[1];
			
			
		}
		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new \UnifiedOrder_pub();
		
		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		$unifiedOrder->setParameter("openid","$openid");//商品描述
		$unifiedOrder->setParameter("body","微信支付");//商品描述
		//自定义订单号，此处仅作举例
		//echo $out_trade_no;die();
		//用户点击返回会再改一遍
		if($status == 1){
			$order = M('order');
			$out_trade_no = $status."x0493123".$id;
			$list = $order->where(array('id'=>$id))->find();
			$order->where(array('id'=>$id))->save(array('zhifunumber'=>$out_trade_no));
			$money = $list['price'] + $list['post_money'];
			//$money = $money*100;
			$money = 1;
		}else if($status == 2){
			$user_pay = M('user_pay');
			$save['status'] = 0;
			$out_trade_no = $status."x493123".$id;
			$save['zhifunumber'] = $out_trade_no;
			$user_pay->where(array('id'=>$id))->save($save); 
			$money = 1;
			//$money = $money*100;
		}
 		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
		$unifiedOrder->setParameter("total_fee","$money");//总金额
		$unifiedOrder->setParameter("notify_url",\WxPayConf_pub::NOTIFY_URL);//通知地址 
		$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
		$prepay_id = $unifiedOrder->getPrepayId();
		
		//echo $prepay_id;die();
		//=========步骤3：使用jsapi调起支付============
		$jsApi->setPrepayId($prepay_id);
		$jsApiParameters = $jsApi->getParameters();
		//echo $jsApiParameters;die();
		$this->assign('out_trade_no',$out_trade_no);
		$this->assign('jsApiParameters',$jsApiParameters);
		$this->display();
}
	
	public function notify(){
		$notify = new \Notify_pub();
		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);
		//验证签名 , 并回应微信.
		//对后台通知交互时 , 如果微信收到商户的应答不是成功或超时 , 微信认为通知失败 ,
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知 ,
		//尽可能提高通知的成功率 , 但微信不保证通知最终能成功.
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======
		//以log文件形式记录回调信息
		//$log_ = new Log_();
		$log_name= "./notify/notify_url.log";//log文件路径
		log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
		if($notify->checkSign() == TRUE){
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				log_result($log_name,"【通信出错】:\n".$xml."\n");
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				log_result($log_name,"【业务出错】:\n".$xml."\n");
			}
			else{
				//some code
				$zhifunumber = $notify->data["out_trade_no"];
				$res = explode("x",$zhifunumber);
				$status = $res[0];
				if($status == 1){
					$order = M('order');
					$time = date('Y-m-d H:i:s',time());
					$list = $order->where(array('zhifunumber'=>$zhifunumber))->find();
					//使用购物券
					$user = M('user');
					$userInfo = $user->where(array('id'=>$list['userid']))->find();
					if($list['coupon_status'] == 1){
						$user->where(array('id'=>$list['userid']))->save(array('youhuiquan'=>2,'vipid'=>$list['vipid']));
					}else{
						if($userInfo['vipid'] < $list['vipid']){
							$user->where(array('id'=>$list['userid']))->save(array('vipid'=>$list['vipid']));
							if($list['vipid'] == 2){
								R("Home/Message/index",array($userInfo['openid'],"恭喜您,您已成为VIP会员",$userInfo['id'],"无限","祝您生活愉快",$url=''));
							}elseif($list['vipid'] == 3){
								R("Home/Message/index",array($userInfo['openid'],"恭喜您,您已成为白金会员",$userInfo['id'],"无限","祝您生活愉快",$url=''));
							}elseif($list['vipid'] == 1){
								R("Home/Message/index",array($userInfo['openid'],"恭喜您,您已成为推广代理",$userInfo['id'],"无限","祝您生活愉快",$url=''));
							}
						}
							
					}
					
					$invite = M('invite');
					$vipMoney = M('vipMoney');
					$rebate = M('rebate');
					$order->where(array('zhifunumber'=>$zhifunumber))->save(array('status'=>2,'paytime'=>$time));
					if($list['vipid'] == 0){
						$this->add_number_one($userInfo,$invite,$user,$vipMoney,$rebate);
					}else{
						$this->add_number($userInfo,$invite,$user,$vipMoney,$rebate);
						
					}
				}
				//此处应该更新一下订单状态，商户自行增删操作
				//$log_->log_result($log_name,"【支付成功】:\n".$xml."\n");
				//例如：推送支付完成信息		
			}
			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息
			
		}    
	}
	//当前用户在商城购买其他商品给上三级返
	public function add_number_one($userInfo,$invite,$user,$money,$rebate){
		$openid = $userInfo['openid'];//当前用户	
		$invite_one = $invite->where(array('unactive'=>$openid))->find();//当前用户的上级
		$system = M('system')->where(array('id'=>1))->find();//返佣比例与数据
		if($invite_one){//上级
			$user_one = $user->where(array('openid'=>$invite_one['active']))->find();
			if($user_one['vipid'] == 2){
				$money = round($money/100*$system['discount']);
				$user->where(array('openid'=>$user_one['openid']))->setInc('integral',$money);
				$add['userid'] = $userInfo['id'];
				$add['money'] = $money;
				$add['status'] = $state;
				$add['time'] = date('Y-m-d H:i:s',time());
				$add['name'] = $userInfo['nickname'];
				$rebate->add($add);
			}elseif($user_one['vipid'] == 3){
				$money = round($money/100*$system['discount1']);
				$user->where(array('openid'=>$user_one['openid']))->setInc('integral',$money);
				$add['userid'] = $userInfo['id'];
				$add['money'] = $money;
				$add['status'] = $state;
				$add['time'] = date('Y-m-d H:i:s',time());
				$add['name'] = $userInfo['nickname'];
				$rebate->add($add);
			}
			$invite_two = $invite->where(array('unactive'=>$invite_one['active']))->find();
			if($invite_two){//上上级
				$user_two = $user->where(array('openid'=>$invite_two['active']))->find();
				if($user_two['vipid'] == 2){
					$money = round($money/100*$system['discount']);
					$user->where(array('openid'=>$user_two['openid']))->setInc('integral',$money);
					$add['userid'] = $userInfo['id'];
					$add['money'] = $money;
					$add['status'] = $state;
					$add['time'] = date('Y-m-d H:i:s',time());
					$add['name'] = $userInfo['nickname'];
					$rebate->add($add);
				}elseif($user_two['vipid'] == 3){
					$money = round($money/100*$system['discount1']);
					$user->where(array('openid'=>$user_two['openid']))->setInc('integral',$money);
					$add['userid'] = $userInfo['id'];
					$add['money'] = $money;
					$add['status'] = $state;
					$add['time'] = date('Y-m-d H:i:s',time());
					$add['name'] = $userInfo['nickname'];
					$rebate->add($add);
				}
				$invite_three = $invite->where(array('unactive'=>$invite_two['active']))->find();
				if($invite_three){//上上上级
					$user_three = $user->where(array('openid'=>$invite_three['active']))->find();
					if($user_three['vipid'] == 2){
						$money = round($money/100*$system['discount']);
						$user->where(array('openid'=>$user_three['openid']))->setInc('integral',$money);
						$add['userid'] = $userInfo['id'];
						$add['money'] = $money;
						$add['status'] = $state;
						$add['time'] = date('Y-m-d H:i:s',time());
						$add['name'] = $userInfo['nickname'];
						$rebate->add($add);
					}elseif($user_three['vipid'] == 3){
						$money = round($money/100*$system['discount1']);
						$user->where(array('openid'=>$user_three['openid']))->setInc('integral',$money);
						$add['userid'] = $userInfo['id'];
						$add['money'] = $money;
						$add['status'] = $state;
						$add['time'] = date('Y-m-d H:i:s',time());
						$add['name'] = $userInfo['nickname'];
						$rebate->add($add);
					}
				}
			}
		}
	}
	//添加当前用户上级上上级上上上级人数 并对其上三级进行返佣
	public function add_number($userInfo,$invite,$user,$vipMoney,$rebate){
		$openid = $userInfo['openid'];//当前用户	
		$invite_one = $invite->where(array('unactive'=>$openid))->find();//当前用户的上级
		$system = M('system')->where(array('id'=>1))->find();//返佣比例与数据
		if($invite_one){//上级
			if($userInfo['vipid'] == 1){
				$user->where(array('openid'=>$invite_one['active']))->setInc('one',1);
			}
			$user_one = $user->where(array('openid'=>$invite_one['active']))->find();
			if($user_one){
				if($userInfo['vipid'] == 1){
					$user->where(array('openid'=>$user_one['openid']))->setInc('integral',$system['dai_money']);
					$add['userid'] = $user_one['id'];
					$add['money'] = $system['dai_money'];
					$add['status'] = 1;
					$add['time'] = date('Y-m-d H:i:s',time());
					$add['name'] = $user_one['nickname'];
					$rebate->add($add);
				}elseif($userInfo['vipid'] == 2){
					if($user_one['vipid'] >1){
						$this->fan_yong($user_one['openid'],2,$system['one'],$user,$user_one,$vipMoney,$rebate,1);
					}
				}elseif($userInfo['vipid'] == 3){
					if($user_one['vipid'] >2){
						$this->fan_yong($user_one['openid'],2,$system['one2'],$user,$user_one,$vipMoney,$rebate,1);
					}
				}
			}
			$invite_two = $invite->where(array('unactive'=>$invite_one['active']))->find();
			if($invite_two){//上上级
				if($userInfo['vipid'] == 1){
					$user->where(array('openid'=>$invite_two['active']))->setInc('two',1);
				}
				$user_two = $user->where(array('openid'=>$invite_two['active']))->find();
				if($user_two){
					if($userInfo['vipid'] == 2){
						if($user_two['vipid'] >1){
							$this->fan_yong($user_two['openid'],1,$system['two'],$user,$user_two,$vipMoney,$rebate,1);
						}
					}elseif($userInfo['vipid'] == 3){
						if($user_two['vipid'] >2){
							$this->fan_yong($user_two['openid'],2,$system['two2'],$user,$user_two,$vipMoney,$rebate,1);
						}
					}
				}
				$invite_three = $invite->where(array('unactive'=>$invite_two['active']))->find();
				if($invite_three){//上上上级
					if($userInfo['vipid'] == 1){
						$user->where(array('openid'=>$invite_three['active']))->setInc('three',1);
					}
					$user_three = $user->where(array('openid'=>$invite_three['active']))->find();
					if($user_three){
						if($userInfo['vipid'] == 2){
							if($user_three['vipid'] >1){
								$this->fan_yong($user_three['openid'],1,$system['three'],$user,$user_three,$vipMoney,$rebate,1);
							}
						}elseif($userInfo['vipid'] == 3){
							if($user_three['vipid'] >2){
								$this->fan_yong($user_three['openid'],2,$system['three2'],$user,$user_three,$vipMoney,$rebate,1);
							}
						}
					}
				}
			}
		}
	}
	//返佣计算
	public function fan_yong($openid,$status,$baifenbi,$user,$userInfo,$vipMoney,$rebate,$state){
		$vip = $vipMoney->where(array('id'=>$status))->find();
		if($status == 2){
			$money = round($vip['vname']/100*$baifenbi/100*90);
		}else{
			$money = round($vip['vname']/100*$baifenbi);
		}
		$user->where(array('openid'=>$openid))->setInc('integral',$money);
		$add['userid'] = $userInfo['id'];
		$add['money'] = $money;
		$add['status'] = $state;
		$add['time'] = date('Y-m-d H:i:s',time());
		$add['name'] = $userInfo['nickname'];
		$rebate->add($add);
	}
	
	//销售提成
	public function  xiao_shou($user,$status,$vipMoney,$rebate,$userInfo,$systemList){
		if($userInfo['vipid'] == 2){
			$number = $userInfo['one'] + $userInfo['two'] + $userInfo['three'] + $userInfo['one2'] + $userInfo['two2'] + $userInfo['three3'];
			$this->xiao_save($user,$status,$vipMoney,$rebate,$userInfo,$number,$systemList);
		}
	}
//返佣操作
	public function xiao_save($user,$status,$vipMoney,$rebate,$userInfo,$number,$systemList){
		$openid = $userInfo['openid'];
		if($number >= 100 && $number <= 499){
			$this->fan_yong($userInfo['openid'],$status,$systemList['fosterpercent1'],$user,$userInfo,$vipMoney,$rebate,2);
			$user->where(array('openid'=>$openid))->setInc('integral',$systemList['foster1']);
			$add_one['money'] = $systemList['foster1'];
			$add_one['time'] = date('Y-m-d H:i:s',time());
			$add_one['userid'] = $userInfo['id'];
			$add_one['status'] = 2;
			$add_one['name'] = $userInfo['nickname'];
			$rebate->add($add_one);
		}elseif($number >= 500 && $number < 2000){
			$this->fan_yong($userInfo['openid'],$status,$systemList['fosterpercent2'],$user,$userInfo,$vipMoney,$rebate,2);
			$user->where(array('openid'=>$openid))->setInc('integral',$systemList['foster2']);
			$add_one['money'] = $systemList['foster2'];
			$add_one['time'] = date('Y-m-d H:i:s',time());
			$add_one['userid'] = $userInfo['id'];
			$add_one['status'] = 2;
			$add_one['name'] = $userInfo['nickname'];
			$rebate->add($add_one);
		}elseif($number >= 2000){
			$this->fan_yong($userInfo['openid'],$status,$systemList['fosterpercent3'],$user,$userInfo,$vipMoney,$rebate,2);
			$user->where(array('openid'=>$openid))->setInc('integral',$systemList['foster3']);
			$add_one['money'] = $systemList['foster3'];
			$add_one['time'] = date('Y-m-d H:i:s',time());
			$add_one['userid'] = $userInfo['id'];
			$add_one['status'] = 2;
			$add_one['name'] = $userInfo['nickname'];
			$rebate->add($add_one);
		}
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
			if($two >= $vip_one['three']){
				M('user')->where(array('id'=>$userInfo['id']))->save(array('vipid'=>2));
			}
			if($one >= $vip_one['four']){
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