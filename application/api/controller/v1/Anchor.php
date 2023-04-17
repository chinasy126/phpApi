<?php

namespace app\api\controller\v1;

use app\api\controller\Send;
use think\Controller;
use think\Db;
use think\Request;
use app\api\controller\Api;
use think\Response;
use app\api\controller\UnauthorizedException;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 * date:2017-07-25
 */
class Anchor extends Common
{
    use Send;

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';

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
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        echo 'save';
        dump($this->request);
        dump($this->clientInfo);
    }

    /**
     * get方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function read()
    {
        echo 'get';
        dump($this->request);
        dump($this->clientInfo);
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
        return 'update';
    }

    /**
     * delete方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete()
    {
        return 'delete';
    }


    public function fans($id)
    {
        return $id;
    }


    public function info()
    {
        echo '123123123';
    }

    public function regUser()
    {
        $postData = $this->request->param();
        $map = array();
        $userInfo = Db::table('w_anchor')
            ->where('username= "' . $postData['username'] . '"  or contactNum=' . $postData['contactNum'])
            ->find();
        if (!empty($userInfo)) {
            return $this->sendsuccess($userInfo, '', 201);
        } else {
            $data = array();
            $data['username'] = $postData['username'];
            $data['password'] = md5($postData['password']);
            $data['contactNum'] = $postData['contactNum'];
            $result = Db::table('w_anchor')->insertGetId($data);
            return $this->sendsuccess($result);
        }
    }

    public function login()
    {
        $postData = $this->request->param();
        $map = array();
        $map['username'] = $postData['username'];
        $map['password'] = md5($postData['password']);
        $result = Db::table('w_anchor')
            ->where($map)
            ->find();
        if (!empty($result)) {
            $token = $this->generateToken($result['id']);
            $result['token'] = $token;
            return $this->sendSuccess($result);
        } else {

            return $this->sendsuccess($result, '用户名或密码输入错误', 201);
        }
    }

}
