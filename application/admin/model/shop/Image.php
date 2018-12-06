<?php
namespace app\admin\model\shop;

use think\Model;

class Image extends Model
{
	public function product(){
		
		return $this->belongsTo('Product', 'pro_id');
	}

	public function admin(){
		
		return $this->belongsTo('app\admin\model\Admin', 'admin_id');
	}
}
