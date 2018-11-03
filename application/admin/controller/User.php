<?php
namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\User as appUser;

class User extends Base
{

	public function lst()
	{	
		$user = new appUser;
		$o_users = $user->where('status', '1')
			->paginate(3);
		$a_arr = array();
		$a_arr['users'] = $o_users;

		return $this->fetch("lst", $a_arr);
	}

	public function add()
	{
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$tel = input('tel', '');
			$password = input('password', '');
			$integral = input('integral', '0');
			if (empty($name)) {
				return $this->no("用户名不能为空");
			}
			if (empty($tel)) {
				return $this->no("请填写电话号码");
			}
			if (empty($password)) {
				return $this->no("密码不能为空");
			}
			$password = md5($password);
			$user = new appUser;
			$is_exit_name = $user->where('name', $name)
				->where('status', '1')
				->find();
			if ($is_exit_name) {
				return $this->no("用户名已存在");
			}
			$user->name = $name;
			$user->password = $password;
			$user->tel = $tel;
			$user->integral = $integral;
			$res = $user->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes("用户增加成功");
		}
		if (Request::instance()->isGet()) {
			return $this->fetch('add');
		}
	}

	public function del()
	{
		$id = input('id');
		$user = new appUser;
		$o_user = $user->where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_user) {
			return $this->no("对象属性错误");
		}
		$o_user->status = 0;
		$res = $o_user->save();
		if (!$res) {
			return $this->no('删除失败');
		}

		return $this->yes("用户删除成功");
	}

	public function edit()
	{
		
		$id = input('id');
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$tel = input('tel', '');
			$password = input('password', '');
			$integral = input('integral', '0');
			if (empty($name)) {
				return $this->no("用户名不能为空");
			}
			if (empty($tel)) {
				return $this->no("请填写电话号码");
			}
			$user = new appUser;
			$is_exit_name = $user->where('name', $name)
				->where('id', 'neq', $id)
				->where('status', '1')
				->find();
			if ($is_exit_name) {
				return $this->no("用户名已存在");
			}
			$o_user = $user->where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_user) {
				return $this->no("对象属性错误");
			}
			if (!empty($password)) {
				$o_user->password = md5($password);
			}
			$o_user->integral = $integral;
			$o_user->name = $name;
			$o_user->tel = $tel;
			$res = $o_user->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("用户信息修改成功");
		}
		if (Request::instance()->isGet()) {
			$user = new appUser;
			$o_user = $user->where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('user', $o_user);

			return $this->fetch('edit');
		}
	}

	public function chongzhi()
	{
		$id = input('id');
		$integral = input('integral');
		$o_user = appUser::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_user) {
			return $this->no("对象属性错误");
		}
		$o_user->integral += $integral;
		$res = $o_user->save();
		if (!$res) {
			return $this->no("充值失败");
		} 

		return $this->yes("充值成功");
	}

}
