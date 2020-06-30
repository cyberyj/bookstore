<?php
/**
 * Created by PhpStorm.
 * user: yingjun
 * Date: 2019-01-26
 * Time: 23:12
 */

namespace app\product\controller;

use app\product\extend\CartExtend;
use app\product\model\BookImgModel;
use app\product\model\BookModel;
use app\product\model\BookOrderModel;
use app\product\model\HarvestAddressModel;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Session;
use think\Request;

class ProductController extends Controller
{
    /**
     * 根据bid显示书籍详细信息
     * @param Request $request
     * @return mixed
     */
    public function show_details(Request $request)
    {
        try{
            // 根据bid查询书籍信息
            $bookData = BookModel::where("bid", $request->bid)->find();
            $this->assign("bookData", $bookData);
            // 根据bid查询书籍图片
            $imgs = BookImgModel::where("b_id", $request->bid)->select();
            $imgs[count($imgs)] = ["img"=>$bookData["cover"]];
            $this->assign("imgs", $imgs);
            // 根据当前书的类型查询相关书籍
            $relatedBooks = BookModel::where("type", $bookData["type"])->where("bid",'<>',$request->bid)->limit(3)->select();
            $this->assign("relatedBooks", $relatedBooks);
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        return $this->fetch('details');
    }

    /**
     * 首页商品列表查看
     * @param Request $request
     * @return mixed
     */
    public function show_plist(Request $request)
    {
        $type = $request->type;
        $search = $request->search;
        try {
            // 展示目录
            $types = BookModel::field("type")->distinct(true)->limit(6)->select();
            $data = array();
            foreach ($types as $item) {
                $item["name"] = $item["type"];
                $item["value"] = BookModel::where("type", $item["type"])->count("type");
                array_push($data, $item);
            }
            $this->assign("catalog_list", $data);
            // 排名
            $bookOrderData = BookModel::order("sell desc")->limit(8)->select();
            $this->assign("bookOrderData", $bookOrderData);
            // 展示商品列表
            $bookModel = new BookModel();
            if ($type != null) {
                // 根据类型进行商品展示
                $bookData = $bookModel->where("type", $type)->paginate(10);
            } else if ($search != null){
                // 搜索商品
                $bookData = $bookModel->whereLike("bname", "%".$search."%")->paginate(10);
            }else {
                // 直接查询Book数据库商品
                $bookData = $bookModel->paginate(10);
            }
            $this->assign("bookData", $bookData);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->fetch("plist");
    }

    /**
     * 显示订单结算页面
     * @return mixed
     */
    public function show_check()
    {
        try{
            // 检查是否登录
            $this->have_session();
            // 根据uid查询用户的所有收货地址
            $harvestDatas = HarvestAddressModel::where("u_id", Session::get("uid"))->select();
            $this->assign("harvestDatas", $harvestDatas);
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        return $this->fetch("check");
    }

    /**
     * 显示购物车
     * @return mixed
     */
    public function show_cart()
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            $this->assign('result', 0);
        } else {
            $allPrice = 0;
            foreach ($cart as &$item) {
                $book = BookModel::get($item['bid']);
                $item['name'] = $book->bname;
                $item['cover'] = $book->cover;
                $item['price'] = $book->price;
                $item['totalPrice'] = $book->price * $item['num'];
                $allPrice += $item['totalPrice'];
            }
            $this->assign('result', 1);
            $this->assign('allPrice', $allPrice);
            $this->assign('cart', $cart);
        }
        return $this->fetch("cart");
    }

    /**
     * 添加购物车
     * @param Request $request
     * @return \think\response\Json
     */
    public function add_cart(Request $request)
    {
        return CartExtend::addCart( $request->post('bid'),$request->post('num'));
    }

    /**
     * 删除购物车项
     * @param Request $request
     * @return \think\response\Json
     */
    public function remove_cart_item(Request $request)
    {
        return CartExtend::removeItem($request->post('bid'));
    }

    /**
     * 改变购物项的数目
     * @param Request $request
     * @return \think\response\Json
     */
    public function cart_item_num(Request $request)
    {
        return CartExtend::changeNum($request->post('bid'),$request->post('flag'));
    }

    /**
     * 通过bid获取书具体信息
     * @param Request $request
     * @return \think\response\Json
     */
    public function book_detail(Request $request)
    {
        try {
            $singleBook = BookModel::where("bid", $request->bid)->find();
            $singleBook["imgs"] = BookImgModel::where("b_id", $request->bid)->select();
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        return json($singleBook);
    }

    /**
     * 获取分类信息
     * @return \think\response\Json
     */
    public function book_header()
    {
        try{
            // 获取分类
            $dataTypes = [];
            $types = BookModel::field("type")->distinct(true)->limit(4)->select();
            foreach ($types as $type)
            {
                $tempItem["type"] = $type["type"];
                $tempItem["value"] = BookModel::field("bname, bid")->where("type", $type["type"])->select();
                array_push($dataTypes, $tempItem);
            }
            // 获取最新
            $dataNew = [];
            $types = BookModel::field("type")->distinct(true)->limit(4)->select();
            foreach ($types as $type)
            {
                $tempItem["type"] = $type["type"];
                $tempItem["value"] = BookModel::field("bname, bid, date")->where("type", $type["type"])->
                order("date desc")->select();
                array_push($dataNew, $tempItem);
            }
            // 获取畅销
            $dataBigSell = [];
            $types = BookModel::field("type")->distinct(true)->limit(3)->select();
            foreach ($types as $type)
            {
                $tempItem["type"] = $type["type"];
                $tempItem["value"] = BookModel::field("bname, bid, sell")->where("type", $type["type"])->
                order("sell desc")->select();
                array_push($dataBigSell, $tempItem);
            }
            // 结果数据
            $result = [
                "dataTypes"=>$dataTypes,
                "dataNew"=>$dataNew,
                "dataBigSell"=>$dataBigSell,
            ];
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        return json($result);
    }

    /**
     * 购物车结算
     * @param Request $request
     * @return \think\response\Json
     */
    public function check_out(Request $request)
    {
        try{
            // 如果是新增收货地址
            $harvest=[
                "u_id"=>Session::get("uid"),
                "consignee"=>$request->consignee,
                "addr"=>$request->addr,
                "contact"=>$request->contact,
            ];
            if($request->h_a_id == null){
                $harvestModel = new HarvestAddressModel();
                $harvestModel->u_id = $harvest["u_id"];
                $harvestModel->consignee = $harvest["consignee"];
                $harvestModel->addr = $harvest["addr"];
                $harvestModel->contact = $harvest["contact"];
                $harvestModel->save();
                // 获取自增ID
                $h_a_id = $harvestModel->h_a_id;
            }else{
                $h_a_id = $request->h_a_id;
            }
            // 把购物车的商品全部结算
            $goods = Session::get("cart");
            $all_price = 0;
            foreach ($goods as $good) {
                $bookItem = BookModel::where("bid", $good["bid"])->find();
                $all_price += $bookItem["price"] * $good["num"];
            }
            if($all_price < 100){
                $all_price += 15;
            }
            // 发布订单
            $bookOrderModel = new BookOrderModel();
            $bookOrderModel->u_id = Session::get("uid");
            $bookOrderModel->status = 1;
            $bookOrderModel->all_price = $all_price;
            $bookOrderModel->date = date("Y-m-d H:i:s");
            $bookOrderModel->discounts = 0;
            $bookOrderModel->h_a_id = $h_a_id;
            $bookOrderModel->l_msg = $request->l_msg;
            $bookOrderModel->save();
            $o_id = $bookOrderModel->o_id;
            // 插入订单详情
            foreach ($goods as $good) {
                Db::name("order_detail")->insert([
                    "b_o_id"=>$o_id,
                    "b_id"=>$good["bid"],
                    "num"=>$good["num"]
                ]);
            }
            Session::set("cart", []);
            return json(["msg"=>1]);
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        return json(["msg"=>'']);
    }

    /**
     * 判断管理员是否登录
     */
    public function have_session()
    {
        if (empty(Session::get('username'))) {
            $this->error('当前未登录', url . 'show_login');
        }
    }

    /**
     * 获取购物车前三条信息
     * @return \think\response\Json
     */
    public function get_cart_info()
    {
        $data = [];
        $total_price = 0;
        try{
            $cart = Session::get("cart");
            if($cart != null){
                foreach ($cart as $key=>$value){
                    if($key == 3){
                        break;
                    }
                    $bookItem = BookModel::where("bid", $value["bid"])->find();
                    $total_price += $bookItem["price"] * $value["num"];
                    $bookItem["num"] = $value["num"];
                    array_push($data, $bookItem);
                }
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        $result = [
            "cart_info"=>$data,
            "total_price"=>$total_price,
        ];
        if(Session::get("cart") != null){
            $result["product_num"] = count(Session::get("cart"));
        }else{
            $result["product_num"] = 0;
        }
        return json($result);
    }

    /**
     * 通过bid删除指定购物车商品
     * @param Request $request
     * @return \think\response\Json
     */
    public function delete_cart_item(Request $request)
    {
        try{
            $cart = Session::get("cart");
            if($cart != null){
                foreach ($cart as $key=>$value){
                    if($value["bid"] == $request->bid){
                        unset($cart[$key]);
                        Session::set("cart", array_values($cart));
                        break;
                    }
                }
            }
        }catch (Exception $e){
            $this->error($e->getMessage());
        }
        return json(["msg"=>1]);
    }
}