<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Course extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function getCourseListSelect()
	{
		$courseInfo = $this->withTrashed()->paginate(5);
		file_put_contents('sql.txt', $this->getLastSql());
		return $courseInfo;
	}
	public function getCourseList()
	{
		// $q = Request::instance()->except(['page'], 'get');
  //       $pageParam = ['query' => []];
  //       $pageParam1 = ['query1' => []];

  //       if (isset($q["typeid"]) && $q["typeid"] !="") {
  //           $pageParam['query']['a.typeid'] = $q["typeid"];
  //           $pageParam1['query1']['typeid'] = $q["typeid"];
  //       }
  //       if (isset($q["ismake"]) && $q["ismake"] !="") {
  //           $pageParam['query']['ismake'] = $q["ismake"];
  //           $pageParam1['query1']['ismake'] = $q["ismake"];
  //       }
  //       if (isset($q["channel"]) && $q["channel"] !="") {
  //           $pageParam['query']['channel'] = $q["channel"];
  //           $pageParam1['query1']['channel'] = $q["channel"];
  //       }
  //       $q = $pageParam['query'];
  //       $q1 = $pageParam1['query1'];
  //       dump($q);
 
        $list = Db::table('lit_course')
            ->alias("course")
            ->join('lit_plate plate','course.plate_id = plate.plate_id')
            ->join('lit_course_count count','count.course_id = course.course_id')
            ->field("course.course_id,course_pic,course_title,plate_title,course.create_time create_time,course.delete_time,course_sales")
            ->paginate(5);
        // $page = $list->render();
        // 模板变量赋值
        // $this->assign('list', $list);
        // $this->assign('page', $page);
        // 渲染模板输出
        return $list;
		
	}
	public function courseInsertData($courseData, $fileURL)
	{
		$this->data([
			'course_title'=>$courseData['course_title'],
			'course_describe'=>$courseData['course_describe'],
			'course_keywords'=>$courseData['course_keywords'],
			'course_teacher'=>$courseData['course_teacher'],
			'course_rate'=>$courseData['course_rate'],
			'course_duration'=>$courseData['course_duration'],
			'course_grade'=>$courseData['course_grade'],
			'course_order'=>$courseData['course_order'],
			'course_url'=>$courseData['course_url'],
			'plate_id'=>$courseData['plate_id'],
			'course_pic'=>$fileURL,
		]);
		$result = $this->save();
		if (((int)$courseData['course_status']==1)) {
			$result = $this->destroy($this->course_id);
		}
		return $this->course_id;
	}
	public function courseChangeData($courseData, $fileURL)
	{
		dump($courseData['course_id']);
		$course = $this->withTrashed()->find($courseData['course_id']);
		$course->data([
			'course_title'	 =>$courseData['course_title'],
			'course_describe'=>$courseData['course_describe'],
			'course_keywords'=>$courseData['course_keywords'],
			'course_teacher' =>$courseData['course_teacher'],
			'course_rate'	 =>$courseData['course_rate'],
			'course_duration'=>$courseData['course_duration'],
			'course_grade'	 =>$courseData['course_grade'],
			'course_order'	 =>$courseData['course_order'],
			'course_url'	 =>$courseData['course_url'],
			'plate_id'	     =>$courseData['plate_id'],
		]);
		$result = $course->save();
		if ($fileURL) {
			$course->data([
			'course_pic'	 =>$fileURL,
			]);
			$result = $course->save();
			if (empty($result)) {
				return ['code'=>500,'info'=>'操作失败1'];
			}
		} 
		
		if ($courseData['course_status']==1) {
			$result = $course->destroy($courseData['course_id']);
		} else {
			
			$course->delete_time = NULL;
			$course->save();
			$result = 1;
		}
		if (empty($result)) {
			return ['code'=>500,'info'=>'操作失败2'];
		}
		return ['code'=>200,'info'=>'操作成功'];
	}
	
	public function getCourseInfo($course_id)
	{
		$result = $this->withTrashed()->find($course_id);
		return $result;
	}
	public function CourseCount()
	{
		return $this->hasOne('CourseCount','course_id');
	}
	public function Comment()
	{
		return $this->hasMany('Comment','course_id');
	}
	public function Collect()
	{
		return $this->hasMany('Collect','course_id');
	}
	public function Note()
	{
		return $this->hasMany('Note','course_id');
	}
	// public function getCourseGradeAttr($value)
	// {
	// 	$status = [0=>'初级',1=>'中级',2=>'高级'];
	// 	return $status[$value];
	// }
}