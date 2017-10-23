<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Plate extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	protected static $treeList = [];
	public function getPlateList($id = 0)
	{	
		self::$treeList = [];
		$plateInfo = $this->getAllPlateInfo();
		$result = $this->getPlateTree($plateInfo,$id);
		return $result;
	}
	public function getPlateListHidden()
	{	
		$plateInfo = $this->onlyTrashed()->select();
		return $plateInfo;
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
	public function plateCheckTitleExist($plateTitlel)
	{
		$result = $this->get(['plate_title'=>$plateTitlel]);
		if (!empty($result)) {
			return ['code'=>200,'info'=>'标题已存在'];
		} else {
			return ['code'=>500,'info'=>'标题正确'];
		}
	}
	/*
	*在plate表中查找对应的字段信息是否存在
	*
	*/
	public function plateCheckTitleExistById($plateTitle,$plateId = null)
	{
		$result = $this->where('plate_id','<>',(int)$plateId)->where('plate_title',$plateTitle)->select();	
		if (!empty($result)) {
			return ['code'=>200,'info'=>'标题已存在'];
		} else {
			return ['code'=>500,'info'=>'标题正确'];
		}	
	}
	/*
	*在plate表中根据plate_id查询对应的模块信息
	*
	*/
	public function getPlateInfo($plateId)
	{
		$plateInfo = $this->get((int)$plateId)->toArray();	
		return $plateInfo;
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
		$result = $this->save();
		if (((int)$plateInfo['hidden']==1)) {
			$result = $this->destroy((int)$plateInfo['id']);
		}
		if ($result) {
			return ['code'=>200,'info'=>'修改成功'];
		} else {
			return ['code'=>500,'info'=>'修改失败'];
		}
	}
	public function plateModifyData($plateInfo)
	{
		$plateData = $this->get((int)$plateInfo['id']);
		$plateData->data([
			'plate_title'=>$plateInfo['title'],
			'plate_order'=>(int)$plateInfo['order'],
			'plate_fid'=>(int)$plateInfo['fid'],
		]);
		$result = $plateData->save();
		if (((int)$plateInfo['hidden']==1)) {
			$result = $this->destroy((int)$plateInfo['id']);
		}
		if ($result) {
			return ['code'=>200,'info'=>'修改成功'];
		} else {
			return ['code'=>500,'info'=>'修改失败'];
		}
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
	/*
	*在版块回收站中恢复plate信息
	*
	*/
	public function plateRecv($plateId)
	{
		$plate = $this->onlyTrashed()->where('plate_id',(int)$plateId)->find();
		$plate->delete_time = NULL;
		$result = $plate->save();
		if ($result) {
			return ['code'=>200,'info'=>'修改成功'];
		} else {
			return ['code'=>500,'info'=>'修改失败'];
		}
	}
}
