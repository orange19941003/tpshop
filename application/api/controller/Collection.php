<?php
namespace app\api\controller;

use think\Request;
use app\admin\model\shop\Collection;

class Collection
{
	//添加收藏接口
	public function add()
	{
		$data = [];
		$user_id = input('user_id');
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
		$pro_id = input('pro_id');
		$collection = new Collection;
		$collection->user_id = $user_id;
		$collection->pro_id = $pro_id;
		$collection->add_time = date("Y-m-d H:i:s");
		$collection->save();
		$data['code'] = 200;
		$data['message'] = "收藏成功";
		$j_all = json_encode($data);

		return $j_all;
	}

	//获取用户所有收藏接口
	public function lst()
	{
		$data = [];
		$user_id = input('user_id');
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
		$a_collection = Db::name('collection')
			->alias('c')
			->where('c.user_id', $user_id)
			->join('product p', 'c.pro_id = p.id')
			->field('c.id, c.pro_id, c.add_time, p.title, p.img, p.price, p.cost')
			->select();
		$data['collection'] = $a_collection;
		$data['code'] = 200;
		$data['message'] = "操作成功";
		$j_all = json_encode($data);

		return $j_all;
	}

	//取消收藏接口
	public function del()
	{
		$data = [];
		$coll_id = input('id');
		$token = input('token', '');
		$user_id = input('user_id');
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
		$o_cllection = Collection::where('id', $coll_id)
			->where('status', '1')
			->find();
		$o_cllection->status = 0;
		$o_cllection->save();
		$data['code'] = 200;
		$data['message'] = "操作成功";
		$j_all = json_encode($data);

		return $j_all;
	}
}
