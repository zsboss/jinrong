<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文本回复模型
 * @author Administrator
 *
 */
class WxTextcountModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('content','require','关键字不能为空',self::EXISTS_VALIDATE ),
			array('code','','关键字已经存在！',0,'unique',1), 
			array('code','require','code不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
			array('content','trim',3,'function'),
	);
}