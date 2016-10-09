<?php
namespace Admin\Model;
use Think\Model;
/**
 * 自定义子菜单模型
 * @author Administrator
 *
 */
class WxMenusModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('name','require','菜单名称不能为空',self::EXISTS_VALIDATE ),
			array('typevalue','require','类型值不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
	);
}