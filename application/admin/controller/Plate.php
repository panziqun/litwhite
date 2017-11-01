<?php  
namespace app\admin\controller;
use app\admin\model\Plate;

class Plate extends Auth{
	protected $plate;
	protected $is_login = ['*'];
	/*
	*初始化获取plate对象
	*
	*/
	public function _initialize()
	{
		parent::_initialize();
		$this->plate = new Plate();
	}
	/*
	*获取plateList.html页面中select标签的信息显示
	*
	*/
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
	*获取plateList.html页面中tbody的的信息显示
	*
	*/
	public function plateListHiddenData()
	{
		$plateListData = $this->plate->getPlateListHidden();
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
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	public function plateAddSun()
	{
		$plateID = $this->request->param('plateId');
		$plateListSelect = $this->plate->getPlateListSelect();
		$plateInfo = $this->plate->getPlateInfo($plateID);
		$this->assign([
			'plateInfo'=>$plateInfo,
			'plateListSelect'=>$plateListSelect
		]);
		return $this->fetch();
	}
	public function plateModify()
	{	
		$plateID = $this->request->param('plateId');
		$plateListSelect = $this->plate->getPlateListSelect();
		$plateInfo = $this->plate->getPlateInfo($plateID);
		$this->assign([
			'plateInfo'=>$plateInfo,
			'plateListSelect'=>$plateListSelect
		]);
		return $this->fetch();
	}

	public function plateModifyInfo()
	{
		$plateInfo = $this->request->param();
		$result = $this->plate->plateModifyData($plateInfo);

		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	
	public function plateModifyCheckTitle()
	{
		$info = $this->request->param();
		if (empty($info['title'])) {
			return json_encode(['code'=>200,'info'=>'标题不能为空'],JSON_UNESCAPED_UNICODE);
		}
		$result = $this->plate->plateCheckTitleExistById($info['title'],$info['id']);
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}

	public function plateAddCheckTitle()
	{
		$plateTitle = $this->request->param('title');
		if (empty($plateTitle)) {
			return json_encode(['code'=>200,'info'=>'标题不能为空'],JSON_UNESCAPED_UNICODE);
		}
		$result = $this->plate->plateCheckTitleExist($plateTitle);
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	public function plateHidden()
	{
		
		$plateListSelect = $this->plate->getPlateListSelect();
		$plateListData = $this->plate->getPlateListHidden();
		$page = $plateListData->render();
		$this->assign([
			'plateListSelect'=>$plateListSelect,
			'plateListData'=>$plateListData,
			'page'=>$page
		]);
		return $this->fetch();
	}
	public function plateListRecvData()
	{
		$id = $this->request->param('id');
		$result = $this->plate->plateRecv($id);
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
}
?>