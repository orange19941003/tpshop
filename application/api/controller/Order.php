<?php
namespace app\api\controller;

use think\Request;
use app\admin\model\User;
use app\admin\model\vip\Vip;
use app\admin\model\shop\Order;
use app\admin\model\vip\VipCate;
use app\admin\model\vip\VipOrder;
use app\admin\model\shop\Product;
use app\admin\model\shop\Address;

class Order
{
	//用户购买商品生成订单接口
	public function Shop
	{
		$pro_id = input('id');
		$user_id = input('user_id');
		$pay_num = input('pay_num');
		$address_id = input('address_id');
		$money = input('money');
		$pay_type = input('pay_type');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		preg_match_all('/\d+/', $pro_id, $arr);
		$o_products = Product::where('id', 'in', $arr)
			->select();
		$o_address = Address::where('id', $address_id)
			->find();
		$order = new Order;
		foreach ($o_products as $vv) {
			$order->user_id = $user_id;
			$order->pro_id = $vv->id;
			$order->tel = $o_address->tel;
			$order->name = $o_address->name;
			$order->money = $vv->price;
			$order->address = $o_address->address;
			$order->pay_type = $pay_type;
			$order->time = time();
			$order->save();
		}
		
		$data['code'] = 200;
		$data['message'] = "请求成功";
		$j_all = json_encode($data);

		return $j_all;			
	}

	//用户成为vip接口
	public function vip()
	{
		$user_id = input('user_id');
		$money = input('money');
		$pay_type = input('pay_type');
		$amount = input('amount');
		$cate_id = input('cate_id');
		$pay_num = input('pay_num');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$order = new VipOrder;
		$order->user_id = $user_id;
		$order->money = $money;
		$order->pay_type = $pay_type;
		$order->cate_id = $cate_id;
		$order->pay_num = $pay_num;
		$order->amount = $amount;
		$order->add_time = date("Y-m-d H:i:s");
		$order->save();
		$o_user = User::where('id', $user_id)
			->find();
		$o_vip_cate = VipCate::where('id', $cate_id);
		if (0 == $o_user->vip) {
			$o_user->vip = $cate_id;
			$o_user->save();
			$vip = new Vip;
			$vip->user_id = $user_id;
			$vip->cate_id = $cate_id;
			$time = time();
			$day = $o_vip_cate->day;
			$time = $time + $day*60*60*24*$amount;
			$vip->end_time = date('Y-m-d H:i:s', $time);
			$vip->add_time = date('Y-m-d H:i:s');
			$vip->save();
		}
		$data['code'] = 200;
		$data['message'] = "请求成功";
		$j_all = json_encode($data);

		return $j_all;
	}
}
