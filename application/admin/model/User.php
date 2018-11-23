<?php
namespace app\admin\model;

use think\Model;

class User extends Model
{
	public function getParent()
	{
		return $this->belongsTo('User','pid');
	}

	public function admin()
	{
		return $this->belongsTo('Admin','uid');
	}

	public function cate()
	{
		return $this->belongsTo('app\admin\model\vip\VipCate', 'vip');
	}
}
