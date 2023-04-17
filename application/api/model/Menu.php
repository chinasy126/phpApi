<?php

namespace app\api\model;

use think\Db;
use think\Model;
use snowflake\SnowFlake;

class Menu extends Model
{
//    protected $createTime = 'createTime';
//    protected $updateTime = 'updateTime';
//    protected $autoWriteTimestamp = 'datetime';


    /**
     *
     * @param $map
     * @param $currentPage
     * @param $pageSize
     * @return array[]
     */
    public function getMenuList($map, $currentPage, $pageSize)
    {
        $dataList = Db::table('menu')->where($map)->limit($pageSize)->page($currentPage)->order('menuOrder desc,id asc')->select();
        $total = Db::table('menu')->where($map)->count();
        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }

    public function getAllMenu($map){
        $menuList =  Db::table('menu')->where($map)->order('menuOrder desc,id asc')->select();
        foreach ($menuList as $key=>$value){
            $menuList[$key]['children'] = null;
            $menuList[$key]['fname'] = null;
            $menuList[$key]['ftitle'] = null;
            $menuList[$key]['menubuttonList'] = null;
        }
        return $menuList;
    }

    public function getSecMenuList($map)
    {
        if (count($map) > 0) {
            $where['fid'] = array('in', $map);
            return Db::name('menu')->where($where)->order('menuOrder desc,id asc')->select();
        }
        return [];
    }

    /**
     *  获取按钮列表
     */
    public function getButtonList($map)
    {
        if (count($map) > 0) {
            $where['menuId'] = array('in', $map);
            return Db::name('menubutton')->where($where)->select();
        }
        return [];
    }

    /**
     * 插入按钮
     * @param $postData
     */
    public function insertBtn($postData){
        //$data['id'] = Snowflake::generateParticle();
//        $data['menuId'] = $postData['menuId'];
//        $data['type'] = $postData['type'];
//        $data['name'] = $postData['name'];
//        $this->table('menubutton')->data($data)->save($data);
       // return Db::name('menubutton')->data($data)->save();

    }

    public function menudeleteBtndel($menuId){
        $this->where("id=".$menuId)->delete();
        $this->name("menubutton")->where("menuId=".$menuId)->delete();
    }

}