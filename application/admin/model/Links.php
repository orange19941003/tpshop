<?php
namespace app\admin\model;

use think\Model;

class Links extends Model
{
	public function admin()
	{
		return $this->belongsTo('Admin','uid');
	}
}
