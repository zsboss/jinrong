<?php
namespace Admin\Controller;
use Think\Controller;
/**
 * 微信自动回复
 * @author Administrator
 *
 */
class WxmsgController extends Controller{
	
	//自动回复列表展示
	function index () {
		$WxReply = M("wxReply");
		$this->replyList = $WxReply->order("id desc")->select();
		$this->display();
	}
	
	//新增关键字
	function add () {
		$this->display();
	}
	
	//新增关键字处理
	function addProcess () {
		$WxReply = D("wxReply"); 
		if (!$WxReply->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxReply->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			if($WxReply->add()){
				$this->success("添加关键字成功！",U('Admin/Wxmsg/index'));
			}else{
				$this->error("添加关键字失败！");
			}
		}
	}
	
	//删除关键字
	function delWxmsg () {
		$WxReply = D("wxReply");
		$id = $_GET['id'];
		if($WxReply->where("id='$id'")->delete()){
			$this->success("删除关键字成功！");
		}else{
			$this->error("删除关键字失败！");
		}
	}
	//上传视频
	public function video(){
		$xml = json_decode($_FILES);
		$log_name= "./notify/notify_url.log";//log文件路径
		log_result($log_name,"【接收到的notify通知】:\n".$xml."\n");
		move_uploaded_file($_FILES['Filedata']['tmp_name'], './Uploads/1.mp4');
	}
	//编辑关键字
	function edit () {
		$WxReply = M("wxReply");
		$id = $_GET['id'];
		$this->reply = $WxReply->where("id='$id'")->find();
		$this->display();
	}
	
	//编辑关键词处理程序
	function editProcess () {
		$WxReply = D("wxReply");
		if (!$WxReply->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxReply->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			if($WxReply->save()){
				$this->success("编辑关键字成功！",U('Admin/Wxmsg/index'));
			}else{
				$this->error("编辑关键字失败！");
			}
		}
	}
	
	//查看回复列表
	function replyList () {
		$WxReply = M("wxReply");
		$wxmsgId = $_GET['id'];
		$this->code = $wxmsgId;
		$reply = $WxReply->where("id='$wxmsgId'")->find();
		//如果是文本类型则跳转到文本页面，如果是图文类型则跳转到图文页面
		if($reply['typeid'] == 1){
			$wxTextcount = M("wxTextcount");
			$textInfo = $wxTextcount->where("code='$wxmsgId'")->find();
			if($textInfo){
				$this->textInfo = $textInfo;
			}
			$this->display("replyText");
		}else{
			$wxTextcount = M("wxPiccount");
			$picInfo = $wxTextcount->where("code='$wxmsgId'")->select();
			if($picInfo){
				$this->picInfo = $picInfo;
			}
			$this->display("replyPicnewsList");
		}
	}
	
	//保存文本消息
	function saveReplyText () {
		$WxTextcount = D("wxTextcount");
		if (!$WxTextcount->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxTextcount->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			if($_POST['id']){
				if($WxTextcount->save()){
					$this->success("保存成功！",U('Admin/Wxmsg/index'));
				}else{
					$this->error("保存失败！");
				}
			}else{
				if($WxTextcount->add()){
					$this->success("保存成功！",U('Admin/Wxmsg/index'));
				}else{
					$this->error("保存失败！");
				}
			}
			
		}
	}
	
	//新增图文消息页面
	function picCount () {
		//获取不到id则为新增，能获取到id则为编辑
		$this->code = $_GET['code'];
		$id = $_GET['id'];
		if($id){
			$wxPiccount = M("wxPiccount");
			$this->list = $wxPiccount->where("id='$id'")->find();
		}
		$this->display();
	}
	
	//删除图文消息
	function delPicnews () {
		$id = $_GET['id'];
		if(M("wxPiccount")->where("id='$id'")->delete()){
			$this->success("删除图文消息成功!");
		}else{
			$this->error("删除图文消息失败！");
		}
	}
	
	//图文消息编辑和填加
	function savePiccount () {
		$WxPiccount = D("wxPiccount");
		if (!$WxPiccount->create()){
			// 如果创建失败 表示验证没有通过 输出错误提示信息
			$this->error($WxPiccount->getError());
		}else{
			// 验证通过 可以进行其他数据操作
			if($_POST['id']){
				if($WxPiccount->save()){
					$this->success("保存成功！",U('Admin/Wxmsg/replyList',array('id'=>$_POST['code'])));
				}else{
					$this->error("保存失败！");
				}
			}else{
				if($WxPiccount->add()){
					$this->success("保存成功！",U('Admin/Wxmsg/replyList',array('id'=>$_POST['code'])));
				}else{
					$this->error("保存失败！");
				}
			}	
		}
	}
}