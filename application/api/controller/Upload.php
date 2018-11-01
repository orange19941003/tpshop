<?php

namespace app\api\controller;

use think\Request;

class Upload
{
	public function index()
	{
		$file = request()->file('file');
		$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		$res = array();
		$res['code'] = 1;
		$res['url'] = $info->getSaveName();
		return $res;
	}
}