<?php
namespace app\index\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;

class CourseCount extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
	
}