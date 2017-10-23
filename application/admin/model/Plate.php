<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Plate extends Model
{
	protected static $treeList = [];
	public function getPlateList($id = 0)
	{	
		self::$treeList = [];
		$plateInfo = $this->getAllPlateInfo();
		$result = $this->getPlateTree($plateInfo,$id);
		return $result;
	}
	public function getPlateListSelect()
	{	
		self::$treeList = [];
		$plateInfo = $this->getAllPlateInfo();
		$result = $this->getPlateTree($plateInfo);
		foreach ($result as $key => $value) {
			$result[$key]['plate_title'] = str_repeat(' &emsp; ', $value['count']) . '|--' . $value['plate_title'];
		}
		return $result;
	}
	/*
	*在plate表中查找对应的字段信息是否存在
	*
	*/
	public function plateCheckTitleExist($plateTitle)
	{
		$result = $this->get(['plate_title'=>$plateTitle]);
		if (isset($result)) {
			return ['code'=>200,'info'=>'标题已存在'];
		} else {
			return ['code'=>500,'info'=>'标题正确'];
		}
	}
	/*
	*在plate表中添加新的模块信息
	*
	*/
	public function addPlate($plateInfo)
	{
		$this->data([
			'plate_title'=>$plateInfo['title'],
			'plate_order'=>(int)$plateInfo['order'],
			'plate_fid'=>(int)$plateInfo['fid'],
		]);
		$this->save();
		return $this->plate_id;
	}
	/*
	*获取plate表中所有的版块信息
	*
	*/
	protected function getAllPlateInfo()
	{
		return Db::table('lit_plate')->select();
	}
	private function getPlateTree($arr, $plate_fid = 0, $count = 0)
	{
		static $treeList = [];
		foreach ($arr as $key => $val) {
			if ( $val['plate_fid'] == $plate_fid) {
				$val['count'] = $count;
				self::$treeList[] = $val;
				$this->getPlateTree($arr,$val['plate_id'],$count+1);
			}
		}
		return self::$treeList;
	}
}
