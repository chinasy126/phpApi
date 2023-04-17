<?php

namespace app\api\model;

use think\Db;
use think\Model;

class NewsModel extends Model
{

    /**
     * @param $map 分页条件
     * @param $pageSize
     * @param $currentPage
     */
    public function getDataList($map, $currentPage, $pageSize)
    {
        $dataList = Db::table('news')->where($map)->limit($pageSize)->page($currentPage)->order('id desc')->select();
        $total = Db::table('news')->where($map)->count();
        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }

    /**
     * @param $data 插入或者修改数据
     */

    public function insertNews($data)
    {
        if (!empty($data['id'])) {
            // update
            return Db::table('news')->where('id=' . $data['id'])->data($data)->update();
        } else {
            return Db::table('news')->data($data)->insert();
        }
    }
}