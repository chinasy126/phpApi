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
class User extends Api
{
    private static $tableName='users';
    public static $userInfo = array();

    public function __construct(Request $request = null)
    {
        self::$userInfo = $this->checkAuth();
        parent::__construct($request);
    }

    /**
     * 允许访问的方式列表，资源数组如果没有对应的方式列表，请不要把该方法写上，如user这个资源，客户端没有delete操作
     */
    public $restMethodList = 'get|post|put|delete';

    public function info()
    {
        $adminId = $this->checkAuth()["id"];
        $adminInfo = Db::table('users')->where('id = '.$adminId)->find();
        $roleId = $adminInfo['roleId'];
        $adminInfo['role'] = Db::table('s_role')->field('id,createTime,creatorId,deleted,describe,name,status,permissions')->where('id = '.$roleId)->find();
        $adminInfo['role']['permissions'] = json_decode($adminInfo['role']['permissions']);
        //return $this->sendSuccess($adminInfo);
        $this->returnmsg(200, '登录成功', $adminInfo);


        //  return json_encode($result);
        $this->returnmsg(200, '登录成功', $result);
        //  return 'info';
    }

    /**
     * @return bool
     */
    public function nav()
    {
        $userInfo = $this->checkAuth();
        $rows = Db::table('users')//表别名
        ->alias('u')//定义一个别名
        ->join('s_role r', 'u.roleId=r.id', 'left')//与agent表进行关联，取名a，并且a表的id字段等于m表的m_pid字段
        ->where('u.id', $userInfo["id"])//条件:状态为1
        ->field("menus")
            ->find();
        // 获取所有权限ID
        $menuIds = json_decode($rows['menus']);
        $map = array();
        $conditionId = implode(',',$menuIds);
        $map['id'] = array('in',$conditionId);
        $result = Db::table('menus')->order('id asc')->where($map)->select();
        foreach ($result as $key=>$value){
            $result[$key]['meta'] = json_decode($value['meta'],true);
        }
//        $routeHome = array(
//            "path"=>'/home',
//            "component"=>"Home",
//            "name"=>"home",
//            "meta"=>array(
//                "title"=>"首页",
//                "icon"=>"dashboard"
//            )
//        );
//        array_unshift($result,$routeHome);
        return $this->sendSuccess($result);
    }

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
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        // 分页信息
        $pageNo = $this->request->param('pageNo');
        $length = $this->request->param('pageSize');
        $offset = ($pageNo-1) * $length;

        $map = array();
//        if(!empty($this->request->param('searchTitle'))){
//            $map['title'] = array('like', "%{$this->request->param('searchTitle')}%", 'or');
//        }

        $list = Db::table(self::$tableName)->where($map)->limit($offset,$length)->select();
        $result['data'] = $list;
        $result['pageSize'] = $length;
        $result['totalCount'] = Db::table(self::$tableName)->where($map)->count();
        $result['pageNo'] =  intval($pageNo);

        return $this->sendSuccess($result);
    }


}
