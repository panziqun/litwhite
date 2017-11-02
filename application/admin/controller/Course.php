<?php  
namespace app\admin\controller;

use app\admin\model\Course;
use app\admin\model\CourseCount;
use app\admin\model\Comment;
use app\admin\model\Collect;
use app\admin\model\Plate;
use app\admin\model\Note;    	
use app\admin\model\Video; 
use think\Db;   	
use think\Session;	
use Qiniu\Auth as Authh;
use Qiniu\Storage\UploadManager;
use PHPExcel_IOFactory;
use PHPExcel;

class Course extends Auth{
	protected $course;
	protected $plate;
	protected $CourseCount;
	protected $comment;
	protected $collect;
	protected $note;
	protected $PHPExcel;

	protected $is_login = ['*'];
	private $website = "http://www.litwhite.com";
	/*
	*初始化获取course对象
	*
	*/
	public function _initialize()
	{
		parent::_initialize();
		$this->course 	   = new Course();
		$this->CourseCount = new CourseCount();
		$this->comment 	   = new Comment();
		$this->collect 	   = new Collect();
		$this->plate  	   = new Plate();
		$this->note  	   = new Note();
		$this->video 	   = new Video();
		$this->auth 	   = new Authh('n7gzSmxbVb3kJGF_W4E1g7frzo5z8S_bi2PrCp_q', 'WOQSbsf1wrpZIh6-W3rL9jOaqExpcD4sZ_Srdg3i');
	}
	/*
	*渲染courseAdd页面
	*
	*/
	public function courseAdd()
	{	
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '添加课程') === false) {
			$this->error('没有权限');
		}
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
		/**************获取课程方向*****************/
		$plate_title = $this->course
						->withTrashed()
						->alias('c')
						->join('lit_plate p','p.plate_id = c.plate_id')
						->where('c.course_id',$course_id)
						->field('p.plate_title')
						->find();
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
			'courseInfo'  	   =>$courseInfo,
			'course_grade'	   =>$course_grade,
			'courseCountInfo'  =>$courseCountInfo,
			'courseCommentInfo'=>$courseCommentInfo,
			'weekCommentCount' =>$weekCommentCount,
			'monthCommentCount'=>$monthCommentCount,
			'courseCollectInfo'=>$courseCollectInfo,
			'weekCollectCount' =>$weekCollectCount,
			'monthCollectCount'=>$monthCollectCount,
			'courseNoteInfo'   =>$courseNoteInfo,
			'weekNoteCount'	   =>$weekNoteCount,
			'monthNoteCount'   =>$monthNoteCount,
			'course_id'		   =>$course_id,
			'plate_title'      =>$plate_title['plate_title'],
		]);
		return $this->fetch();
	}
	public function courseList()
	{
		if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '课程列表') === false) {
			$this->error('没有权限');
		}
		// $courseListData = $this->course->getCourseListSelect();
		// $page = $courseListData->render();
		$courseListSelect = $this->plate->getCoursePlateSelect();
		$courseListData = $this->course->getCourseList();
		$page = $courseListData->render();
		$this->assign([
			'courseListSelect'=>$courseListSelect,
			'courseListData'=>$courseListData,
			'page'=>$page,
			'plate_id'=>1,
			'course_status'=>2,
		]);
		return $this->fetch();
	}
    public function uploadVideo()
    {
    	return $this->fetch();
    }
    public function uptoken()
    {
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

		$upToken = $this->auth->uploadToken('litwhite', null, 3600, $policy);
		return json_encode($upToken);
    }
    public function uptokenUrl()
    {
    	
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

		$upToken = $this->auth->uploadToken('litwhite', null, 3600, $policy);
		echo json_encode(['uptoken' => $upToken]);
    }
    public function video()
    {
    	if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '添加视频') === false) {
			$this->error('没有权限');
		}
    	$videos = $this->video->paginate(2);
    	$page = $videos->render();
    	$this->assign('domain', 'http://oyo3pxmpc.bkt.clouddn.com/');
		$this->assign('uptokenUrl', 'uptoken');
		$this->assign('videos', $videos);
		$this->assign('page', $page);
		$this->assign('uptoken', $this->uptoken());
    	return $this->fetch();
    
	}
	public function videoInsert()
    {
    	$video = $this->request->param();
    	$this->video->data([
    		'video_link'=>$video['video_link'],
    		'video_fname'=>$video['video_fname'],
    		'video_bucket'=>$video['video_bucket'],
    		'video_size'=>$video['video_size'],
    	]);
    	$result = $this->video->save();
    	if ($result) {
    		$this->success('上传成功' . $this->video->video_id);
    	} else {
    		$this->error('上传失败');
    	}
    }
    public function videoList()
    {
    	if (Session::get('admin_html') != 'super' && strpos(Session::get('admin_html'), '视频列表') === false) {
			$this->error('没有权限');
		}
    	$videos = $this->video->paginate(2);
    	$page = $videos->render();
    	$this->assign('domain', 'http://oyo3pxmpc.bkt.clouddn.com/');
		$this->assign('uptokenUrl', 'uptoken');
		$this->assign('videos', $videos);
		$this->assign('page', $page);
		$this->assign('uptoken', $this->uptoken());
    	return $this->fetch();
    
	}
	public function courseSearchByInput()
	{
		$courseListSelect = $this->plate->getCoursePlateSelect();
		$plate_id = $this->request->param('plate_id');
		$course_status = $this->request->param('course_status');
		$plateIdArr = $this->plate->select();
		$plateIdArr = $this->plate->getPlateIdByInpu($plate_id);

		if ($course_status==2) {
			$courseListData = Db::table('lit_course')
	            ->alias("course")
	            ->join('lit_plate plate','course.plate_id = plate.plate_id')
	            ->join('lit_course_count count','count.course_id = course.course_id')
	            ->where('course.plate_id','in',$plateIdArr)
	            ->field("course.course_id,course_pic,course_title,plate_title,course.create_time create_time,course.delete_time,course_sales")
	            ->paginate(5,false,['query'=>['plate_id'=>$plate_id,'course_status'=>$course_status]]);
		} else if ($course_status==0) {
			$courseListData =$this->course
	            ->alias("course")
	            ->join('lit_plate plate','course.plate_id = plate.plate_id')
	            ->join('lit_course_count count','count.course_id = course.course_id')
	            ->where('course.plate_id','in',$plateIdArr)
	            ->field("course.course_id,course_pic,course_title,plate_title,course.create_time create_time,course.delete_time,course_sales")
	            ->paginate(5,false,['query'=>['plate_id'=>$plate_id,'course_status'=>$course_status]]);
		} else if ($course_status==1) {
			$courseListData =$this->course
				->onlyTrashed()
	            ->alias("course")
	            ->join('lit_plate plate','course.plate_id = plate.plate_id')
	            ->join('lit_course_count count','count.course_id = course.course_id')
	            ->where('course.plate_id','in',$plateIdArr)
	            ->field("course.course_id,course_pic,course_title,plate_title,course.create_time create_time,course.delete_time,course_sales")
	            ->paginate(5,false,['query'=>['plate_id'=>$plate_id,'course_status'=>$course_status]]);
		}
		
		

		$page = $courseListData->render();
		$this->assign([
			'courseListSelect' => $courseListSelect,
			'courseListData'   => $courseListData,
			'course_status'    => $course_status,
			'plate_id'		   => $plate_id,
			'page'			   => $page,
		]);

		//dump($plateIdArr);
		return $this->fetch('courseList');
	}

	public function courseExcel()
	{
		//获取课程course_id
		$course_id = $this->request->param('course_id');
		//查询lit_course获取课程基本信息
		$courseInfo = $this->course->withTrashed()->find($course_id);
		/**************获取课程方向*****************/
		$plate_title = $this->course
						->withTrashed()
						->alias('c')
						->join('lit_plate p','p.plate_id = c.plate_id')
						->where('c.course_id',$course_id)
						->field('p.plate_title')
						->find();
		//通过获取器获取课程等级
		$course_grade = $courseInfo->course_grade;
		//查询lit_course获取课程总记信息统计
		$courseCountInfo = $courseInfo->CourseCount;

		$PHPExcel = new PHPExcel();
		$PHPSheet = $PHPExcel->getActiveSheet();
		$PHPSheet->setTitle('demo');
		$PHPSheet->setCellValue('A1','课程ID')
				 ->setCellValue('B1','课程名称')
				 ->setCellValue('C1','课程方向')
				 ->setCellValue('D1','学习人数')
				 ->setCellValue('E1','课程时长')
				 ->setCellValue('F1','课程评分')
				 ->setCellValue('G1','创建时间');

		$PHPSheet->setCellValue('A2',$course_id)
				 ->setCellValue('B2',$courseInfo['course_title'])
				 ->setCellValue('C2',$plate_title['plate_title'])
				 ->setCellValue('D2',$courseCountInfo['course_sales'])
				 ->setCellValue('E2',$courseInfo['course_duration'])
				 ->setCellValue('F2',$courseInfo['course_start'])
				 ->setCellValue('G2',$courseInfo['create_time']);
				

		$PHPWriter = PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');
		ob_end_clean();//清除缓冲区,避免乱码
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="课程信息表(' . date('Ymd-His') . ').xlsx"');
		header('Cache-Control: max-age=0');
		$PHPWriter->save("php://output");
	}


}
?>