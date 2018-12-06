<?php
namespace app\api\controller;

use think\Db;
use think\Session;
use think\Request;
use app\admin\model\User;
use app\admin\model\Income;
use app\admin\model\Withdraw;
use app\admin\model\shop\Order;
use app\admin\model\shop\ShopCate;

class Index
{
	//首页接口cate_id,page
	public function index()
	{
		$data = [];
		$cate_id = input('cate_id', '-1');
		$page = input('page', '1');
		$page_s = ($page-1)*6 + 1;
		$page_e = $page_s + 5;
		$s_cate_id = $cate_id == '-1' ? 'neq' : 'eq';
		if ($cate_id != '-1') {
			$o_cate = ShopCate::where('id', $cate_id)
				->where('status', '1')
				->find();
			if (!$o_cate) {
				$data['code'] = 400;
				$data['message'] = "参数错误";
				$j_all = json_encode($data);

				return $j_all;
			}
			$a_cates = Db::name('shop_cate')->where('pid', $cate_id)
				->where('status', '1')
				->field('id')
				->select();
			$a_cate = [];
			foreach ($a_cates as $vv) {
				$a_cate[] = $vv['id'];
			}
			$a_products = Db::name('product')->where('status', '1')
				->where('cate_id', 'in', $a_cate)
				->order('pv', 'desc')
				->limit($page_s, $page_e)
				->field('id, title, img, price, cost')
				->select();
		} else {
			$a_products = Db::name('product')->where('status', '1')
				->where('cate_id', 'neq', $cate_id)
				->order('pv', 'desc')
				->limit($page_s, $page_e)
				->field('id, title, img, price, cost')
				->select();
		}
			
		$data['product'] = $a_products;
		$data['code'] = 200;
		$data['message'] = "请求成功";
		$j_all = json_encode($data);

		return $j_all;
	}

	//商品详情接口
	public function details()
	{
		$data = [];
		$pro_id = input('pro_id');
		$pro_id = 2;
		$a_products = Db::name('product')
			->where('id', $pro_id)
			->where('status', '1')
			->field('id, title, img, price, cost')
			->find();
		$data['content'] = $a_products;
		$a_pro_types = Db::name('pro_type')
			->where('pro_id', $pro_id)
			->where('status', '1')
			->field('id', 'type')
			->select();
		$data['type'] = $a_pro_types;
		$data['code'] = 200;
		$data['message'] = "请求成功";
		$j_all = json_encode($data);

		return $j_all;
	}

	//首页分类banner图接口
	public function cate()
	{
		$data = [];
		$a_top_cate = [];
		$a_top_cates = Db::name('shop_cate')->where('status', '1')
			->where('level', '1')
			->order('weight', 'desc')
			->limit(4)
			->field('id,title')
			->select();
		$data['top_cate'] = $a_top_cates;
		$a_banner = [];
		$a_banner = Db::name('banner')->where('status', '1')
			->order('weight', '1')
			->field('id, img, url')
			->select();
		$data['banner'] = $a_banner;
		$data['code'] = 200;
		$data['message'] = "请求成功";
		$j_all = json_encode($data);

		return $j_all;	
	}

