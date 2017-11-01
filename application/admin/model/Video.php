<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use traits\model\SoftDelete;
class Video extends Model
{
	use SoftDelete;
	protected $deleteTime = 'delete_time';
}