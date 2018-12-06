<?php
namespace app\admin\model;

use think\Model;

class Income extends Model
{
	public function user()
	{
		return $this->belongsTo('User','user_id');
	}

	public function admin()
	{
		return $this->belongsTo('Admin','uid');
	}
}
