<?php

namespace app\api\model;

use think\Db;
use think\Model;

/**
 * 存储用户appid，app_secret等值，为每个用户分配对应的值，生成access_token
 */
class LoginModel extends Model
{
    /**
     * @param string $username
     * @return array|bool|\PDOStatement|string|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkLoginUser($username = '')
    {
        $where['username'] = $username;
        $result = Db::table('t_user')->where($where)->find();
        return $result;
    }

    public function findUserById($userId)
    {
        return Db::table('t_user')->where("id =" . $userId)->find();
    }

}