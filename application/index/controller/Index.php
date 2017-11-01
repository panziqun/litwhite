<?php  
namespace app\index\controller;
use think\Controller;
use think\Db;
use app\index\model\Plate;
use app\index\model\Course;
use app\index\model\Carousel;
class Index extends Controller{
	protected $plate;
	protected $course;
	protected $carousel;

	private static $treeList;
	/*
	*初始化获取plate对象
	*
	*/
	public function _initialize()
	{
		$this->plate  = new Plate();
		$this->course = new Course();
		$this->carousel = new Carousel();
	}
	public function index()
	{
		//获取首页大模块列表
		$bigPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',1)
							->select();

		//获取首页小模块列表
		$smallPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid','>',1)
							->select();	
						
		$homePlateList = $this->getPlateTree($bigPlateList, $smallPlateList);
		//dump($homePlateList);
		//获取首页实战推荐模块课程
		$courseFree	= $this->course
					->alias('c')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->where('c.course_order','=',3)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,p.plate_title')
					->limit(5)
					->select();
		//获取首页新上好课模块课程
		$courseNew	= $this->course
					->alias('c')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->where('c.course_order','=',2)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,p.plate_title')
					->limit(5)
					->select();
		$this->assign([
			'courseFree'	=>$courseFree,
			'courseNew'		=>$courseNew,
			'homePlateList' =>$homePlateList,
		]);
		//轮播图
		$carousel = $this->carousel
					->alias('ca')
					->join('lit_course co', 'ca.course_title=co.course_title')
					->select();
		$this->assign('carousel', $carousel);
		return $this->fetch();
	}
	public function getPlateTree($bigPlateList, $smallPlateList)
	{
		
		foreach ($bigPlateList as $k => $val) {
			$bigPlateList[$k]['sunId'] = '';
			$count = 0;
			foreach ($smallPlateList as $key => $value) {
				if ( $val['plate_id'] == $value['plate_fid']) {
					$bigPlateList[$k]['smallPlate'][$count]['plate_title'] = $value['plate_title'];
					$bigPlateList[$k]['smallPlate'][$count]['plate_id'] = $value['plate_id'];
					$bigPlateList[$k]['smallPlate'][$count]['plate_fid'] = $value['plate_fid'];
					$bigPlateList[$k]['sunId'] .= $value['plate_id'] . ',';
					$count++;
				}
			}
		}
		foreach ($bigPlateList as $k => $val) {
			$bigPlateList[$k]['courseList'] = [];
			$arr = Db::table('lit_course')
				->join('lit_course_count','lit_course_count.course_id = lit_course.course_id')
				->where('plate_id','in',$val['sunId'])
				->order('lit_course.create_time desc')
				->field('course_title,course_rate,course_grade,course_pic,course_teacher,lit_course_count.course_sales,lit_course.course_id')
				->limit(4)
				->select();
				$bigPlateList[$k]['courseList'] = $arr;
		}
		return $bigPlateList;
	}

