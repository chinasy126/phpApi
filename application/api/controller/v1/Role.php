<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Db;
use think\Request;
use app\api\controller\Api;
use think\Response;
use app\api\controller\UnauthorizedException;

/**
 * 所有资源类接都必须继承基类控制器
 * 基类控制器提供了基础的验证，包含app_token,请求时间，请求是否合法的一系列的验证
 * 在所有子类中可以调用$this->clientInfo对象访问请求客户端信息，返回为一个数组
 * 在具体资源方法中，不需要再依赖注入，直接调用$this->request返回为请具体信息的一个对象
 * date:2017-07-25
 */
class Role extends Api
{
    private static $tableName = 's_role';
    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';

    /**
     * restful没有任何参数
     *
     * @return \think\Response
     */
    public function index()
    {
        return 'index';
    }

    /**
     * post方式
     *
     * @param \think\Request $request
     * @return \think\Response
     */
    public function save()
    {
        $postData = $this->request->param();
        $data = array();
        $data['name'] = $postData['name']; // 角色名称
        $data['status'] = $postData['status']; // 角色状态 1正常 / 2禁用
        $data['createTime'] = getMilliSecond(); // 角色名称
        $data['creatorId'] = $this->checkAuth()['id']; // 创建人ID
        $data['deleted'] = '1'; // 是否删除 0 未删除 1删除
        $data['describe'] = $postData['describe']; // 角色描述
        $data['menusPermissions'] = json_encode($postData['permissions']); // 方便数据绑定
        $data['menus'] = json_encode($this->getMenus($postData['permissions'])); // 所有菜单
        $data['permissions'] = json_encode($this->getPermissions($postData['permissions'])); // 所有按钮权限
        $result = Db::table(self::$tableName)->data($data)->insert();
        return $this->sendSuccess($result);
    }

    /**
     * @param array $data 所有权限
     * @return array 菜单ID集合
     *    获取菜单的ID
     */

    function getMenus($data = array())
    {
        $menuIds = array();
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $arr = explode('_', $value);
                if ($arr[0] != "permissions") {
                    array_push($menuIds, end($arr));
                }

            }
        }
        return $menuIds;
    }

    /**
     * @param array $data
     * @return array
     * 获取权限
     */
    function getPermissions($data = array())
    {
        $actionArr = array();
        if (!empty($data)) {
            $subMenu = array(); // 二级菜单的合计
            // S 获取一级菜单合集
            foreach ($data as $key => $value) {
                $arr = explode('_', $value);
                if ($arr[0] == "secondmenu") {
                    array_push($subMenu, $arr[count($arr) - 3]);
                    //  array_push($menuIds,end($arr));
                }
            }
            // E 获取一级菜单合集
            foreach ($subMenu as $key => $value) {
                $actions = $this->getActions($value, $data);
                array_push($actionArr, array("permissionId" => $value, "actionEntitySet" => $actions));
            }
        }
        return $actionArr;
    }

    /**
     * @param $menu
     * @param $data
     * @return array
     * 获取按钮
     */
    function getActions($menu, $data)
    {
        // 获取返回数组合计
        $actions = array();
        foreach ($data as $k => $v) {
            $arr = explode('_', $v);
            if ($arr[0] == 'permissions') {
                if (strpos($v, $menu) != false) {
                    array_push($actions, array("action" => end($arr)));
                }
            }
        }
        return $actions;
    }

    /**
     * get方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function read()
    {
        $map = array();
        $id = $this->request->param("id");
        $result = Db::table(self::$tableName)->find($id);
        return $this->sendSuccess($result);
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
        $postData = $this->request->param();
        $map = array();
        $map["id"] = $postData['id'];
        $data = array();
        $data['name'] = $postData['name']; // 角色名称
        $data['status'] = $postData['status']; // 角色状态 1正常 / 2禁用
        $data['createTime'] = getMilliSecond(); // 角色名称
        $data['creatorId'] = $this->checkAuth()['id']; // 创建人ID
        $data['deleted'] = '1'; // 是否删除 0 未删除 1删除
        $data['describe'] = $postData['describe']; // 角色描述
        $data['menusPermissions'] = json_encode($postData['permissions']); // 方便数据绑定
        $data['menus'] = json_encode($this->getMenus($postData['permissions'])); // 所有菜单
        $data['permissions'] = json_encode($this->getPermissions($postData['permissions'])); // 所有按钮权限
        $result = Db::table(self::$tableName)->where($map)->data($data)->update();
        return $this->sendSuccess($result);
    }

    /**
     * delete方式
     *
     * @param int $id
     * @return \think\Response
     */
    public function delete()
    {
        $postData = $this->request->param();
        $id = $postData['id'];
        $result = Db::table(self::$tableName)->delete($id);
        return $this->sendSuccess($result);
    }


    public function fans($id)
    {
        return $id;
    }


    public function info()
    {
        echo '123123123';
    }

    public function rolelist()
    {
        $postData = $this->request->param();
        // 分页信息
        $pageNo = $postData['pageNo'];
        $length = $postData['pageSize'];
        $offset = ($pageNo - 1) * $length;
        $map = array();

        if (!empty($postData['name'])) {
            $map['name'] = array('like', "%{$postData['name']}%", 'or');
        }

        if (!empty($postData['status'])) {
            $map['status'] = $postData['status'];
        }

        $list = Db::table(self::$tableName)->where($map)->limit($offset, $length)->select();
        $result['data'] = $list;
        $result['pageSize'] = $length;
        $result['totalCount'] = Db::table(self::$tableName)->where($map)->count();
        $result['pageNo'] = intval($pageNo);
        return $this->sendSuccess($result);
    }

    public function allRoleList()
    {
        $result = Db::table(self::$tableName)->select();
        return $this->sendSuccess($result);
    }

}
