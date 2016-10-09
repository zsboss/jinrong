<?php
namespace Admin\Controller;
/**
 * 后台登录首页显示页面，以及修改密码
 * @author Administrator
 *
 */
class IndexController extends PublicController{
	
	public function index () {
		$this->username = session("username");
		$this->display();
	}
	
	//修改密码
	public function changePassword () {		
		$this->display();
	}
	
	//修改密码处理
	public function changePasswordProcess () {
		$adminUser = M("adminUser");	
		$password = md5($_POST['password']);
		$password1 = md5($_POST['password1']);	
		$username = session("username");
		$adminUserInfo = $adminUser->where("username = '$username'")->find();
		if($password == $adminUserInfo['password']){
			$data['password'] = $password1;
			if($adminUser->where("username = '$username'")->save($data)){
				$this->success("修改密码成功！");
			}else{
				$this->error("修改密码失败！");
			}
		}else{
			$this->error("原始密码输入错误！");
		}
	}
}