<?php
namespace app\index\model;
use think\Model;
class Note extends Model
{
	/*
	*根据时间戳与笔记创建时间的时间戳相差 小于86400秒(1天) 显示 n小时前
	*如果超过86400秒(1天) 显示n天前
	*如果超过7*86400秒(7天) 显示2017-07-08
	*/
	public function getCreateTimeAttr($value)
	{
		$sec = time() - $value;
		if ($sec < 86400) {
			$realTime = floor($sec/3600) . ' 小时前';
		} else if(($sec>86400)&&($sec<604800)){
			$realTime = floor($sec/86400) . ' 天前';
		} else {
			$realTime = date('Y-m-d',$sec); 
		}
		return $realTime;
	}
}