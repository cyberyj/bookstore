<?php

namespace app\index\controller;

use app\product\model\BookModel;
use think\Controller;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\Exception;
use think\exception\DbException;
use think\Request;

class IndexController extends Controller
{
    /**
     * 跳转到主页
     * @param int $type
     * @return mixed
     */
    public function show_index($type = 1)
    {
        try {
            $randBook=BookModel::orderRaw('rand()')->find();
            $this->assign('randBook', $randBook);
        } catch (Exception $e) {
            $e->getMessage();
        }

        if ($type == 1) {
            $this->feature_book();
            try {
                $new_books = BookModel::order('date desc')->limit(10)->select();
                $this->assign('type', 1);
                $this->assign('books', $new_books);
            } catch (Exception $e) {
                $e->getMessage();
            }
        } else if ($type == 2) {
            $this->feature_book();
            try {
                $sell_books = BookModel::order('sell desc')->limit(10)->select();
                $this->assign('type', 2);
                $this->assign('books', $sell_books);
            } catch (Exception $e) {
                $e->getMessage();
            }
        } else {
            $this->feature_book();
            try {
                $main_books = BookModel::orderRaw('rand()')->limit(10)->select();
                $this->assign('type', 3);
                $this->assign('books', $main_books);
            } catch (Exception $e) {
                $e->getMessage();
            }
        }
        return $this->fetch("index");
    }

    /**
     * 查询特色图书
     */
    public function feature_book()
    {
        try {
            $main_books1 = BookModel::orderRaw('rand()')->limit(10)->select();
            $main_books2 = BookModel::orderRaw('rand()')->limit(10)->select();
            $this->assign('featureOne', $main_books1);
            $this->assign('featureTwo', $main_books2);
        } catch (Exception $e) {
            $e->getMessage();
        }
    }
}

