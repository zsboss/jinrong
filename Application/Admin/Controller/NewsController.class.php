<?php
namespace Admin\Controller;
/**
 * 小区公告控制器
 * @author Administrator
 *
 */
class NewsController extends PublicController{
	//公告列表
	public function index () {
		$news = M('news'); 
		$villageId = 100;
		$count = $news->where("villageid='$villageId'")->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $news->where("villageid='$villageId'")->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display();
	}
	//添加小区公告
	public function add () {
		$this->display();
	}
	//添加小区公告处理
	public function addProcess () {
		$news = M('news'); 
		$data['villageid'] = 100;
		$data['title'] = $_POST['title'];
		$data['content'] = $_POST['content'];
		$data['time'] = date("y-m-d H:i:s");
		if($news->add($data)){
			$this->success("添加成功！",'index');
		}else{
			$this->error("添加失败！");
		}
	}
	//编辑商家
	public function edit () {
		$id = $_GET['id'];
		$this->list = M('news')->where("id='$id'")->find();
		$this->display();
	}
	
	//编辑商家处理
	public function editProcess () {
		$news = M('news');
		$id = $_POST['id'];
		$data['title'] = $_POST['title'];
		$data['content'] = $_POST['content'];
		if($news->where("id='$id'")->save($data)){
			$this->success("修改成功！",'index');
		}else{
			$this->error("修改失败！");
		}
	}
	//删除商家处理
	public function del () {
		$id = $_GET['id'];
		if(M('news')->where("id='$id'")->delete()){
			$this->success("删除成功！");
		}else{
			$this->error("删除失败！");
		}
	}
}