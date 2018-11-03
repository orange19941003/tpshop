<?php

namespace app\admin\controller;

use think\Session;
use think\Controller;
use app\admin\model\Admin as adminUser;

class Base extends Controller
{
	public $admin_id;

	public function _initialize() 
	{
		header("Access-Control-Allow-Origin: *");
		$res = Session::has('user');
		if (!$res) {
			return $this->error('请先登录', 'Login/login');
		}
		$this->admin_id = Session::get('admin_id');
		$adminUser = Session::get('user');
		$this->assign('adminUser', $adminUser);
	}

	public function yes($msg)
	{
		$data = array();
		$data['msg'] = $msg;
		$data['code'] = 1;

		return $data;
	}

	public function no($msg)
	{
		$data = array();
		$data['msg'] = $msg;
		$data['code'] = 0;

		return $data;
	}

	//无限级分类排序
	public function sort($data,$pid=0,$level=0){
	    static $arr = [];
	    foreach($data as $v){
	        if($v['pid'] == $pid){
	            $v['level'] = $level;
	            $arr[] = $v;
	            $level++;
	            $this->sort($data,$v['id'],$level);
	        }       
	    }
	    return $arr;
	}
}