	//我的页面接口
	public function self() {
		$data = [];
		$id = input('id', '-1');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		if ($id == '-1') {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$o_user = User::where('id', $id)
			->where('status', '1')
			->find();
		if (!$o_user) {
			$data['code'] = 400;
			$data['message'] = "参数错误";
			$j_all = json_encode($data);

			return $j_all;
		}
		$data['name'] = $o_user->name;
		$data['id'] = $id;
		$f_month_incomes = Income::where('user_id', $id)
			->whereTime("add_time", 'm')
			->where('status', '1')
			->sum('money');
		$f_day_incomes = Income::where('user_id', $id)
			->whereTime("add_time", 'd')
			->where('status', '1')
			->sum('money');
		$data['month_incomes'] = $f_month_incomes;
		$data['day_incomes'] = $f_day_incomes;
		$data['code'] = 200;
		$data['message'] = '请求成功';
		$j_data = json_encode($data);

		return $j_data;
	}

	//订单接口
	public function order()
	{
		$data = [];
		$user_id = input('user_id');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$a_orders = Db::name('order')
			->alias('o')
			->where('o.user_id', $user_id)
			->where('o.is_del', '1')
			->join('product p', 'o.pro_id = p.id')
			->field('o.id, o.pro_id, o.money, o.address, o.information, o.time, p.title, p.img, p.price, p.cost')
			->select();
		$data['order'] = $a_orders;
		$data['code'] = 200;
		$data['message'] = '请求成功';
		$j_data = json_encode($data);

		return $j_data;
	}

	//分类页面分类数据接口
	public function more_cate()
	{
		$data = [];
		$a_cate = Db::name('shop_cate')->where('level', '2')
			->where('status','1')
			->order('weight', 'desc')
			->field('id,title')
			->select();
		$data['cates'] = $a_cate;
		$data['code'] = 200;
		$data['message'] = '请求成功';
		$j_data = json_encode($data);

		return $j_data;
	}

	//分类页面商品接口
	public function product()
	{
		$page = input('page', '1');
		$page_s = ($page-1)*18 + 1;
		$page_e = $page_s + 17;
		$cate_id = input('cate_id', '0');
		$data = [];
		$a_products = Db::name('product')->where('cate_id', $cate_id)
			->where('status', '1')
			->order('pv', 'desc')
			->limit($page_s, $page_e)
			->field('id,title,desc,img,price,pv')
			->select();
		$data['products'] = $a_products;
		$data['code'] = 200;
		$data['message'] = '请求成功';
		$j_data = json_encode($data);

		return $j_data;
	}

	//用户点击确认收货接口
	public function confirm()
	{
		$data = [];
		$user_id = input('id');
		$order_id = input('order_id');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$order = Order::where('id', $order_id)
			->where('type', '1')
			->where('status', 'neq', '2')
			->find();
		if (!$order) {
			$data['code'] = 400;
			$data['message'] = "收货失败";
			$j_all = json_encode($data);

			return $j_all;
		}
		$o_incomes = Income::where('order_id', $order_id)
			->where('status', '1')
			->select();
		$user = new User;
		foreach ($o_incomes as $vv) {
			$user_id = $vv->user_id;
			$o_user = $user->where('id', $user_id)
				->where('status', '1')
				->find();
			$o_user->fmoney -= $vv->money;
			$o_user->money += $vv->money;
			$o_user->save();
		}
		$order->status = 2;
		$order->save();
		$data['code'] = 200;
		$data['message'] = "操作成功";
		$j_all = json_encode($data);

		return $j_all;
	}

	//发起提现接口
	public function withdraw()
	{
		$data = [];
		$user_id =  input('user_id');
		$money = input('money');
		$zhifubao = input('zhifubao');
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		// 启动事务
        Db::startTrans();
        try {
			if (empty($money) || empty($zhifubao)) {
				$data['code'] = 400;
				$data['message'] = "参数错误";
				$j_all = json_encode($data);

				return $j_all;
			}
			$o_user = User::where('id', $user_id)
				->find();
			$user_money = $o_user->money;
			if ($user_money < $money) 
			{
				$data['code'] = 401;
				$data['message'] = "余额不足";
				$j_all = json_encode($data);

				return $j_all;
			}
			$o_user->money -= $money;
			$withdraw = new Withdraw;
			$withdraw->user_id = $user_id;
			$withdraw->zhifubao = $zhifubao;
			$withdraw->money = $money;
			$withdraw->add_time = date("Y-m-d H:i:s");
			$o_user->save();
			$withdraw->save();

			$data['code'] = 200;
			$data['message'] = "操作成功";
			$j_all = json_encode($data);

			return $j_all;
		} catch (\Exception $e) {

			$data['code'] = 500;
			$data['message'] = "系统错误";
			$j_all = json_encode($data);

			return $j_all;
            // 回滚事务
            Db::rollback();
        }
	}

	//提现明细接口
	public function allWithdraw()
	{
		$data = [];
		$user_id =  input('user_id');
		$user_id = 1;
		$token = input('token', '');
		if (empty($token)) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$api_token = Session::get('token$user_id');
		if ($api_token != $token) {
			$data['code'] = 201;
			$data['message'] = "用户未登录";
			$j_all = json_encode($data);

			return $j_all;
		}
		$a_withdraw = Withdraw::where('user_id', $user_id)
			->where('status', 'neq', '3')
			->order('add_time', 'desc')
			->field('id, zhifubao, money, reason, add_time, audit_time, status')
			->select();
		$data['withdraw'] = $a_withdraw;
		$data['code'] = 200;
		$data['message'] = '请求成功';
		$j_data = json_encode($data);

		return $j_data;
	}
}
                                           