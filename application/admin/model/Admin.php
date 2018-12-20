<?php
namespace app\admin\model;

use think\Model;

class Admin extends Model
{
	public function userRole()
	{
		return $this->belongsTo('app\admin\model\sys\UserRole', 'user_id');
	}
}
