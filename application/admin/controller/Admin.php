<?php
namespace app\admin\controller;

use think\Db;
use think\Request;
use app\admin\controller\Base;
use app\admin\model\Admin as adminUser;

class Admin extends Base
{
	private $lst_code = "3-3-0";
	private $add_code = "3-3-1";
	private $edit_code = "3-3-2";
	private $del_code = "3-3-3";

	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['edit_code_status'] = $edit_code_status;
		$data['add_code_status'] = $add_code_status;
		$data['del_code_status'] = $del_code_status;
		$a_admins = db::name('admin')
			->where('status', 1)
			->select();
		$all_admins = [];
		foreach ($a_admins as $value) {
			$id = $value['id'];
			$a_role_id = Db::name('user_role')
				->where('user_id', $id)
				->column('role_id');
			$a_role_name = Db::name('role')
				->where('id', 'in', $a_role_id)
				->column('name');
			$value['role'] = $a_role_name;
			$all_admins[] = $value; 				
		}
		$this->assign('admins', $all_admins);

		return $this->fetch('lst', $data);
	}

	public function add()
	{
		$add_code = $this->add_code;
		$add_code_status = $this->checkCode($add_code);
		if ($add_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		if (Request::instance()->isGet()) {
			$a_roles = Db::name('role')
				->where('status', 1)
				->field('name, id')
				->select();
			$this->assign('roles', $a_roles);

			return $this->fetch('add');
		}
		if (Request::instance()->isAjax()) {
			$role = input('role', '');
			$name = input('name', '');
			$password = input('password', '');
			if (empty($role)) {
				return $this->no("请选择用户角色");
			}
			if (empty($name)) {
				return $this->no("昵称不能为空");
			}
			if (empty($password)) {
				return $this->no("密码不能为空");
			}
			preg_match_all('/\d+/', $role, $arr);
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
			$data = [];
			$data['user_id'] = $admin->id;
			$data['add_time'] = date('Y-m-d H:i:s');
			$data['uid'] = $this->admin_id;
			foreach ($arr['0'] as $value) {
				$data['role_id'] = $value;
				Db::name('user_role')
					->insert($data);
			}

			return $this->yes("新增成功");
		}

	}

	public function edit()
	{
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
			$a_roles = Db::name('role')
				->where('status', 1)
				->field('name, id')
				->select();
			$this->assign('roles', $a_roles);
			$a_role = Db::name('user_role')
				->where('user_id', $id)
				->where('status', 1)
				->column('role_id');
			$this->assign('a_role', $a_role);

			return $this->fetch('edit');
		}
		if (Request::instance()->isAjax()) {
			$role = input('role', '');
			$name = input('name', '');
			$password = input('password', '');
			if (empty($role)) {
				return $this->no("请选择用户角色");
			}
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
			preg_match_all('/\d+/', $role, $arr);
			$o_admin = $admin->where('id', $id)
				->where('status', '1')
				->find();
			$o_admin->name = $name;
			if (!empty($password)) {
				$o_admin->password = md5($password);
			}
			$res = $o_admin->save();
			Db::name('user_role')
				->where('user_id', $id)
				->update(["status"=>'0']);
			$data = [];
			$data['user_id'] = $id;
			$data['add_time'] = date('Y-m-d H:i:s');
			$data['uid'] = $this->admin_id;
			foreach ($arr['0'] as $value) {
				$data['role_id'] = $value;
				Db::name('user_role')
					->insert($data);
			}
			return $this->yes("修改成功");
		}
	
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
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
