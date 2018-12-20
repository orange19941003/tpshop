<?php

namespace app\admin\controller\sys;

use think\Db;
use think\Request;
use app\admin\controller\Base;
use app\admin\model\sys\Role as adminRole;

class Role extends Base
{
	private $lst_code = '3-4-0';
	private $add_code = '3-4-1';
	private $edit_code = '3-4-2';
	private $del_code = '3-4-3';

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
		$o_roles = adminRole::where('status', 1)
			->select();
		$this->assign('roles', $o_roles);

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
			$a_permissions = Db::name('permission')
				->where('status', '1')
				->field('id, name, level, pid')
				->select();
			$one_permissions = [];
			$two_permissions = [];
			$three_permissions = [];
			foreach ($a_permissions as $value) {
				if ($value['level'] == 1)
				{
					$one_permissions[] = $value; 
				}
				if ($value['level'] == 2)
				{
					$two_permissions[] = $value; 
				}
				if ($value['level'] == 3)
				{
					$three_permissions[] = $value;
				}
			}
			$data = [];
			$data['a_permissions'] = $one_permissions;
			$data['b_permissions'] = $two_permissions;
			$data['c_permissions'] = $three_permissions;

			return $this->fetch('add', $data);
		}
		if (Request::instance()->isAjax()) {
			$name = input('name', '');
			$permissions = input('permission', '');
			if (empty($permissions))
			{
				return $this->no("请给角色添加权限");
			}
			if (empty($name))
			{
				return $this->no("角色名不能为空");
			}
			preg_match_all('/\d+/', $permissions, $arr);
			$data = [];
			$data['name'] = $name;
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['uid'] = $this->admin_id;
			$id = Db::name('role')
				->insertGetId($data);
			if (!$id) {
				return $this->no('新增失败');
			}
			$data = [];
			$data['role_id'] = $id;
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['uid'] = $this->admin_id;
			foreach ($arr[0] as $vv) {
				$data['per_id'] = $vv;
				Db::name('permission_role')
					->insert($data);
			}

			return $this->yes('新增成功');
		}
	}

	public function edit()
	{
		$edit_code = $this->edit_code;
		$edit_code_status = $this->checkCode($edit_code);
		if ($edit_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		if (Request::instance()->isGet()) {
			$id = input('id');
			$name = Db::name('role')
				->where('id', $id)
				->value('name');
			$a_role_permissions = db::name('permission_role')
				->where('role_id', $id)
				->where('status', '1')
				->column('per_id');
			$a_permissions = Db::name('permission')
				->where('status', '1')
				->field('id, name, level, pid')
				->select();
			$one_permissions = [];
			$two_permissions = [];
			$three_permissions = [];
			foreach ($a_permissions as $value) {
				if ($value['level'] == 1)
				{
					$one_permissions[] = $value; 
				}
				if ($value['level'] == 2)
				{
					$two_permissions[] = $value; 
				}
				if ($value['level'] == 3)
				{
					$three_permissions[] = $value;
				}
			}
			$data = [];
			$data['id'] = $id;
			$data['name'] = $name;
			$data['permissions'] = $a_role_permissions;
			$data['a_permissions'] = $one_permissions;
			$data['b_permissions'] = $two_permissions;
			$data['c_permissions'] = $three_permissions;

			return $this->fetch('edit', $data);
		}
		if (Request::instance()->isAjax()) {
			$id = input('id');
			$name = input('name', '');
			$permissions = input('permission', '');
			if (empty($permissions))
			{
				return $this->no("请给角色添加权限");
			}
			if (empty($name))
			{
				return $this->no("角色名不能为空");
			}
			preg_match_all('/\d+/', $permissions, $arr);
			$data = [];
			$data['name'] = $name;
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['uid'] = $this->admin_id;
			Db::name('role')
				->where('id', $id)
				->update($data);
			$data = [];
			$data['status'] = 0;
			Db::name('permission_role')
				->where('role_id', $id)
				->update($data);
			$data = [];
			$data['role_id'] = $id;
			$data['add_time'] = date("Y-m-d H:i:s");
			$data['uid'] = $this->admin_id;
			foreach ($arr[0] as $vv) {
				$data['per_id'] = $vv;
				Db::name('permission_role')
					->insert($data);
			}

			return $this->yes('修改成功');
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
		$data = [];
		$data['status'] = 0;
		$res = Db::name('role')
			->where('id', $id)
			->update($data);
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
