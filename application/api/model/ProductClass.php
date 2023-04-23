<?php

namespace app\api\model;

use think\Db;
use think\Model;

class ProductClass extends Model
{
    protected $table = 'productclass';
    protected $pk = 'classid';
//    protected $mapFields = [
//        // 为混淆字段定义映射
//        'id'=>  'classid',
//    ];
    public function getDataList($map, $currentPage, $pageSize)
    {
        $dataList = $this->where($map)->limit($pageSize)->page($currentPage)->order("classpower asc,classid asc")->select();
        $total = $this->where($map)->count();
        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }



}