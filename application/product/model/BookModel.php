<?php
/**
 * Created by PhpStorm.
 * User: yingjun
 * Date: 2019-01-29
 * Time: 20:08
 */
namespace app\product\model;
use think\Model;

class BookModel extends Model{
 protected $pk='bid';
    //自动写入时间戳
    protected $autoWriteTimestamp = 'datetime';
    //数据库中对应的字段
    protected $createTime = 'date';
}