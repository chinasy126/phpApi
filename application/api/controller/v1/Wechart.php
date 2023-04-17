<?php

namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\controller\Send;
use think\Db;

class Wechart extends Api
{
    public function getUserInfo()
    {
        $id = $this->checkAuth()['id'];
        $result = Db::table('w_anchor')->find($id);
        return $this->sendsuccess($result);
    }

    public function updateUserInfo()
    {
        $id = $this->checkAuth()['id'];
        $postData = $this->request->param();
        unset($postData['version']);
        $result = Db::table('w_anchor')->data($postData)->where('id=' . $id)->update();
        return $this->sendsuccess($result);
    }

    /**
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取所有商品
     */
    public function getAllGoods()
    {
        $result = Db::table('b_good')->order('id desc')->select();
        return $this->sendsuccess($result);
    }

    /**
     * 收藏商品
     */
    public function collectionGoods()
    {
        $postData = $this->request->param();
        $map['goodId'] = $postData['goodId'];
        $map['userId'] = $this->checkAuth()['id'];
        $info = Db::table('w_collectGood')->where($map)->find();
        if (empty($info)) {
            $data = $map;
            $data['createTime'] = getMilliSecond();
            Db::table('w_collectGood')->data($data)->insert();
        } else {
            Db::table('w_collectGood')->where($map)->delete();
        }
        return $this->sendSuccess($info);
    }

    public function isCollGood()
    {
        $postData = $this->request->param();
        $map['goodId'] = $postData['goodId'];
        $map['userId'] = $this->checkAuth()['id'];
        $res = Db::table('w_collectGood')->where($map)->find();
        return $this->sendSuccess($res);
    }

    /**
     * 选择商品的列表
     */
    public function selectedGoodsList(){
        $postData = $this->request->param();
        $start = ($postData['index']-1)*$postData['pageSize'];
        $end = $postData['pageSize'];
        $map = array();
        $map['userId'] = $this->checkAuth()['id'];
        $list = Db::table('selectedGoods')->limit($start,$end)->where($map)->select();

        // 计算总页数
        $count = Db::table('selectedGoods')->where($map)->count();
        $totalPage = ceil($count/ $end);
        $result['data'] = $list;
        $result['totalPage'] = $totalPage;
        return $this->sendSuccess($result);
    }

    /**
     * @return LIST
     * 所有商品列表
     */

    public function allGoodsList(){
        $postData = $this->request->param();
        $start = ($postData['index']-1)*$postData['pageSize'];
        $end = $postData['pageSize'];
        $map = array();
        $list = Db::table('b_good')->order("id desc")->limit($start,$end)->select();
        // 计算总页数
        $count = Db::table('b_good')->count();
        $totalPage = ceil($count/ $end);
        $result['data'] = $list;
        $result['totalPage'] = $totalPage;
        return $this->sendSuccess($result);
    }

    public function allSupplierList(){
        $postData = $this->request->param();
        $start = ($postData['index']-1)*$postData['pageSize'];
        $end = $postData['pageSize'];
        $map = array();
        $list = Db::table('supplier')->order("id desc")->limit($start,$end)->select();
        // 计算总页数
        $count = Db::table('supplier')->count();
        $totalPage = ceil($count/ $end);
        $result['data'] = $list;
        $result['totalPage'] = $totalPage;
        return $this->sendSuccess($result);

    }

}