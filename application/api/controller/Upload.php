<?php
namespace app\api\controller;

use think\Image;
use think\Request;

class Upload
{
	public function index()
	{
		$pic_a = request()->file('file');
		/*$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		$res = array();
		$res['code'] = 1;
		$res['id'] = rand(1, 1000);
		$res['url'] = $info->getSaveName();
		return $res;*/

		$info = $pic_a->move(ROOT_PATH . 'public' . DS . 'uploads');
		$pic = $info->getSaveName();
		$image = Image::open($pic_a);
		$time = date('Ymd');
		$saveName = $time."/".time().'.png';
		$image->thumb(150, 150)->save(ROOT_PATH . 'public' . DS . 'uploads/'.$saveName);
		$res = array();
		$res['code'] = 1;
		$res['id'] = rand(1, 1000);
		$res['url'] = $saveName;
		return $res;
	}

}