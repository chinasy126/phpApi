<?php

namespace app\api\controller\controllers;


use app\api\controller\Api;

use think\Db;

use think\Loader;



class Upload extends Api
{
    public function uploadFile()
    {

        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if ($info) {
                $result = "/public/uploads/" . $info->getSaveName();
                $result = str_replace('\\', "/", $result);
                return $this->sendSuccess(
                    array("file"=>$result)
                );
            } else {
                // 上传失败获取错误信息
                $result = $file->getError();
                return $this->sendError("文件上传失败!");
            }
        }
    }


    public function excelfile()
    {
        $file = request()->file('excel');
        if ($file) {
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/excel');
            if ($info) {
                $saveName = $info->getSaveName();
                $path = "/public/uploads/excel/" . $saveName;

                $result = $this->insertData($path);

//                $result['path'] = "/public/uploads/excel/" . $saveName;
//                $result['url'] = (isHTTPS() ? 'https://' : 'http://') .
//                    $_SERVER['HTTP_HOST'] .
//                    "/public/uploads/" . $info->getSaveName();
//                $result = str_replace('\\', "/", $result);
                return $this->returnmsg(200, 'ok', $result);

            } else {
                // 上传失败获取错误信息
                $result = $file->getError();
                return $this->returnmsg(200, 'ok', $result);
            }
        }
    }

    /*
     * 插入数据
     */
    public function insertData($file_path){
        Loader::import('PHPExcel.PHPExcel');
        Loader::import('PHPExcel.PHPExcel.PHPExcel_IOFactory');
        Loader::import('PHPExcel.PHPExcel.PHPExcel_Cell');
        //实例化PHPExcel

        $objPHPExcel = new \PHPExcel();
        // $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        // 根据扩展名后缀判断。

        $array = explode('.',$file_path);
        if($array[1] == 'xlsx'){
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
        }else{
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
        }

        //$file_path = ROOT_PATH.'public/uploads/excel/20210808/78a35e7abffb36640c7c4fa72d054d15.xlsx';

        $objData = $objReader->load(ROOT_PATH.$file_path);
        $excel_array = $objData->getSheet(0)->toArray(); // 需要删除第一行
        unset($excel_array[0]);

        $num = 0;
        $insertData = array();
        $adminId = $this->request->param('adminId');
        $adminName = $this->request->param('adminName');

        foreach ($excel_array as $key => $value) {
            if(!empty($value[0]) && !empty($value[2]) && !empty($value[2]) && !empty($value[3])){
                $insertData[$num] ['adminId'] = $adminId;
                $insertData[$num] ['adminName'] = $adminName;
                $insertData[$num]['type'] = $value[0];// 车型
                $insertData[$num]['size'] = $value[1];// 尺寸
                $insertData[$num]['brand'] = $value[2]; //轮胎品牌
                $insertData[$num]['price'] = $value[3]; // 销售价格
                $num++;
            }
        }

        return Db::name('tiredata')->insertAll($insertData);

    }

    /*
     * 获取扩展名
     */
    function getExt($url)
    {
        $path=parse_url($url);
        $str=explode('.',$path['path']);
        return $str[1];
    }



}