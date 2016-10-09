<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文本回复模型
 * @author Administrator
 *
 */
class WxPiccountModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('title','require','标题不能为空',self::EXISTS_VALIDATE ),
			array('summary','require','描述不能为空',self::EXISTS_VALIDATE ),
			array('picurl','require','图片不能为空',self::EXISTS_VALIDATE ),
			array('url','require','链接地址不能为空',self::EXISTS_VALIDATE ),
			array('code','require','code不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
			
	);
}