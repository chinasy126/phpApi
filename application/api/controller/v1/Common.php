<?php

namespace app\api\controller\v1;

use Firebase\JWT\JWT;
use think\Controller;
use think\Request;
use app\api\controller\Send;
use think\Db;

class Common extends Controller
{
    private $jwtKey = 'jwtKey';

    public function generateToken($id)
    {
        $payload = array(
            "iat" => time(),
            "nbf" => time(),
            'exp' => time() + 24 * 60 * 60,
            'id' => $id,
        );
        $key = $this->jwtKey;
        return JWT::encode($payload, $key, 'HS256');
    }
}