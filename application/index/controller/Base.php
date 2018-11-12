<?php

namespace app\index\controller;

use think\Session;
use think\Controller;
use app\admin\model\User;
use app\admin\model\shop\Cate;

class Base extends Controller
{
	public $appUser;
	public $appUser_id;

	public function _initialize() 
	{
		$o_cates = Cate::where('status', '1')
			->select();
		$this->assign('header_cates', $o_cates);
		$this->appUser = Session::get('appUser');
		$appUser = $this->appUser;
		$this->appUser_id = User::where('name', $appUser)
			->where('status', '1')
			->value('id');
		if (!$appUser) {
			$appUser = '';
		}
		$this->assign('appUser', $appUser);
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
}
