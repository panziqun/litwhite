<?php  
namespace app\admin\controller;
use app\admin\model\Plate;

class Plate extends Auth{
	protected $plate;
	/*
	*获取plateList.html页面中select标签的信息显示
	*
	*/
	public function _initialize()
	{
		$this->plate = new Plate();
	}
	public function plateList()
	{
		$plateListSelect = $this->plate->getPlateListSelect();
		$this->assign([
			'plateListSelect'=>$plateListSelect
		]);
		return $this->fetch();
	}
	/*
	*获取plateList.html页面中tbody的的信息显示
	*
	*/
	public function plateListData()
	{
		$id = $this->request->param('id');
		$plateListData = $this->plate->getPlateList((int)$id);
		$this->assign([
			'plateListData'=>$plateListData
		]);
		return $this->fetch();
	}
	/*
	*获取plateSet.html页面中的信息显示
	*
	*/
	public function plateSet()
	{
		$plateListSelect = $this->plate->getPlateListSelect();
		$this->assign([
			'plateListSelect'=>$plateListSelect
		]);
		return $this->fetch();
	}
	public function plateAdd()
	{
		$plateInfo = $this->request->param();
		$result = $this->plate->addPlate($plateInfo);
		return $result;
	}
	public function plateModify()
	{
		$plateListSelect = $this->plate->getPlateListSelect();
		
		$this->assign([
			'plateListSelect'=>$plateListSelect
		]);
		return $this->fetch();
	}
	public function plateAddCheckTitle()
	{
		$plateTitle = $this->request->param('title');
		$result = $this->plate->plateCheckTitleExist($plateTitle);
		return $result;
	}

	public function plateHidden()
	{
		return $this->fetch();
	}
}
?>