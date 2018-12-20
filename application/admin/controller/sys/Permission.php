<?php

namespace app\admin\controller\sys;

use think\Db;
use think\Request;
use app\admin\controller\Base;
use app\admin\model\sys\Permission as adminPermission;

class Permission extends Base
{
	private $lst_code = '3-2-0';
	private $add_code = '3-2-1';
	private $edit_code = '3-2-2';
	private $del_code = '3-2-3';

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
		$o_permissions = adminPermission::where('status', '1')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $this->assign('permissions', $o_permissions);

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
			$p_permissions = adminPermission::where('level', '1')
				->where('status', '1')
				->select();
			$permissions = adminPermission::where('level', 'in', [1, 2])
				->where('status', '1')
				->select();
			$permissions =$this->sort($permissions);
			$this->assign('p_permissions', $p_permissions);
			$this->assign('permissions', $permissions);

			return $this->fetch('add');
		}

		if (Request::instance()->isAjax())
		{
			$data = [];
			$level = input('level');
			if ($level == 1)
			{
				$name = input('name_a', '');
				$code = input('code_a', '');
				if (empty($name))
				{
					return $this->no("名称不能为空");
				}
				if (empty($code))
				{
					return $this->no("code不能为空");
				}
				$data['level'] = $level;
				$data['name'] = $name;
				$data['code'] = $code;
				$data['uid'] = $this->admin_id;
				$data['add_time'] = date("Y-m-d H:i:s");
				$res = Db::name('permission')->insert($data);
				if (!$res) {
					return $this->no("新增失败");
				}

				return $this->yes("新增成功");
			}
			if ($level == 2)
			{
				$data['level'] = $level;
				$data['pid'] = input('pid_b');
				$data['name'] = input('name_b', '');
				$data['code'] = input('code_b', '');
				$data['path'] = input('path_b', '');
				$data['description'] = input('description_b', '');
				$data['add_time'] = date("Y-m-d H:i:s");
				$data['uid'] = $this->admin_id;
				if (empty($data['name']))
				{
					return $this->no("名称不能为空");
				}
				if (empty($data['code']))
				{
					return $this->no("code不能为空1");
				}
				if (empty($data['path']))
				{
					return $this->no("路径不能为空");
				}
				$res = Db::name('permission')->insert($data);
				if (!$res) {
					return $this->no("新增失败");
				}

				return $this->yes("新增成功");
			}
			if ($level == 3)
			{
				$data['level'] = $level;
				$data['pid'] = input('pid_c');
				$data['name'] = input('name_c', '');
				$data['code'] = input('code_c', '');
				$data['path'] = input('path_c', '');
				$data['description'] = input('description_b', '');
				$data['add_time'] = date("Y-m-d H:i:s");
				$data['uid'] = $this->admin_id;
				if ($data['pid'] == '0')
				{
					return $this->no("请选择父节点");
				}
				if (empty($data['name']))
				{
					return $this->no("名称不能为空");
				}
				if (empty($data['code']))
				{
					return $this->no("code不能为空");
				}
				if (empty($data['path']))
				{
					return $this->no("路径不能为空");
				}
				$res = Db::name('permission')->insert($data);
				if (!$res) {
					return $this->no("新增失败");
				}

				return $this->yes("新增成功");
			}
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
			$o_permission = adminPermission::where('id', $id)
				->where('status', '1')
				->find();
			$this->assign('permission', $o_permission);
			$p_permissions = adminPermission::where('level', '1')
				->where('status', '1')
				->select();
			$permissions = adminPermission::where('level', 'in', [1, 2])
				->where('status', '1')
				->select();
			$permissions =$this->sort($permissions);
			$this->assign('p_permissions', $p_permissions);
			$this->assign('permissions', $permissions);

			return $this->fetch('edit');
		}

		if (Request::instance()->isAjax())
		{
			$id = input('id');
			$data = [];
			$level = input('level');
			if ($level == 1)
			{
				$name = input('name_a', '');
				$code = input('code_a', '');
				if (empty($name))
				{
					return $this->no("名称不能为空");
				}
				if (empty($code))
				{
					return $this->no("code不能为空");
				}
				$data['level'] = $level;
				$data['name'] = $name;
				$data['code'] = $code;
				$data['uid'] = $this->admin_id;
				$res = Db::name('permission')
					->where('id', $id)
					->update($data);
				if (!$res) {
					return $this->no("修改失败");
				}

				return $this->yes("修改成功");
			}
			if ($level == 2)
			{
				$data['level'] = $level;
				$data['pid'] = input('pid_b');
				$data['name'] = input('name_b', '');
				$data['code'] = input('code_b', '');
				$data['path'] = input('path_b', '');
				$data['description'] = input('description_b', '');
				$data['uid'] = $this->admin_id;
				if (empty($data['name']))
				{
					return $this->no("名称不能为空");
				}
				if (empty($data['code']))
				{
					return $this->no("code不能为空1");
				}
				if (empty($data['path']))
				{
					return $this->no("路径不能为空");
				}
				$res = Db::name('permission')
					->where('id', $id)
					->update($data);
				if (!$res) {
					return $this->no("修改失败");
				}

				return $this->yes("修改成功");
			}
			if ($level == 3)
			{
				$data['level'] = $level;
				$data['pid'] = input('pid_c');
				$data['name'] = input('name_c', '');
				$data['code'] = input('code_c', '');
				$data['path'] = input('path_c', '');
				$data['description'] = input('description_b', '');
				$data['uid'] = $this->admin_id;
				if ($data['pid'] == '0')
				{
					return $this->no("请选择父节点");
				}
				if (empty($data['name']))
				{
					return $this->no("名称不能为空");
				}
				if (empty($data['code']))
				{
					return $this->no("code不能为空");
				}
				if (empty($data['path']))
				{
					return $this->no("路径不能为空");
				}
				$res = Db::name('permission')
					->where('id', $id)
					->update($data);
				if (!$res) {
					return $this->no("修改失败");
				}

				return $this->yes("修改成功");
			}
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
		$o_permission = adminPermission::where('id', $id)
			->find();
		if (!$o_permission)
		{
			return $this->no("对象属性错误");
		}
		$o_permission->status = 0;
		$o_permission->uid = $this->admin_id;
		$o_permission->save();

		return $this->yes('删除成功');
	}
}