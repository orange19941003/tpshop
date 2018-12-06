<?php
namespace app\admin\model\shop;

use think\Model;
use app\admin\model\Admin;
use app\admin\model\shop\Product;

class Order extends Model
{
	public function admin()
	{

		return $this->belongsTo('app\admin\model\Admin', 'admin_id');
	}

	public function product()
	{

		return $this->belongsTo('Product', 'pro_id');
	}

	public function user(){
		
		return $this->belongsTo('app\admin\model\User', 'user_id');
	}
}
