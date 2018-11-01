<?php
namespace app\admin\controller;

use think\Controller;
use app\admin\controller\Base;

class Index extends Base
{
	public function index() 
	{
		header("Access-Control-Allow-Origin: *");
		return $this->fetch('index');
	}
}
