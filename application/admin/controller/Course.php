<?php  
namespace app\admin\controller;
use app\admin\model\Course;
use app\admin\model\CourseCount;
use app\admin\model\Plate;

class Course extends Auth{
	protected $course;
	protected $plate;
	protected $is_login = ['*'];
	/*
	*初始化获取course对象
	*
	*/
	public function _initialize()
	{
		$this->course = new Course();
		$this->CourseCount = new CourseCount();
		$this->plate  = new Plate();
	}
	/*
	*渲染courseAdd页面
	*
	*/
	public function courseAdd()
	{	
		$courseListSelect = $this->plate->getCoursePlateSelect();
		$this->assign([
			'courseListSelect'=>$courseListSelect,
		]);
		return $this->fetch();
	}
	/*
	*通过AJAX提交数据插入course表
	*
	*/
	public function courseInsert()
	{
		$courseData = $this->request->param();
		$courseId 	= $this->course->courseInsertData($courseData);
		$result 	= $this->CourseCount->courseCountInsertData($courseId);
		return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	public function courseDetail()
	{
		return $this->fetch();
	}
	public function courseList()
	{
		// $courseListData = $this->course->getCourseListSelect();
		// $page = $courseListData->render();
		$courseListData = $this->course->getCourseList();
		$page = $courseListData->render();
		$this->assign([
			//'courseListSelect'=>$courseListSelect,
			'courseListData'=>$courseListData,
			'page'=>$page
		]);
		return $this->fetch();
	}
	//已经解决，thinkphp5多表分页+多条件筛选+分页地址栏额外参数传递不丢失+联合查询时表字段相同where条件参数+php页面的URL尾部参数出现查询条件=0，分页条件接收不到值
    public function test()
    {

        $q = Request::instance()->except(['page', 'sub'], 'get');;
        $pageParam = ['query' => []];
        $pageParam1 = ['query1' => []];

        if (isset($q["typeid"]) && $q["typeid"] !="") {
            $pageParam['query']['a.typeid'] = $q["typeid"];
            $pageParam1['query1']['typeid'] = $q["typeid"];
        }
        if (isset($q["ismake"]) && $q["ismake"] !="") {
            $pageParam['query']['ismake'] = $q["ismake"];
            $pageParam1['query1']['ismake'] = $q["ismake"];
        }
        if (isset($q["channel"]) && $q["channel"] !="") {
            $pageParam['query']['channel'] = $q["channel"];
            $pageParam1['query1']['channel'] = $q["channel"];
        }
        $q = $pageParam['query'];
        $q1 = $pageParam1['query1'];
        dump($q);
 
        $list = Db::connect("db_config2")
            ->table('dede_archives')
//            ->strict(false)
            ->alias("a")
//            ->field("a.typeid")
            ->join('dede_addonarticle af','a.id = af.aid')
            ->where($q)
            ->paginate(5, false, [
            'type' => 'bootstrap',
            'var_page' => 'page',
            'query' => $q1,
        ]);
        $page = $list->render();
        // 模板变量赋值
        $this->assign('list', $list);
        $this->assign('page', $page);
        // 渲染模板输出
        return $this->fetch();
    }
}
?>