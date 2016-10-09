<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文本回复模型
 * @author Administrator
 *
 */
class WxAppConfigModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('appid','require','appid不能为空',self::EXISTS_VALIDATE ),
			array('appsecret','require','appsecret不能为空',self::EXISTS_VALIDATE ),
			array('token','require','token不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
	);
}