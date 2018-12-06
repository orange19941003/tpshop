<?php
namespace app\api\controller;

use think\Request;
use think\Session;
use app\admin\model\User;
use app\admin\model\Reward;
use app\admin\model\vip\VipCate;
use app\admin\model\shop\Product;
use app\admin\model\shop\ShopCate;

class Share
{
	//share页面
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
		$a = Session::get("share$user_id");
		if ($a == null) {
			$data['message'] = '请求成功';
			$data['code'] = 200;
			$data['content'] = '';
			$j_data = json_encode($data);

			return $j_data; 
		}
		$o_user = User::where('id', $user_id)
			->where('status', '1')
			->field('id,vip')
			->find();
		if (!$o_user) {
			$data['message'] = '用户不存在';
			$data['code'] = 202;
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
		$addition = Reward::where('status', '1')
			->value('addition_one');
		$addition = $addition*0.01;
		if ($o_user->vip != 0) {
			$vip_addition = VipCate::where('id', $o_user->vip)
				->where('status', '1')
				->value('addition');
			$addition = $addition+$vip_addition*0.01;
		}
		foreach ($o_products as $vv) {
			$res = $vv->toArray();
			$res['reward'] = $vv['price']*$addition;
			$content[] = $res;
		}
		$data['message'] = '请求成功';
		$data['code'] = 200;
		$data['content'] = $content;
		$j_data = json_encode($data);

		return $j_data; 
	}

	//加入share池
	public function add()
	{
		$data = [];
		$token = input('token', '');
		$user_id = input('user_id', '-1');
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
		$a = Session::get("share$user_id");
		if ($a == null) {
			$session = [];
			Session::set("share$user_id", $session);
			$a = Session::get("share$user_id");
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
			$data['message'] = '商品已经在分享池中';
			$data['code'] = 401;
			$j_data = json_encode($data);

			return $j_data;
		}		
		$a[] = $pro_id;
		Session::set("share$user_id", $a);
		$data['message'] = '加入分享池成功';
		$data['code'] = 200;
		$j_data = json_encode($data);

		return $j_data;
	}

	//删除share池商品
	public function del() {
		$data = [];
		$user_id = input('id', '-1');
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
		$pro_id = input('pro_id', '');
		if (empty($pro_id)) {
			$data['message'] = '参数错误';
			$data['code'] = 202;
			$j_data = json_encode($data);

			return $j_data;
		}
		preg_match_all('/\d+/', $pro_id, $arr);
		$a = Session::get("share$user_id");
		$res = [];
		$arr[] = 1;
		foreach ($a as $vv) {
			if (!in_array($vv, $arr)) {
				$res[] = $vv;
			}
		}
		Session::set("share$user_id", $a);
		$data['message'] = '删除成功';
		$data['code'] = 200;
		$j_data = json_encode($data);

		return $j_data;
	}
}
