<?php

namespace app\api\controller\v1;

use app\api\controller\Api;

class BusImages extends Api
{
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put';


    public function uploadImg(){
// 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $result['path'] = "/public/uploads/" . $info->getSaveName();
               // $result['url'] = (isHTTPS() ? 'https://' : 'http://') .
                    $_SERVER['HTTP_HOST'] .
                    "/public/uploads/" . $info->getSaveName();
                $result = str_replace('\\', "/", $result);
                return $this->returnmsg(200, 'ok', $result);
            } else {
                // 上传失败获取错误信息
                $result = $file->getError();
                return $this->returnmsg(200, 'ok', $result);
            }
        }
    }

}
