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
		$arr = Db::table('lit_plate')->select();
		
		$result = $this->getPlateTree($arr,$id);
		return $result;
	}
	public function getPlateListSelect()
	{	
		self::$treeList = [];
		$arr = Db::table('lit_plate')->select();
		$result = $this->getPlateTree($arr);
		foreach ($result as $key => $value) {
			$result[$key]['plate_title'] = str_repeat(' &emsp; ', $value['count']) . '|--' . $value['plate_title'];
		}
		return $result;
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
