<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 登录页面以及登录控制类
 * @author Administrator
 *
 */
class LoginController extends Controller {
	
	//登录页面展示
    public function index () {
        $this->display("index");
    }
    
    //生成验证码
    public function verify () {
    	$Verify = new \Think\Verify();
    	$Verify->entry();
    }
    
    //登录验证
    public function loginCheck () {
    	$verify = $_POST['verify'];
    	$username = $_POST['username'];
    	$password = md5($_POST['password']);
    	//首先检测验证码是否正确
    	//if(!$this->checkVerify($verify)){
    	//	$this->error("验证码错误！");
    	//}else{
    		//检测用户名和密码
    		if($this->checkPassword($username, $password)){
    			session('username',$username);
    			$this->success("登录成功！",U('Admin/Index/index'));
    		}else{
    			$this->error("用户名或密码错误！");
    		}
    	//}
    }
    
    /**
     * 检测输入的验证码是否正确
     * @param 验证码  $code
     * @return boolean
     */
    function checkVerify($code){
    	$verify = new \Think\Verify();
    	return $verify->check($code);
    }
    
    /**
     * 检测用户名和密码是否正确
     * @param 用户名 $username
     * @param 密码 $password
     * @return boolean
     */
    function checkPassword ($username,$password) {
    	$adminUser = M("adminUser");
    	$adminUserInfo = $adminUser->where("username = '$username'")->find();
    	if($password == $adminUserInfo['password']){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    //退出系统
    function loginOut () {
    	$_SESSION = array();
    	if (isset($_COOKIE[session_name()])) {
    		setcookie(session_name(), '', time()-42000, '/');
    	}
    	session_destroy();
    	$this->index();
    }
    
}