<?php

namespace app\api\controller\v1;

use app\api\controller\Api;
use app\api\controller\Send;
use think\Controller;
use think\Db;

class WechOther extends Controller
{
    use Send;

    public function getAllGoods()
    {
        $result = Db::table('b_good')->order('id desc')->limit('30')->select();
        return $this->sendsuccess($result);
    }

    public function goodDetail(){
        $postData = $this->request->param();
        $result = Db::table('b_good')->find($postData['id']);

        return $this->sendsuccess($result);
    }

}