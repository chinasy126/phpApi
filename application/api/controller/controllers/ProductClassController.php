<?php


namespace app\api\controller\controllers;


use app\api\controller\Api;
use app\api\model\Product;
use app\api\model\ProductClass;
use think\Db;
use think\Model;
use think\Request;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class ProductClassController extends Api
{
    private $postData;
    private $classPower = '0000';

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->postData = $this->request->param();
    }

    /**
     * @return 返回列表所需数据
     */
    public function category()
    {
        $model = new ProductClass();
        $list = $model->order("classpower asc,classid desc")->select();
        return $this->sendSuccess($this->sendData($list));

    }

    /**
     * 分类列表
     */

    public function dataList()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['classname']) ? $map['classname'] = ['like', "%" . $postData['classname'] . "%"] : '';
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $result = new ProductClass();
        $res = $result->getDataList($map, $currentPage, $pageSize);
        return $this->sendSuccess($res);
    }

    /**
     * 修改
     */

    public function modify()
    {
        $postData = $this->request->param();
        $model = new ProductClass();
        // 查询自身信息
        $info = Db::table('productclass')->where("classid=" . $postData["classid"])->find();
        if ($info["rootid"] == $postData["rootid"]  ) {
            //没有修改分类
            $res = $model->isUpdate(true)->data($postData)->save();
        } else {

            // 修改了分类
            // 当前分类列表
            $sql = "SELECT
                    *
                FROM
                    productclass
                WHERE
                    classpower LIKE CONCAT(
                        (
                            SELECT
                                classpower
                            FROM
                                productclass AS p
                            WHERE
                                p.classid = " . $postData['classid'] . "
                        ),
                        '%'
                    )
                ORDER BY
	            classpower ASC";
            $list = Db::query($sql);

            foreach ($list as $key=>$item) {
                if($key == 0){
                    $item["rootid"] =  $postData["rootid"];
                }
                $modifyData = $item;
                $info = $this->getClassification($item);
                $modifyData["depth"] = $info["depth"];
                $modifyData["classpower"] = $info["classpower"];
                $modifyData["rootid"] = $info["rootid"];
                $model->isUpdate(true)->data($modifyData)->save();
            }
        }
       // $res = $model->isUpdate(true)->data($postData)->save();
        return $this->sendSuccess('ok');
    }

    public function insert()
    {
        $postData = $this->request->param();
        $model = new ProductClass();
        // 获取分类
        $info = $this->getClassification($postData);
        $postData["classpower"] = $info['classpower'];
        $postData["depth"] = $info['depth'];
        $postData["rootid"] = $info['rootid'];
        $res = $model->data($postData)->save();
        return $this->sendSuccess($res);
    }

    public function delete()
    {
        $postData = $this->request->param();
        $model = new ProductClass();
        $res = $model->where("classid=" . $postData["classid"])->find();
        $where["classpower"] = array('like', $res["classpower"] . '%');
        $deleteInfo = $model->where($where)->delete();
        return $this->sendSuccess($deleteInfo);
    }

    /**
     * 获取分类
     */
    public function getClassification($param)
    {
        $model = new ProductClass();
        $classData = $param;
        if (empty($param["rootid"])) {
            $classData["rootid"] = 0;
            $classData["depth"] = strlen($this->classPower);
            // 查询最大+1
            $where = array();
            $where['depth'] = strlen($this->classPower);
            $where["rootid"] = 0;
            $res = $model->where($where)->order("classpower desc")->find();
            $classpower = $this->classPower;
            if (!empty($res)) {
                $classpower = sprintf("%04d", intval($res['classpower']) + 1);
            } else {
                $classpower = sprintf("%04d", intval($classpower) + 1);
            }
            $classData["classpower"] = $classpower;
        } else {
            // 多级分类
            $where["rootid"] = $param["rootid"];
            // 找到所选父级数据
            $classInfo = Db::name("productclass")->where("classid=" . $param["rootid"])->find();
            // 所选父级分类下边是否有数据
            $res = [];
            if(!empty($classInfo)){
                $map["classpower"] = array("like",$classInfo["classpower"]."%");
                $map["classid"] = array("neq",$param["rootid"]);
                $res = Db::name("productclass")->where($map)->order("classpower desc")->find();
            }
            if (!empty($res)) {
                $classData["classpower"] = $this->addNumberToStringArray($res["classpower"], 1);
            } else {
                $classData["classpower"] = $classInfo['classpower'] .
                    $this->addNumberToStringArray($this->classPower, 1);
            }
            $classData["depth"] = strlen($this->classPower) + $classInfo['depth'];
        }
        return $classData;
    }


    /**
     * @param $stringArray
     * @param $number
     * @return string
     * 分类数字转换
     */
    function addNumberToStringArray($stringArray, $number)
    {
        // 转换字符串为整数
        $intNumber = intval($number);
        // 遍历数组，将每个字符串转换为整数并相加
        $result = "";
        $intValue = intval($stringArray);
        $intValue += $intNumber;
        // 根据原始字符串长度生成新字符串
        $result .= sprintf("%0" . strlen($stringArray) . "d", $intValue);
        return $result;
    }


}