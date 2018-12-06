<?php
namespace app\api\controller;

use think\Request;
use think\Session;
use app\admin\model\shop\Product;

class Shopcart
{
	//购物车页面
	public function index() {
		$data = [];
		$user_id = input('user_id', '-1');
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
		if ($user_id == '-1') {
			$data['message'] = '用户未登录';
			$data['code'] = 201;
			$j_data = json_encode($data);

			return $j_data;
		}
		$a = Session::get("$user_id");
		if ($a == null) {
			$data['message'] = '请求成功';
			$data['code'] = 200;
			$data['content'] = '';
			$j_data = json_encode($data);

			return $j_data; 
		}
		$o_products = Product::where('status', '1')
			->where('id', 'in', $a)
			->order('pv', 'desc')
			->field('id, title, img, price')
			->select();
		$res = [];
		$content = [];
		foreach ($o_products as $vv) {
			$res = $vv->toArray();
			$content[] = $res;
		}
		$data['message'] = '请求成功';
		$data['code'] = 200;
		$data['content'] = $content;
		$j_data = json_encode($data);

		return $j_data; 
	}

	//加入购物车
	public function add()
	{
		$data = [];
		$user_id = input('user_id', '-1');
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
		if ($user_id == '-1') {
			$data['message'] = '用户未登录';
			$data['code'] = 201;
			$j_data = json_encode($data);

			return $j_data;
		}
		$a = Session::get("$user_id");
		if ($a == null) {
			$session = [];
			Session::set("$user_id", $session);
			$a = Session::get("$user_id");
		}
		$pro_id = input('pro_id', '');
		if (empty($pro_id)) {
			$data['message'] = '参数错误';
			$data['code'] = 202;
			$j_data = json_encode($data);

			return $j_data;
		}
		$res = in_array($pro_id, $a);
		if ($res == 'true') {
			$data['message'] = '商品已经在购物车中';
			$data['code'] = 401;
			$j_data = json_encode($data);

			return $j_data;
		}		
		$a[] = $pro_id;
		Session::set("$user_id", $a);
		$data['message'] = '加入购物车成功';
		$data['code'] = 200;
		$j_data = json_encode($data);

		return $j_data;
	}

	//删除购物车商品
	public function del() {
		$token = input('token', '');
		$user_id = input('id', '-1');
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
		if ($user_id == '-1') {
			$data['message'] = '用户未登录';
			$data['code'] = 201;
			$j_data = json_encode($data);

			return $j_data;
		}
		$pro_id = input('pro_id', '');
		if (empty($pro_id)) {
			$data['message'] = '参数错误';
			$data['code'] = 202;
			$j_data = json_encode($data);

			return $j_data;
		}
		preg_match_all('/\d+/', $pro_id, $arr);
		$a = Session::get("$user_id");
		$res = [];
		foreach ($a as $vv) {
			if (!in_array($vv, $arr)) {
				$res[] = $vv;
			}
		}
		$a = $res;
		Session::set("$user_id", $a);
		$data['message'] = '删除成功';
		$data['code'] = 200;
		$j_data = json_encode($data);

		return $j_data;
	}
}
