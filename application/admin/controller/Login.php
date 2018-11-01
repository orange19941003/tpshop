<?php
namespace app\admin\controller;

use think\Session;
use think\Request;
use think\Controller;
use app\admin\model\Admin as adminUser;

class Login extends Controller 
{
	public function login() 
	{
		if (Request::instance()->isGet()) {
			return $this->fetch('login/login');
		} else {
            $name = input('name', '');
			$password = input('password', '');
			if (empty($name)) {
				return $this->error('用户名不能为空');
			} elseif (empty($password)) {
				return $this->error('请输入您的密码');
			} else {
				$user = new adminUser;
				$o_user = $user->where('name', $name)
					->where('status', '1')
					->find();
				if (!$o_user) {
					return $this->error('用户名不存在');
				}
				$password = md5($password);
				if ($password != $o_user->password) {
					return $this->error('密码错误');
				}
				Session::set('user', $name);

				return $this->success('登录成功', 'Index/Index');
			}
		}
		
	}

	public function logout()
	{
		Session::delete('user');
		return $this->success('退出成功', 'Login/login');
	}
}
