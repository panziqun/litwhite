<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class Collect extends Model
{
	public function getWeekCollect($course_id)
	{
		$beginWeek = mktime(0,0,0,date('m'),date('d')-date('w')+1,date('Y'));
		$endWeek = mktime(23,59,59,date('m'),date('d')-date('w')+7,date('Y'));

		$weekCollect = Db::table('lit_course')
					->alias('course')
					->join('lit_collect collect', 'course.course_id = collect.course_id')
					->field('course.course_id')
					->where('collect.create_time', 'between', [$beginWeek,$endWeek])
					->where('course.course_id',$course_id)
					->count();
		return $weekCollect;
	}
	public function getMonthCollect($course_id)
	{
		$beginMonth = mktime(0,0,0,date('m'),1,date('Y'));
		$endMonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
		$monthCollect = Db::table('lit_course')
					->alias('course')
					->join('lit_collect collect', 'course.course_id = collect.course_id')
					->field('course.course_id')
					->where('collect.create_time', 'between', [$beginMonth,$endMonth])
					->where('course.course_id',$course_id)
					->count();
		return $monthCollect;
	}
}