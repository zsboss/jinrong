<?php
namespace Wxpay\Controller;
/*
 *企业转帐
 */
class TransfersController extends PublicController 
{
 /**
     * 初始化
     */
	public function _initialize(){
        //引入WxPayPubHelper
        vendor('WxPayPubHelper.WxPayPubHelper');
    }
	
	//企业转帐
	public function index(){
		$comment = new \Common_util_pub();
		$jsApi = new \JsApi_pub();
		//用户openid
		$money = $_GET['agree_money'];	//提现金额
		$id = $_GET['id'];//提现记录id
		$data['openid'] = $_GET['openid'];
		/*$JS_API_CALL_URL = "http://www.sxsimex.com/wx_meiluo_rtm/zhifu/demo/transfers.php?total_fee=".$money."x".$id;
		if (!isset($_GET['code'])){
			// 触发微信返回code码
			$url = $jsApi->createOauthUrlForCode($JS_API_CALL_URL);
			Header("Location: $url");
			die();
		}else{*/
			
			// 获取code码，以获取openid
			//echo $data['openid'];die();
			//公众账号APPID
			$data['mch_appid'] = \WxPayConf_pub::APPID;
			//商户号
			$data['mchid'] = \WxPayConf_pub::MCHID;
			$partner_trade_no = $data['mchid'].$id;
			$user_reflect = M('user_reflect');
			$list = $user_reflect->where(array('id'=>$id))->find();
			$userInfo = M('user')->where(array('id'=>$list['userid']))->find();
			if($money > $userInfo['integral']){
				$this->error('用户所剩余额不足!',U('Admin/User/user_reflect_list'));			
			}
			$user_reflect->where(array('id'=>$id))->save(array('tinumber'=>$partner_trade_no));
			//随机字符串
			$data['nonce_str'] = $comment->createNoncestr();
			//商户订单号
			$date = date("Ymd",time());
			$randomNum = rand(1000000000,9999999999);
			$data['partner_trade_no'] = $data['mchid'].$id;
			//校验用户姓名选项	NO_CHECK：不校验真实姓名 
			$data['check_name'] ="NO_CHECK";	
			//收款用户姓名
			$data['re_user_name']="名称";
			$money = $money*100;
			//金额
			$data['amount']=$money;
			//企业付款描述信息
			$data['desc']="提现记录";
			//IP地址
			$data['spbill_create_ip'] = "120.25.87.123";
			//签名
			$data['sign'] = $comment->getSign($data);//签名
			//企业付款接口
			$gourl = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
			//企业付款操作
			$resultstr = $comment->arrayToXml($data);
			$url = $gourl;
			$vars = $resultstr;
			$second=30;
			$aHeader=array();
			$ch = curl_init();
			//超时时间
			curl_setopt($ch,CURLOPT_TIMEOUT,$second);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			//这里设置代理，如果有的话
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
			
			//cert 与 key 分别属于两个.pem文件\WxPayConf_pub::SSLCERT_PATH
			curl_setopt($ch,CURLOPT_SSLCERT,\WxPayConf_pub::SSLCERT_PATH);
			curl_setopt($ch,CURLOPT_SSLKEY,\WxPayConf_pub::SSLKEY_PATH);
			curl_setopt($ch,CURLOPT_CAINFO,\WxPayConf_pub::SSLROOT_PATH);
			if( count($aHeader) >= 1 ){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
			}
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
			$data = curl_exec($ch);
			curl_close($ch);
			$res = 0;
			if($data){
			$xmlObj = simplexml_load_string ( $data, 'SimpleXMLElement', LIBXML_NOCDATA );
		
			$return_code=(string)strtolower($xmlObj->return_code); 
			$result_code=(string)strtolower($xmlObj->result_code); 
			if($return_code == 'success'){
				if($result_code == 'success'){
					$res = 1;
					//发放成功	添加商户订单号和红包订单号 并剪去用户钱数
					$partner_trade_no=$xmlObj->partner_trade_no;
					$payment_no=$xmlObj->payment_no;
					$payment_time=$xmlObj->payment_time;
					$user_reflect = M('user_reflect');
					$list = $user_reflect->where(array('tinumber'=>$partner_trade_no))->find();
					M('user')->where(array('id'=>$list['userid']))->setDec('integral',$list['money']);
					M('user_reflect')->where(array('tinumber'=>$partner_trade_no))->save(array('status'=>2,'pi_time'=>$payment_time));
					//数据库操作
				}else{
					$res = 4;
				}
			}else{
				$res = 5;
			}
		}else{
			$res = 6;
		}
		$this->res = $res;
		$this->display();
	}
	
}
?>
