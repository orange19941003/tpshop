<?php
namespace app\admin\model\sys;

use think\Model;

class Permission extends Model
{
	public function admin()
	{
		return $this->belongsTo('app\admin\model\Admin', 'uid');
	}

	public function permission()
	{
		return $this->belongsTo('Permission', 'pid');
	}
}
