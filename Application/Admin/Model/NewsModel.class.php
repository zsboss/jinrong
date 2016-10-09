<?php
namespace Admin\Model;
use Think\Model;
/**
 * 文本回复模型
 * @author Administrator
 *
 */
class NewsModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('title','require','标题不能为空',self::EXISTS_VALIDATE ),
			array('picurl','require','图片不能为空',self::EXISTS_VALIDATE ),
			array('content','require','内容不能为空',self::EXISTS_VALIDATE ),
	);
	
	//自动完成
	protected $_auto = array (
			array('time','getDate',3,'callback'),
			array('content','htmlspecialchars_decode',3,'function'),
	);
	function getDate () {
		return date('Y-m-d H:i:s');
	}
}