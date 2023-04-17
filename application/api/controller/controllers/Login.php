<?php

namespace app\api\controller\controllers;


use app\api\model\LoginModel;
use Firebase\JWT\ExpiredException;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Model;
use think\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\api\controller\Send;
use think\captcha\Captcha;


vendor('autoload.php');

class Login extends Controller
{
    use Send;

    private $jwtKey = 'jwtKey';
    private $data = [];
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';

    public function __construct(Request $request = null)
    {
        error_reporting(0);
        parent::__construct($request);
        $this->data = $this->request->param();
    }

    /**
     * 设置验证码
     */
    public function verificationcode()
    {
        $captcha = new Captcha();
        $captcha->fontSize = 14;
        $captcha->imageW = 95;
        $captcha->imageH = 25;
        $captcha->length = 1; // 验证码位数
        $captcha->useNoise = false;
        return $captcha->entry();
    }

    /**
     * 登录接口
     */
    public function signIn()
    {
        // 校验验证码
        $captcha = new Captcha();
        $data = $this->request->param();
        if (!$captcha->check($data['code'])) {
            return $this->sendError('验证码输入错误!');
        }

        $User = new LoginModel();
        $isExitUser = $User->checkLoginUser($data['username']);

        if (empty($isExitUser)) {
            return $this->sendError('登录用户不存在!');
        }

        if ($isExitUser['password'] != md5($data['password'])) {
            return $this->sendError('密码输入错误!');
        } else {

            $payload = array(
                "iat" => time(),
                "nbf" => time(),
                'exp' => time() + 24 * 60 * 60,
                'id' => $isExitUser['id'],
            );

            $key = $this->jwtKey;
            $jwt = JWT::encode($payload, $key, 'HS256');
            $ret = array("token" => $jwt);
            return $this->sendSuccess($ret, '登录成功!', 20000);
        }
    }

    /**
     * 获取用户信息
     */

    public function userInfo()
    {
        $data = $this->data;

        try {
            $res = JWT::decode($data['token'], new Key($this->jwtKey, 'HS256')); // 获取ID
            $resUser = get_object_vars($res); // 讲数组转成数组
            $User = new LoginModel();
            $userInfo = $User->findUserById($resUser['id']);
            $userInfo['name'] = $userInfo['username'];
            return $this->sendSuccess($userInfo, '登录成功!');
        } catch (Exception $e) {
            return $this->sendError('token过期了!');
        }
    }


    public function nav()
    {
        $header = getallheaders();
        $res = JWT::decode($header['X-Token'], new Key($this->jwtKey, 'HS256')); // 获取ID
        $resUser = get_object_vars($res); // 讲数组转成数组
        $User = new LoginModel();
        $userInfo = $User->findUserById($resUser['id']);
        // 查询菜单
        $menuBtnList = $this->getMenuList($userInfo['roleId']);
        $userInfo['menusList'] = $menuBtnList;
        $data['data'] = $userInfo;
        return $this->sendSuccess($data, '登录成功!');
    }


    /**
     * 获取菜单
     */
    public function getMenuList($roleId): array
    {
        $roleMenuList = Db::table('rolemenus')->where('roleId = ' . $roleId)->select();
        $menuIds = [];
        foreach ($roleMenuList as $key => $value) {
            array_push($menuIds, $value['menuId']);
        }
        $menuList = Db::table('menu')->order("menuOrder desc")->whereIn('id', $menuIds)->select();

        // 获取按钮列表
        $menuButtonList = Db::table('rolebuttons')->where("roleId = " . $roleId)->select();

        foreach ($menuList as $key => $value) {
            $menuList[$key]['menubuttonList'] = $this->findBtnList($menuList[$key]['id'], $menuButtonList);
        }
        return $menuList;
    }

    /**
     * @param $id
     * @param $list
     * @return array
     * 匹配按钮
     */
    public function findBtnList($id, $list)
    {
        $result = [];
        foreach ($list as $key => $value) {
            if ($value['menuId'] == $id) {
                $btnArr = array(
                    "name" => $value['buttonName'],
                    "type" => $value['buttonType']
                );
                array_push($result, $btnArr);
            }
        }
        return $result;
    }

    /**
     * 退出登录
     */
    public function logout(){
        return $this->sendSuccess([], '退出成功!');
    }

}