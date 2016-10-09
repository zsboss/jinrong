<?php
namespace Admin\Model;
use Think\Model;
/**
 * 用户留言模型
 * @author Administrator
 *
 */
class MessageModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('userid','require','标题不能为空',self::EXISTS_VALIDATE ),
			array('name','require','图片不能为空',self::EXISTS_VALIDATE ),
			array('content','require','内容不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
			array('time','getDate',3,'callback'),
	);
	function getDate () {
		return date('Y-m-d H:i:s');
	}
}