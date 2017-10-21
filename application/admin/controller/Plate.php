<?php  
namespace app\admin\controller;
use app\admin\model\Plate;

class Plate extends Auth{
	protected $plate;
	public function _initialize()
	{
		$this->plate = new Plate();
	}
	public function plateList()
	{
		
		//$plateList = $this->plate->getPlateList();
		$plateListSelect = $this->plate->getPlateListSelect();
		$this->assign([
			'plateListSelect'=>$plateListSelect
	]);
		return $this->fetch();
	}
	public function plateListData()
	{
		$id = $this->request->param('id');
		$plateListData = $this->plate->getPlateList((int)$id);
		$this->assign([
			'plateListData'=>$plateListData
		]);
		return $this->fetch();
		
	}
	public function plateSet()
	{
		return $this->fetch();
	}
	public function plateHidden()
	{
		return $this->fetch();
	}
}
?>