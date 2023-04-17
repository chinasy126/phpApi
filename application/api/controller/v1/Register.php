<?php

namespace app\api\controller\v1;

use app\api\controller\Send;
use think\Controller;
use think\Db;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 * date:2017-07-25
 */
class Register extends Controller
{
    use Send;
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';

    public static $tableName = 'users';

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
        $postData = $this->request->param();
        $data['password'] = md5($postData['password']);
        $data['telephone'] = $postData['telephone'];
        $data['username'] = $postData['username'];
        $data['createTime'] = getMilliSecond();
        $data['lastLoginTime'] = getMilliSecond();
        $data['lastLoginIp'] = getIPAddress();
        $data['deleted'] = 0;
        $data['status'] = 1;
        $data['roleId'] = $postData['regType']== 1? 2 : 3 ; // 1 商家 2团长
        $isUser = $this->isRegister();
        if ($isUser) {
            return $this->sendError('该用户或手机已经被注册');
        }
        $result = Db::table(self::$tableName)->insertGetId($data);
        $this->regType($result);
        return $this->sendSuccess($result);
    }

    /**
     * 商家团长扩展信息
     */
    public function regType($userId = ''){
        $postData = $this->request->param();
        // 商家
        $data = array();
        $data['userId'] = $userId;
        $data['regType'] = $postData['regType'];
        Db::table('usersExtend')->data($data)->insert();
    }

    /**
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 查询用户名是否被注册
     */
    public function isRegister()
    {
        $postData = $this->request->param();
        $map = array();
        $map['username'] = $postData['username'];
        $mapOr['telephone'] = $postData['telephone'];
        $userInfo = Db::table(self::$tableName)->where($map)->whereOr($mapOr)->find();
        if (!empty($userInfo)) {
            return true;
        }
        return false;
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
}
