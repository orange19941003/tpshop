<?php
namespace app\admin\model;

use think\Model;

class Banner extends Model
{
	public function admin()
	{
		return $this->belongsTo('Admin', 'uid');
	}
}
