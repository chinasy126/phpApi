<?php

namespace app\api\model;

use think\Db;
use think\Model;
use snowflake\SnowFlake;

class Product extends Model
{
    protected $table = 'product';

    /**
     *
     * @param $map
     * @param $currentPage
     * @param $pageSize
     * @return array[]
     */
    public function getProductList($map, $currentPage, $pageSize)
    {

        $dataList = Db::table("product")->where($map)->
        field('p.*,c.classname')->
        alias("p")->
        join('productclass c', 'c.classid=p.pid')->
        limit($pageSize)->
        order("top desc,id desc")->
        page($currentPage)->select();

        // $dataList = $this->where($map)->limit($pageSize)->page($currentPage)->order('top desc,id desc')->select();
        $total = $this->where($map)->count();
        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }


}