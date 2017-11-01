<?php  
namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\CourseCount;
use app\admin\model\Comment;
use app\admin\model\Collect;
use app\admin\model\Plate;
use app\admin\model\Note;    	
use Qiniu\Auth as Authh;
use Qiniu\Storage\UploadManager;

class Course extends Auth{
	protected $course;
	protected $plate;
	protected $CourseCount;
	protected $comment;
	protected $collect;
	protected $note;
	protected $is_login = ['*'];
	private $website = "http://www.litwhite.com";
	/*
	*初始化获取course对象
	*
	*/
	public function _initialize()
	{
		parent::_initialize();
		$this->course = new Course();
		$this->CourseCount = new CourseCount();
		$this->comment = new Comment();
		$this->collect = new Collect();
		$this->plate  = new Plate();
		$this->note  = new Note();
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
		$courseFile = $this->request->file('course_pic');
		if ((!$courseData) || (!$courseFile)) {
			$this->error('上传失败');
		}
		$fileInfo = $courseFile->move(ROOT_PATH . 'public' . DS .'uploads');
		
		if ($fileInfo) {
			$fileURL = dirname($fileInfo->getSaveName()) .'/' . $fileInfo->getFileName();
			$courseId 	= $this->course->courseInsertData($courseData, $fileURL);
			$result 	= $this->CourseCount->courseCountInsertData($courseId);
			if ($result) {
				$this->success('上传成功</br>' . $fileURL);
			} else {
				$this->error('上传失败');
			}
		}else {
			$this->error($courseFile->getError());
		}
		//jq 上传时打开 需要返回值
		//return json_encode($result,JSON_UNESCAPED_UNICODE);
	}
	public function courseModify()
	{
		$course_id = $this->request->param('course_id');
		$courseInfo = $this->course->getCourseInfo($course_id);
		$courseListSelect = $this->plate->getCoursePlateSelect();
		$this->assign([
			'courseListSelect'=>$courseListSelect,
			'courseInfo'=> $courseInfo,
		]);
		return $this->fetch();
	}
	public function courseChange()
	{
		$courseData = $this->request->param();
		$courseFile = $this->request->file('course_pic');
		if (!$courseData) {
			$this->error('上传失败');
		}
		if ($courseFile) {
			$fileInfo = $courseFile->move(ROOT_PATH . 'public' . DS .'uploads');
			$fileURL  = dirname($fileInfo->getSaveName()) .'/' . $fileInfo->getFileName();
		}else {
			$fileURL = '';
		}

		$result = $this->course->courseChangeData($courseData, $fileURL);
		if ($result['code']==200) {
			$this->success( $result['info'].'</br>' . $fileURL);
		} else {
			$this->error($result['info']);
		}
	}
	public function courseDetail()
	{	
		//获取课程course_id
		$course_id = $this->request->param('course_id');
		//查询lit_course获取课程基本信息
		$courseInfo = $this->course->withTrashed()->find($course_id);
		//通过获取器获取课程等级
		$course_grade = $courseInfo->course_grade;
		//查询lit_course获取课程总记信息统计
		$courseCountInfo = $courseInfo->CourseCount;

		/**************获取评论相关信息comment*****************/
		//查询lit_comment获取课程总评论信息统计
		$courseCommentInfo = count($courseInfo->Comment);
		//查询lit_comment获取课程本周评论信息统计
		$weekCommentCount = $this->comment->getWeekComment($course_id);
		//查询lit_comment获取课程本月评论信息统计
		$monthCommentCount = $this->comment->getMonthComment($course_id);
		/*****************************************************/

		/**************获取评论相关信息collect*****************/
		//查询lit_Collect获取课程总收藏信息统计
		$courseCollectInfo = count($courseInfo->Collect);
		//查询lit_Collect获取课程本周收藏信息统计
		$weekCollectCount = $this->collect->getWeekCollect($course_id);
		//查询lit_Collect获取课程本月收藏信息统计
		$monthCollectCount = $this->collect->getMonthCollect($course_id);
		/*****************************************************/
		
		//查询lit_Note获取课程总收藏信息统计
		$courseNoteInfo = count($courseInfo->Note);
		//查询lit_Collect获取课程本周日记信息统计
		$weekNoteCount = $this->note->getWeekNote($course_id);
		//查询lit_Collect获取课程本月日记信息统计
		$monthNoteCount = $this->note->getMonthNote($course_id);
		$this->assign([
			'courseInfo'  =>$courseInfo,
			'course_grade'=>$course_grade,
			'courseCountInfo'=>$courseCountInfo,
			'courseCommentInfo'=>$courseCommentInfo,
			'weekCommentCount'=>$weekCommentCount,
			'monthCommentCount'=>$monthCommentCount,
			'courseCollectInfo'=>$courseCollectInfo,
			'weekCollectCount'=>$weekCollectCount,
			'monthCollectCount'=>$monthCollectCount,
			'courseNoteInfo'=>$courseNoteInfo,
			'weekNoteCount'=>$weekNoteCount,
			'monthNoteCount'=>$monthNoteCount,

		]);
		return $this->fetch();
	}
	public function courseList()
	{
		// $courseListData = $this->course->getCourseListSelect();
		// $page = $courseListData->render();
		$courseListSelect = $this->plate->getCoursePlateSelect();
		$courseListData = $this->course->getCourseList();
		$page = $courseListData->render();
		$this->assign([
			'courseListSelect'=>$courseListSelect,
			'courseListData'=>$courseListData,
			'page'=>$page
		]);
		return $this->fetch();
	}
	//已经解决，thinkphp5多表分页+多条件筛选+分页地址栏额外参数传递不丢失+联合查询时表字段相同where条件参数+php页面的URL尾部参数出现查询条件=0，分页条件接收不到值
		//     public function test()
		//     {

