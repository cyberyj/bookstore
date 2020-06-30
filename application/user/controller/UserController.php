<?php
/**
 * Created by PhpStorm.
 * user: yingjun
 * Date: 2019-01-26
 * Time: 21:11
 */

namespace app\user\controller;

use app\product\model\BookModel;
use app\product\model\BookOrderModel;
use app\product\model\HarvestAddressModel;
use app\product\model\OrderDetailModel;
use app\user\extend\UserExtend;
use app\user\model\UserModel;
use app\user\validate\UserValidate;
use think\Controller;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Request;
use think\facade\Session;

class UserController extends Controller
{
    /**
     * 显示注册页面
     * @return mixed
     */
    public function show_register()
    {
        return $this->fetch("register");//展示注册页面
    }

    /**
     * 用户注册
     * @param Request $request
     * @return \think\response\Json
     */
    public function user_register(Request $request)
    {
        $checkRes = UserExtend::select_username($request->username);
        if ($checkRes['msg'] == 0) {
            $jsonRes = ['msg' => 9];
        } else {
            $data = [
                'name' => $request->username,//获取用户名
                'password' => $request->password,//获取用户密码
                'password_confirm' => $request->password_confirm,//获取用户的确认密码
                'phone' => $request->phone,//获取用户电话
                'email' => $request->email,//获取用户邮箱
                'address' => $request->address//获取用户的地址
            ];//封装需要验证的数据
            $validate = new UserValidate();//实例化用户验证模型
            if (!$validate->check($data)) {
                $jsonRes = ['msg' => $validate->getError()];//验证不通过,获得验证不通过的信息
            } else {
                $result = UserModel::create($request->post());//添加用户的数据到数据库
                if ($result) {
                    $jsonRes = ['msg' => "success"];//添加用户信息成功
                } else {
                    $jsonRes = ['msg' => "fail"];//添加用户信息失败
                }
            }
        }


        return json($jsonRes);//返回json数据
    }

    /**
     * 检查用户名是否重复
     * @param Request $request
     * @return \think\response\Json
     */
    public function user_check(Request $request)
    {
        $username = $request->username;//获取用户名
        $jsonRes = UserExtend::select_username($username);
        return json($jsonRes);//返回json数据
    }


    /** 跳转到登录页面
     * @return mixed
     */
    public function show_login()
    {
        return $this->fetch("login"); // 请求该方法，跳转到登录页面
    }

    /**
     * 判断用户名或密码是否正确，然后输出相关信息。
     * @param Request $request
     * @return \think\response\Json
     */
    public function user_login(Request $request)
    {
        try {
            $result = UserModel::where([
                "username" => $request->username,
                "password" => $request->password
            ])->find();
            if (!empty($result)) {
                Session::set("username", $request->username); // 登录成功后设置session;
                Session::set("uid", $result["uid"]);
                $jsonRes = ['msg' => 1];// 登录成功
            } else {
                $jsonRes = ["msg" => "用户名或密码错误！"]; // 登录失败
            }
        } catch (Exception $e) {
            $jsonRes = ["msg" => $e->getMessage()];
        }
        return json($jsonRes);
    }

    public function user_logout()
    {
        Session::delete('username');
        Session::delete('uid');
        Session::delete('cart');
        $this->success('退出成功!', url . '/show_index');
    }

    public function show_user_order()
    {

        $uid = Session::get('uid');
        try {
            $user_order = BookOrderModel::where('u_id', $uid)->select();

            foreach ($user_order as &$order_item) {
                $user = HarvestAddressModel::where('h_a_id', $order_item['h_a_id'])->find();
                $order_item['user']=$user;
                $order_details = OrderDetailModel::where('b_o_id', $order_item['o_id'])->select();
                foreach ($order_details as &$order_detail) {
                    $book_name = BookModel::get($order_detail['b_id']);
                    $bookInfo[] = $book_name;
                    $order_item['bname'] = $bookInfo;
                }

                unset($bookInfo);
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
        $this->assign('user_orders', $user_order);
        return $this->fetch('user_order');
    }

}