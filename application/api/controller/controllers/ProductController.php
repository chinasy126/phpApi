<?php


namespace app\api\controller\controllers;


use app\api\controller\Api;
use app\api\model\Product;
use think\Request;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class ProductController extends Api
{
    private $postData;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->postData = $this->request->param();
    }

    /**
     * @return 返回列表所需数据
     */
    public function productList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['name']) ? $map['name'] = ['like', "%" . $postData['name'] . "%"] : '';
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $result = new Product();
        $res = $result->getProductList($map, $currentPage, $pageSize);
        return $this->sendSuccess($res);
    }

    public function saveOrUpdate()
    {
        $postData = $this->request->param();
        $model = new Product();
        $isUpdate = !empty($postData["id"]) ? true : false;
        $model->isUpdate($isUpdate)->data($postData)->save();
        return $this->sendSuccess("ok");
    }

    public function productDelete(){
        $postData = $this->request->param();
        $model = new Product();
        $model->where("id=".$postData["id"])->delete();
        return $this->sendSuccess("ok");
    }

}