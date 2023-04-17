<?php

namespace app\api\controller\v1;

use app\api\controller\Api;
use snowflake\SnowFlake;
use think\Db;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 * date:2017-07-25
 */
class Good extends Api
{
    public static $tableName = 'b_good';
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';

    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */
    public function index()
    {

    }

    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $postData = $this->request->param();
        unset($postData['version']);
        $postData['id'] = SnowFlake::make();
        $postData['userId'] = $this->checkAuth()['id'];
        $postData['createTime'] = getMilliSecond();
        $postData['goodPic'] = json_encode($postData['goodPic']);
        $result = Db::table(self::$tableName)->data($postData)->insert();
        return $this->sendSuccess($result);
    }

    /**
     * get方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function read()
    {
        $id = $this->request->param('id');
        $result = Db::table(self::$tableName)->find($id);
        return $this->sendSuccess($result);
    }

    /**
     * PUT方式
     *
     * @param \think\Request $request
     * @param int $id
     * @return \think\Response
     */
    public function update()
    {
        $postData = $this->request->param();
        unset($postData['version']);
        $map = array();
        $map['id'] = $postData['id'];
        $postData['userId'] = $this->checkAuth()['id'];
        $postData['createTime'] = getMilliSecond();
        $postData['goodPic'] = json_encode($postData['goodPic']);
        $result = Db::table(self::$tableName)->data($postData)->where($map)->update();
        return $this->sendSuccess($result);
    }

    /**
     * delete方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete()
    {
        $id = $this->request->param('id');
        $result = Db::table(self::$tableName)->delete($id);
        return $this->sendSuccess($result);
    }

    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function goodList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['goodName']) ? $map['goodName'] = ['like', "%" . $postData['goodName'] . "%"] : '';
        !empty($postData['goodBrand']) ? $map['goodBrand'] = ['like', "%" . $postData['goodBrand'] . "%"] : '';

        $pageSize = $postData['pageSize'];
        $length = ($postData['pageNum'] - 1) * $pageSize;
        $list = Db::table(self::$tableName)->where($map)->limit($pageSize, $length)->order('id desc')->select();
        $result['list'] = $list;
        $result['currPage'] = $postData['pageNum'];
        $result['pageSize'] = $pageSize;
        $count = Db::table(self::$tableName)->where($map)->count();
        $result['totalPage'] = ceil($count / $pageSize);
        $result['totalCount'] = $count;
        return $this->sendSuccess($result);
    }

    public function storeInfo()
    {
        $id = $this->checkAuth()['id'];
        $res = Db::table('users')
            ->join('usersExtend', 'users.id=usersExtend.userId', 'right')//左表交集
            ->where('users.id=' . $id)//条件:状态为1
            ->find();
        return $this->sendSuccess($res);
    }

    /**
     * 保存商家信息
     */
    public function saveStroeInfo()
    {
        $postData = $this->request->param();
        $map = array();
        $data = array();
        $data = $postData;
        unset($data['version']);
        $result = Db::table('usersExtend')->where('userId=' . $this->checkAuth()['id'])->update($data);
        return $this->sendSuccess($result);
    }

    public function allGoods()
    {
        $postData = $this->request->param();
        $type = $postData['type'];
        if ($type == 'all') {
            $result = Db::table(self::$tableName)->order('id desc')->select();
            return $this->sendSuccess($result);
        } else {
            $result = Db::table(self::$tableName)->where('userId =' . $this->checkAuth()['id'])->order('id desc')->select();
            return $this->sendSuccess($result);
        }

    }

    public function headChoose()
    {
        $postData = $this->request->param();
        unset($postData['version']);
        //$postData['goodId'] = json_encode($postData['goodId']);
        $data = array();
        foreach ($postData['goodId'] as $key => $value) {
            $data[] = array(
                "title" => $postData['title'],
                "aId" => $postData['aId'],
                "goodId" => $value,
                "createTime" => getMilliSecond(),
                "headId" => $this->checkAuth()['id']
            );
        }
        $result = Db::table('w_good')->insertAll($data);
        return $this->sendSuccess($result);
    }

    public function anchorList()
    {
        $result = Db::table('w_anchor')->order('id desc')->select();
        return $this->sendSuccess($result);
    }

    public function chooseList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['goodName']) ? $map['goodName'] = ['like', "%" . $postData['goodName'] . "%"] : '';
        !empty($postData['goodBrand']) ? $map['goodBrand'] = ['like', "%" . $postData['goodBrand'] . "%"] : '';

        $pageSize = $postData['pageSize'];
        $length = ($postData['pageNum'] - 1) * $pageSize;
        $list = Db::table('chooseGoods')->where($map)->limit($pageSize, $length)->order('id desc')->select();
        $result['list'] = $list;
        $result['currPage'] = $postData['pageNum'];
        $result['pageSize'] = $pageSize;
        $count = Db::table('chooseGoods')->where($map)->count();
        $result['totalPage'] = ceil($count / $pageSize);
        $result['totalCount'] = $count;
        return $this->sendSuccess($result);
    }

    public function chooseDelete()
    {
        $postData = $this->request->param();
        $result = Db::table('w_good')->delete($postData['id']);
        return $this->sendSuccess($result);
    }


    public function selected()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['goodName']) ? $map['goodName'] = ['like', "%" . $postData['goodName'] . "%"] : '';
        !empty($postData['goodBrand']) ? $map['goodBrand'] = ['like', "%" . $postData['goodBrand'] . "%"] : '';
        $pageSize = $postData['pageSize'];
        $length = ($postData['pageNum'] - 1) * $pageSize;
        $list = Db::table('chooseGoods')->where($map)->limit($pageSize, $length)->order('id desc')->select();
        $result['list'] = $list;
        $result['currPage'] = $postData['pageNum'];
        $result['pageSize'] = $pageSize;
        $count = Db::table('chooseGoods')->where($map)->count();
        $result['totalPage'] = ceil($count / $pageSize);
        $result['totalCount'] = $count;
        return $this->sendSuccess($result);
    }


    public function mailList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['goodName']) ? $map['goodName'] = ['like', "%" . $postData['goodName'] . "%"] : '';
        $pageSize = $postData['pageSize'];
        $length = ($postData['pageNum'] - 1) * $pageSize;
        $list = Db::table('mailgood')->where($map)->limit($pageSize, $length)->order('id desc')->select();
        $result['list'] = $list;
        $result['currPage'] = $postData['pageNum'];
        $result['pageSize'] = $pageSize;
        $count = Db::table('mailgood')->where($map)->count();
        $result['totalPage'] = ceil($count / $pageSize);
        $result['totalCount'] = $count;
        return $this->sendSuccess($result);
    }

    public function saveMailGood()
    {
        $postData = $this->request->param();
        foreach ($postData['goodId'] as $key => $value) {
            $data[] = array(
                "goodId" => $value,
                "kwaiId" => $postData['kwaiId'],
                "isSale" => $postData['isSale'],
                "mailAddr" => $postData['mailAddr'],
                "userId" => $this->checkAuth()['id'],
                "createTime" => getMilliSecond()
            );
        }
        $result = Db::table('b_goodMail')->insertAll($data);
        return $this->sendSuccess($result);
    }

    public function deleteMailGood(){
        $postData = $this->request->param();
        $id = $postData['id'];
        $result = Db::table('b_goodMail')->delete($id);
        return $this->sendSuccess($result);
    }

}
