<?php
namespace app\admin\controller\shop;

use think\Request;
use app\admin\model\User;
use app\admin\controller\Base;
use app\admin\model\shop\Address as shopAddress;

class Address extends Base
{
	public function lst()
	{
		$user_name = input('user_name', '');
		$user_id = '-1';
		$s_user_id_eq = 'neq';
		if (!empty($user_name)) {
			$user_id = User::where('name', $user_name)
				->where('status', '1')
				->value('id');
			if (!$user_id) {
				$user_id = '-1';
				$s_user_id_eq = 'eq';
			} else {
				$s_user_id_eq = 'eq';
			}
		}
		$o_address = shopAddress::where('status', '1')
			->where('user_id', $s_user_id_eq, $user_id)
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
		$this->assign('addresss', $o_address);
		$this->assign('user_name', $user_name);

		return $this->fetch('lst');
	}
}
