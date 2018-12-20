<?php

namespace app\admin\controller\vip;

use think\Request;
use app\admin\model\User;
use app\admin\controller\Base;
use app\admin\model\vip\VipCate;
use app\admin\model\vip\Vip as appVip;

class Vip extends Base
{
	private $lst_code = '1-3-0';
	private $del_code = '1-3-1';
	
	public function lst()
	{
		$lst_code = $this->lst_code;
		$lst_code_status = $this->checkCode($lst_code);
		if ($lst_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		$data = [];
		$data['del_code_status'] = $del_code_status;
		$name = input('name', '');
		$id = User::where('name', $name)
			->where('status', '1')
			->value('id');
		if (!$id) {
			$id = '-1';
		}
		$s_id_eq = $id == '-1' ? 'neq' : 'eq';
		$cate_id = input('cate_id', '-1');
		$s_cate_id_eq = $cate_id == '-1' ? 'neq' : 'eq';
		$o_vips = appVip::where('user_id', $s_id_eq, $id)
			->where('cate_id', $s_cate_id_eq, $cate_id)
			->where('status', '1')
			->order('add_time', 'desc')
			->paginate(10, false, [
                'query' => Request::instance()->param(),//不丢失已存在的url参数
            ]);
        $o_cates = VipCate::all();
    	$data['name'] = $name;
    	$data['cate_id'] = $cate_id;
    	$data['cates'] = $o_cates;
    	$data['vips'] = $o_vips;

		return $this->fetch('lst', $data);
	}

	public function del()
	{
		$del_code = $this->del_code;
		$del_code_status = $this->checkCode($del_code);
		if ($del_code_status == 0) {
			exception('请不要乱输谢谢！', 100006);
		}
		$id = input('id');
		$o_vip = appVip::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_vip) {
			return $this->no("对象属性错误");
		}
		$o_vip->status = 0;
		$o_vip->uid = $this->admin_id;
		$res = $o_vip->save();
		if (!$res) {
			return $this->no("删除失败");
		}
		$o_user = User::where('id', $o_vip->user_id)
			->where('status', '1')
			->find();
		$o_user->vip = 0;
		$res = $o_user->save();
		if (!$res) {
			return $this->no("删除失败");
		}

		return $this->yes("删除成功");
	}
}
