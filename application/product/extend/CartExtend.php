<?php
/**
 * Created by PhpStorm.
 * User: yingjun
 * Date: 2019-02-01
 * Time: 13:17
 */

namespace app\product\extend;
use think\facade\Session;

class CartExtend
{
    /**
     * 改变购物项数量
     * @param $bid
     * @param $flag
     * @return \think\response\Json
     */
    static function changeNum($bid, $flag)
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            $jsonRes = ['msg' => 0];
        } else {
            if ($flag != 1) {
                foreach ($cart as &$item) {
                    if ($item['bid'] == $bid) {
                        if ($item['num'] == 1) {
                            $jsonRes = ['msg' => 2];
                        } else {
                            $item['num'] -= 1;
                            $jsonRes = ['msg' => 1];
                        }
                    }
                }
            } else {
                foreach ($cart as &$item) {
                    if ($item['bid'] == $bid) {
                        $item['num'] += 1;
                        $jsonRes = ['msg' => 1];
                    }
                }
            }
            Session::set('cart', $cart);
        }
        return json($jsonRes);
    }

    /**
     * 移除购物车项
     * @param $bid
     * @return \think\response\Json
     */
    static function removeItem($bid)
    {
        $cart = Session::get('cart');
        if (empty($cart)) {
            $jsonRes = ['msg' => 0];
        } else {
            foreach ($cart as $key => $item) {
                if ($item['bid'] == $bid) {
                    unset($cart[$key]);
                }
            }
            Session::set('cart', $cart);
            $jsonRes = ['msg' => 1];
        }
        return json($jsonRes);
    }

    /**
     * 添加购物车
     * @param $bid
     * @param $num
     * @return \think\response\Json
     */
    static function addCart($bid, $num)
    {
        if (empty($bid)) {
            return json(['msg' => 0]);
        } else if ($num > 0) {
            if (empty(Session::get('cart'))) {
                $cart = array(
                    array('bid' => $bid, 'num' => $num)
                );
                Session::set('cart', $cart);
            } else {
                $flag = false;
                $cart = Session::get('cart');
                foreach ($cart as $item) {
                    if ($item['bid'] == $bid) {
                        $flag = true;
                    }
                }
                if ($flag) {
                    foreach ($cart as &$item) {
                        if ($item['bid'] == $bid) {
                            $item['num'] += $num;
                        }
                    }
                    Session::set('cart', $cart);
                } else {
                    $cart[] = array('bid' => $bid, 'num' => $num);
                    Session::set('cart', $cart);

                }
            }
            return json(['msg' => 1]);
        } else {
            return json(['msg' => 0]);
        }
    }
}