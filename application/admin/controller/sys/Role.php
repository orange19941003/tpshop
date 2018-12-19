<?php

namespace app\admin\controller\sys;

use think\Db;
use think\Request;
use app\admin\controller\Base;
use app\admin\model\sys\Role as adminRole;

class Role extends Base
{
	public function lst()
	{
		$o_roles = adminRole::where('status', 1)
			->select();
		$this->assign('roles', $o_roles);

		return $this->fetch('lst');
	}

	public function add()
	{
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
	}

	public function edit()
	{

	}

	public function del()
	{

	}
}
