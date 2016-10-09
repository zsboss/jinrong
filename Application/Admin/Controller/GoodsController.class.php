<?php
namespace Admin\Controller;
/**
 * 拍品管理控制器
 * @author Administrator
 *
 */
class GoodsController extends PublicController{
	//总后台商品列表
	public function index () {
		$where['status'] = array('neq',0);
		$this->goods($where);
		$this->display();	
	}
	//商品公用
	public function goods($where){
		$goods = M("goods");
		$count = $goods->where($where)->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $goods->where($where)->join('mdb_sort on mdb_goods.sortid=mdb_sort.id')->field('mdb_goods.*,mdb_sort.name as sort_name')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach($list as $k=>$v){
			$list[$k]['number'] = $k+1;
		}
		$this->list = $list;
		$this->page = $show;
	}
	//添加修改商品页面
	public function add_goods(){
		$goods = M('goods');
		if(I('id')){
			$id = I('id');
			$goods = $goods->where(array('id'=>$id))->find();
			$goods['introduction'] = htmlspecialchars_decode($goods['introduction']);
			M('admin_user')->where(array('username'=>$_SESSION['username']))->save(array('image'=>$goods['image']));
			$this->goods = $goods;
		}else{
			M('admin_user')->where(array('username'=>$_SESSION['username']))->save(array('image'=>''));
		}
		$this->sort = M('sort')->select();
		$this->display();
	}
	//添加商品处理
	public function keep(){
		$goods = D('goods');
		$goods->create();
		$goods->time = date('Y-m-d H:i:s',time());
		$admin_user = M('admin_user');
		$admin = $admin_user->where(array('username'=>$_SESSION['username']))->find();
		if($admin['image']){
			$goods->image = $admin['image'];
			M('admin_user')->where(array('username'=>$_SESSION['username']))->save(array('image'=>''));
		}else{
			$data['status'] = 3;
			$this->ajaxReturn($data);
		}
		if(I('id')){
			if($goods->save()){
				$data['status'] = 1;
				$this->ajaxReturn($data);
			}else{
				$data['status'] = 2;
				$this->ajaxReturn($data);
			}
		}else{
			unset($_POST['id']);
			if($goodsid = $goods->add()){
				$data['status'] = 1;
				$this->ajaxReturn($data);
			}else{
				$data['status'] = 2;
				$this->ajaxReturn($data);
			}
			
		
		}
	}
	//拍品详情显示
	public function goodCon(){
		$id = I('id');
		if(!$id){
			$this->error('请重新点击!');
		}
		$this->goods = M('goods')->where(array('id'=>$id))->find();//拍品
		$this->images = M('images')->where(array('goodsid'=>$id))->select();//拍品图片
		$bid = M('bid')->where(array('goodsid'=>$id))->order('time ASC')->select();//拍品出价信息
		foreach($bid as $k=>$v){
			$user = M('user')->where(array('id'=>$v['userid']))->find();//用户信息
			$bid[$k]['username'] = $user['name'];
		}
		$this->bid = $bid;
		$this->display();
	}
	//拍品分类列表
	public function sort(){
		$this->sort = M('sort')->select();
		$this->display();
	}
	//拍品分类添加与修改页面显示
	public function sortAdd(){
		if(I('id')){
			$id = I('id');
			$this->sort = M('sort')->where(array('id'=>$id))->find();
		}
		$this->display();
	}
	//拍品分类添加与修改操作
	public function sortProcess(){
		$sort = D('sort');
		$sort->create();
		if(I('id')){
			$status = $sort->save();
		}else{
			$status = $sort->add();
		}
		if($status){
			$this->success('操作成功!','sort');
		}else{
			$this->error('操作失败!');
		}
		
	}
	//商品分类删除
	public function del(){
		if(I('id')){
			$id = I('id');
			$status = M('sort')->where(array('id'=>$id))->delete();
			if($status){
				$this->success('删除成功!');
			}else{
				$this->error('删除失败!');
			}
		}
	}
	//商品删除
	public function dele(){
		if(I('id')){
			$id = I('id');
			$status = M('goods')->where(array('id'=>$id))->delete();
			if($status){
				$this->success('删除成功!');
			}else{
				$this->error('删除失败!');
			}
		}
	}
	//上传图片
	public function uploadProcess (){
            $upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize   =     3145728 ;// 设置附件上传大小
			$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
			$upload->savePath  =     ''; // 设置附件上传（子）目录
			// 上传文件 
			$info   =   $upload->upload();
			if(!$info) {// 上传错误提示错误信息
			    $this->error($upload->getError());
			}else{// 上传成功
				$a = $info["fileUp"]['savepath'].$info["fileUp"]['savename'];
				$user = M('user')->where(array('status'=>1))->find();
				$photo = './Uploads/'.$a;
				$userid = $user['id'];
				$goodsid = isset($_POST['id'])? $_POST['id'] : '0';
				if(M('admin_user')->where(array('username'=>$_SESSION['username']))->save(array('image'=>$photo))){
					echo "<script>parent.stopSend('".$a."')</script>";
				}
			}		
	}
		//商品图片显示
	public function images(){
		$admin_user = M('admin_user');
		$this->admin = $admin_user->where(array('username'=>$_SESSION['username']))->find();
		//dump($this->admin);die();
		$this->display();
	}
}