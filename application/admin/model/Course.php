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
            ->field("course.course_id,course_title,plate_title,course.create_time create_time,course.delete_time,course_sales")
            ->paginate(5);
        // $page = $list->render();
        // 模板变量赋值
        // $this->assign('list', $list);
        // $this->assign('page', $page);
        // 渲染模板输出
        return $list;
		
	}
	public function courseInsertData($courseData)
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
			'course_pic'=>$courseData['course_pic'],
			'course_url'=>$courseData['course_url'],
			'plate_id'=>$courseData['plate_id'],
		]);
		$result = $this->save();
		if (((int)$courseData['course_status']==1)) {
			$result = $this->destroy($this->course_id);
		}
		return $this->course_id;
	}
}