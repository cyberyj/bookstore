<?php
/**
 * Created by PhpStorm.
 * user: yingjun
 * Date: 2019-01-26
 * Time: 21:10
 */

namespace app\admin\controller;

use app\admin\model\AdminModel;
use app\product\model\HarvestAddressModel;
use app\product\model\OrderStateModel;
use app\product\model\BookImgModel;
use app\product\model\BookModel;
use app\product\model\BookOrderModel;
use app\user\model\UserModel;
use think\Controller;
use think\Db;
use think\exception\DbException;
use think\facade\App;
use think\facade\Session;
use think\Request;
use think\Exception;
use upload_util\UploadExtend;


class AdminController extends Controller
{
    /**
     * 显示管理员登录
     * @return mixed
     */
    public function show_admin_login()
    {
        return $this->fetch('login');
    }

    /**
     * 显示管理员主页
     * @return mixed
     */
    public function show_admin_index()
    {
        $this->have_session();
        return $this->fetch('index');
    }

    /**
     * 显示欢迎页面的基本信息
     * @return mixed
     */
    public function show_admin_welcome()
    {
        $this->have_session();
        $time = date('Y-m-d H:i', time());
        $order_count = BookOrderModel::select();//查询订单数量
        $user_count = UserModel::select();//查询用户数量
        $book_count = BookModel::select();//查询书的数量
        $count = ['user_count' => count($user_count), 'book_count' => count($book_count), 'order_count' => count($order_count)];//将三种数量放入集合
        $version = Db::query('SELECT VERSION() AS ver');//查询数据库版本
        $info = array(
            'system_os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'env_version' => $_SERVER['SERVER_SOFTWARE'],
            'run_method' => php_sapi_name(),
            'tp_version' => App::version(),
            'upload_limit' => ini_get('upload_max_filesize'),
            'mysql_version' => $version[0]['ver'],
            'run_time' => ini_get('max_execution_time') . '秒',
            'server_ip' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            'su_space' => round((disk_free_space('.') / (1024 * 1024)), 2) . 'M',

        );//查询一系列的系统信息
        $this->assign('info', $info);
        $this->assign('time', $time);
        $this->assign('count', $count);
        return $this->fetch('welcome');
    }

    /**
     * 显示用户列表
     * @return mixed
     */
    public function show_member_list()
    {
        $this->have_session();
        try {
            $user = new UserModel();
            $user_count = UserModel::select();
            $result = $user->paginate(10);//分页显示用户列表
        } catch (DbException $e) {
        }
        $this->assign('user_count', count($user_count));
        $this->assign('result', $result);
        return $this->fetch('member_list');
    }

    /**
     * 显示用户添加页面
     * @return mixed
     */
    public function show_member_add()
    {
        $this->have_session();
        return $this->fetch('member_add');
    }

    /**
     * 显示用户编辑
     * @param $uid
     * @return mixed
     */
    public function show_member_edit($uid)
    {
        $this->have_session();
        $user = UserModel::get($uid);
        $this->assign('user', $user);
        return $this->fetch('member_edit');
    }

    /**
     * 显示所有的书
     * @return mixed
     */
    public function show_book_list()
    {
        $this->have_session();
        try {
            $book = new BookModel();
            $book_count = BookModel::select();
            $books = $book->paginate(10);//分页显示书
            $this->assign('books', $books);
            $this->assign('book_count', count($book_count));
        } catch (Exception $e) {
            $e->getMessage();
        }
        return $this->fetch('book_list');
    }

    /**
     * 显示添加书界面
     * @return mixed
     */
    public function show_book_add()
    {
        $this->have_session();
        return $this->fetch('book_add');
    }

    /**
     * 显示编辑书界面
     * @param $bid
     * @return mixed
     */
    public function show_book_edit($bid)
    {
        $result = BookModel::get($bid);
        $this->assign('result', $result);
        return $this->fetch('book_edit');

    }

