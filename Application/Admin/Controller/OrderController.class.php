<?php
namespace Admin\Controller;
/**
 * 订单管理控制器
 * @author Administrator
 */
class OrderController extends PublicController{
	//订单列表
	public function index () {
		$order = M("order");
		$where = $this->search(I('paytime_sta'),I('paytime_end'));
		$this->order($where);
	}
	//订单公用
	public function order($where){
		$goods = M("order");
		$count = $goods->where($where)->count();
		$Page = new \Think\Pagewangjun($count,25);
		$show = $Page->show();
		$list = $goods->where($where)->limit($Page->firstRow.','.$Page->listRows)->order("id DESC")->select();
		foreach($list as $k=>$v){
			$list[$k]['goodname'] = $user['name'];//商品
		}
		$this->list = $list;
		$this->page = $show;
		$this->display('Order/index');
	}
	//订单详情显示
	public function orderCon(){
		$id = I('id');
		if(!$id){
			$this->error('请重新点击!');
		}
		$order = M('order')->where(array('id'=>$id))->find();//订单
		$this->order_content = M('order_content')->where(array('orderid'=>$order['id']))->select();
		$this->order = $order;
		
		$this->display();
	}
	
	//收入(付款订单统计)
	public function income(){
		//开始时间
		$manp['strate_time'] =  I('strate_time');	
		$manp['end_time'] =  I('end_time');
		//开始时间 结束时间搜索
		$time = $this->time_sou(I('strate_time'),I('end_time'));
		if($time){
			$where['paytime'] = $time;
		}
		$where['status'] = array('in','2,3,4,5');//状态除了未支付
		$order = M('order')->where($where)->select();
		foreach($order as $k=>$v){
			$total['price']  += $v['price'];
			$total['number'] += 1;
			if($v['status'] ==5){
				$error_total['price']  += $v['price'];
				$error_total['number'] += 1; 
			}else{
				$good_total['price']  += $v['price'];
				$good_total['number'] += 1;
			}
		}
		$this->total = $total;//订单总数与总价
		$this->error_total = $error_total;//异常订单总数与总价
		$this->good_total = $good_total;//正常订单总数与总价
		$this->manp = $manp;
		$this->display();
	}
	//一块钱换算多少PV
	public function integral () {
		$this->list = M("integral")->find();
		$this->display();
	}
	
	public function turnPvNumProcess () {
		$data['num'] = $_POST['num'];
		if(is_numeric($data['num'])){
			if(M('integral')->where("id=1")->save($data)){
				$this->success("设置成功！");
			}else{
				$this->error("设置失败！");
			}
		}else{
			$this->error("请输入数字！");
		}
	}
	/*//订单商品统计
	public function goods(){
		$where = $this->search(I('paytime_sta'),I('paytime_end'));
		$where['status'] = array('gt',1);
		$order = M('order');
		$list = $order->where($where)->join('mdb_village on mdb_order.villageid=mdb_village.id')->join('mdb_order_content on mdb_order.id=mdb_order_content.orderid')->field('mdb_order.title,mdb_village.name as village_name,mdb_order_content.*')->select();
		$goods = array();
		$total = 0;
		foreach($list as $k=>$v){
			$goods[$v['goodsid']]['village_name'] = $v['village_name'];
			$goods[$v['goodsid']]['goods_name'] = $v['goods_name'];
			$goods[$v['goodsid']]['number'] += $v['number'];
			$goods[$v['goodsid']]['price'] = $v['price'];
			$goods[$v['goodsid']]['total'] += $v['number']*$v['price'];
			$total += $v['number']*$v['price'];
		}
		$this->total = $total;
		$this->goods = $goods;
		$this->village = M('village')->select();
		$this->manp = htmlspecialchars(json_encode($goods));
		$this->display();
	}
	//导出订单
	public function excel(){
		$goods = json_decode(htmlspecialchars_decode($_POST['manp']),true);
		Vendor('PHPExcel');//加载PHPEcel.php
		$out_excel=new \PHPExcel();
		$out_excel->getActiveSheet()->setCellValue('A1','小区名称');//写入单元格,写上单元格坐标;
		$out_excel->getActiveSheet()->setCellValue('B1','商品名称');//写入单元格,写上单元格坐标;
		$out_excel->getActiveSheet()->setCellValue('C1','商品数量');
		$out_excel->getActiveSheet()->setCellValue('D1','商品价格');
		$out_excel->getActiveSheet()->setCellValue('E1','总价格');
		$i = 2;
		$total = 0;
		foreach($goods as $k=>$v){
			$out_excel->getActiveSheet()->setCellValue('A'.$i,$v['village_name']);//写入单元格,写上单元格坐标;
			$out_excel->getActiveSheet()->setCellValue('B'.$i,$v['goods_name']);//写入单元格,写上单元格坐标;
			$out_excel->getActiveSheet()->setCellValue('C'.$i,$v['number']);
			$out_excel->getActiveSheet()->setCellValue('D'.$i,$v['price']);
			$out_excel->getActiveSheet()->setCellValue('E'.$i,$v['total']);
			$total += $v['number']*$v['price'];
			$i++;
		}
		$i++;
		$out_excel->getActiveSheet()->setCellValue('A'.$i,'总计');//写入单元格,写上单元格坐标;
		$out_excel->getActiveSheet()->setCellValue('E'.$i,$total);
		$out_excel->createSheet();//创建表(默认sheet1)
		$out_writer=\PHPExcel_IOFactory::createWriter($out_excel,'Excel2007');
		$filename=date('Ymd',time()).'_订单.xlsx';//导出文件名
		//没有以下此段，excel直接输出，有则提示下载
		header('Content-Type:application/vnd.ms-excel;charset=utf-8');
		header('Content-Disposition:attachment;filename='.$filename);
		header('Cache-Control:max-age=0');
		$out_writer->save('php://output');//保存excel
	}*/

//确认发货
public function fahuo(){
	$id = I('id');
	$order = M('order');
	if($order->where(array('id'=>$id))->save(array('status'=>3))){
		$this->success("发货成功!",U('Admin/Order/index'));
	}else{
		$this->error("发货失败!",U('Admin/Order/index'));
	}
}
//返佣记录
public function rebate(){
	$rebate = M('rebate');
	$paytime = $this->time_sou(I('paytime_sta'),I('paytime_end'));
	if($paytime){
			$where['time'] = $paytime;
	}
	if(I('name')){
		$where['name'] = array('like','%'.I('name').'%');
	}
	$count = $rebate->where($where)->count();
	$Page = new \Think\Pagewangjun($count,25);
	$show = $Page->show();
	$list = $rebate->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
	$this->assign('list',$list);
	$this->assign('page',$show);
	$this->display();
}

	//
	public function diannao(){
		$this->display();
	}
}
