<?php
namespace Admin\Controller;
/**
 * 后台自定义菜单控制器
 * @author Administrator
 *
 */
class WxMenusController extends PublicController{
	//appid
	private $appid;
	//appsecret
	private $appsecret;
	public function index () {
		$wxMenu = M("wxMenu");
		$wxMenus = M("wxMenus");
		//获取底部菜单
		$bottomMenu = $wxMenu->select();
		foreach($bottomMenu as $v){
			switch($v['id']){
				case 1: $this->bottomMenu1 = $v['name'];
					break;
				case 2: $this->bottomMenu2 = $v['name'];
					break;
				case 3: $this->bottomMenu3 = $v['name'];
					break;
			}
		}
		//获取子菜单
		$menus = $wxMenus->select();
		foreach($menus as $v){
			switch($v['tid']){
				case 1: $menus1[] = $v;
				break;
				case 2: $menus2[] = $v;
				break;
				case 3: $menus3[] = $v;
				break;
			}
		}
		//赋值到页面
		$this->menus1 = $menus1;
		$this->menus2 = $menus2;
		$this->menus3 = $menus3;
		$this->display();
	}
	//编辑子菜单页面显示
	public function editSubmenu () {
		$id = $_GET['id'];
		$this->list = M('WxMenus')->where(array('id'=>$id))->find();
		$this->display();
	}
	//子菜单编辑处理
	public function editSubmenuProcess () {
		$WxMenus = D('WxMenus');
		if($WxMenus->create()){
			if($WxMenus->save()){
				$this->success('修改成功!','index');
			}else{
				$this->error('修改失败!');
			}
		}else{
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxMenus->getError());
		}
	}
	//编辑底部菜单页面显示
	public function editMenu () {
		$id = $_GET['id'];
		$this->list = M('WxMenu')->where(array('id'=>$id))->find();
		$this->display();
	}
	//编辑底部菜单处理
	public function editMenuProcess () {
		$WxMenu = D('WxMenu');
		if($WxMenu->create()){
			if($WxMenu->save()){
				$this->success('修改成功!','index');
			}else{
				$this->error('修改失败!');
			}
		}else{
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxMenu->getError());
		}
	
	}
	//生成自定义菜单
	public function createMenus () {
		$this->getWxAppConfig();
		$menujsons = $this->getMenuJson();
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $this->getAccessToken ();
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $menujsons);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		$info = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			echo 'Errno' . curl_error ( $ch );
		}
		curl_close ( $ch );
		var_dump ( $info );
	}
	//获取APPID和appsecret
	public function getWxAppConfig () {
		$wxAppConfigInfo = M("wxAppConfig")->find();
		$this->appid = $wxAppConfigInfo['appid'];
		$this->appsecret = $wxAppConfigInfo['appsecret'];
	}
	//获取自定义菜单json数据
	public function getMenuJson () {
		$wxMenu = M("wxMenu");
		$wxMenus = M("wxMenus");
		//三个底部菜单
		$caidan1 = $wxMenu->where("id=1")->find();
		$caidan2 = $wxMenu->where("id=2")->find();
		$caidan3 = $wxMenu->where("id=3")->find();
		$caidan = array($caidan1['name'],$caidan2['name'],$caidan3['name']);
		
		$menus = array();
		
		if($caidan1['name'] == ''){
			$menu1 = $wxMenus->field("name,type,typevalue")->where("tid=1 and open=1")->find();
			$zacaidan11['type'] = $menu1['type'];
			$zacaidan11['name'] = $menu1['name'];
			if($menu1['type'] == "click"){
				$zacaidan11['key'] = $menu1['typevalue'];
			}else{
				$zacaidan11['url'] = $menu1['typevalue'];
			}
		}else{
			$zacaidan1 = array();
			$menu1 = $wxMenus->field("name,type,typevalue")->where("tid=1 and open=1")->select();
			foreach($menu1 as $v){
				if($v['type'] == "click"){
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'key'=>$v['typevalue']);
				}else{
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'url'=>$v['typevalue']);
				}
					
				array_push($zacaidan1, $arr);
			}
			$zacaidan11['name'] = $caidan1['name'];
			$zacaidan11['sub_button'] = $zacaidan1;
		}
		
		if($caidan2['name'] == ''){
			$menu2 = $wxMenus->field("name,type,typevalue")->where("tid=2 and open=1")->find();
			$zacaidan22['type'] = $menu2['type'];
			$zacaidan22['name'] = $menu2['name'];
			if($menu2['type'] == "click"){
				$zacaidan22['key'] = $menu2['typevalue'];
			}else{
				$zacaidan22['url'] = $menu2['typevalue'];
			}
		}else{
			$zacaidan2 = array();
			$menu2 = $wxMenus->field("name,type,typevalue")->where("tid=2 and open=1")->select();
			foreach($menu2 as $v){
				if($v['type'] == "click"){
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'key'=>$v['typevalue']);
				}else{
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'url'=>$v['typevalue']);
				}
				array_push($zacaidan2, $arr);
			}
			$zacaidan22['name'] = $caidan2['name'];
			$zacaidan22['sub_button'] = $zacaidan2;
		}
		
		if($caidan3['name'] == ''){
			$menu3 = $wxMenus->field("name,type,typevalue")->where("tid=3 and open=1")->find();
			$zacaidan33['type'] = $menu3['type'];
			$zacaidan33['name'] = $menu3['name'];
			if($menu3['type'] == "click"){
				$zacaidan33['key'] = $menu3['typevalue'];
			}else{
				$zacaidan33['url'] = $menu3['typevalue'];
			}
		}else{
			$zacaidan3 = array();
			$menu3 = $wxMenus->field("name,type,typevalue")->where("tid=3 and open=1")->select();
			foreach($menu3 as $v){
				if($v['type'] == "click"){
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'key'=>$v['typevalue']);
				}else{
					$arr = array('name'=>$v['name'],'type'=>$v['type'],'url'=>$v['typevalue']);
				}
				array_push($zacaidan3, $arr);
				
			}
			$zacaidan33['name'] = $caidan3['name'];
			$zacaidan33['sub_button'] = $zacaidan3;
		}

		
		
		
		
		
		
		
		
		
		
		
		
		
		$menus=array(
				'button' => array(
						$zacaidan11,$zacaidan22,$zacaidan33
				)
		);
		$this->arrayRecursive($menus, 'urlencode', true);
		$json = json_encode($menus);
		return urldecode($json);
	}
	/**************************************************************
	 *
	*    使用特定function对数组中所有元素做处理
	*    @param  string  &$array     要处理的字符串
	*    @param  string  $function   要执行的函数
	*    @return boolean $apply_to_keys_also     是否也应用到key上
	*    @access public
	*
	*************************************************************/
	private function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
	{
		static $recursive_counter = 0;
		if (++$recursive_counter > 1000) {
			die('possible deep recursion attack');
		}
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$this->arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else {
				$array[$key] = $function($value);
			}
	
			if ($apply_to_keys_also && is_string($key)) {
				$new_key = $function($key);
				if ($new_key != $key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
		}
		$recursive_counter--;
	}
	/**
	 * WxPayHelper::getAccessToken()
	 * 获取access_token
	 *
	 * @return string
	 */
	public function getAccessToken() {
		$url = 'https://api.weixin.qq.com/cgi-bin/token';
		$params = array ();
		$params ['grant_type'] = 'client_credential';
		$params ['appid'] = $this->appid;
		$params ['secret'] = "2c3f24db310f5c4662750dca877790ce";
		$httpstr = $this->http ( $url, $params );
		$token = json_decode ( $httpstr, true );
		//dump($token);die();
		return $token ['access_token'];
	}
	private function http($url, $params, $method = 'GET', $header = array(), $multi = false) {
		$opts = array (
				CURLOPT_TIMEOUT => 30,
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_HTTPHEADER => $header
		);
	
		/* 根据请求类型设置特定参数 */
		switch (strtoupper ( $method )) {
			case 'GET' :
				$param = is_array ( $params ) ? '?' . http_build_query ( $params ) : '';
				$opts [CURLOPT_URL] = $url . $param;
				break;
			case 'POST' :
				// 判断是否传输文件
				// $params = $multi ? $params : http_build_query($params);
				$opts [CURLOPT_URL] = $url;
				$opts [CURLOPT_POST] = 1;
				$opts [CURLOPT_POSTFIELDS] = $params;
				break;
			default :
				die ( '不支持的请求方式！' );
		}
	
		/* 初始化并执行curl请求 */
		$ch = curl_init ();
		curl_setopt_array ( $ch, $opts );
		$data = curl_exec ( $ch );
		$error = curl_error ( $ch );
		curl_close ( $ch );
		if ($error)
			die ( '请求发生错误：' . $error );
		return $data;
	}
}