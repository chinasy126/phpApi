<?php


namespace app\api\controller\controllers;

use app\api\controller\Api;
use app\api\model\RoleModel;
use snowflake\SnowFlake;
use think\Db;
use think\Request;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class RoleController extends Api
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
    public function getListOfData()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['roleName']) ? $map['roleName'] = ['roleName', "%" . $postData['roleName'] . "%"] : '';
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $result = new RoleModel();
        $res = $result->getDataList($map, $currentPage, $pageSize);
        return $this->sendSuccess($res);
    }

    /**
     * 修改角色数据
     */
    public function modify()
    {
        $postData = $this->request->param();
        $res = Db::table('role')->where('id =' . $postData['id'])->data($postData)->update();
        // 修改按钮数据
        $this->modifyRoleMenus($postData);
        return $this->sendSuccess($res);
    }

    /**
     * @param $data 菜单按钮数据
     * 编辑按钮数据
     */
    public function modifyRoleMenus($data)
    {
        $dataBaseMenuList = Db::table('rolemenus')->where('roleId = ' . $data['id'])->select();
        // 1 菜单删除则按钮所有都删除
        foreach ($dataBaseMenuList as $value) {
            $isExistMenu = false;
            foreach ($data['roleMenus'] as $v) {
                if ($value['menuId'] == $v['menuId']) {
                    $isExistMenu = true;
                }
            }
            if (!$isExistMenu) {
                $this->deleteMenuAndButton($value);
            }
        }

        // 2 菜单新增则按钮新增,
        foreach ($data['roleMenus'] as $roleMenu) {
            $idExitMenu = false;
            foreach ($dataBaseMenuList as $dbMenuItem) {
                if ($roleMenu['menuId'] == $dbMenuItem['menuId']) {
                    $idExitMenu = true; // 数据库中存在菜单 则对比按钮
                }
            }
            $this->doMenuFn($idExitMenu, $roleMenu, $data);
        }
        return $this->sendSuccess([]);
    }

    public function doMenuFn($idExitMenu, $roleMenu, $data)
    {
        if (!$idExitMenu) {
            // 插入菜单，插入按钮
            $insertData = array();
            $insertData['roleId'] = $data['id'];
            $insertData['menuId'] = $roleMenu['menuId'];
            $insertData['menuTitle'] = $roleMenu['menuTitle'];
            $insertData['menuButton'] = '';
            Db::table('rolemenus')->data($insertData)->insert();
            $lastInsId = Db::table('rolemenus')->getLastInsID();
            // 插入所有按钮
            foreach ($roleMenu['rolebuttonsList'] as $value) {
                $btnData = array();
                $btnData['id'] = '';
                $btnData['roleMenuId'] = $lastInsId;
                $btnData['buttonName'] = $value['buttonName'];
                $btnData['buttonType'] = $value['buttonType'];
                $btnData['roleId'] = $data['id'];
                $btnData['menuId'] = $roleMenu['menuId'];
                Db::table('rolebuttons')->data($btnData)->insert();
            }
        } else {
            // 对比按钮
            $where = array();
            $where['roleId'] = $data['id'];
            $where['menuId'] = $roleMenu['menuId'];
            $buttonList = Db::table('rolebuttons')->where($where)->select();

            // 前端传递  数据库查询
            $this->compButton($roleMenu, $buttonList, $data);
        }
    }

    /**
     * 对比按钮
     */
    public function compButton($staticList, $dataBaseList, $data)
    {
        $roleMenu = $staticList;
        $buttonList = $dataBaseList;

        // 前端菜单循环
        foreach ($roleMenu['rolebuttonsList'] as $value) {
            $isExitButton = false;
            $res = Db::table('rolemenus')->where(" menuId = " . $staticList['menuId'] . " and roleId = " . $data['id'])->find();

            $roleMenuId = $res['id'];
            foreach ($buttonList as $v) {
                if ($roleMenu['menuId'] == $v['menuId']) {
                    if ($value['buttonType'] == $v['buttonType']) {
                        $isExitButton = true;
                    }
                }
            }

            // 如果按钮不存在则新增按钮
            if (!$isExitButton) {
                $insertData = array();
                $insertData['id'] = SnowFlake::generateParticle();
                $insertData['roleMenuId'] = $roleMenuId;
                $insertData['buttonName'] = $value['buttonName'];
                $insertData['buttonType'] = $value['buttonType'];
                $insertData['roleId'] = $data['id'];
                $insertData['menuId'] = $roleMenu['menuId'];
                Db::table('rolebuttons')->data($insertData)->insert();
            }
        }

        // 后台数据库循环，删除按钮
        foreach ($buttonList as $value) {
            $isExitButton = false;
            foreach ($roleMenu['rolebuttonsList'] as $v) {
                if ($value['buttonType'] == $v['buttonType']) {
                    $isExitButton = true;
                }
            }
            // 删除不存在的按钮
            if (!$isExitButton) {
                $where = array();
                $where['id'] = $value['id'];
                Db::table('rolebuttons')->where($where)->delete();
            }

        }
    }

    /**
     * @param $params
     * 删除菜单与按钮
     */
    public function deleteMenuAndButton($params)
    {
        // 删除菜单
        $deleteMenu = array();
        $deleteMenu['id'] = $params['id'];
        Db::table('rolemenus')->where($deleteMenu)->delete();
        // 删除按钮
        $deleteBtn = array();
        $deleteBtn['roleMenuId'] = $params['id'];
        Db::table('rolebuttons')->where($deleteBtn)->delete();
    }

    public function insert()
    {
        $postData = $this->request->param();
        $roleData['roleName'] = $postData['roleName'];
        $roleData['roleDesc'] = $postData['roleDesc'];
        $roleData['roleMenus'] = '';

        Db::startTrans();
        try {
            // 角色插入
            Db::name('role')->data($roleData)->insert();
            $roleId = Db::name('role')->getLastInsID();

            // 插入菜单
            foreach ($postData['roleMenus'] as $value) {
                $menuData = array();
                $menuData['roleId'] = $roleId;
                $menuData['menuId'] = $value['menuId'];
                $menuData['menuTitle'] = $value['menuTitle'];
                $menuData['menuButton'] = '';
                Db::name('rolemenus')->data($menuData)->insert();
                $roleMenuId = Db::name('rolemenus')->getLastInsID();

                // 插入按钮
                foreach ($value['rolebuttonsList'] as $v) {
                    $buttonData = array();
                    $buttonData['id'] = SnowFlake::generateParticle();
                    $buttonData['roleMenuId'] = $roleMenuId;
                    $buttonData['buttonName'] = $v['buttonName'];
                    $buttonData['buttonType'] = $v['buttonType'];
                    $buttonData['roleId'] = $roleId;
                    $buttonData['menuId'] = $value['menuId'];
                    Db::name('rolebuttons')->data($buttonData)->insert();
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return $this->sendSuccess('ok');
    }


    public function delete()
    {
        $postData = $this->request->param();
        Db::startTrans();
        try {
            // 删除按钮
            Db::name('rolebuttons')->where("roleId=" . $postData['id'])->delete();
            // 删除菜单
            Db::name('rolemenus')->where("roleId=" . $postData['id'])->delete();
            // 删除角色
            Db::name('role')->where("id=" . $postData['id'])->delete();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return $this->sendSuccess('ok');
    }

    public function rolelist()
    {
        $roleList = Db::name("role")->order("id desc")->select();
        return $this->sendSuccess($this->sendData($roleList));
    }

}