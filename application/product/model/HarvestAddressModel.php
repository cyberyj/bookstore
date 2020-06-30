<?php
/**
 * Created by PhpStorm.
 * User: FireLang
 * Date: 2019/1/29
 * Time: 22:37
 */

namespace app\product\model;


use think\Model;

class HarvestAddressModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'harvest_address';
    protected $pk = 'h_a_id';
}