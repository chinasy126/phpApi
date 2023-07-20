<?php

namespace app\api\model;

use think\Db;
use think\Model;
use snowflake\SnowFlake;

class User extends Model
{
    protected $table = 't_user';
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
    protected $autoWriteTimestamp = 'datetime';

    /**
     * @param $map 分页条件
     * @param $pageSize
     * @param $currentPage
     */
    public function getDataList($map, $currentPage, $pageSize)
    {
        $dataList = Db::table('t_user')->where($map)->limit($pageSize)->page($currentPage)->order('id desc')->select();
        $total = Db::table('t_user')->where($map)->count();

        $roleList = $this->getRoleNameList($dataList);
        foreach ($dataList as $key => $value) {
            foreach ($roleList as $k => $v) {
                if ($value["roleId"] == $v["id"]) {
                    $dataList[$key]["roleName"] = $v["roleName"];
                }
            }
        }

        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }

    public function getRoleNameList($dataList)
    {
        $roleIds = array();
        foreach ($dataList as $value) {
            array_push($roleIds, $value['roleId']);
        }
        $where["id"] = array("in", $roleIds);
        return Db::name("role")->where($where)->select();
    }

    public function insertUser(array $userData = [])
    {
        return Db::table($this->table)->data($userData)->insert();
    }

    public function updateUser(array $userData = [])
    {
        return Db::table($this->table)->where("id=" . $userData['id'])->data($userData)->update();
    }

}