<?php

namespace app\api\model;

use think\Db;
use think\Model;

class RoleModel extends Model
{
    public $tableName = 'role';

    /**
     * @param $map 分页条件
     * @param $pageSize
     * @param $currentPage
     * role 表明
     */
    public function getDataList($map, $currentPage, $pageSize)
    {
        // 所有角色列表
        $dataList = Db::table($this->tableName)->where($map)->limit($pageSize)->page($currentPage)->order('id desc')->select();
        // 获取角色的菜单以及按钮列表
        $roleMenuBtn = $this->getRoleMenus($dataList);
        // roleMenus
        // 拼接角色列表与菜单按钮列表

        // 第一层角色菜单
        foreach ($dataList as $key => $value) {
            $roleMenus = array();
            foreach ($roleMenuBtn as $k => $v) {
                if ($value['id'] == $v['roleId']) {
                    array_push($roleMenus, $v);
                }
            }
            $dataList[$key]['roleMenus'] = $roleMenus;
        }

        $total = Db::table($this->tableName)->where($map)->count();
        $pages = ceil($total / $pageSize);
        return resultFormat($dataList, $currentPage, $pages, $pageSize, $total);
    }


    /**
     * @param $list 获取角色菜单
     */
    public function getRoleMenus($list)
    {
        $ids = array();
        foreach ($list as $key => $value) {
            array_push($ids, $value['id']);
        }

        if (count($ids) != 0) {
            // 菜单
            $roleMenuList = Db::query('SELECT r.roleId, r.id, m.fid AS menuFid, m.id AS menuId, m.title AS menuTitle  FROM menu m LEFT JOIN rolemenus r on r.menuId=m.id WHERE r.roleId in (' . implode(',', $ids) . ')');
            // 按钮
            $roleMenuBtnList = Db::query('SELECT r.* FROM rolebuttons r WHERE roleId in (' . implode(',', $ids) . ')');
            foreach ($roleMenuList as $key => $value) {
                $rolebuttonsList = array();
                foreach ($roleMenuBtnList as $k => $v) {
                    if ($value['roleId'] == $v['roleId'] && $value['menuId'] == $v['menuId']) {
                        $arr = array(
                            'buttonName' => $v['buttonName'],
                            'buttonType' => $v['buttonType'],
                            'roleMenuId' => $v['menuId'],
                            'id' => $v['id']
                        );
                        array_push($rolebuttonsList, $arr);
                    }
                }
                $roleMenuList[$key]['rolebuttonsList'] = $rolebuttonsList;
            }
            return $roleMenuList;
        }
        return [];
    }

    /**
     * @param $data 插入或者修改数据
     */

    public function insertNews($data)
    {
        if (!empty($data['id'])) {
            // update
            return Db::table($this->tableName)->where('id=' . $data['id'])->data($data)->update();
        } else {
            return Db::table($this->tableName)->data($data)->insert();
        }
    }
}