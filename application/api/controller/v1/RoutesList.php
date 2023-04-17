<?php

namespace app\api\controller\v1;

use app\api\controller\Api;
use think\Controller;
use think\Db;
use think\Exception;
use think\exception\PDOException;

class RoutesList extends Api
{

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';
    private static $tableName = 'menus';

    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */

    public function index()
    {
        return $this->sendSuccess([]);
    }

    public function save()
    {
        // 分页信息
        $pageNo = $this->request->param('pageNo');
        $length = $this->request->param('pageSize');
        $offset = ($pageNo-1) * $length;

        if(!empty($this->request->param('searchTitle'))){
            $map['title'] = array('like', "%{$this->request->param('searchTitle')}%", 'or');
        }
        $map['parentId'] = 0;

        $list = Db::table(self::$tableName)->where($map)->limit($offset,$length)->select();

        foreach ($list as $key => $value) {
            $list[$key]['meta'] = json_decode($value['meta']);
            $list[$key]['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
            $list[$key]['subList'] = $this->getSubList($value['id']);
        }
        $result['data'] = $list;
        $result['pageSize'] = $length;
        $result['totalCount'] = Db::table(self::$tableName)->where($map)->count();
        $result['pageNo'] =  intval($pageNo);


        return $this->sendSuccess($result);
    }

    /**
     * @param $parentId
     * @return array|bool|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 获取TABLE子列表
     */

    public function getSubList($parentId){
        $result = array();
        $map = array();
        $map['parentId'] = $parentId;
        $list = Db::table(self::$tableName)->order('id asc')->where($map)->select();
        foreach ($list as $key => $value) {
            $list[$key]['meta'] = json_decode($value['meta']);
            $list[$key]['createTime'] = date('Y-m-d H:i:s', $value['createTime']);
        }
        return $list;
    }

    /**
     * delete方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete()
    {
        $map = array();
        $map['id'] = $deleteId = $this->request->param('id');
        $data = Db::table(self::$tableName)->data($map)->find();
        if (!empty($data)) {
            $result = Db::table(self::$tableName)->delete($deleteId);
            if ($data['parentId'] != 0) {
                $deleteMap = array();
                $deleteMap['parentId'] = $deleteId;
                Db::table(self::$tableName)->where($deleteMap)->delete();
            }
            return $this->sendSuccess($result);
        }
    }

    /**
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws Exception
     * @throws PDOException
     * 修改数据
     */
    public function update()
    {
        $map = array();
        $data = array();
        $map['id'] = $this->request->param('id');
        $data['parentId'] = $this->request->param('parentId');
        if (empty($data['parentId'])) {
            $data['component'] = 'RouteView';
        } else {
            $data['component'] = ucfirst($this->request->param('name'));
        }
        $data['name'] = $this->request->param('name');
        // 权限数组
        $actions = $this->request->param('actions/a');
        if(count($actions) != 0 ){
            $data['actions'] = json_encode($actions);;
        }
        // 权限数组

        $data['meta'] = json_encode(array(
            'icon' => $this->request->param('icon'),
            'title' => $this->request->param('title'),
        ));
        $data['title'] = $this->request->param('title');
        $data['createTime'] = strtotime(date('Y-m-d H:i:s'));
        //$data['path'] = '';
        $data['hidden'] =  $this->request->param('hidden') ? 1 : 0;
        $result = Db::table(self::$tableName)->where($map)->data($data)->update();
        return $this->sendSuccess($result);
    }

    /**
     * @return \think\Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\Xml
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 返回详情数据
     */
    public function read()
    {
        $map = array();
        $map['id'] = $this->request->param('id');
        $result = Db::table(self::$tableName)->where($map)->find();

        if($result['actions'] != ''){
            $result['actions'] = json_decode($result['actions']);
        }

        return $this->sendSuccess($result);
    }
}