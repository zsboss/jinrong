<?php
namespace Admin\Controller;
/**
 * 微信配置页面
 * @author Administrator
 *
 */
class WxAppConfigController extends PublicController{
	
	public function index () {
		$wxAppConfig = M("wxAppConfig");
		$this->list = $wxAppConfig->where("id=1")->find();
		$this->display();
	}
	
	public function process () {
		$wxAppConfig = D("wxAppConfig");
		if (!$wxAppConfig->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($wxAppConfig->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			if($wxAppConfig->save()){
				$this->success("保存成功！");
			}else{
				$this->error("保存失败！");
			}
		}
	}
}