    /**
     * 显示订单列表
     * @return mixed
     */
    public function show_order_list()
    {
        try {
            // 判断是否登录
            $this->have_session();
            //显示订单
            $bookOrderModel = new BookOrderModel();
            $bookOrderData = $bookOrderModel->paginate(10);
            foreach ($bookOrderData as $bookOrderItem) {
                $bookOrderItem['status'] = OrderStateModel::where('os_id', $bookOrderItem['status'])->value('state');
                $userAddrInfo = HarvestAddressModel::where('h_a_id', $bookOrderItem['h_a_id'])->find();
                if ($userAddrInfo == null) {
                    $this->error('订单数据显示错误：没有找到相应的用户收件地址');
                }
                $bookOrderItem['consignee'] = $userAddrInfo['consignee'];// 收货人
                $bookOrderItem['addr'] = $userAddrInfo['addr'];// 收件地址
                $bookOrderItem['contact'] = $userAddrInfo['contact'];// 电话
                $bookOrderItem['pay'] = $bookOrderItem['all_price'] - $bookOrderItem['discounts'];
            }
            $this->assign('bookOrderData', $bookOrderData);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch('order_list');
    }

    /**
     * 展示订单修改页面
     * @param Request $request
     * @return mixed
     */
    public function show_order_edit(Request $request)
    {
        try {
            // 判断是否登录
            $this->have_session();
            // 通过订单ID查询并显示某个订单信息
            $orderData = BookOrderModel::where('o_id', $request->oid)->select();
            $orderData[0]['status'] = OrderStateModel::where('os_id', $orderData[0]['status'])->value('state');
            $userAddrInfo = HarvestAddressModel::where('u_id', $orderData[0]['u_id'])->select();
            if (count($userAddrInfo) == 0) {
                $this->error('订单数据显示错误：没有找到相应的用户收件地址');
            }
            $orderData[0]['consignee'] = $userAddrInfo[0]['consignee'];// 收货人
            $orderData[0]['addr'] = $userAddrInfo[0]['addr'];// 收件地址
            $orderData[0]['contact'] = $userAddrInfo[0]['contact'];// 电话
            $orderData[0]['h_a_id'] = $userAddrInfo[0]['h_a_id'];// 电话
            $orderData[0]['pay'] = $orderData[0]['all_price'] - $orderData[0]['discounts'];
            $this->assign('order_data', $orderData);
            // 获取所有订单状态
            $this->assign('order_status', OrderStateModel::select());
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch('order_edit');
    }

    /**
     * 判断管理员是否登录
     */
    public function have_session()
    {
        if (empty(Session::get('admin'))) {
            $this->error('当前未登录', url . 'show_admin_login');
        }
    }

    /**
     * 管理员登录
     * @param Request $request
     */
    public function admin_login(Request $request)
    {
        try {
            $result = AdminModel::where([
                'username' => $request->post('username'),
                'password' => $request->post('password')
            ])->find();
            if (!empty($result)) {
                Session::set('admin', $request->post('username'));
                $this->success('登录成功！', url . 'show_admin_index');
            } else {
                $this->error('账号或密码错误', url . 'show_admin_login');
            }
        } catch (Exception $e) {
            $e->getMessage();
        }
    }

    /**
     * 管理员退出
     */
    public function admin_logout()
    {
        Session::delete('admin');
        $this->success('退出成功', url . 'show_admin_login');
    }

    /**
     * 用户删除
     * @param Request $request
     * @return \think\response\Json
     */
    public function member_delete(Request $request)
    {
        $result = UserModel::destroy($request->post('uid'));
        if ($result > 0) {
            $jsonRes = ['msg' => 1];
        } else {
            $jsonRes = ['msg' => 0];
        }
        return json($jsonRes);
    }

    /**
     * 用户批量删除
     * @param Request $request
     * @return \think\response\Json
     */
    public function member_delete_s(Request $request)
    {
        $result = UserModel::destroy($request->post('uids'));
        if ($result) {
            $jsonRes = ['msg' => 1];
        } else {
            $jsonRes = ['msg' => 0];
        }
        return json($jsonRes);
    }

    /**
     * 用户编辑
     * @param Request $request
     * @return \think\response\Json
     */
    public function member_edit(Request $request)
    {

        $user = UserModel::get($request->post('uid'));
        $result = $user->save($request->post());
        if ($result) {
            $jsonRes = ['msg' => 1];
        } else {
            $jsonRes = ['msg' => 0];
        }

        return json($jsonRes);
    }

    /**
     * 添加书
     * @param Request $request
     */
    public function book_add(Request $request)
    {

        if (empty($request->file()['book_img'][0])) {
            $this->error('至少一张图片');
        } else {
            $books = UploadExtend::upload_more($request->file('book_img'), url_upload);
//            $image = Image::open(url_upload . $books[0]);
//            $image->thumb(150, 150)->save(url_upload . $books[0]);
            $book = BookModel::create([
                'bname' => $request->post('bname'),
                'detail' => $request->post('detail'),
                'price' => $request->post('price'),
                'type' => $request->post('type'),
                'writer' => $request->post('writer'),
                'printer' => $request->post('printer'),
                'store' => $request->post('store'),
                'cover' => $books[0]
            ]);
            if (count($books) > 1) {
                for ($i = 1; $i < count($books); $i++) {
                    $bookImg = BookImgModel::create([
                        'b_id' => $book->bid,
                        'img' => $books[$i]
                    ]);
//                    $image = Image::open(url_upload . $books[$i]);
//                    $image->thumb(150, 150)->save(url_upload . $books[$i]);
                }
            }
            if (!empty($bookImg) || !empty($book)) {
                $this->success("添加成功！", url . '/show_book_list');
            }
        }
    }

    /**
     * 书本编辑
     * @param Request $request
     * @return \think\response\Json
     */
    public function book_edit(Request $request)
    {
        $book = BookModel::get($request->post('bid'));
        $result = $book->save($request->post());
        if ($result) {
            $jsonRes = ['msg' => 1];
        } else {
            $jsonRes = ['msg' => 0];
        }

        return json($jsonRes);
    }

    /**
     * 书批量删除
     * @param Request $request
     * @return \think\response\Json
     */
    public function book_delete_s(Request $request)
    {
        $bids = implode(',', $request->post('bids'));
        try {
            $books = BookModel::all($request->post('bids'));
            foreach ($books as $key => $book) {
                unlink(url_upload . $book->cover);
            }
            $result = BookModel::destroy($request->post('bids'));
            $book_imgs = BookImgModel::where("b_id in ($bids)")->select();
            if (!empty($book_imgs)) {
                foreach ($book_imgs as $key => $book_img) {
                    unlink(url_upload . $book_img->img);
                }
            }
            BookImgModel::where("b_id in ($bids)")->delete();
            if ($result > 0) {
                $jsonRes = ['msg' => 1];
            } else {
                $jsonRes = ['msg' => 0];
            }
        } catch (Exception $e) {
            $jsonRes = ['msg' => $e->getMessage()];
        }
        return json($jsonRes);

    }

    /**
     * 删除书
     * @param Request $request
     * @return \think\response\Json
     */
    public function book_delete(Request $request)
    {
        try {
            $book = BookModel::get($request->post('bid'));
            if (!empty($book)) {
                unlink(url_upload . $book['cover']);//删除图片
            }
            $book_imgs = BookImgModel::where('b_id', $request->post('bid'))->select();
            if (!empty($book_imgs)) {
                foreach ($book_imgs as $key => $book_img) {
                    unlink(url_upload . $book_img->img);
                }
            }
            $result = BookModel::destroy($request->post('bid'));
            BookImgModel::where('b_id', $request->post('bid'))->delete();
            if ($result) {
                $jsonRes = ['msg' => 1];
            } else {
                $jsonRes = ['msg' => 0];
            }
        } catch (Exception $e) {
            $jsonRes = ['msg' => $e->getMessage()];
        }
        return json($jsonRes);

    }


    /**
     * 订单删除
     * @param Request $request
     * @return \think\response\Json
     */
    public function order_delete(Request $request)
    {
        try {
            // 判断是否登录
            $this->have_session();
            BookOrderModel::where('o_id', $request->oid)->delete();
        } catch (Exception $e) {
            return json(['msg' => $e->getMessage()]);
        }
        return json(['msg' => 1]);
    }

    /**
     * 删除多个订单
     * @param Request $request
     * @return \think\response\Json
     */
    public function order_delete_s(Request $request)
    {
        try {
            // 判断是否登录
            $this->have_session();
            BookOrderModel::destroy($request->oids);
        } catch (Exception $e) {
            return json(['msg' => $e->getMessage()]);
        }
        return json(['msg' => 1]);
    }

    /**
     * 修改订单信息
     * @param Request $request
     * @return \think\response\Json
     */
    public function order_update(Request $request)
    {
        try {
            // 判断是否登录
            $this->have_session();
            // 修改订单基础信息
            BookOrderModel::where('o_id', $request->oid)->update([
                'status' => $request->status,
                'all_price' => $request->all_price,
                'discounts' => $request->discounts
            ]);
            // 修改收货信息
            HarvestAddressModel::where('h_a_id', $request->h_a_id)->update([
                'consignee' => $request->consignee,
                'addr' => $request->addr,
                'contact' => $request->contact
            ]);
            return json(["msg" => 1]);
        } catch (Exception $e) {
            return json(["msg" => $e->getMessage()]);
        }
    }
}