<?php

namespace app\api\controller\v1;

use Firebase\JWT\JWT;
use think\Controller;
use think\Request;
use app\api\controller\Send;
use think\Db;

class Auth extends Controller
{
    use Send;
    private $jwtKey = 'jwtKey';
    /**
     * 构造函数
     * 初始化检测请求时间，签名等
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 登录
     */
    public function login()
    {

        $map = array();
        $map['username'] = trim($this->request->param('username'));
        $map['password'] = trim($this->request->param('password'));
        $result = Db::table('users')->where($map)->find();
        if (!empty($result)) {
            // 更新用户信息
            $this->updateLoginInfo();
            $token = $this->getToken($result['id']);
            $result['token'] = $token;
            return $this->sendSuccess($result);
        } else {
            return $this->sendError('账号密码错误');
        }
    }

    /**
     * 更新登录信息
     */
    function updateLoginInfo(){
        $map = array();
        $map['username'] = trim($this->request->param('username'));
        $map['password'] = trim($this->request->param('password'));
        $data['lastLoginTime'] = getMilliSecond();
        $data['lastLoginIp'] = getIPAddress();
        Db::table('users')->where($map)->data($data)->update();
    }

    /**
     * @param $id
     * @return string
     * 获取TOKEN 3600
     */
    public function getToken($id){
        $payload = array(
            "iat" => time(),
            "nbf" => time(),
            'exp' => time() + 24*60*60,
            'id' => $id,
        );
        $key = $this->jwtKey;
        return JWT::encode($payload, $key, 'HS256');
    }

    public function logout()
    {

    }
}