<?php
namespace Admin\Model;
use Think\Model;
/**
 * 关键字回复模型
 * @author Administrator
 *
 */
class WxReplyModel extends Model{
	
	//自动验证
	protected $_validate = array(
			array('title','require','关键字不能为空',self::EXISTS_VALIDATE ),
			array('title','','关键字已经存在！',0,'unique',1), 
			array('typeid',array(1,2),'类型不正确！',1,'in'), 
			array('status',array(1,2,3,4),'回复时机不正确',1,'in'),	
	);
	
	//自动完成
	protected $_auto = array (
			array('status','setStatus',3,'callback'),
	);
	
	public function setStatus () {
		$status = $_POST['status'];
		switch($status){
			case 1:
				$cate = M("wxReply");
				$data = $cate->where("status=1")->find();
				if($data){
					$id = $data['id'];
					$data['status'] = 4;
					$cate->where("id='$id'")->save($data);
				}
				$data = $cate->where("status=3")->find();
				if($data){
					$id = $data['id'];
					$data['status'] = 2;
					$cate->where("id='$id'")->save($data);
				}
				break;
			case 2:
				$cate = M("wxReply");
				$data = $cate->where("status=2")->find();
				if($data){
					$id = $data['id'];
					$data['status'] = 4;
					$cate->where("id='$id'")->save($data);
				}
				$data = $cate->where("status=3")->find();
				if($data){
					$id = $data['id'];
					$data['status'] = 1;
					$cate->where("id='$id'")->save($data);
				}
				break;
			case 3:
				$cate = M("wxReply");
				$data = $cate->where("status=1")->find();
				if($data){
					$id = $data['id'];
					$data['status'] = 4;
					$cate->where("id='$id'")->save($data);
				}			
				$data1 = $cate->where("status=2")->find();
				if($data1){
					$id = $data1['id'];
					$data1['status'] = 4;
					$cate->where("id='$id'")->save($data1);
				}
				$data2 = $cate->where("status=3")->find();
				if($data2){
					$id = $data2['id'];
					$data2['status'] = 4;
					$cate->where("id='$id'")->save($data2);
				}
				break;
		}
		return $status;
	}
}