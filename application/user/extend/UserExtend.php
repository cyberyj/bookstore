<?php
/**
 * Created by PhpStorm.
 * User: yingjun
 * Date: 2019-01-29
 * Time: 14:28
 */
namespace app\user\extend;
use app\user\model\UserModel;
use think\Exception;
class UserExtend{
    public static function select_username($username)
    {
        try {
            $result = UserModel::where(['username' => $username])->find();
            //根据username查询数据表
            if (!empty($result)) {
                $jsonRes = ['msg' => 0];//如果查询有数据返回0
            } else {
                $jsonRes = ['msg' => 1];//如果查询没有数据返回1
            }
        } catch (Exception $e) {
            $jsonRes = ['msg' => $e->getMessage()];
        }
        return $jsonRes;
    }

}