	public function indexBigPlateList()
	{
		$plate_id = $this->request->param('plate_id');
		//获取首页大模块列表
		$bigPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',1)
							->select();
		//获取首页小模块列表
		$smallPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid','=',$plate_id)
							->select();	
		$plateListId = '';
		foreach ($smallPlateList as $key => $value) {
			$plateListId .= $value['plate_id'] . ',';
		}
		//获取课程列表
		$courseList	= $this->course
					->alias('c')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->where('c.plate_id','in',$plateListId)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,c.course_describe,p.plate_title')
					->paginate(13);
		$page = $courseList->render();
		//file_put_contents('sql.txt', $this->course->getLastSql());
		$this->assign([
			'bigPlateList'	=>$bigPlateList,
			'plateListId'	=>$plateListId,//小模块ID
			'plateListFid'	=>$plate_id,//大模块ID
			'smallPlateList'=>$smallPlateList,
			'courseList'	=>$courseList,
			'page'			=>$page,
		]);
		return $this->fetch('indexList');
	}
	public function indexSmallPlateList()
	{
		$plate_id = $this->request->param('plate_id');
		//获取首页大模块列表
		$bigPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',1)
							->select();
		//获取首页小模块fid
		$smallPlate = $this->plate->get($plate_id);					
		//获取首页小模块列表
		$smallPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid','=',$smallPlate['plate_fid'])
							->select();	
		//获取课程列表
		$courseList	= $this->course
					->alias('c')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->where('c.plate_id',$plate_id)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,c.course_describe,p.plate_title')
					->paginate(13);
		$page = $courseList->render();
		//file_put_contents('sql.txt', $this->course->getLastSql());
		$this->assign([
			'bigPlateList'	=>$bigPlateList,
			'plateListId'	=>$plate_id,//小模块ID
			'plateListFid'	=>$smallPlate['plate_fid'],//大模块ID
			'smallPlateList'=>$smallPlateList,
			'courseList'	=>$courseList,
			'page'			=>$page,
		]);
		return $this->fetch('indexList');
	}
	public function courseGradeList()
	{
		$plateListId = $this->request->param('plateListId');
		//$plateListFid = $this->request->param('plateListFid');
		$course_grade = $this->request->param('course_grade');
		//获取首页大模块列表
		$bigPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',1)
							->select();
		//获取首页小模块fid
		$plate_id = explode(',',$plateListId);
		$smallPlate = $this->plate->get($plate_id[0]);
		//dump($arr);						
		//获取首页小模块列表
		$smallPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',$smallPlate['plate_fid'])
							->select();
		//dump($smallPlateList);
		//获取课程列表
		$courseList	= $this->course
					->alias('c')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->where('c.plate_id','in',$plateListId)
					->where('c.course_grade','in',$course_grade)
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,c.course_describe,p.plate_title')
					->paginate(13);	
		$page = $courseList->render();
		$this->assign([
			'bigPlateList'	=>$bigPlateList,
			'plateListId'	=>$plateListId,
			'plateListFid'  =>$smallPlateList[0]['plate_fid'],
			'smallPlateList'=>$smallPlateList,
			'courseList'	=>$courseList,
			'page'			=>$page,
		]);
		return $this->fetch('indexList');
	}
	public function courseAllList()
	{
		$plateListId = $this->request->param('plateListId');
		//获取首页大模块列表
		$bigPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid',1)
							->select();
		//获取首页小模块列表
		$smallPlateList = Db::table('lit_plate')
							->field('plate_id,plate_fid,plate_title')
							->where('plate_fid','>',1)
							->select();	
		$plateListId = '';
		foreach ($smallPlateList as $key => $value) {
			$plateListId .= $value['plate_id'] . ',';
		}
		//获取课程列表
		$courseList	= $this->course
					->alias('c')
					->join('lit_course_count ct', 'c.course_id = ct.course_id')
					->join('lit_plate p', 'c.plate_id = p.plate_id')
					->order('ct.course_sales','desc')
					->field('c.course_id,c.course_title,c.course_grade,ct.course_sales,c.course_start,c.course_rate,c.course_pic,c.course_describe,p.plate_title')
					->paginate(13);
		$page = $courseList->render();
		file_put_contents('sql.txt', $this->course->getLastSql());
		$this->assign([
			'bigPlateList'	=>$bigPlateList,
			'plateListId'	=>$plateListId,
			'smallPlateList'=>$smallPlateList,
			'courseList'	=>$courseList,
			'page'			=>$page,
		]);
		return $this->fetch('indexList');
	}
	public function indexSearch()
	{
		$keyword = $this->request->param('keyword');
		$courses = $this->course
			->alias('c')
			->join('lit_course_count cc','c.course_id=cc.course_id')
			->where('course_title','like',"%$keyword%")
			->paginate(3,false,['query' => ['keyword'=>$keyword] ]);
		$count = Db::table('lit_course')->where('course_title','like',"%$keyword%")->count('course_id');
		$page = $courses->render();
		$this->assign('list',$courses);
		$this->assign('page', $page);
		$this->assign('count',$count);
		return $this->fetch();
	}
}
?>