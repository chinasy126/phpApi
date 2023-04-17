<?php


namespace app\api\controller\controllers;

use app\api\controller\Api;
use app\api\model\Menu;
use app\api\model\Menubutton;

use snowflake\SnowFlake;
use think\Db;
use think\Model;
use think\Request;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class MenuController extends Api
{
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
    protected $autoWriteTimestamp = true;
    private $postData;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->postData = $this->request->param();
    }

    /**
     *  获取菜单按钮列表
     */
    public function multiclassclassification()
    {
        $menuList = Db::table('menu')->order('menuOrder desc')->select();
        // 折叠菜单数据
        $menus = $this->collMenuData($menuList);
        return $this->sendSuccess($this->sendData($menus));
    }

    /**
     * 折叠菜单数据
     */
    public function collMenuData($menuyList)
    {
        $buttons = Db::table('menubutton')->select();

        $menus = array();
        foreach ($menuyList as $key => $value) {
            if ($value['fid'] == 0) {
                // 获取二级菜单
                $secMenus = array();
                foreach ($menuyList as $k => $v) {
                    if ($value['id'] == $v['fid']) {
                        $v['menubuttonList'] = $this->collButtonData($v, $buttons);
                        array_push($secMenus, $v);
                    }
                }
                $value['children'] = $secMenus;
                array_push($menus, $value);
            }
        }
        return $menus;
    }

    /**
     * @param $btn 菜单数据
     * @param $btnList 按钮列表
     * @return array 返回的按钮数据
     * 展开按钮
     */
    public function collButtonData($btn, $btnList)
    {
        $buttons = array();
        foreach ($btnList as $key => $value) {
            if ($btn['id'] == $value['menuId']) {
                array_push($buttons, $value);
            }
        }
        return $buttons;
    }

    /**
     * 列表
     */
    public function menulist()
    {
        $postData = $this->postData;
        $map = array();
        !empty($postData['title']) ? $map['title'] = ['like', "%" . $postData['title'] . "%"] : '';
        $map['fid'] = 0;
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $model = new Menu();
        $res = $model->getMenuList($map, $currentPage, $pageSize);

        // 查询二级菜单
        $secMenuList = $this->secMenu($res['data']['records']);
        // 获取按钮
        $buttonList = $this->buttonList($secMenuList);
        // 拼接二级菜单按钮
        $secMenuArray = $this->spliceMenuButton($secMenuList, $buttonList);
        // 拼接一级菜单二级菜单
        $res['data']['records'] = $this->spliceMenu($res['data']['records'], $secMenuArray);
        return $this->sendSuccess($res);
    }


    public function category()
    {
        $model = new Menu();
        $where['fid'] = 0;
        $menuList = $model->getAllMenu($where);
        return $this->sendSuccess($this->sendData($menuList));
    }


    public function getSecMenuList()
    {
        $postData = $this->postData;
        $where['fid'] = $postData['id'];
        $menuList = Db::name('menu')->where($where)->select();
        return $this->sendSuccess($this->sendData($menuList));
    }

    /**
     * @param $firMenuList
     * @param $secMenuList
     * @return array
     * 拼接按钮
     */
    public function spliceMenu($firMenuList, $secMenuList)
    {
        foreach ($firMenuList as $key => $item) {
            $senMenu = array();
            foreach ($secMenuList as $v) {
                if ($item['id'] == $v['fid']) {
                    array_push($senMenu, $v);
                }
            }
            $firMenuList[$key]['children'] = $senMenu;
        }
        return $firMenuList;
    }


    /**
     * @param $secMenuList 二级菜单列表
     * @param $buttonList 按钮列表
     * @return array  拼接的二级菜单以及按钮
     * 拼接二级菜单与按钮
     */
    public function spliceMenuButton($secMenuList, $buttonList)
    {
        foreach ($secMenuList as $key => $value) {
            $menuButtonList = array();
            foreach ($buttonList as $v) {
                if ($v['menuId'] == $value['id']) {
                    array_push($menuButtonList, $v);
                }
            }
            $secMenuList[$key]['menubuttonList'] = $menuButtonList;
        }
        return $secMenuList;
    }

    /**
     * 获取二级菜单
     * @param $res
     */
    public function secMenu($res)
    {
        $model = new Menu();
        $secMenuIds = array();
        foreach ($res as $value) {
            array_push($secMenuIds, $value['id']);
        }
        return $model->getSecMenuList($secMenuIds);
    }

    /**
     * 获取按钮列表
     */
    public function buttonList($secMenuList)
    {
        $model = new Menu();
        $buttonids = array();
        foreach ($secMenuList as $value) {
            array_push($buttonids, $value['id']);
        }
        return $model->getButtonList($buttonids);
    }


    public function insertBtn()
    {
        $postData = $this->postData;
        $model = new Menubutton();
        $data['id'] = SnowFlake::generateParticle();
        $data['menuId'] = $postData['menuId'];
        $data['type'] = $postData['type'];
        $data['name'] = $postData['name'];
        $model->data($data)->save();
        return $this->sendSuccess('ok');
    }

    public function deleteBtn()
    {
        $postData = $this->postData;
        $model = new Menubutton();
        $model->where("id=" . $postData['id'])->delete();
        return $this->sendSuccess('ok');
    }

    /**
     * 菜单新增或者修改
     */
    public function saveOrUpdate()
    {
        $menuModel = new Menu();
        $postData = $this->postData;
        $data['id'] = !empty($postData['id']) ? $postData['id'] : '';
        $data['title'] = $postData['title'];
        $data['name'] = $postData['name'];
        $data['menuOrder'] = $postData['menuOrder'];

        if (!empty($postData['parentMenu'])) {
            $menuName = explode(',', $postData['parentMenu'])[1];
            $res = $menuModel->where("name = '" . $menuName . "'")->find();
            $data['fid'] = !empty($res) ? $res['id'] : 0;
        } else {
            $data['fid'] = 0;
        }

        $isUpdate = false;
        if (!empty($postData['id'])) {
            $isUpdate = true;
        } else {
            $isUpdate = false;
        }
        $result = $menuModel->isUpdate($isUpdate)->data($data)->save();
        return $this->sendSuccess($result);
    }

    /**
     * 删除菜单
     */
    public function menudelete()
    {
        $postData = $this->postData;
        $menuModel = new Menu();
        $res = $menuModel->menudeleteBtndel($postData['id']);
        return $this->sendSuccess($res);
    }


    /**
     * 批量删除
     */

    public function batchdelete()
    {
        $postData = $this->postData;
        // 删除菜单 一级菜单
        $where['id'] = array('in', $postData['ids']);
        Db::name("menu")->where($where)->delete();
        // 删除一级菜单按钮
        $whereBtn["menuId"] = array('in', $postData['ids']);
        Db::name("menubutton")->where($whereBtn)->delete();
        // 删除二级菜单以及按钮
        $map['fid'] = array('in', $postData['ids']);
        $menuList = Db::table("menu")->where($map)->select();
        $menuIds = array();
        if (count($menuList) > 0) {
            foreach ($menuList as $value) {
                array_push($menuIds, $value["id"]);
            }
            $mapBtn["menuId"] = array('in', $menuIds);
            Db::name("menubutton")->where($mapBtn)->delete();
        }
        Db::table("menu")->where($map)->delete();
        return $this->sendSuccess('ok');
    }


    /**
     * 插入菜单按钮
     * @param $params 菜单以及按钮
     */
    public function insertMenu($fid, $params)
    {
        $data = array();
        $data["title"] = $params["title"];
        $data["name"] = $params["name"];
        $data["fid"] = $fid;
        $data["menuOrder"] = $params["meta"]["menuOrder"];
        $data["icon"] = $params["meta"]["icon"];
        Db::name("menu")->data($data)->insert();
        $menuId = Db::name('menu')->getLastInsID();
        // 插入按钮
        if (!empty($params["button"]) && count($params["button"]) > 0) {
            foreach ($params["button"] as $v) {
                $this->insertMenuBtn($menuId, $v);
            }
        }
        // 递归调用
        if (!empty($params["children"])) {
            foreach ($params["children"] as $value) {
                $this->insertMenu($menuId, $value);
            }
        }

    }

    /**
     * 插入按钮
     * @param $menuId
     * @param $param
     */
    public function insertMenuBtn($menuId, $param)
    {
        $btnModel = new Menubutton();
        $btnData["id"] = Snowflake::generateParticle();
        $btnData["menuId"] = $menuId;
        $btnData["type"] = $param["type"];
        $btnData["name"] = $param["title"];
        $btnModel->data($btnData)->save();
    }


    /**
     *  批量新增菜单
     */
    public function batchInsertMenu()
    {
        $postData = $this->postData;
        $dbMenuList = Db::name("menu")->select();
        Db::startTrans();
        try {
            // 1 先查看一级菜单是否存在 ，如果不存在则插入一级菜单二级菜单
            foreach ($postData as $value) {
                $isExist = false;
                foreach ($dbMenuList as $v) {
                    if ($value["name"] == $v["name"]) {
                        $isExist = true;
                    }
                }
                // 插入一级菜单 二级菜单 按钮

                if (!$isExist) {
                    $this->insertMenu(0, $value);
                } else {

                    // 比较二级菜单
                    if (!empty($value['children'])) {
                        $isSubExist = false;
                        foreach ($value['children'] as $v) {
                            foreach ($dbMenuList as $dbItem) {
                                if ($v["name"] == $dbItem["name"]) {
                                    $isSubExist = true;
                                }
                            }
                            if (!$isSubExist) {
                                $parentItem = Db::name("menu")->where("name='" . $value["name"] . "'")->find();
                                $this->insertMenu($parentItem["id"], $v);
                            }
                        }
                    }

                }
            }
            // 2 一级菜单存在 比较二级菜单

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
        }
        return $this->sendSuccess('ok');
    }

}