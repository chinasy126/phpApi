<?php


namespace app\api\controller\v1;

use app\api\controller\Api;
use think\Db;
use think\Exception;
use think\exception\PDOException;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class Admin extends Api
{
    private static $tableName = 'users';
    private static $roleTableName = 's_role';

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
        return 'index';
    }

    /**
     * @param $id
     * @return mixed
     * 获取用户信息
     */
    function getRoleInfo($id)
    {
        $result = Db::table(self::$roleTableName)->find($id);
        return $result;
    }

    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $currentUser = $this->checkAuth();
        $postData = $this->request->param();
        $data = array();
        $map = array();
        $map['username'] = $data['username'] = $postData['username'];

        $info = Db::table(self::$tableName)->where($map)->find();
        if (!empty($info)) {
            return $this->sendSuccess($info);
        }

        $data['password'] = $postData['password'];
        $data['name'] = $postData['name'];
        $data['status'] = $postData['status'];
        $data['lastLoginIp'] = '';
        $data['lastLoginTime'] = '';
        $data['creatorId'] = $currentUser['id'];
        $data['createTime'] = getMilliSecond();
        $data['roleId'] = $postData['roleId'];
        $data['roleName'] = $postData['roleId'];
        $result = Db::table(self::$tableName)->data($data)->insert();
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
        $currentUser = $this->checkAuth();
        $postData = $this->request->param();
        $data = array();
        $map['id'] = $postData['id'];
        if (!empty($postData['password'])) {
            $data['password'] = $postData['password'];
        }
        $data['name'] = $postData['name'];
        $data['status'] = $postData['status'];
        $data['creatorId'] = $currentUser['id'];
        $data['createTime'] = getMilliSecond();
        $data['roleId'] = $this->getRoleInfo($postData['roleId'])['id'];
        $data['roleName'] = $this->getRoleInfo($postData['roleId'])['name'];
        $result = Db::table(self::$tableName)->where($map)->data($data)->update();
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
        $id = $this->request->param("id");
        $data = array();
        $data['deleted'] = 1;
        $map = array();
        $map['id'] = $id;
        $result = Db::table(self::$tableName)->where($map)->data($data)->update();
        return $this->sendSuccess($result);
    }


    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function adminlist()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['username']) ? $map['username'] = ['like', "%" . $postData['username'] . "%"] : '';
        !empty($postData['name']) ? $map['name'] = ['like', "%" . $postData['name'] . "%"] : '';
        !empty($postData['status']) ? $map['status'] = $postData['status'] : '';
        !empty($postData['deleted']) ? $map['deleted'] = $postData['deleted'] : '';

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

    /**
     * @return
     * @throws Exception
     * @throws PDOException
     * 多个物理删除
     */
    public function deleteAdmin()
    {
        $postData = $this->request->param();
        $map = array();
        if(count($postData) != 0){
            $delIds = implode(',',$postData);
            $map['id'] = array('in',$delIds);
            $result = Db::table(self::$tableName)->where($map)->delete();
            echo Db::table(self::$tableName)->getLastSql();
            return $this->sendSuccess($result);
        }

    }

}