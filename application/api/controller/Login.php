<?php
namespace app\api\controller;

use think\Session;
use think\Request;
use app\admin\model\User;

class Login
{
	//登录接口
	public function login()
	{
		$data = [];
		$name = input('name', '');
		$password = input('password', '');
		if (empty($name) || empty($password)) {
			$data['code'] = 400;
			$data['message'] = "用户名密码不能为空";
			$j_data = json_encode($data);

			return $j_data; 
		}
		$o_user = User::where('name', $name)
			->where('sattus', '1')
			->find();
		if (!$o_user) {
			$data['code'] = 400;
			$data['message'] = "用户不存在";
			$j_data = json_encode($data);

			return $j_data; 
		}
		if (md5($o_user->password) != $password) {
			$data['code'] = 400;
			$data['message'] = "密码错误";
			$j_data = json_encode($data);

			return $j_data;
		}
		$id = $o_user->id;
		$token = md5(md5("$name"));
		session::set("token$id", "$token");
		$data['token'] = $token;
		$data['code'] = 200;
		$data['message'] = "登录成功";
		$j_data = json_encode($data);

		return $j_data;
	}

	public function logout()
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
		Session::delete('token$user_id');
		$data['code'] = 200;
		$data['message'] = "退出成功";
		$j_data = json_encode($data);

		return $j_data;
	}
}
