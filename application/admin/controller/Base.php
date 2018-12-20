<?php

namespace app\admin\controller;

use think\Db;
use think\Session;
use think\Controller;
use app\admin\model\Admin;
use app\admin\model\sys\Permission;

class Base extends Controller
{
	protected $admin_id;

	public function _initialize() 
	{
		$res = Session::has('user');
		if (!$res) {
			return $this->error('请先登录', 'Login/login');
		}
		$this->admin_id = Session::get('admin_id');
		$adminUser = Session::get('user');
		$this->assign('adminUser', $adminUser);

		//获取权限
		$o_one_permissions = $this->getAllPermission(1);
		$this->assign('one_permissions', $o_one_permissions);
		$o_two_permissions = $this->getAllPermission(2);
		$this->assign('two_permissions', $o_two_permissions);
	}

	protected function checkCode($code)
	{
		$user_code = $this->getUserCode();
		if (in_array($code, $user_code))
		{
			return 1;
		}

		return 0;
	}

	private function getAllPermission($level)
	{
		$user_code = $this->getUserCode();
		$o_permissions = Permission::where('status', '1')
			->where('code', 'in', $user_code)
			->where('level', "$level")
			->select();
		return $o_permissions;
	}

	private function getAllPermissionCode()
	{
		$a_permissions = Permission::where('status', 1)
			->column('code');

		return $a_permissions;
	}

	private function getUserCode()
	{
		$user_id = $this->admin_id;
		$a_role = Db::name('user_role')
			->where('user_id', $user_id)
			->where('status', 1)
			->column('role_id');
		$user_permissions = Db::name('Permission_role')
			->where('role_id', 'in', $a_role)
			->where('status', 1)
			->column('per_id');
		$user_permissions = array_unique($user_permissions);
		$user_code = Permission::where('id', 'in', $user_permissions)
			->where('status', 1)
			->column('code');
		return $user_code;
	}

	protected function yes($msg)
	{
		$data = array();
		$data['msg'] = $msg;
		$data['code'] = 1;

		return $data;
	}

	protected function no($msg)
	{
		$data = array();
		$data['msg'] = $msg;
		$data['code'] = 0;

		return $data;
	}

	protected function time($time)
	{
		$time = date("Y-m-d H:i:s", $time);

		return $time;
	}

	//无限级分类排序
	public function sort($data,$pid=0)
	{
	    static $arr = [];
	    foreach($data as $v){
	        if($v['pid'] == $pid){
	            //$v['level'] = $level;
	            $arr[] = $v;
	            //$level++;
	            $this->sort($data,$v['id']);
	        }
	    }

	    return $arr;
	}
}
