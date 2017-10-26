<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class Comment extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	public function getWeekComment($course_id)
	{
		//本周开始时间时间戳1508688000
		$beginWeek = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		//本周开始时间时间戳1509292799
		$endWeek = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));
		$weekComment = Db::table('lit_course')
            ->alias("course")
            ->join('lit_comment comment', 'course.course_id = comment.course_id')
            ->field("course.course_id")
            ->where('comment.create_time', 'between', [$beginWeek,$endWeek])
            ->where('course.course_id',$course_id)
            ->count();
		return $weekComment;
	}
	public function getMonthComment($course_id)
	{
		$beginMonth = mktime(0,0,0,date('m'),1,date('Y'));
		$endMonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
		$monthComment = Db::table('lit_course')
			->alias('course')
			->join('lit_comment comment', 'course.course_id = comment.course_id')
			->field('course.course_id')
			->where('comment.create_time', 'between', [$beginMonth,$endMonth])
			->where('course.course_id',$course_id)
			->count();
		return $monthComment;
	}
}