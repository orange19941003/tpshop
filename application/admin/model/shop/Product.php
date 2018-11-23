<?php
namespace app\admin\model\shop;

use think\Model;

class Product extends Model
{
	public function cate(){
		return $this->belongsTo('Cate','cate_id');
	}
}
