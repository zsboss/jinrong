<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文本回复模型
 * @author Administrator
 *
 */
class WageModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('name','require','姓名不能为空',self::EXISTS_VALIDATE ),
			array('jobnumber','require','工号不能为空',self::EXISTS_VALIDATE ),
			array('department','require','部门不能为空',self::EXISTS_VALIDATE ),
			array('year','require','日期不能为空',self::EXISTS_VALIDATE ),
			array('month','require','日期不能为空',self::EXISTS_VALIDATE ),
	);
}