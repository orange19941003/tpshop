<?php
namespace app\index\controller;

use think\Db;
use think\Session;
use app\admin\model\User;
use app\index\controller\Base;
use app\admin\model\shop\Cate;
use app\admin\model\shop\Order;
use app\admin\model\shop\Product;
use app\admin\model\shop\Address;
use app\admin\model\shop\Qiandao;

class Index extends Base
{
    public function index()
    {
        $o_cates = Cate::where('status', '1')
            ->select();
        $product = new Product;
        $a_cates = array();   
        foreach ($o_cates as $vv) {
            $vv = $vv->toArray();
            $o_products = $product->where('cate_id', $vv['id'])
                ->where('status', '1')
                ->select();
            $vv['product'] = $o_products;
            $a_cates[] = $vv;
        }
        $this->assign('cates', $a_cates);
        $hot_puducts = $product->where('status', '1')
            ->order('pv', 'desc')
            ->limit(4)
            ->select();
        $this->assign('hot_products', $hot_puducts);
        $o_products = $product->where('status', '1')
            ->order('cate_id')
            ->limit(9)
            ->select();
        $this->assign('products', $o_products);
 
        return $this->fetch('index');
    }

    public function liebiao()
    {
        $id = input('id', '0');
        $s_id_eq = $id == '0' ? 'neq' : 'eq';
        $o_products = Product::where('cate_id', $s_id_eq, $id)
            ->where('status', '1')
            ->select();
        $this->assign('products', $o_products);
        $cate_title = "所有商品";
        if ($id != '0') {
            $cate_title = Cate::where('id', $id)
                ->where('status', '1')
                ->value('title');
        }
        $this->assign('title', $cate_title);

        return $this->fetch('liebiao');
    }

    public function xiangqing()
    {
        $id = input('id');
        $o_product = Product::where('id', $id)
            ->where('status', '1')
            ->find();
        $o_product->pv += 1;
        $o_product->save();
        $this->assign('product', $o_product);
        $name = $this->appUser;
        $integral = 'no';
        $address = '';
        if (!empty($name)) {
            $o_user = User::where('name', $name)
                ->where('status', '1')
                ->find();
            $integral = $o_user->integral;
            $address = Address::where('user_id', $o_user->id)
                ->where('status', '1')
                ->value('address');
            $this->assign('inte', $integral);
        } else {
            $this->assign('inte', $integral);
        }
        
        $this->assign('address', $address);
        
        return $this->fetch('xiangqing');
    }

    public function order()
    {
        $name = $this->appUser;
        $o_orders = Order::where('user_name', $name)
            ->where('is_del', '1')
            ->order('time', 'desc')
            ->limit(3)
            ->select();
        $this->assign('o_orders', $o_orders);
        $a_orders = array();
        foreach ($o_orders as $vv) {
            $o_product = Product::where('id', $vv->pro_id)
                ->where('status', '1')
                ->find();
            $vv = $vv->toArray();
            $vv['img'] = $o_product->img;
            $vv['price'] = $o_product->price;
            $a_orders[] = $vv;
        }
        $this->assign('orders', $a_orders);

        return $this->fetch('order');
    }

    public function order_all()
    {
        $id = input('id');
        $order = Order::where('id', $id)
            ->where('is_del', '1')
            ->find();
        $o_product = Product::where('id', $order->pro_id)
                ->where('status', '1')
                ->find();
        $a_order = $order->toArray();
        $a_order['pro_name'] = $o_product->title;
        $a_order['price'] = $o_product->price;
        $this->assign('order', $a_order);

        return $this->fetch('order_all');
    }

    public function self()
    {
        $username = $this->appUser;
        $o_user = User::where('name', $username)
            ->where('status', '1')
            ->find();
        $this->assign('self', $o_user);
        $o_address = Address::where('user_id', $o_user->id)
            ->where('status', '1')
            ->find();
        $this->assign('address', $o_address);

        return $this->fetch('self');
    }

