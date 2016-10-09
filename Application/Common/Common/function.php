<?php

	/**
	 * 根据关键字类型编号获取关键字类型名称
	 * @param 类型编号 $typeid
	 */
	function getTypenameByTypeid ($typeid) {
		switch($typeid){
			case 1:return "文本消息";
			break;
			case 2:return "图文消息";
			break;
		}
	}
	/**
	 * 根据关键字的status属性获取该关键字的触发时机
	 * @param 触发时机 $status
	 */
	function getReplyNameBystatus ($status) {
		switch($status){
			case 1:return "首次关注时回复";
			break;
			case 2:return "没有关键字时回复";
			break;
			case 3:return "首次关注并没有关键字时回复";
			break;
			case 4:return "匹配时回复";
			break;
		}
	}

		//微信记录日志
function  log_result($file,$word){
    $fp = fopen($file,"a");
    flock($fp, LOCK_EX) ;
    fwrite($fp,"执行日期：".strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
 }