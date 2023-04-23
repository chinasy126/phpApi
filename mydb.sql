/*
Navicat MySQL Data Transfer

Source Server         : 本地数据库
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : mydb

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2023-04-23 17:00:35
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `menu`
-- ----------------------------
DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '菜单名称',
  `name` varchar(255) DEFAULT NULL COMMENT '菜单name',
  `fid` int(11) DEFAULT '0' COMMENT '是否二级菜单，父级菜单ID',
  `button` varchar(255) DEFAULT NULL,
  `menuOrder` int(11) DEFAULT '0',
  `icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menu
-- ----------------------------
INSERT INTO `menu` VALUES ('1', '系统管理', 'permissions', '0', null, '1', 'el-icon-s-help');
INSERT INTO `menu` VALUES ('2', '角色管理', 'role', '1', null, '0', 'form');
INSERT INTO `menu` VALUES ('3', '用户管理', 'user', '1', null, '0', 'form');
INSERT INTO `menu` VALUES ('4', '菜单管理', 'menu', '1', null, '0', 'form');
INSERT INTO `menu` VALUES ('5', '新闻', 'news', '0', null, '0', 'el-icon-s-help');
INSERT INTO `menu` VALUES ('6', '新闻组件', 'newsindex', '5', null, '1', 'form');
INSERT INTO `menu` VALUES ('7', '产品', 'product', '0', null, '0', 'el-icon-s-help');
INSERT INTO `menu` VALUES ('9', '产品管理', 'productindex', '7', null, '0', 'form');
INSERT INTO `menu` VALUES ('8', '产品分类', 'productclass', '7', null, '0', 'form');

-- ----------------------------
-- Table structure for `menubutton`
-- ----------------------------
DROP TABLE IF EXISTS `menubutton`;
CREATE TABLE `menubutton` (
  `id` bigint(20) NOT NULL,
  `menuId` int(11) DEFAULT NULL,
  `createTime` date DEFAULT NULL,
  `updateTime` date DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT '按钮名称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of menubutton
-- ----------------------------
INSERT INTO `menubutton` VALUES ('5333429219769646', '2', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219776528', '2', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219781571', '2', '2023-04-22', '2023-04-22', 'add', '新增');
INSERT INTO `menubutton` VALUES ('5333429219779741', '3', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219783621', '3', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219783159', '3', '2023-04-22', '2023-04-22', 'batchDelete', '批量删除');
INSERT INTO `menubutton` VALUES ('5333429219783797', '3', '2023-04-22', '2023-04-22', 'add', '新增');
INSERT INTO `menubutton` VALUES ('5333429219789186', '4', '2023-04-22', '2023-04-22', 'batchSync', '批量同步');
INSERT INTO `menubutton` VALUES ('5333429219792508', '4', '2023-04-22', '2023-04-22', 'batchDelete', '批量删除');
INSERT INTO `menubutton` VALUES ('5333429219792708', '4', '2023-04-22', '2023-04-22', 'addButton', '新增按钮');
INSERT INTO `menubutton` VALUES ('5333429219794383', '4', '2023-04-22', '2023-04-22', 'addMenu', '新增菜单');
INSERT INTO `menubutton` VALUES ('5333429219793454', '4', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219795789', '4', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219802696', '6', '2023-04-22', '2023-04-22', 'batchDeletion', '批量删除');
INSERT INTO `menubutton` VALUES ('5333429219802435', '6', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219803060', '6', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219803827', '6', '2023-04-22', '2023-04-22', 'export', '导出');
INSERT INTO `menubutton` VALUES ('5333429219803880', '6', '2023-04-22', '2023-04-22', 'import', '导入');
INSERT INTO `menubutton` VALUES ('5333429219805982', '6', '2023-04-22', '2023-04-22', 'add', '添加');
INSERT INTO `menubutton` VALUES ('5333429219809356', '8', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219807724', '8', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219811333', '8', '2023-04-22', '2023-04-22', 'add', '添加');
INSERT INTO `menubutton` VALUES ('5333429219813403', '9', '2023-04-22', '2023-04-22', 'delete', '删除');
INSERT INTO `menubutton` VALUES ('5333429219815955', '9', '2023-04-22', '2023-04-22', 'edit', '编辑');
INSERT INTO `menubutton` VALUES ('5333429219818378', '9', '2023-04-22', '2023-04-22', 'add', '添加');

-- ----------------------------
-- Table structure for `news`
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `fTitle` varchar(400) DEFAULT NULL COMMENT '副标题',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `s_pic` varchar(70) DEFAULT NULL COMMENT '缩略图',
  `contents` text COMMENT '内容',
  `update` date DEFAULT NULL COMMENT '时间',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '点击数',
  `top` int(11) NOT NULL DEFAULT '0' COMMENT '推荐值',
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `webtitle` varchar(80) DEFAULT NULL COMMENT '网页标题',
  `webkey` varchar(160) DEFAULT NULL COMMENT '网页关键词',
  `webdes` varchar(200) DEFAULT NULL COMMENT '网页描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of news
-- ----------------------------
INSERT INTO `news` VALUES ('1', '第一个新闻', '副标题', '/public/uploads/20230423/c04fb45a37aef2f8fdfdd23ae17ba4cd.png', null, null, '2023-04-23', '1', '1', null, null, null, null);

-- ----------------------------
-- Table structure for `product`
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `contents` text,
  `num` int(11) NOT NULL DEFAULT '0',
  `top` int(11) NOT NULL DEFAULT '0',
  `update` int(11) DEFAULT NULL,
  `author` varchar(30) DEFAULT NULL,
  `webtitle` varchar(80) DEFAULT NULL COMMENT '网页标题',
  `webkey` varchar(160) DEFAULT NULL COMMENT '网页关键词',
  `webdes` varchar(200) DEFAULT NULL COMMENT '网页描述',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of product
-- ----------------------------
INSERT INTO `product` VALUES ('1', '1', '产品1', null, '/public/uploads/20230423/d82b34298290bab4e779d932af71c272.png', '<p><img src=\"/public/uploads/20230423/d5ef8ec0e3b1d9ddd5ef894e32fc7955.jpg\"></p>', '1', '1', null, null, null, null, null);

-- ----------------------------
-- Table structure for `productclass`
-- ----------------------------
DROP TABLE IF EXISTS `productclass`;
CREATE TABLE `productclass` (
  `classid` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号|0|0|0',
  `classname` varchar(50) NOT NULL COMMENT '分类名称|1|0|1',
  `classpower` varchar(100) NOT NULL COMMENT '分类编码|0|0|0',
  `depth` int(11) NOT NULL COMMENT '分类深度|0|0|0',
  `rootid` int(11) NOT NULL COMMENT '分类根ID|0|0|0',
  `classcontents` varchar(500) DEFAULT NULL,
  `classpic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`classid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='产品分类';

-- ----------------------------
-- Records of productclass
-- ----------------------------
INSERT INTO `productclass` VALUES ('1', '第一个分类', '0008', '4', '0', '分类内容', '/public/uploads/20230423/8ee22f3675b79df2fba509d6db564ac1.jpg');

-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) DEFAULT NULL COMMENT '角色名称',
  `roleDesc` varchar(255) DEFAULT NULL COMMENT '角色描述',
  `roleMenus` text COMMENT '角色权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '系统管理员', '管理员应该拥有所有权限', '');
INSERT INTO `role` VALUES ('2', '内容管理员', '管理日常工作', '');

-- ----------------------------
-- Table structure for `rolebuttons`
-- ----------------------------
DROP TABLE IF EXISTS `rolebuttons`;
CREATE TABLE `rolebuttons` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `roleMenuId` bigint(20) DEFAULT NULL COMMENT '关联ID，角色属于菜单ID',
  `buttonName` varchar(255) DEFAULT NULL COMMENT '按钮中文名称',
  `buttonType` varchar(255) DEFAULT NULL COMMENT '按钮英文类型便于匹配是否有权限',
  `roleId` int(11) DEFAULT NULL,
  `menuId` int(11) NOT NULL COMMENT '菜单真实ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5333436650163730 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rolebuttons
-- ----------------------------
INSERT INTO `rolebuttons` VALUES ('5333436650138025', '2', '批量删除', 'batchDeletion', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650142525', '2', '删除', 'delete', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650142484', '2', '编辑', 'edit', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650141626', '2', '导出', 'export', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650146561', '2', '导入', 'import', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650145999', '2', '添加', 'add', '1', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650149389', '4', '删除', 'delete', '1', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650150654', '4', '编辑', 'edit', '1', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650153299', '4', '添加', 'add', '1', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650152255', '5', '删除', 'delete', '1', '9');
INSERT INTO `rolebuttons` VALUES ('5333436650152826', '5', '编辑', 'edit', '1', '9');
INSERT INTO `rolebuttons` VALUES ('5333436650159803', '5', '添加', 'add', '1', '9');
INSERT INTO `rolebuttons` VALUES ('5333436650159735', '7', '删除', 'delete', '1', '2');
INSERT INTO `rolebuttons` VALUES ('5333436650156657', '7', '编辑', 'edit', '1', '2');
INSERT INTO `rolebuttons` VALUES ('5333436650156747', '7', '新增', 'add', '1', '2');
INSERT INTO `rolebuttons` VALUES ('5333436650156625', '8', '编辑', 'edit', '1', '3');
INSERT INTO `rolebuttons` VALUES ('5333436650159633', '8', '删除', 'delete', '1', '3');
INSERT INTO `rolebuttons` VALUES ('5333436650157682', '8', '批量删除', 'batchDelete', '1', '3');
INSERT INTO `rolebuttons` VALUES ('5333436650157345', '8', '新增', 'add', '1', '3');
INSERT INTO `rolebuttons` VALUES ('5333436650162801', '9', '批量同步', 'batchSync', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650162647', '9', '批量删除', 'batchDelete', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650163716', '9', '新增按钮', 'addButton', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650162308', '9', '新增菜单', 'addMenu', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650163124', '9', '删除', 'delete', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650162512', '9', '编辑', 'edit', '1', '4');
INSERT INTO `rolebuttons` VALUES ('5333436650163718', '13', '批量删除', 'batchDeletion', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163719', '13', '删除', 'delete', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163720', '13', '编辑', 'edit', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163721', '13', '导出', 'export', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163722', '13', '导入', 'import', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163723', '13', '添加', 'add', '2', '6');
INSERT INTO `rolebuttons` VALUES ('5333436650163724', '15', '删除', 'delete', '2', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650163725', '15', '编辑', 'edit', '2', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650163726', '15', '添加', 'add', '2', '8');
INSERT INTO `rolebuttons` VALUES ('5333436650163727', '16', '删除', 'delete', '2', '9');
INSERT INTO `rolebuttons` VALUES ('5333436650163728', '16', '编辑', 'edit', '2', '9');
INSERT INTO `rolebuttons` VALUES ('5333436650163729', '16', '添加', 'add', '2', '9');

-- ----------------------------
-- Table structure for `rolemenus`
-- ----------------------------
DROP TABLE IF EXISTS `rolemenus`;
CREATE TABLE `rolemenus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleId` int(11) DEFAULT '0' COMMENT '角色ID',
  `menuId` int(11) DEFAULT NULL COMMENT '菜单ID',
  `menuTitle` varchar(255) DEFAULT NULL COMMENT '菜单名称',
  `menuButton` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of rolemenus
-- ----------------------------
INSERT INTO `rolemenus` VALUES ('1', '1', '5', '新闻', '');
INSERT INTO `rolemenus` VALUES ('2', '1', '6', '新闻组件', '');
INSERT INTO `rolemenus` VALUES ('3', '1', '7', '产品', '');
INSERT INTO `rolemenus` VALUES ('4', '1', '8', '产品管理', '');
INSERT INTO `rolemenus` VALUES ('5', '1', '9', '产品分类', '');
INSERT INTO `rolemenus` VALUES ('6', '1', '1', '系统管理', '');
INSERT INTO `rolemenus` VALUES ('7', '1', '2', '角色管理', '');
INSERT INTO `rolemenus` VALUES ('8', '1', '3', '用户管理', '');
INSERT INTO `rolemenus` VALUES ('9', '1', '4', '菜单管理', '');
INSERT INTO `rolemenus` VALUES ('13', '2', '6', '新闻组件', '');
INSERT INTO `rolemenus` VALUES ('12', '2', '5', '新闻', '');
INSERT INTO `rolemenus` VALUES ('14', '2', '7', '产品', '');
INSERT INTO `rolemenus` VALUES ('15', '2', '8', '产品管理', '');
INSERT INTO `rolemenus` VALUES ('16', '2', '9', '产品分类', '');

-- ----------------------------
-- Table structure for `t_user`
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码，加密存储',
  `birthday` date DEFAULT NULL,
  `roleId` int(11) DEFAULT '0',
  `createTime` date DEFAULT NULL,
  `updateTime` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of t_user
-- ----------------------------
INSERT INTO `t_user` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', '2023-04-22', '1', null, '2023-04-23', 'public/avatar/202304/1c685a5c-2068-4f42-a4e8-a49a028aeb0e.jpg');
