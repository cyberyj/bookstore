<?php
/**
 * Created by PhpStorm.
 * User: FireLang
 * Date: 2019/1/29
 * Time: 22:22
 */

namespace app\product\model;
use think\Model;

class BookOrderModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'book_order';
    protected $pk = 'o_id';
}