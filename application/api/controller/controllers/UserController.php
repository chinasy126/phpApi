<?php


namespace app\api\controller\controllers;


use app\api\controller\Api;

use app\api\controller\Oauth;
use app\api\model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use think\Db;
use think\Model;
use think\Request;


/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class UserController extends Api
{
    private $postData;
    private $jwtKey = 'jwtKey';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->postData = $this->request->param();
    }

    /**
     * 用户列表
     */
    public function userList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['username']) ? $map['username'] = ['like', "%" . $postData['username'] . "%"] : '';
        !empty($postData['createTime']) ? $map['createTime'] = $postData['createTime'] : '';
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $result = new User();
        $res = $result->getDataList($map, $currentPage, $pageSize);
        return $this->sendSuccess($res);
    }


    public function userInsert()
    {
        $postData = $this->request->param();
        $postData["password"] = md5($postData["password"]);
        $model = new User();
        $res = Db::name("t_user")->where("username='" . $postData["username"] . "'")->select();
        if (!empty($res)) {
            return $this->sendError("用户名已存在!");
        }
        $user = $model->data($postData)->save();
        // $user =  Db::name("t_user")->data($postData)->insert();
        return $this->sendSuccess($user);
    }

    public function userDelete()
    {
        $postData = $this->request->param();
        Db::name("t_user")->where("id=" . $postData["id"])->delete();
        return $this->sendSuccess("用户删除成功!");
    }

    public function userBatchDelete()
    {
        $postData = $this->request->param();

        $model = new User();
        $where = array();
        $where["id"] = array("in", $postData);
        $model->where($where)->delete();
        return $this->sendSuccess("用户删除成功!");
    }

    /**
     * 修改用户
     */
    public function userModify()
    {
        $postData = $this->request->param();
        $map["id"] = $postData["id"];
        $model = new User();
        if (!empty($postData["password"])) {
            $postData["password"] = md5($postData["password"]);
        } else {
            unset($postData["password"]);
        }

        $model->isUpdate(true)->data($postData)->save();
        return $this->sendSuccess("用户修改成功!");
    }

    /**
     * 比较密码
     */
    public function comPassSame()
    {
        $postData = $this->request->param();
        // $postData['password']
        $oauch = new Oauth();

        $model = new User();
        $toeknForUser = $oauch->getCurrentInfo();
        $userInfo = $model->where("id=" . $toeknForUser["id"])->find();
        if ($userInfo["password"] == md5($postData["password"])) {
            return $this->sendSuccess("旧密码输入正确");
        }
        return $this->sendError("旧密码输入错误");
    }

    public function updatePassword()
    {
        $postData = $this->request->param();
        $oauch = new Oauth();
        $model = new User();
        $toeknForUser = $oauch->getCurrentInfo();
        $postData["id"] = $toeknForUser["id"];
        $postData["password"] = md5($postData["password"]);
        $model->data($postData)->isUpdate(true)->save();
        return $this->sendSuccess("ok");
    }

    /**
     * 修改头像
     */
    public function setUserAvatar()
    {
        $oauch = new Oauth();
        $currentInfo = $oauch->getCurrentInfo();

        try {
            $map = array();
            $user = new User();
            $data['avatar'] = $this->postData['avatar'];
            $data['id'] = $currentInfo['id'];
            $res = $user->isUpdate(true)->data($data)->save();
            return $this->sendSuccess($res, '登录成功!');
        } catch (Exception $e) {
            return $this->sendError('token过期了!');
        }
    }
}