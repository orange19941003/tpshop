<?php
namespace app\admin\model\shop;

use think\Model;

class Address extends Model
{
	public function user(){
		return $this->belongsTo('app\admin\model\User', 'user_id');
	}
}
