<?php  
namespace app\admin\model;

use think\Model;
use traits\model\SoftDelete;
class Adminlist extends Model{
	use SoftDelete;
	public function adminToRole()
	{
		return $this->belongsToMany('adminrole','adminlist_adminrole','adminrole_id','adminlist_id');
		// return $this->hasOne('adminlist_adminrole');
	}
}
?>