    public function edit()
    {
        $val = input('val');
        $id = input('id');
        $title = input('title');
        $o_user = User::where('id', $id)
            ->where('status', '1')
            ->find();
        if ($title == 'name') {
            $s_exit_name = User::where('name', $val)
                ->where('status', '1')
                ->find();
            if ($s_exit_name) {
                return $this->no("用户名已存在");
            }
            $o_user->name = $val;
            $res = $o_user->save();
            if (!$res) {
                return $this->no('修改失败');
            }
            Session::set('appUser', $val);

            return $this->yes("修改成功");
        }
        if ($title == 'tel') {
            if (!preg_match("/^1[34578]\d{9}$/", $val)) {
                return $this->error("请输入正确的手机号码");
            }
            $s_exit_tel = User::where('tel', $val)
                ->where('status', '1')
                ->find();
            if ($s_exit_tel) {
                return $this->no("手机号码已存在");
            }
            $o_user->tel = $val;
            $res = $o_user->save();
            if (!$res) {
                return $this->no('修改失败');
            }

            return $this->yes("修改成功");
        }
        if ($title == 'password') {
            $password = md5($val);
            $o_user->password = $password;
            $res = $o_user->save();
            if (!$res) {
                return $this->no('修改失败');
            }

            return $this->yes("修改成功");
        }
        if ($title == 'address') {
            $user_id = User::where('name', $this->appUser)
                ->where('status', '1')
                ->value('id');
            $o_address = Address::where('user_id', $user_id)
                ->where('status', '1')
                ->find();
            $o_address->address = $val;
            $res = $o_address->save();
            if (!$res) {
                return $this->no('修改失败');
            }

            return $this->yes("修改成功");
        }
    }

    public function add_address() {
        $user_id = input('id');
        $s_address = input('val', '');
        if (empty($s_address)) {
            return $this->no('输入的地址为空');
        }
        $address = new Address;
        $address->user_id = $user_id;
        $address->address = $s_address;
        $res = $address->save();
        if (!$res) {
            return $this->no('添加失败');   
        }

        return $this->yes("添加成功");
    }

    public function buy()
    {  
        try{ 
            $id = input('id');
            $name = input('name', '');
            $tel = input('tel', '');
            $address = input('address', '');
            if (empty($name) || empty($tel)) {
                return $this->no("请将信息填写完整");
            } 
            $o_product = Product::where('id', $id)
                ->where('status', '1')
                ->find();
            $price = $o_product->price;
            if ($o_product->type == '1' && empty($address)) {
                return $this->no("请填写收货地址");
            }
            $o_user = User::where('name', $this->appUser)
                ->where('status', '1')
                ->find();
            if ($price > $o_user->integral) {
                return $this->no("积分不足");
            }
              // 启动事务
            Db::startTrans();
            $order = new Order;
            $order->name = $name;
            $order->tel = $tel;
            $order->address = $address;
            $order->user_name = $this->appUser;
            $order->pro_id = $id;
            $order->time = time();
            $res = $order->save();
            if(!$res){
                return $this->no('兑换失败');
            }else{
                $o_user->integral = $o_user->integral - $price;
                $res = $o_user->save();
                if (!$res) {
                    return $this->no("兑换失败");
                } else {
                    return $this->yes("兑换成功");
                } 
                return $this->yes("兑换成功");
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
             return $this->no($e->getMessage());
            // 回滚事务
            Db::rollback();
        }
    }

    public function integral()
    {
        $o_qiandao = Qiandao::where('user_id', $this->appUser_id)
            ->find();
        if (!$o_qiandao) {
            $day = 0;
            $integral = 50;
        } else {
            $date = date("Y-m-d");
            $qiandao_date = date("Y-m-d", $o_qiandao->time);
            $d=strtotime($date);
            $q_d = strtotime($qiandao_date);
            if ($d - $q_d > 3600*24) {
                $o_qiandao->num = 0;
                $o_qiandao->save();
            }
            $day = $o_qiandao->num;
            $integral = 50 + 50*$day;
        }
        $a_arr = array();
        $a_arr['day'] = $day;
        $a_arr['inte'] = $integral;

        return $this->fetch('integral', $a_arr);
    }

    public function qiandao()
    {
        $inte = input('inte');
        $qiandao = new Qiandao;
        $o_qiandao = $qiandao->where('user_id', $this->appUser_id)
            ->find();
        $o_user = User::where('id', $this->appUser_id)
            ->where('status', '1')
            ->find();
        if (!$o_qiandao) {
            $qiandao->user_id = $this->appUser_id;
            $qiandao->num = 1;
            $qiandao->time = time();
            $res = $qiandao->save();
            if (!$res) {
                return $this->no("签到失败");
            }
            $o_user->integral += $inte;
            $o_user->save();  

            return $this->yes("签到成功");
        } else {
            $date = date("Y-m-d");
            $time = date("Y-m-d", $o_qiandao->time);
            if ($date == $time) {
                return $this->no("今天已签到");
            } else {
                $o_qiandao->num += 1;
                $o_qiandao->time = time();
                $res = $o_qiandao->save();
                if (!$res) {
                    return $this->no("签到失败");
                }
                $o_user->integral += $inte;
                $o_user->save();  
                
                return $this->yes("签到成功");
            }

        }
    }

}
