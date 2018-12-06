<?php
namespace app\admin\controller;

use think\Request;
use app\admin\controller\Base;
use app\admin\model\Admin as adminUser;

class Admin extends Base
{
	public function lst()
	{
		$o_admins = adminUser::where('status', 1)
			->select();
		$this->assign('admins', $o_admins);

		return $this->fetch('lst');
	}

	public function add()
	{
		if (Request::instance()->isGet()) {
			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$password = input('password', '');
			if (empty($name)) {
				return $this->no("昵称不能为空");
			}
			if (empty($password)) {
				return $this->no("密码不能为空");
			}
			$password = md5($password);
			$admin = new adminUser;
			$admin->name = $name;
			$admin->uid = $this->admin_id;
			$time = time();
			$admin->add_time = $this->time($time);
			$admin->password = $password;
			$res = $admin->save();
			if (!$res) {
				return $this->no("新增失败");
			}

			return $this->yes("新增成功");
		}

	}

	public function edit()
	{
		$id = input('id');
		$admin = new adminUser;
		if (Request::instance()->isGet()) {
			$o_admin = $admin->where('id', $id)
				->where('status', '1')
				->find();
			if (!$o_admin) {
				return $this->no("对象属性错误");
			}
			$this->assign('admin', $o_admin);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$password = input('password', '');
			if (empty($name)) {
				return $this->no("昵称不能为空");
			}
			$is_exit_name = $admin->where('name', $name)
				->where('id', 'neq', $id)
				->where('status', '1')
				->find();
			if ($is_exit_name) {
				return $this->no("名称已存在");
			}
			$o_admin = $admin->where('id', $id)
				->where('status', '1')
				->find();
			$o_admin->name = $name;
			if (!empty($password)) {
				$o_admin->password = md5($password);
			}
			$res = $o_admin->save();
			if (!$res) {
				return $this->no("修改失败");
			}

			return $this->yes("修改成功");
		}
	
	}

	public function del()
	{
		$id = input('id');
		$o_admin = adminUser::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_admin) {
			return $this->no("对象属性错误");
		}
		$o_admin->status = 0;
		$o_admin->save();

		return $this->yes('删除成功');
	}
}
