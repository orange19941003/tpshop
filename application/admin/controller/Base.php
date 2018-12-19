<?php

namespace app\admin\controller;

use think\Session;
use think\Controller;
use app\admin\model\Admin;
use app\admin\model\sys\Permission;

class Base extends Controller
{
	protected $admin_id;

	public function _initialize() 
	{
		$o_one_permissions = $this->getAllPermission(1);
		$this->assign('one_permissions', $o_one_permissions);
		$o_two_permissions = $this->getAllPermission(2);
		$this->assign('two_permissions', $o_two_permissions);
		$res = Session::has('user');
		if (!$res) {
			return $this->error('请先登录', 'Login/login');
		}
		$this->admin_id = Session::get('admin_id');
		$adminUser = Session::get('user');
		$this->assign('adminUser', $adminUser);
	}

	private function getAllPermission($level)
	{
		$o_permissions = Permission::where('status', '1')
			->where('level', "$level")
			->select();
		return $o_permissions;
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
