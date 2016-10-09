<?php
namespace Admin\Controller;
/**
 * 邮费管理控制器
 * @author Administrator
 */
class PostController extends PublicController{
	//邮费列表
	public function index () {
		$post = M("postMoney");
		$list = $post->select();
		foreach($list as $k=>$v){
			$list[$k]['number'] = $k+1;
		}
		$this->list = $list;
		$this->display();
	}
	//添加修改信息显示
	public function addEdit(){
		$id = I('id');
		if($id){
			$this->list = M('postMoney')->where(array('id'=>$id))->find();
		}
		$this->display();
	}
	
	//添加修改操作
	public function saveProcess(){
		$id = I('id');
		$post = M('postMoney');
		if($post->create()){
			if($id){
				if($post->save()){
					$this->success('修改成功!',U('Admin/Post/index'));
				}else{
					$this->error('修改失败!',U('Admin/Post/addEdit',array('id'=>$id)));
				}
			}else{
				if($post->add()){
					$this->success('添加成功!',U('Admin/Post/index'));
				}else{
					$this->error('添加失败!',U('Admin/Post/addEdit'));
				}
			}
		}else{
			exit($User->getError());
		}
		if($id){
			$this->list = M('postMoney')->where(array('id'=>$id))->find();
		}
	}
	
	//删除记录
	public function dele(){
		$id = I('id');
		if($id){
			if(M('postMoney')->where(array('id'=>$id))->delete()){
				$this->success('删除成功!',U('Admin/Post/index'));
			}else{
				$this->error('删除失败!',U('Admin/Post/index'));
			}
		}
	}
}

?>