		//         $q = Request::instance()->except(['page', 'sub'], 'get');;
		//         $pageParam = ['query' => []];
		//         $pageParam1 = ['query1' => []];

		//         if (isset($q["typeid"]) && $q["typeid"] !="") {
		//             $pageParam['query']['a.typeid'] = $q["typeid"];
		//             $pageParam1['query1']['typeid'] = $q["typeid"];
		//         }
		//         if (isset($q["ismake"]) && $q["ismake"] !="") {
		//             $pageParam['query']['ismake'] = $q["ismake"];
		//             $pageParam1['query1']['ismake'] = $q["ismake"];
		//         }
		//         if (isset($q["channel"]) && $q["channel"] !="") {
		//             $pageParam['query']['channel'] = $q["channel"];
		//             $pageParam1['query1']['channel'] = $q["channel"];
		//         }
		//         $q = $pageParam['query'];
		//         $q1 = $pageParam1['query1'];
		 
		 
		//         $list = Db::connect("db_config2")
		//             ->table('dede_archives')
		// //            ->strict(false)
		//             ->alias("a")
		// //            ->field("a.typeid")
		//             ->join('dede_addonarticle af','a.id = af.aid')
		//             ->where($q)
		//             ->paginate(5, false, [
		//             'type' => 'bootstrap',
		//             'var_page' => 'page',
		//             'query' => $q1,
		//         ]);
		//         $page = $list->render();
		//         // 模板变量赋值
		//         $this->assign('list', $list);
		//         $this->assign('page', $page);
		//         // 渲染模板输出
		//         return $this->fetch();
		//     }

    public function uploadVideo()
    {
    	return $this->fetch();
    }
    public function uptoken()
    {
    	$auth = new Authh('n7gzSmxbVb3kJGF_W4E1g7frzo5z8S_bi2PrCp_q', 'WOQSbsf1wrpZIh6-W3rL9jOaqExpcD4sZ_Srdg3i');
		//notify url
		//$wmImg = Qiniu\base64_urlSafeEncode('http://rwxf.qiniudn.com/logo-s.png');
		//$pfopOps = "avthumb/m3u8/wmImage/$wmImg";	
		$pfopOps = "avthumb/mp4/ab/128k/ar/22050/acodec/libfaac/r/30/vb/300k/vcodec/libx264/s/320x240/autoscale/1/stripmeta/0";	
		$policy = array(
		    'persistentOps' => $pfopOps,
		    //'persistentNotifyUrl' => 'http://127.0.0.1/callback.php',
		    //'persistentNotifyUrl' => 'http://172.30.251.210:8080/cb.php',
		    //'persistentPipeline' => 'litwhite',
		);

		$upToken = $auth->uploadToken('litwhite', null, 3600, $policy);

		return json_encode($upToken);
    }
    public function uptokenUrl()
    {
    	$auth = new Authh('n7gzSmxbVb3kJGF_W4E1g7frzo5z8S_bi2PrCp_q', 'WOQSbsf1wrpZIh6-W3rL9jOaqExpcD4sZ_Srdg3i');
		//notify url
		//$wmImg = Qiniu\base64_urlSafeEncode('http://rwxf.qiniudn.com/logo-s.png');
		//$pfopOps = "avthumb/m3u8/wmImage/$wmImg";	
		$pfopOps = "avthumb/mp4/ab/128k/ar/22050/acodec/libfaac/r/30/vb/300k/vcodec/libx264/s/320x240/autoscale/1/stripmeta/0";	
		$policy = array(
		    'persistentOps' => $pfopOps,
		    //'persistentNotifyUrl' => 'http://127.0.0.1/callback.php',
		    //'persistentNotifyUrl' => 'http://172.30.251.210:8080/cb.php',
		    //'persistentPipeline' => 'litwhite',
		);

		$upToken = $auth->uploadToken('litwhite', null, 3600, $policy);
		echo json_encode(['uptoken' => $upToken]); 
		//return json_encode($upToken); 
    }
    public function video()
    {
    	$this->assign('domain', 'http://oyo3pxmpc.bkt.clouddn.com/');
		$this->assign('uptokenUrl', 'uptoken');
		$this->assign('uptoken', $this->uptoken());
    	return $this->fetch();
    
	}
	public function addVideo()
    {
    	$link = $this->request->param('link');

    	file_put_contents('1.txt', $link);
    	return 11111;
    }

}
?>