<?php

namespace app\api\controller\v1;


use app\api\controller\Api;
use app\api\controller\Send;
use think\Controller;
use think\Db;

class Routes extends Api
{
    private static $tableName = 'menus';

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete|patch|head|options';

    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */

    public function index()
    {
        $map = array();
        $map['parentId'] = '0';
        $result = Db::table('menus')->where($map)->order('id asc')->select();
        foreach ($result as $key=>$value){
            $result[$key]['meta'] = json_decode($value['meta']);
        }
        return $this->sendSuccess($result);
    }

    public function save(){
        $data = array();
        $map = array();
        $resources = Db::table('menus');
        $map['name'] = $data['name'] = strtolower($this->request->param('name'));

        // 权限数组
        $actions = $this->request->param('actions/a');
        if(count($actions) != 0 ){
            $data['actions'] = json_encode($actions);;
        }
        // 权限数组

        // 先查询
        $res = Db::table('menus')->where($map)->find();
        if(!empty($res)){
            return $this->sendSuccess($res);
        }
        $data['parentId'] = $this->request->param('parentId');
        if(empty($data['parentId'])){
            $data['component'] = 'RouteView';
        }else {
            $data['component'] = ucfirst($this->request->param('name'));
        }

        $data['meta'] = json_encode(array(
            'icon'=>$this->request->param('icon'),
            'title'=>$this->request->param('title'),
        )); // {icon: "dashboard", title: "仪表盘", show: true , target: "_blank" }
        $data['createTime'] = strtotime(date('Y-m-d H:i:s')) + '000';
        //$data['path'] =  '';// $this->request->param('path');
        $data['title'] =  $this->request->param('title');// $this->request->param('path');
        $data['hidden'] =  $this->request->param('hidden') ? 1 : 0;
        $result = Db::table('menus')->insert($data);
        return $this->sendSuccess([]);
    }

    /**
     * @return string
     * 获取所有菜单以及权限的列表
     */
    public function getAllMenuPermissions(){
        $map = array();
        $map['parentId'] = 0;
        $result = Db::table(self::$tableName)->where($map)->order('id asc')->select();
        foreach ($result as $key=>$value ){
            $list = Db::table(self::$tableName)->where('parentId = '.$value['id'])->order('id asc')->select();
            foreach ($list as $k=>$v){
                $list[$k]['actions'] = $this->getActionBtnList(json_decode($v['actions'],true));
            }
            $result[$key]['permissions'] = $list;
        }
        return $this->sendSuccess($result);
    }

    /**
     * @param $actionList
     * 返回指定格式的按钮列表
     * {label: "新增" value: "add"}
     */
    public function getActionBtnList($actionList){
        if(count($actionList) > 0 ){
            $resultArr = array();
            foreach ($actionList as $key=>$value){
               $arr = array("label"=>$value['describe'],"value"=>$value['action']);
                array_push($resultArr,$arr);
            }
            return $resultArr;
        }
        return [];
    }



}