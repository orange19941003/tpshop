<?php
namespace app\admin\model;

use think\Model;

class Withdraw extends Model
{
	public function admin()
	{
		return $this->belongsTo('Admin', 'uid');
	}

	public function user()
	{
		return $this->belongsTo('User', 'user_id');
	}
}
