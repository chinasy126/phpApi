<?php

namespace app\api\controller\v1;


use Firebase\JWT\ExpiredException;
use think\Controller;
use think\Db;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\DbException;
use think\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use app\api\controller\Send;

vendor('autoload.php');

class Login extends Controller
{
    use Send;

    private $jwtKey = 'jwtKey';

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */

    public function index()
    {


        $payload = array(
            "iat" => time(),
            "nbf" => time(),
            'exp' => time() + 30,
            'id' => 1,
        );
        $key = $this->jwtKey;
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {

        $map = array();
        $map['name'] = $this->request->param('name');
        $map['password'] = $this->request->param('password');

        $result = Db::table('users')->where($map)->find();
        $result['permissions'] = array(
            array(
                "id"=> "queryForm",
                "operation"=>array("add","edit")
            )
        );
        $result['roles'] = array(
            array("id"=>'admin',"operation"=>array("add","edit","delete"))
        );

        $result['user'] = array(
            "address" => "天津市",
            "avatar" => "https://gw.alipayobjects.com/zos/rmsportal/cnrhVkzwxjPwAaCfPbdc.png",
            "name" => "JACK",
        );
        $result['expireAt'] = "2022-05-05T21:38:38.802Z";

        $payload = array(
            "iat" => time(),
            "nbf" => time(),
            'exp' => time() + 3600,
            'id' => 1,
        );
        $key = $this->jwtKey;
        $jwt = JWT::encode($payload, $key, 'HS256');
        $result['token'] = $jwt;

        try {
            $this->returnmsg(200, '登录成功', $result);
        } catch (\Exception $e) {
            $this->sendError(500, '没有查询结果', 500);
        }

//        dump($result);
//        die();
//        $headers = getallheaders(); //获取所有header头信息
//        $token = $headers['Token'];
//        $key = $this->jwtKey;
//        try {
//            $decoded = JWT::decode($token, new Key($key, 'HS256'));
//            return 'Token 有效';
//        } catch (ExpiredException $exception){
//            return 'Token 无效';
//        }


//        dump($this->request);
//        dump($this->clientInfo);

//        $payload = array(
//            "iat" => time(),
//            "nbf" => time(),
//            'exp' => time()+60,
//            'id' => 1,
//        );
//        $key = $this->jwtKey;
//        $jwt = JWT::encode($payload, $key,'HS256');
//        return $jwt;


        die();
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

}