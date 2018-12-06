<?php
namespace app\admin\model;

use think\Model;

class User extends Model
{
	public function parent()
	{
		return $this->belongsTo('User','pid');
	}

	public function pparent()
	{
		return $this->belongsTo('User','ppid');
	}

	public function ppparent()
	{
		return $this->belongsTo('User','pppid');
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
