<?php
/**
 * Created by PhpStorm.
 * User: FireLang
 * Date: 2019/1/29
 * Time: 22:28
 */

namespace app\product\model;


use think\Model;

class OrderStateModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'order_state';
    protected $pk = 'os_id';
}