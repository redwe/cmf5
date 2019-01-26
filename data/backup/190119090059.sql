/*
MySQL Backup
Source Server Version: 5.5.40
Source Database: cmf5
Date: 2019/1/19 ������ 09:00:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
--  Table structure for `cmf_admin_menu`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_admin_menu`;
CREATE TABLE `cmf_admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '菜单类型;1:有界面可访问菜单,2:无界面可访问菜单,0:只作为菜单',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态;1:显示,0:不显示',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `app` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '应用名',
  `controller` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '控制器名',
  `action` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '操作名称',
  `param` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '额外参数',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '菜单图标',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parent_id` (`parent_id`),
  KEY `controller` (`controller`)
) ENGINE=InnoDB AUTO_INCREMENT=164 DEFAULT CHARSET=utf8mb4 COMMENT='后台菜单表';

-- ----------------------------
--  Table structure for `cmf_asset`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_asset`;
CREATE TABLE `cmf_asset` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小,单位B',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:可用,0:不可用',
  `download_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `file_key` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件惟一码',
  `filename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `file_path` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件路径,相对于upload目录,可以为url',
  `file_md5` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件md5值',
  `file_sha1` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `suffix` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀名,不包括点',
  `more` text COMMENT '其它详细信息,JSON格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资源表';

-- ----------------------------
--  Table structure for `cmf_auth_access`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_access`;
CREATE TABLE `cmf_auth_access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类,请加应用前缀,如admin_',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限授权表';

-- ----------------------------
--  Table structure for `cmf_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_rule`;
CREATE TABLE `cmf_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `app` varchar(40) NOT NULL DEFAULT '' COMMENT '规则所属app',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(100) NOT NULL DEFAULT '' COMMENT '额外url参数',
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则描述',
  `condition` varchar(200) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `module` (`app`,`status`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COMMENT='权限规则表';

-- ----------------------------
--  Table structure for `cmf_comment`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_comment`;
CREATE TABLE `cmf_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '被回复的评论id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论内容 id',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `dislike_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '不喜欢数',
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '楼层数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:已审核,0:未审核',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '评论内容所在表，不带表前缀',
  `full_name` varchar(50) NOT NULL DEFAULT '' COMMENT '评论者昵称',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '评论者邮箱',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '层级关系',
  `url` text COMMENT '原文地址',
  `content` text CHARACTER SET utf8mb4 COMMENT '评论内容',
  `more` text CHARACTER SET utf8mb4 COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `table_id_status` (`table_name`,`object_id`,`status`),
  KEY `object_id` (`object_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
--  Table structure for `cmf_hook`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_hook`;
CREATE TABLE `cmf_hook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '钩子类型(1:系统钩子;2:应用钩子;3:模板钩子;4:后台模板钩子)',
  `once` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只允许一个插件运行(0:多个;1:一个)',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子',
  `app` varchar(15) NOT NULL DEFAULT '' COMMENT '应用名(只有应用钩子才用)',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子表';

-- ----------------------------
--  Table structure for `cmf_hook_plugin`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_hook_plugin`;
CREATE TABLE `cmf_hook_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子插件表';

-- ----------------------------
--  Table structure for `cmf_link`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_link`;
CREATE TABLE `cmf_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:不显示',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接地址',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '友情链接名称',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '友情链接图标',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `rel` varchar(50) NOT NULL DEFAULT '' COMMENT '链接与网站的关系',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='友情链接表';

-- ----------------------------
--  Table structure for `cmf_members`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_members`;
CREATE TABLE `cmf_members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户类型;1:admin;2:会员',
  `sex` tinyint(2) NOT NULL DEFAULT '0' COMMENT '性别;0:保密,1:男,2:女',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态;0:禁用,1:正常,2:未验证',
  `user_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码;cmf_password加密',
  `user_nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网址',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `signature` varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '中国手机不带国家代码，国际手机号格式为：国家代码-手机号',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `user_login` (`user_login`),
  KEY `user_nickname` (`user_nickname`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
--  Table structure for `cmf_nav`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_nav`;
CREATE TABLE `cmf_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_main` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否为主导航;1:是;0:不是',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '导航位置名称',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='前台导航位置表';

-- ----------------------------
--  Table structure for `cmf_nav_menu`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_nav_menu`;
CREATE TABLE `cmf_nav_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nav_id` int(11) NOT NULL COMMENT '导航 id',
  `parent_id` int(11) NOT NULL COMMENT '父 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '打开方式',
  `href` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `icon` varchar(20) NOT NULL DEFAULT '' COMMENT '图标',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '层级关系',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='前台导航菜单表';

-- ----------------------------
--  Table structure for `cmf_option`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_option`;
CREATE TABLE `cmf_option` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `autoload` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否自动加载;1:自动加载;0:不自动加载',
  `option_name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置名',
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '配置值',
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='全站配置表';

-- ----------------------------
--  Table structure for `cmf_plugin`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_plugin`;
CREATE TABLE `cmf_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '插件类型;1:网站;8:微信',
  `has_admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理,0:没有;1:有',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:开启;0:禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `hooks` varchar(255) NOT NULL DEFAULT '' COMMENT '实现的钩子;以“,”分隔',
  `author` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `description` varchar(255) NOT NULL COMMENT '插件描述',
  `config` text COMMENT '插件配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='插件表';

-- ----------------------------
--  Table structure for `cmf_portal_category`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_category`;
CREATE TABLE `cmf_portal_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类文章数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `year` int(11) DEFAULT NULL,
  `teacher` varchar(20) DEFAULT NULL,
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '分类描述',
  `path` varchar(100) NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `seo_title` varchar(50) NOT NULL DEFAULT '',
  `seo_keywords` varchar(50) NOT NULL DEFAULT '',
  `seo_description` varchar(100) NOT NULL DEFAULT '',
  `list_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类列表模板',
  `one_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类文章页模板',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=459 DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章分类表';

-- ----------------------------
--  Table structure for `cmf_portal_category_post`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_category_post`;
CREATE TABLE `cmf_portal_category_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章id',
  `category_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `term_taxonomy_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='portal应用 分类文章对应表';

-- ----------------------------
--  Table structure for `cmf_portal_post`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_post`;
CREATE TABLE `cmf_portal_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `post_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型,1:文章;2:页面',
  `post_format` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '内容格式;1:html;2:md',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '发表者用户id',
  `post_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:已发布;0:未发布;',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论状态;1:允许;0:不允许',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶;1:置顶;0:不置顶',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_hits` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `post_favorites` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `post_like` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `comment_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `published_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `post_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'post标题',
  `post_keywords` varchar(150) NOT NULL DEFAULT '' COMMENT 'seo keywords',
  `post_excerpt` varchar(255) NOT NULL DEFAULT '' COMMENT 'post摘要',
  `post_source` varchar(150) NOT NULL DEFAULT '' COMMENT '转载文章的来源',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `post_content` text COMMENT '文章内容',
  `post_content_filtered` text COMMENT '处理过的文章内容',
  `more` text COMMENT '扩展属性,如缩略图;格式为json',
  `market_price` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `year_price1` decimal(10,2) DEFAULT NULL,
  `year_price2` decimal(10,2) DEFAULT NULL,
  `year_price3` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type_status_date` (`post_type`,`post_status`,`create_time`,`id`),
  KEY `parent_id` (`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='portal应用 文章表';

-- ----------------------------
--  Table structure for `cmf_portal_tag`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_tag`;
CREATE TABLE `cmf_portal_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签文章数',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章标签表';

-- ----------------------------
--  Table structure for `cmf_portal_tag_post`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_tag_post`;
CREATE TABLE `cmf_portal_tag_post` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签 id',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='portal应用 标签文章对应表';

-- ----------------------------
--  Table structure for `cmf_post_lession`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_post_lession`;
CREATE TABLE `cmf_post_lession` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) DEFAULT NULL,
  `capter_id` int(11) DEFAULT NULL,
  `lession_name` varchar(100) DEFAULT NULL,
  `lession_url` varchar(255) DEFAULT NULL,
  `status` tinyint(2) DEFAULT NULL,
  `free` tinyint(2) DEFAULT NULL,
  `viewedNum` int(11) DEFAULT NULL,
  `created_time` int(11) DEFAULT NULL,
  `content` text,
  `typeid` int(11) DEFAULT NULL,
  `typestr` varchar(100) DEFAULT NULL,
  `mediaid` varchar(100) DEFAULT NULL,
  `timelength` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
--  Table structure for `cmf_recycle_bin`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_recycle_bin`;
CREATE TABLE `cmf_recycle_bin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0' COMMENT '删除内容 id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `table_name` varchar(60) DEFAULT '' COMMENT '删除内容所在表名',
  `name` varchar(255) DEFAULT '' COMMENT '删除内容名称',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=' 回收站';

-- ----------------------------
--  Table structure for `cmf_role`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_role`;
CREATE TABLE `cmf_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父角色ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态;0:禁用;1:正常',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `list_order` float NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
--  Table structure for `cmf_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_role_user`;
CREATE TABLE `cmf_role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色 id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色对应表';

-- ----------------------------
--  Table structure for `cmf_route`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_route`;
CREATE TABLE `cmf_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态;1:启用,0:不启用',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'URL规则类型;1:用户自定义;2:别名添加',
  `full_url` varchar(255) NOT NULL DEFAULT '' COMMENT '完整url',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '实际显示的url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COMMENT='url路由表';

-- ----------------------------
--  Table structure for `cmf_slide`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_slide`;
CREATE TABLE `cmf_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示,0不显示',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片分类',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分类备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片表';

-- ----------------------------
--  Table structure for `cmf_slide_item`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_slide_item`;
CREATE TABLE `cmf_slide_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slide_id` int(11) NOT NULL DEFAULT '0' COMMENT '幻灯片id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '幻灯片名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片图片',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片链接',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `description` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '幻灯片描述',
  `content` text CHARACTER SET utf8 COMMENT '幻灯片内容',
  `more` text COMMENT '扩展信息',
  PRIMARY KEY (`id`),
  KEY `slide_id` (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片子项表';

-- ----------------------------
--  Table structure for `cmf_theme`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_theme`;
CREATE TABLE `cmf_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后升级时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模板状态,1:正在使用;0:未使用',
  `is_compiled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为已编译模板',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '主题目录名，用于主题的维一标识',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '主题名称',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '主题版本号',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '主题作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `lang` varchar(10) NOT NULL DEFAULT '' COMMENT '支持语言',
  `keywords` varchar(50) NOT NULL DEFAULT '' COMMENT '主题关键字',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '主题描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `cmf_theme_file`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_theme_file`;
CREATE TABLE `cmf_theme_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公共的模板文件',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '模板名称',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模板文件名',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
  `file` varchar(50) NOT NULL DEFAULT '' COMMENT '模板文件，相对于模板根目录，如Portal/index.html',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '模板文件描述',
  `more` text COMMENT '模板更多配置,用户自己后台设置的',
  `config_more` text COMMENT '模板更多配置,来源模板的配置文件',
  `draft_more` text COMMENT '模板更多配置,用户临时保存的配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `cmf_third_party_user`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_third_party_user`;
CREATE TABLE `cmf_third_party_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '本站用户id',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'access_token过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:正常;0:禁用',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `third_party` varchar(20) NOT NULL DEFAULT '' COMMENT '第三方惟一码',
  `app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方应用 id',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `access_token` varchar(512) NOT NULL DEFAULT '' COMMENT '第三方授权码',
  `openid` varchar(40) NOT NULL DEFAULT '' COMMENT '第三方用户id',
  `union_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方用户多个产品中的惟一 id,(如:微信平台)',
  `more` text COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方用户表';

-- ----------------------------
--  Table structure for `cmf_user`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user`;
CREATE TABLE `cmf_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户类型;1:admin;2:会员',
  `sex` tinyint(2) NOT NULL DEFAULT '0' COMMENT '性别;0:保密,1:男,2:女',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态;0:禁用,1:正常,2:未验证',
  `user_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码;cmf_password加密',
  `user_nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网址',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `signature` varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '中国手机不带国家代码，国际手机号格式为：国家代码-手机号',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `user_login` (`user_login`),
  KEY `user_nickname` (`user_nickname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
--  Table structure for `cmf_user_action`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_action`;
CREATE TABLE `cmf_user_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  `reward_number` int(11) NOT NULL DEFAULT '0' COMMENT '奖励次数',
  `cycle_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '周期类型;0:不限;1:按天;2:按小时;3:永久',
  `cycle_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '周期时间值',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `app` varchar(50) NOT NULL DEFAULT '' COMMENT '操作所在应用名或插件名等',
  `url` text COMMENT '执行操作的url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户操作表';

-- ----------------------------
--  Table structure for `cmf_user_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_action_log`;
CREATE TABLE `cmf_user_action_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问次数',
  `last_visit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `object` varchar(100) NOT NULL DEFAULT '' COMMENT '访问对象的id,格式:不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作名称;格式:应用名+控制器+操作名,也可自己定义格式只要不发生冲突且惟一;',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user_id`,`object`,`action`),
  KEY `user_object_action_ip` (`user_id`,`object`,`action`,`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访问记录表';

-- ----------------------------
--  Table structure for `cmf_user_balance_log`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_balance_log`;
CREATE TABLE `cmf_user_balance_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `change` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '更改余额',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '更改后余额',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户余额变更日志表';

-- ----------------------------
--  Table structure for `cmf_user_favorite`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_favorite`;
CREATE TABLE `cmf_user_favorite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '收藏内容的标题',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `url` varchar(255) DEFAULT NULL COMMENT '收藏内容的原文地址，JSON格式',
  `description` text COMMENT '收藏内容的描述',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '收藏实体以前所在表,不带前缀',
  `object_id` int(10) unsigned DEFAULT '0' COMMENT '收藏内容原来的主键id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户收藏表';

-- ----------------------------
--  Table structure for `cmf_user_like`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_like`;
CREATE TABLE `cmf_user_like` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容原来的主键id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '内容以前所在表,不带前缀',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '内容的原文地址，不带域名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '内容的标题',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `description` text COMMENT '内容的描述',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户点赞表';

-- ----------------------------
--  Table structure for `cmf_user_login_attempt`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_login_attempt`;
CREATE TABLE `cmf_user_login_attempt` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login_attempts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试次数',
  `attempt_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试登录时间',
  `locked_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '锁定时间',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户 ip',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '用户账号,手机号,邮箱或用户名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户登录尝试表';

-- ----------------------------
--  Table structure for `cmf_user_score_log`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_score_log`;
CREATE TABLE `cmf_user_score_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户操作积分等奖励日志表';

-- ----------------------------
--  Table structure for `cmf_user_token`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_token`;
CREATE TABLE `cmf_user_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `token` varchar(64) NOT NULL DEFAULT '' COMMENT 'token',
  `device_type` varchar(10) NOT NULL DEFAULT '' COMMENT '设备类型;mobile,android,iphone,ipad,web,pc,mac,wxapp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='用户客户端登录 token 表';

-- ----------------------------
--  Table structure for `cmf_verification_code`
-- ----------------------------
DROP TABLE IF EXISTS `cmf_verification_code`;
CREATE TABLE `cmf_verification_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当天已经发送成功的次数',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后发送成功时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证码过期时间',
  `code` varchar(8) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '最后发送成功的验证码',
  `account` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号或者邮箱',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='手机邮箱数字验证码表';

-- ----------------------------
--  Records 
-- ----------------------------
INSERT INTO `cmf_admin_menu` VALUES ('6','0','0','1','0','admin','Setting','default','','设置','cogs','系统设置入口'), ('7','6','1','1','50','admin','Link','index','','友情链接','','友情链接管理'), ('8','7','1','0','10000','admin','Link','add','','添加友情链接','','添加友情链接'), ('9','7','2','0','10000','admin','Link','addPost','','添加友情链接提交保存','','添加友情链接提交保存'), ('10','7','1','0','10000','admin','Link','edit','','编辑友情链接','','编辑友情链接'), ('11','7','2','0','10000','admin','Link','editPost','','编辑友情链接提交保存','','编辑友情链接提交保存'), ('12','7','2','0','10000','admin','Link','delete','','删除友情链接','','删除友情链接'), ('13','7','2','0','10000','admin','Link','listOrder','','友情链接排序','','友情链接排序'), ('14','7','2','0','10000','admin','Link','toggle','','友情链接显示隐藏','','友情链接显示隐藏'), ('15','6','1','1','10','admin','Mailer','index','','邮箱配置','','邮箱配置'), ('16','15','2','0','10000','admin','Mailer','indexPost','','邮箱配置提交保存','','邮箱配置提交保存'), ('17','15','1','0','10000','admin','Mailer','template','','邮件模板','','邮件模板'), ('19','15','1','0','10000','admin','Mailer','test','','邮件发送测试','','邮件发送测试'), ('20','6','1','0','10000','admin','Menu','index','','后台菜单','','后台菜单管理'), ('21','20','1','0','10000','admin','Menu','lists','','所有菜单','','后台所有菜单列表'), ('22','20','1','0','10000','admin','Menu','add','','后台菜单添加','','后台菜单添加'), ('23','20','2','0','10000','admin','Menu','addPost','','后台菜单添加提交保存','','后台菜单添加提交保存'), ('24','20','1','0','10000','admin','Menu','edit','','后台菜单编辑','','后台菜单编辑'), ('25','20','2','0','10000','admin','Menu','editPost','','后台菜单编辑提交保存','','后台菜单编辑提交保存'), ('26','20','2','0','10000','admin','Menu','delete','','后台菜单删除','','后台菜单删除'), ('27','20','2','0','10000','admin','Menu','listOrder','','后台菜单排序','','后台菜单排序'), ('28','20','1','0','10000','admin','Menu','getActions','','导入新后台菜单','','导入新后台菜单'), ('29','6','1','1','30','admin','Nav','index','','导航管理','','导航管理'), ('30','29','1','0','10000','admin','Nav','add','','添加导航','','添加导航'), ('31','29','2','0','10000','admin','Nav','addPost','','添加导航提交保存','','添加导航提交保存'), ('32','29','1','0','10000','admin','Nav','edit','','编辑导航','','编辑导航'), ('33','29','2','0','10000','admin','Nav','editPost','','编辑导航提交保存','','编辑导航提交保存'), ('34','29','2','0','10000','admin','Nav','delete','','删除导航','','删除导航'), ('35','29','1','0','10000','admin','NavMenu','index','','导航菜单','','导航菜单'), ('36','35','1','0','10000','admin','NavMenu','add','','添加导航菜单','','添加导航菜单'), ('37','35','2','0','10000','admin','NavMenu','addPost','','添加导航菜单提交保存','','添加导航菜单提交保存'), ('38','35','1','0','10000','admin','NavMenu','edit','','编辑导航菜单','','编辑导航菜单'), ('39','35','2','0','10000','admin','NavMenu','editPost','','编辑导航菜单提交保存','','编辑导航菜单提交保存'), ('40','35','2','0','10000','admin','NavMenu','delete','','删除导航菜单','','删除导航菜单'), ('41','35','2','0','10000','admin','NavMenu','listOrder','','导航菜单排序','','导航菜单排序'), ('49','110','0','1','10000','admin','User','default','','管理组','','管理组'), ('50','49','1','1','10000','admin','Rbac','index','','角色管理','','角色管理'), ('51','50','1','0','10000','admin','Rbac','roleAdd','','添加角色','','添加角色'), ('52','50','2','0','10000','admin','Rbac','roleAddPost','','添加角色提交','','添加角色提交'), ('53','50','1','0','10000','admin','Rbac','roleEdit','','编辑角色','','编辑角色'), ('54','50','2','0','10000','admin','Rbac','roleEditPost','','编辑角色提交','','编辑角色提交'), ('55','50','2','0','10000','admin','Rbac','roleDelete','','删除角色','','删除角色'), ('56','50','1','0','10000','admin','Rbac','authorize','','设置角色权限','','设置角色权限'), ('57','50','2','0','10000','admin','Rbac','authorizePost','','角色授权提交','','角色授权提交'), ('58','0','1','1','20000','admin','RecycleBin','index','','回收站','trash-o','回收站'), ('59','58','2','0','10000','admin','RecycleBin','restore','','回收站还原','','回收站还原'), ('60','58','2','0','10000','admin','RecycleBin','delete','','回收站彻底删除','','回收站彻底删除'), ('61','6','1','1','10000','admin','Route','index','','URL美化','','URL规则管理'), ('62','61','1','0','10000','admin','Route','add','','添加路由规则','','添加路由规则'), ('63','61','2','0','10000','admin','Route','addPost','','添加路由规则提交','','添加路由规则提交'), ('64','61','1','0','10000','admin','Route','edit','','路由规则编辑','','路由规则编辑'), ('65','61','2','0','10000','admin','Route','editPost','','路由规则编辑提交','','路由规则编辑提交'), ('66','61','2','0','10000','admin','Route','delete','','路由规则删除','','路由规则删除'), ('67','61','2','0','10000','admin','Route','ban','','路由规则禁用','','路由规则禁用'), ('68','61','2','0','10000','admin','Route','open','','路由规则启用','','路由规则启用'), ('69','61','2','0','10000','admin','Route','listOrder','','路由规则排序','','路由规则排序'), ('70','61','1','0','10000','admin','Route','select','','选择URL','','选择URL'), ('71','6','1','1','0','admin','Setting','site','','网站信息','','网站信息'), ('72','71','2','0','10000','admin','Setting','sitePost','','网站信息设置提交','','网站信息设置提交'), ('73','6','1','0','10000','admin','Setting','password','','密码修改','','密码修改'), ('74','73','2','0','10000','admin','Setting','passwordPost','','密码修改提交','','密码修改提交'), ('75','6','1','1','10000','admin','Setting','upload','','上传设置','','上传设置'), ('76','75','2','0','10000','admin','Setting','uploadPost','','上传设置提交','','上传设置提交'), ('77','6','1','0','10000','admin','Setting','clearCache','','清除缓存','','清除缓存'), ('78','6','1','1','40','admin','Slide','index','','幻灯片管理','','幻灯片管理'), ('79','78','1','0','10000','admin','Slide','add','','添加幻灯片','','添加幻灯片'), ('80','78','2','0','10000','admin','Slide','addPost','','添加幻灯片提交','','添加幻灯片提交'), ('81','78','1','0','10000','admin','Slide','edit','','编辑幻灯片','','编辑幻灯片'), ('82','78','2','0','10000','admin','Slide','editPost','','编辑幻灯片提交','','编辑幻灯片提交'), ('83','78','2','0','10000','admin','Slide','delete','','删除幻灯片','','删除幻灯片'), ('84','78','1','0','10000','admin','SlideItem','index','','幻灯片页面列表','','幻灯片页面列表'), ('85','84','1','0','10000','admin','SlideItem','add','','幻灯片页面添加','','幻灯片页面添加'), ('86','84','2','0','10000','admin','SlideItem','addPost','','幻灯片页面添加提交','','幻灯片页面添加提交'), ('87','84','1','0','10000','admin','SlideItem','edit','','幻灯片页面编辑','','幻灯片页面编辑'), ('88','84','2','0','10000','admin','SlideItem','editPost','','幻灯片页面编辑提交','','幻灯片页面编辑提交'), ('89','84','2','0','10000','admin','SlideItem','delete','','幻灯片页面删除','','幻灯片页面删除'), ('90','84','2','0','10000','admin','SlideItem','ban','','幻灯片页面隐藏','','幻灯片页面隐藏'), ('91','84','2','0','10000','admin','SlideItem','cancelBan','','幻灯片页面显示','','幻灯片页面显示'), ('92','84','2','0','10000','admin','SlideItem','listOrder','','幻灯片页面排序','','幻灯片页面排序'), ('93','6','1','1','10000','admin','Storage','index','','文件存储','','文件存储'), ('94','93','2','0','10000','admin','Storage','settingPost','','文件存储设置提交','','文件存储设置提交'), ('110','0','0','1','10','user','AdminIndex','default','','用户管理','group','用户管理'), ('111','49','1','1','10000','admin','User','index','','管理员','','管理员管理'), ('112','111','1','0','10000','admin','User','add','','管理员添加','','管理员添加'), ('113','111','2','0','10000','admin','User','addPost','','管理员添加提交','','管理员添加提交'), ('114','111','1','0','10000','admin','User','edit','','管理员编辑','','管理员编辑'), ('115','111','2','0','10000','admin','User','editPost','','管理员编辑提交','','管理员编辑提交'), ('116','111','1','0','10000','admin','User','userInfo','','个人信息','','管理员个人信息修改'), ('117','111','2','0','10000','admin','User','userInfoPost','','管理员个人信息修改提交','','管理员个人信息修改提交'), ('118','111','2','0','10000','admin','User','delete','','管理员删除','','管理员删除'), ('119','111','2','0','10000','admin','User','ban','','停用管理员','','停用管理员'), ('120','111','2','0','10000','admin','User','cancelBan','','启用管理员','','启用管理员'), ('121','0','0','1','30','portal','AdminIndex','default','','门户管理','th','门户管理'), ('122','0','1','1','10000','portal','AdminArticle','index','','课程管理','','课程列表'), ('123','122','1','0','10000','portal','AdminArticle','add','','添加文章','','添加文章'), ('124','122','2','0','10000','portal','AdminArticle','addPost','','添加文章提交','','添加文章提交'), ('125','122','1','0','10000','portal','AdminArticle','edit','','编辑文章','','编辑文章'), ('126','122','2','0','10000','portal','AdminArticle','editPost','','编辑文章提交','','编辑文章提交'), ('127','122','2','0','10000','portal','AdminArticle','delete','','文章删除','','文章删除'), ('128','122','2','0','10000','portal','AdminArticle','publish','','文章发布','','文章发布');
INSERT INTO `cmf_admin_menu` VALUES ('129','122','2','0','10000','portal','AdminArticle','top','','文章置顶','','文章置顶'), ('130','122','2','0','10000','portal','AdminArticle','recommend','','文章推荐','','文章推荐'), ('131','122','2','0','10000','portal','AdminArticle','listOrder','','文章排序','','文章排序'), ('132','0','1','1','10000','portal','AdminCategory','index','','课程分类','list-ul','文章分类列表'), ('133','132','1','0','10000','portal','AdminCategory','add','','添加文章分类','','添加文章分类'), ('134','132','2','0','10000','portal','AdminCategory','addPost','','添加文章分类提交','','添加文章分类提交'), ('135','132','1','0','10000','portal','AdminCategory','edit','','编辑文章分类','','编辑文章分类'), ('136','132','2','0','10000','portal','AdminCategory','editPost','','编辑文章分类提交','','编辑文章分类提交'), ('137','132','1','0','10000','portal','AdminCategory','select','','文章分类选择对话框','','文章分类选择对话框'), ('138','132','2','0','10000','portal','AdminCategory','listOrder','','文章分类排序','','文章分类排序'), ('139','132','2','0','10000','portal','AdminCategory','delete','','删除文章分类','','删除文章分类'), ('140','121','1','1','10000','portal','AdminPage','index','','页面管理','','页面管理'), ('141','140','1','0','10000','portal','AdminPage','add','','添加页面','','添加页面'), ('142','140','2','0','10000','portal','AdminPage','addPost','','添加页面提交','','添加页面提交'), ('143','140','1','0','10000','portal','AdminPage','edit','','编辑页面','','编辑页面'), ('144','140','2','0','10000','portal','AdminPage','editPost','','编辑页面提交','','编辑页面提交'), ('145','140','2','0','10000','portal','AdminPage','delete','','删除页面','','删除页面'), ('146','121','1','1','10000','portal','AdminTag','index','','文章标签','','文章标签'), ('147','146','1','0','10000','portal','AdminTag','add','','添加文章标签','','添加文章标签'), ('148','146','2','0','10000','portal','AdminTag','addPost','','添加文章标签提交','','添加文章标签提交'), ('149','146','2','0','10000','portal','AdminTag','upStatus','','更新标签状态','','更新标签状态'), ('150','146','2','0','10000','portal','AdminTag','delete','','删除文章标签','','删除文章标签'), ('151','0','1','0','10000','user','AdminAsset','index','','资源管理','file','资源管理列表'), ('152','151','2','0','10000','user','AdminAsset','delete','','删除文件','','删除文件'), ('153','0','0','1','10000','user','AdminIndex','default1','','会员管理','user','用户组'), ('154','153','1','1','10000','user','AdminIndex','index','','本站用户','','本站用户'), ('155','154','2','0','10000','user','AdminIndex','ban','','本站用户拉黑','','本站用户拉黑'), ('156','154','2','0','10000','user','AdminIndex','cancelBan','','本站用户启用','','本站用户启用'), ('157','153','1','1','10000','user','AdminOauth','index','','第三方用户','','第三方用户'), ('158','157','2','0','10000','user','AdminOauth','delete','','删除第三方用户绑定','','删除第三方用户绑定'), ('159','6','1','1','10000','user','AdminUserAction','index','','用户操作管理','','用户操作管理'), ('160','159','1','0','10000','user','AdminUserAction','edit','','编辑用户操作','','编辑用户操作'), ('161','159','2','0','10000','user','AdminUserAction','editPost','','编辑用户操作提交','','编辑用户操作提交'), ('162','159','1','0','10000','user','AdminUserAction','sync','','同步用户操作','','同步用户操作'), ('163','0','1','1','10000','admin','Plugin','index','','插件管理','cube','');
INSERT INTO `cmf_auth_rule` VALUES ('1','1','admin','admin_url','admin/Hook/index','','钩子管理',''), ('2','1','admin','admin_url','admin/Hook/plugins','','钩子插件管理',''), ('3','1','admin','admin_url','admin/Hook/pluginListOrder','','钩子插件排序',''), ('4','1','admin','admin_url','admin/Hook/sync','','同步钩子',''), ('5','1','admin','admin_url','admin/Link/index','','友情链接',''), ('6','1','admin','admin_url','admin/Link/add','','添加友情链接',''), ('7','1','admin','admin_url','admin/Link/addPost','','添加友情链接提交保存',''), ('8','1','admin','admin_url','admin/Link/edit','','编辑友情链接',''), ('9','1','admin','admin_url','admin/Link/editPost','','编辑友情链接提交保存',''), ('10','1','admin','admin_url','admin/Link/delete','','删除友情链接',''), ('11','1','admin','admin_url','admin/Link/listOrder','','友情链接排序',''), ('12','1','admin','admin_url','admin/Link/toggle','','友情链接显示隐藏',''), ('13','1','admin','admin_url','admin/Mailer/index','','邮箱配置',''), ('14','1','admin','admin_url','admin/Mailer/indexPost','','邮箱配置提交保存',''), ('15','1','admin','admin_url','admin/Mailer/template','','邮件模板',''), ('16','1','admin','admin_url','admin/Mailer/templatePost','','邮件模板提交',''), ('17','1','admin','admin_url','admin/Mailer/test','','邮件发送测试',''), ('18','1','admin','admin_url','admin/Menu/index','','后台菜单',''), ('19','1','admin','admin_url','admin/Menu/lists','','所有菜单',''), ('20','1','admin','admin_url','admin/Menu/add','','后台菜单添加',''), ('21','1','admin','admin_url','admin/Menu/addPost','','后台菜单添加提交保存',''), ('22','1','admin','admin_url','admin/Menu/edit','','后台菜单编辑',''), ('23','1','admin','admin_url','admin/Menu/editPost','','后台菜单编辑提交保存',''), ('24','1','admin','admin_url','admin/Menu/delete','','后台菜单删除',''), ('25','1','admin','admin_url','admin/Menu/listOrder','','后台菜单排序',''), ('26','1','admin','admin_url','admin/Menu/getActions','','导入新后台菜单',''), ('27','1','admin','admin_url','admin/Nav/index','','导航管理',''), ('28','1','admin','admin_url','admin/Nav/add','','添加导航',''), ('29','1','admin','admin_url','admin/Nav/addPost','','添加导航提交保存',''), ('30','1','admin','admin_url','admin/Nav/edit','','编辑导航',''), ('31','1','admin','admin_url','admin/Nav/editPost','','编辑导航提交保存',''), ('32','1','admin','admin_url','admin/Nav/delete','','删除导航',''), ('33','1','admin','admin_url','admin/NavMenu/index','','导航菜单',''), ('34','1','admin','admin_url','admin/NavMenu/add','','添加导航菜单',''), ('35','1','admin','admin_url','admin/NavMenu/addPost','','添加导航菜单提交保存',''), ('36','1','admin','admin_url','admin/NavMenu/edit','','编辑导航菜单',''), ('37','1','admin','admin_url','admin/NavMenu/editPost','','编辑导航菜单提交保存',''), ('38','1','admin','admin_url','admin/NavMenu/delete','','删除导航菜单',''), ('39','1','admin','admin_url','admin/NavMenu/listOrder','','导航菜单排序',''), ('40','1','admin','admin_url','admin/Plugin/default','','插件中心',''), ('41','1','admin','admin_url','admin/Plugin/index','','插件管理',''), ('42','1','admin','admin_url','admin/Plugin/toggle','','插件启用禁用',''), ('43','1','admin','admin_url','admin/Plugin/setting','','插件设置',''), ('44','1','admin','admin_url','admin/Plugin/settingPost','','插件设置提交',''), ('45','1','admin','admin_url','admin/Plugin/install','','插件安装',''), ('46','1','admin','admin_url','admin/Plugin/update','','插件更新',''), ('47','1','admin','admin_url','admin/Plugin/uninstall','','卸载插件',''), ('48','1','admin','admin_url','admin/Rbac/index','','角色管理',''), ('49','1','admin','admin_url','admin/Rbac/roleAdd','','添加角色',''), ('50','1','admin','admin_url','admin/Rbac/roleAddPost','','添加角色提交',''), ('51','1','admin','admin_url','admin/Rbac/roleEdit','','编辑角色',''), ('52','1','admin','admin_url','admin/Rbac/roleEditPost','','编辑角色提交',''), ('53','1','admin','admin_url','admin/Rbac/roleDelete','','删除角色',''), ('54','1','admin','admin_url','admin/Rbac/authorize','','设置角色权限',''), ('55','1','admin','admin_url','admin/Rbac/authorizePost','','角色授权提交',''), ('56','1','admin','admin_url','admin/RecycleBin/index','','回收站',''), ('57','1','admin','admin_url','admin/RecycleBin/restore','','回收站还原',''), ('58','1','admin','admin_url','admin/RecycleBin/delete','','回收站彻底删除',''), ('59','1','admin','admin_url','admin/Route/index','','URL美化',''), ('60','1','admin','admin_url','admin/Route/add','','添加路由规则',''), ('61','1','admin','admin_url','admin/Route/addPost','','添加路由规则提交',''), ('62','1','admin','admin_url','admin/Route/edit','','路由规则编辑',''), ('63','1','admin','admin_url','admin/Route/editPost','','路由规则编辑提交',''), ('64','1','admin','admin_url','admin/Route/delete','','路由规则删除',''), ('65','1','admin','admin_url','admin/Route/ban','','路由规则禁用',''), ('66','1','admin','admin_url','admin/Route/open','','路由规则启用',''), ('67','1','admin','admin_url','admin/Route/listOrder','','路由规则排序',''), ('68','1','admin','admin_url','admin/Route/select','','选择URL',''), ('69','1','admin','admin_url','admin/Setting/default','','设置',''), ('70','1','admin','admin_url','admin/Setting/site','','网站信息',''), ('71','1','admin','admin_url','admin/Setting/sitePost','','网站信息设置提交',''), ('72','1','admin','admin_url','admin/Setting/password','','密码修改',''), ('73','1','admin','admin_url','admin/Setting/passwordPost','','密码修改提交',''), ('74','1','admin','admin_url','admin/Setting/upload','','上传设置',''), ('75','1','admin','admin_url','admin/Setting/uploadPost','','上传设置提交',''), ('76','1','admin','admin_url','admin/Setting/clearCache','','清除缓存',''), ('77','1','admin','admin_url','admin/Slide/index','','幻灯片管理',''), ('78','1','admin','admin_url','admin/Slide/add','','添加幻灯片',''), ('79','1','admin','admin_url','admin/Slide/addPost','','添加幻灯片提交',''), ('80','1','admin','admin_url','admin/Slide/edit','','编辑幻灯片',''), ('81','1','admin','admin_url','admin/Slide/editPost','','编辑幻灯片提交',''), ('82','1','admin','admin_url','admin/Slide/delete','','删除幻灯片',''), ('83','1','admin','admin_url','admin/SlideItem/index','','幻灯片页面列表',''), ('84','1','admin','admin_url','admin/SlideItem/add','','幻灯片页面添加',''), ('85','1','admin','admin_url','admin/SlideItem/addPost','','幻灯片页面添加提交',''), ('86','1','admin','admin_url','admin/SlideItem/edit','','幻灯片页面编辑',''), ('87','1','admin','admin_url','admin/SlideItem/editPost','','幻灯片页面编辑提交',''), ('88','1','admin','admin_url','admin/SlideItem/delete','','幻灯片页面删除',''), ('89','1','admin','admin_url','admin/SlideItem/ban','','幻灯片页面隐藏',''), ('90','1','admin','admin_url','admin/SlideItem/cancelBan','','幻灯片页面显示',''), ('91','1','admin','admin_url','admin/SlideItem/listOrder','','幻灯片页面排序',''), ('92','1','admin','admin_url','admin/Storage/index','','文件存储',''), ('93','1','admin','admin_url','admin/Storage/settingPost','','文件存储设置提交',''), ('94','1','admin','admin_url','admin/Theme/index','','模板管理',''), ('95','1','admin','admin_url','admin/Theme/install','','安装模板',''), ('96','1','admin','admin_url','admin/Theme/uninstall','','卸载模板',''), ('97','1','admin','admin_url','admin/Theme/installTheme','','模板安装',''), ('98','1','admin','admin_url','admin/Theme/update','','模板更新',''), ('99','1','admin','admin_url','admin/Theme/active','','启用模板',''), ('100','1','admin','admin_url','admin/Theme/files','','模板文件列表','');
INSERT INTO `cmf_auth_rule` VALUES ('101','1','admin','admin_url','admin/Theme/fileSetting','','模板文件设置',''), ('102','1','admin','admin_url','admin/Theme/fileArrayData','','模板文件数组数据列表',''), ('103','1','admin','admin_url','admin/Theme/fileArrayDataEdit','','模板文件数组数据添加编辑',''), ('104','1','admin','admin_url','admin/Theme/fileArrayDataEditPost','','模板文件数组数据添加编辑提交保存',''), ('105','1','admin','admin_url','admin/Theme/fileArrayDataDelete','','模板文件数组数据删除',''), ('106','1','admin','admin_url','admin/Theme/settingPost','','模板文件编辑提交保存',''), ('107','1','admin','admin_url','admin/Theme/dataSource','','模板文件设置数据源',''), ('108','1','admin','admin_url','admin/Theme/design','','模板设计',''), ('109','1','admin','admin_url','admin/User/default','','管理组',''), ('110','1','admin','admin_url','admin/User/index','','管理员',''), ('111','1','admin','admin_url','admin/User/add','','管理员添加',''), ('112','1','admin','admin_url','admin/User/addPost','','管理员添加提交',''), ('113','1','admin','admin_url','admin/User/edit','','管理员编辑',''), ('114','1','admin','admin_url','admin/User/editPost','','管理员编辑提交',''), ('115','1','admin','admin_url','admin/User/userInfo','','个人信息',''), ('116','1','admin','admin_url','admin/User/userInfoPost','','管理员个人信息修改提交',''), ('117','1','admin','admin_url','admin/User/delete','','管理员删除',''), ('118','1','admin','admin_url','admin/User/ban','','停用管理员',''), ('119','1','admin','admin_url','admin/User/cancelBan','','启用管理员',''), ('120','1','portal','admin_url','portal/AdminArticle/index','','课程管理',''), ('121','1','portal','admin_url','portal/AdminArticle/add','','添加文章',''), ('122','1','portal','admin_url','portal/AdminArticle/addPost','','添加文章提交',''), ('123','1','portal','admin_url','portal/AdminArticle/edit','','编辑文章',''), ('124','1','portal','admin_url','portal/AdminArticle/editPost','','编辑文章提交',''), ('125','1','portal','admin_url','portal/AdminArticle/delete','','文章删除',''), ('126','1','portal','admin_url','portal/AdminArticle/publish','','文章发布',''), ('127','1','portal','admin_url','portal/AdminArticle/top','','文章置顶',''), ('128','1','portal','admin_url','portal/AdminArticle/recommend','','文章推荐',''), ('129','1','portal','admin_url','portal/AdminArticle/listOrder','','文章排序',''), ('130','1','portal','admin_url','portal/AdminCategory/index','','课程分类',''), ('131','1','portal','admin_url','portal/AdminCategory/add','','添加文章分类',''), ('132','1','portal','admin_url','portal/AdminCategory/addPost','','添加文章分类提交',''), ('133','1','portal','admin_url','portal/AdminCategory/edit','','编辑文章分类',''), ('134','1','portal','admin_url','portal/AdminCategory/editPost','','编辑文章分类提交',''), ('135','1','portal','admin_url','portal/AdminCategory/select','','文章分类选择对话框',''), ('136','1','portal','admin_url','portal/AdminCategory/listOrder','','文章分类排序',''), ('137','1','portal','admin_url','portal/AdminCategory/delete','','删除文章分类',''), ('138','1','portal','admin_url','portal/AdminIndex/default','','门户管理',''), ('139','1','portal','admin_url','portal/AdminPage/index','','页面管理',''), ('140','1','portal','admin_url','portal/AdminPage/add','','添加页面',''), ('141','1','portal','admin_url','portal/AdminPage/addPost','','添加页面提交',''), ('142','1','portal','admin_url','portal/AdminPage/edit','','编辑页面',''), ('143','1','portal','admin_url','portal/AdminPage/editPost','','编辑页面提交',''), ('144','1','portal','admin_url','portal/AdminPage/delete','','删除页面',''), ('145','1','portal','admin_url','portal/AdminTag/index','','文章标签',''), ('146','1','portal','admin_url','portal/AdminTag/add','','添加文章标签',''), ('147','1','portal','admin_url','portal/AdminTag/addPost','','添加文章标签提交',''), ('148','1','portal','admin_url','portal/AdminTag/upStatus','','更新标签状态',''), ('149','1','portal','admin_url','portal/AdminTag/delete','','删除文章标签',''), ('150','1','user','admin_url','user/AdminAsset/index','','资源管理',''), ('151','1','user','admin_url','user/AdminAsset/delete','','删除文件',''), ('152','1','user','admin_url','user/AdminIndex/default','','用户管理',''), ('153','1','user','admin_url','user/AdminIndex/default1','','会员管理',''), ('154','1','user','admin_url','user/AdminIndex/index','','本站用户',''), ('155','1','user','admin_url','user/AdminIndex/ban','','本站用户拉黑',''), ('156','1','user','admin_url','user/AdminIndex/cancelBan','','本站用户启用',''), ('157','1','user','admin_url','user/AdminOauth/index','','第三方用户',''), ('158','1','user','admin_url','user/AdminOauth/delete','','删除第三方用户绑定',''), ('159','1','user','admin_url','user/AdminUserAction/index','','用户操作管理',''), ('160','1','user','admin_url','user/AdminUserAction/edit','','编辑用户操作',''), ('161','1','user','admin_url','user/AdminUserAction/editPost','','编辑用户操作提交',''), ('162','1','user','admin_url','user/AdminUserAction/sync','','同步用户操作','');
INSERT INTO `cmf_hook` VALUES ('1','1','0','应用初始化','app_init','cmf','应用初始化'), ('2','1','0','应用开始','app_begin','cmf','应用开始'), ('3','1','0','模块初始化','module_init','cmf','模块初始化'), ('4','1','0','控制器开始','action_begin','cmf','控制器开始'), ('5','1','0','视图输出过滤','view_filter','cmf','视图输出过滤'), ('6','1','0','应用结束','app_end','cmf','应用结束'), ('7','1','0','日志write方法','log_write','cmf','日志write方法'), ('8','1','0','输出结束','response_end','cmf','输出结束'), ('9','1','0','后台控制器初始化','admin_init','cmf','后台控制器初始化'), ('10','1','0','前台控制器初始化','home_init','cmf','前台控制器初始化'), ('11','1','1','发送手机验证码','send_mobile_verification_code','cmf','发送手机验证码'), ('12','3','0','模板 body标签开始','body_start','','模板 body标签开始'), ('13','3','0','模板 head标签结束前','before_head_end','','模板 head标签结束前'), ('14','3','0','模板底部开始','footer_start','','模板底部开始'), ('15','3','0','模板底部开始之前','before_footer','','模板底部开始之前'), ('16','3','0','模板底部结束之前','before_footer_end','','模板底部结束之前'), ('17','3','0','模板 body 标签结束之前','before_body_end','','模板 body 标签结束之前'), ('18','3','0','模板左边栏开始','left_sidebar_start','','模板左边栏开始'), ('19','3','0','模板左边栏结束之前','before_left_sidebar_end','','模板左边栏结束之前'), ('20','3','0','模板右边栏开始','right_sidebar_start','','模板右边栏开始'), ('21','3','0','模板右边栏结束之前','before_right_sidebar_end','','模板右边栏结束之前'), ('22','3','1','评论区','comment','','评论区'), ('23','3','1','留言区','guestbook','','留言区'), ('24','2','0','后台首页仪表盘','admin_dashboard','admin','后台首页仪表盘'), ('25','4','0','后台模板 head标签结束前','admin_before_head_end','','后台模板 head标签结束前'), ('26','4','0','后台模板 body 标签结束之前','admin_before_body_end','','后台模板 body 标签结束之前'), ('27','2','0','后台登录页面','admin_login','admin','后台登录页面'), ('28','1','1','前台模板切换','switch_theme','cmf','前台模板切换'), ('29','3','0','主要内容之后','after_content','','主要内容之后'), ('30','2','0','文章显示之前','portal_before_assign_article','portal','文章显示之前'), ('31','2','0','后台文章保存之后','portal_admin_after_save_article','portal','后台文章保存之后'), ('32','2','1','获取上传界面','fetch_upload_view','user','获取上传界面'), ('33','3','0','主要内容之前','before_content','cmf','主要内容之前'), ('34','1','0','日志写入完成','log_write_done','cmf','日志写入完成'), ('35','1','1','后台模板切换','switch_admin_theme','cmf','后台模板切换'), ('36','1','1','验证码图片','captcha_image','cmf','验证码图片'), ('37','2','1','后台模板设计界面','admin_theme_design_view','admin','后台模板设计界面'), ('38','2','1','后台设置网站信息界面','admin_setting_site_view','admin','后台设置网站信息界面'), ('39','2','1','后台清除缓存界面','admin_setting_clear_cache_view','admin','后台清除缓存界面'), ('40','2','1','后台导航管理界面','admin_nav_index_view','admin','后台导航管理界面'), ('41','2','1','后台友情链接管理界面','admin_link_index_view','admin','后台友情链接管理界面'), ('42','2','1','后台幻灯片管理界面','admin_slide_index_view','admin','后台幻灯片管理界面'), ('43','2','1','后台管理员列表界面','admin_user_index_view','admin','后台管理员列表界面'), ('44','2','1','后台角色管理界面','admin_rbac_index_view','admin','后台角色管理界面'), ('45','2','1','门户后台文章管理列表界面','portal_admin_article_index_view','portal','门户后台文章管理列表界面'), ('46','2','1','门户后台文章分类管理列表界面','portal_admin_category_index_view','portal','门户后台文章分类管理列表界面'), ('47','2','1','门户后台页面管理列表界面','portal_admin_page_index_view','portal','门户后台页面管理列表界面'), ('48','2','1','门户后台文章标签管理列表界面','portal_admin_tag_index_view','portal','门户后台文章标签管理列表界面'), ('49','2','1','用户管理本站用户列表界面','user_admin_index_view','user','用户管理本站用户列表界面'), ('50','2','1','资源管理列表界面','user_admin_asset_index_view','user','资源管理列表界面'), ('51','2','1','用户管理第三方用户列表界面','user_admin_oauth_index_view','user','用户管理第三方用户列表界面'), ('52','2','1','后台首页界面','admin_index_index_view','admin','后台首页界面'), ('53','2','1','后台回收站界面','admin_recycle_bin_index_view','admin','后台回收站界面'), ('54','2','1','后台菜单管理界面','admin_menu_index_view','admin','后台菜单管理界面'), ('55','2','1','后台自定义登录是否开启钩子','admin_custom_login_open','admin','后台自定义登录是否开启钩子'), ('56','4','0','门户后台文章添加编辑界面右侧栏','portal_admin_article_edit_view_right_sidebar','portal','门户后台文章添加编辑界面右侧栏'), ('57','4','0','门户后台文章添加编辑界面主要内容','portal_admin_article_edit_view_main','portal','门户后台文章添加编辑界面主要内容'), ('58','2','1','门户后台文章添加界面','portal_admin_article_add_view','portal','门户后台文章添加界面'), ('59','2','1','门户后台文章编辑界面','portal_admin_article_edit_view','portal','门户后台文章编辑界面'), ('60','2','1','门户后台文章分类添加界面','portal_admin_category_add_view','portal','门户后台文章分类添加界面'), ('61','2','1','门户后台文章分类编辑界面','portal_admin_category_edit_view','portal','门户后台文章分类编辑界面'), ('62','2','1','门户后台页面添加界面','portal_admin_page_add_view','portal','门户后台页面添加界面'), ('63','2','1','门户后台页面编辑界面','portal_admin_page_edit_view','portal','门户后台页面编辑界面'), ('64','2','1','后台幻灯片页面列表界面','admin_slide_item_index_view','admin','后台幻灯片页面列表界面'), ('65','2','1','后台幻灯片页面添加界面','admin_slide_item_add_view','admin','后台幻灯片页面添加界面'), ('66','2','1','后台幻灯片页面编辑界面','admin_slide_item_edit_view','admin','后台幻灯片页面编辑界面'), ('67','2','1','后台管理员添加界面','admin_user_add_view','admin','后台管理员添加界面'), ('68','2','1','后台管理员编辑界面','admin_user_edit_view','admin','后台管理员编辑界面'), ('69','2','1','后台角色添加界面','admin_rbac_role_add_view','admin','后台角色添加界面'), ('70','2','1','后台角色编辑界面','admin_rbac_role_edit_view','admin','后台角色编辑界面'), ('71','2','1','后台角色授权界面','admin_rbac_authorize_view','admin','后台角色授权界面');
INSERT INTO `cmf_hook_plugin` VALUES ('1','10000','1','send_mobile_verification_code','MobileCodeDemo');
INSERT INTO `cmf_link` VALUES ('1','1','1','8','thinkcmf官网','http://www.thinkcmf.com','ThinkCMF','','_blank','');
INSERT INTO `cmf_members` VALUES ('1','1','0','0','1547518884','0','0','0.00','1547518825','1','admin','###04720e4ea18f5dd7dbcfedd638e4120e','admin','18364120@qq.com','','','','127.0.0.1','','',NULL);
INSERT INTO `cmf_nav` VALUES ('1','1','主导航','主导航'), ('2','0','底部导航','');
INSERT INTO `cmf_nav_menu` VALUES ('1','1','0','1','0','首页','','home','','0-1');
INSERT INTO `cmf_option` VALUES ('1','1','site_info','{\"site_name\":\"\\u5965\\u521b\\u767e\\u79d1\",\"site_seo_title\":\"\\u5965\\u521b\\u767e\\u79d1\",\"site_seo_keywords\":\"\\u5965\\u521b\\u767e\\u79d1\",\"site_seo_description\":\"\\u5965\\u521b\\u767e\\u79d1\"}');
INSERT INTO `cmf_plugin` VALUES ('1','1','0','1','0','MobileCodeDemo','手机验证码演示插件','','','ThinkCMF','','1.0','手机验证码演示插件','{\"account_sid\":\"\",\"auth_token\":\"\",\"app_id\":\"\",\"template_id\":\"\",\"expire_minute\":\"30\"}');
INSERT INTO `cmf_portal_category` VALUES ('1','0','0','1','0','10000','监理工程师',NULL,NULL,'','0-1','','','','list','article','{\"thumbnail\":\"\"}'), ('2','0','0','1','0','10000','安全工程师',NULL,NULL,'','0-2','','','','list','article','{\"thumbnail\":\"\"}'), ('3','0','0','1','0','10000','2019 BIM',NULL,NULL,'','0-3','','','','list','article','{\"thumbnail\":\"\"}'), ('4','0','0','1','0','10000','二级造价师',NULL,NULL,'','0-4','','','','list','article','{\"thumbnail\":\"\"}'), ('5','0','0','1','0','10000','一级造价师',NULL,NULL,'','0-5','','','','list','article','{\"thumbnail\":\"\"}'), ('6','1','0','1','0','10000','案例分析',NULL,NULL,'','0-1-6','','','','list','article','{\"thumbnail\":\"\"}'), ('7','1','0','1','0','10000','进度控制',NULL,NULL,'','0-1-7','','','','list','article','{\"thumbnail\":\"\"}'), ('8','2','0','1','0','10000','安全法规',NULL,NULL,'','0-2-8','','','','list','article','{\"thumbnail\":\"\"}'), ('9','31','0','1','0','10000','案例分析',NULL,NULL,'','0-31-9','','','','list','article','{\"thumbnail\":\"\"}'), ('10','32','0','1','0','10000','水运材料',NULL,NULL,'','0-32-10','','','','list','article','{\"thumbnail\":\"\"}'), ('11','33','0','1','0','10000','建筑学',NULL,NULL,'','0-33-11','','','','list','article','{\"thumbnail\":\"\"}'), ('12','3','0','1','0','10000','工程管理',NULL,NULL,'','0-3-12','','','','list','article','{\"thumbnail\":\"\"}'), ('13','3','0','1','0','10000','BIM应用工程师《建筑》',NULL,NULL,'','0-3-13','','','','list','article','{\"thumbnail\":\"\"}'), ('14','3','0','1','0','10000','BIM应用工程师《结构》',NULL,NULL,'','0-3-14','','','','list','article','{\"thumbnail\":\"\"}'), ('15','4','0','1','0','10000','科目',NULL,NULL,'','0-4-15','','','','list','article','{\"thumbnail\":\"\"}'), ('16','34','0','1','0','10000','公路',NULL,NULL,'','0-34-16','','','','list','article','{\"thumbnail\":\"\"}'), ('17','35','0','1','0','10000','通讯与广电',NULL,NULL,'','0-35-17','','','','list','article','{\"thumbnail\":\"\"}'), ('18','35','0','1','0','10000','水利',NULL,NULL,'','0-35-18','','','','list','article','{\"thumbnail\":\"\"}'), ('19','34','0','1','0','10000','水利',NULL,NULL,'','0-34-19','','','','list','article','{\"thumbnail\":\"\"}'), ('20','33','0','1','0','10000','造价专业',NULL,NULL,'','0-33-20','','','','list','article','{\"thumbnail\":\"\"}'), ('21','1','0','1','0','10000','管理',NULL,NULL,'','0-1-21','','','','list','article','{\"thumbnail\":\"\"}'), ('22','1','0','1','0','10000','法规',NULL,NULL,'','0-1-22','','','','list','article','{\"thumbnail\":\"\"}'), ('23','5','0','1','0','10000','造价管理',NULL,NULL,'','0-5-23','','','','list','article','{\"thumbnail\":\"\"}'), ('24','5','0','1','0','10000','造价案例分析',NULL,NULL,'','0-5-24','','','','list','article','{\"thumbnail\":\"\"}'), ('25','5','0','1','0','10000','建设工程计价',NULL,NULL,'','0-5-25','','','','list','article','{\"thumbnail\":\"\"}'), ('26','33','0','1','0','10000','风景园林',NULL,NULL,'','0-33-26','','','','list','article','{\"thumbnail\":\"\"}'), ('27','5','0','1','0','10000','技术与计量（土建）',NULL,NULL,'','0-5-27','','','','list','article','{\"thumbnail\":\"\"}'), ('28','32','0','1','0','10000','交通工程',NULL,NULL,'','0-32-28','','','','list','article','{\"thumbnail\":\"\"}'), ('29','30','0','1','0','10000','BIM应用工程师《机电》',NULL,NULL,'','0-30-29','','','','list','article','{\"thumbnail\":\"\"}'), ('30','0','0','1','0','10000','BIM',NULL,NULL,'','0-30','','','','list','article','{\"thumbnail\":\"\"}'), ('31','0','0','1','0','10000','一级消防工程师',NULL,NULL,'','0-31','','','','list','article','{\"thumbnail\":\"\"}'), ('32','0','0','1','0','10000','公路水运检测师',NULL,NULL,'','0-32','','','','list','article','{\"thumbnail\":\"\"}'), ('33','0','0','1','0','10000','湖南中级工程师',NULL,NULL,'','0-33','','','','list','article','{\"thumbnail\":\"\"}'), ('34','0','0','1','0','10000','二级建造师',NULL,NULL,'','0-34','','','','list','article','{\"thumbnail\":\"\"}'), ('35','0','0','1','0','10000','一级建造师',NULL,NULL,'','0-35','','','','list','article','{\"thumbnail\":\"\"}'), ('36','5','0','1','0','10000','技术与计量（安装）',NULL,NULL,'','0-5-36','','','','list','article','{\"thumbnail\":\"\"}'), ('37','30','0','1','0','10000','BIM建模工程师',NULL,NULL,'','0-30-37','','','','list','article','{\"thumbnail\":\"\"}'), ('38','31','0','1','0','10000','综合能力',NULL,NULL,'','0-31-38','','','','list','article','{\"thumbnail\":\"\"}'), ('39','31','0','1','0','10000','技术实务',NULL,NULL,'','0-31-39','','','','list','article','{\"thumbnail\":\"\"}'), ('40','32','0','1','0','10000','地基结构',NULL,NULL,'','0-32-40','','','','list','article','{\"thumbnail\":\"\"}'), ('41','32','0','1','0','10000','桥梁与隧道',NULL,NULL,'','0-32-41','','','','list','article','{\"thumbnail\":\"\"}'), ('42','32','0','1','0','10000','公共基础',NULL,NULL,'','0-32-42','','','','list','article','{\"thumbnail\":\"\"}'), ('43','32','0','1','0','10000','道路工程',NULL,NULL,'','0-32-43','','','','list','article','{\"thumbnail\":\"\"}'), ('44','33','0','1','0','10000','建筑与环境',NULL,NULL,'','0-33-44','','','','list','article','{\"thumbnail\":\"\"}'), ('45','33','0','1','0','10000','给排水工程',NULL,NULL,'','0-33-45','','','','list','article','{\"thumbnail\":\"\"}'), ('46','33','0','1','0','10000','市政工程',NULL,NULL,'','0-33-46','','','','list','article','{\"thumbnail\":\"\"}'), ('47','33','0','1','0','10000','建筑工程',NULL,NULL,'','0-33-47','','','','list','article','{\"thumbnail\":\"\"}'), ('48','34','0','1','0','10000','机电',NULL,NULL,'','0-34-48','','','','list','article','{\"thumbnail\":\"\"}'), ('49','34','0','1','0','10000','市政',NULL,NULL,'','0-34-49','','','','list','article','{\"thumbnail\":\"\"}'), ('50','34','0','1','0','10000','建筑',NULL,NULL,'','0-34-50','','','','list','article','{\"thumbnail\":\"\"}'), ('51','34','0','1','0','10000','法规',NULL,NULL,'','0-34-51','','','','list','article','{\"thumbnail\":\"\"}'), ('52','34','0','1','0','10000','管理',NULL,NULL,'','0-34-52','','','','list','article','{\"thumbnail\":\"\"}'), ('53','35','0','1','0','10000','公路',NULL,NULL,'','0-35-53','','','','list','article','{\"thumbnail\":\"\"}'), ('54','35','0','1','0','10000','机电','0','王小江','','0-35-54','','','','list','article','{\"thumbnail\":\"\"}'), ('55','35','0','1','0','10000','市政',NULL,NULL,'','0-35-55','','','','list','article','{\"thumbnail\":\"\"}'), ('56','35','0','1','0','10000','建筑',NULL,NULL,'','0-35-56','','','','list','article','{\"thumbnail\":\"\"}'), ('57','35','0','1','0','10000','经济',NULL,NULL,'','0-35-57','','','','list','article','{\"thumbnail\":\"\"}'), ('58','35','0','1','0','10000','法规',NULL,NULL,'','0-35-58','','','','list','article','{\"thumbnail\":\"\"}'), ('59','35','0','1','0','10000','管理',NULL,NULL,'','0-35-59','','','','list','article','{\"thumbnail\":\"\"}'), ('60','6','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-1-6-60','','','','list','article','{\"thumbnail\":\"\"}'), ('61','7','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-1-7-61','','','','list','article','{\"thumbnail\":\"\"}'), ('62','8','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-2-8-62','','','','list','article','{\"thumbnail\":\"\"}'), ('63','12','0','1','0','10000','工程管理《土建》','2019',NULL,'我的网课','0-3-12-63','','','','list','article','{\"thumbnail\":\"\"}'), ('64','13','0','1','0','10000','BIM应用工程师《建筑》','2019',NULL,'我的网课','0-3-13-64','','','','list','article','{\"thumbnail\":\"\"}'), ('65','14','0','1','0','10000','BIM应用工程师《结构》','2019',NULL,'我的网课','0-3-14-65','','','','list','article','{\"thumbnail\":\"\"}'), ('66','15','0','1','0','10000','备考解读','2019',NULL,'我的网课','0-4-15-66','','','','list','article','{\"thumbnail\":\"\"}'), ('67','23','0','1','0','10000','百学解题班','2019',NULL,'我的直播','0-5-23-67','','','','list','article','{\"thumbnail\":\"\"}'), ('68','23','0','1','0','10000','科学冲刺班','2018',NULL,'我的网课','0-5-23-68','','','','list','article','{\"thumbnail\":\"\"}'), ('69','23','0','1','0','10000','百学解题班','2018',NULL,'我的网课','0-5-23-69','','','','list','article','{\"thumbnail\":\"\"}'), ('70','23','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-5-23-70','','','','list','article','{\"thumbnail\":\"\"}'), ('71','23','0','1','0','10000','奥学备考班','2018',NULL,'我的网课','0-5-23-71','','','','list','article','{\"thumbnail\":\"\"}'), ('72','24','0','1','0','10000','科学冲刺班','2018',NULL,'我的网课','0-5-24-72','','','','list','article','{\"thumbnail\":\"\"}'), ('73','24','0','1','0','10000','百学解题班','2018',NULL,'我的网课','0-5-24-73','','','','list','article','{\"thumbnail\":\"\"}'), ('74','24','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-5-24-74','','','','list','article','{\"thumbnail\":\"\"}'), ('75','24','0','1','0','10000','奥学备考班','2018',NULL,'我的网课','0-5-24-75','','','','list','article','{\"thumbnail\":\"\"}'), ('76','25','0','1','0','10000','科学冲刺班','2018',NULL,'我的网课','0-5-25-76','','','','list','article','{\"thumbnail\":\"\"}'), ('77','25','0','1','0','10000','百学解题班','2018',NULL,'我的网课','0-5-25-77','','','','list','article','{\"thumbnail\":\"\"}'), ('78','25','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-5-25-78','','','','list','article','{\"thumbnail\":\"\"}'), ('79','25','0','1','0','10000','奥学备考班','2018',NULL,'我的网课','0-5-25-79','','','','list','article','{\"thumbnail\":\"\"}'), ('80','27','0','1','0','10000','科学冲刺班','2018',NULL,'我的网课','0-5-27-80','','','','list','article','{\"thumbnail\":\"\"}'), ('81','27','0','1','0','10000','百学解题班','2018',NULL,'我的网课','0-5-27-81','','','','list','article','{\"thumbnail\":\"\"}'), ('82','27','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-5-27-82','','','','list','article','{\"thumbnail\":\"\"}'), ('83','27','0','1','0','10000','奥学备考班','2018',NULL,'我的网课','0-5-27-83','','','','list','article','{\"thumbnail\":\"\"}'), ('84','36','0','1','0','10000','科学冲刺班','2018',NULL,'我的网课','0-5-36-84','','','','list','article','{\"thumbnail\":\"\"}'), ('85','36','0','1','0','10000','百学解题班','2018',NULL,'我的网课','0-5-36-85','','','','list','article','{\"thumbnail\":\"\"}'), ('86','36','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-5-36-86','','','','list','article','{\"thumbnail\":\"\"}'), ('87','36','0','1','0','10000','奥学备考班','2018',NULL,'我的网课','0-5-36-87','','','','list','article','{\"thumbnail\":\"\"}'), ('88','29','0','1','0','10000','BIM应用工程师《机电》','2019',NULL,'我的网课','0-30-29-88','','','','list','article','{\"thumbnail\":\"\"}'), ('89','37','0','1','0','10000','实操技能','2018',NULL,'我的网课','0-30-37-89','','','','list','article','{\"thumbnail\":\"\"}'), ('90','37','0','1','0','10000','理论知识','2018',NULL,'我的网课','0-30-37-90','','','','list','article','{\"thumbnail\":\"\"}'), ('91','9','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-31-9-91','','','','list','article','{\"thumbnail\":\"\"}'), ('92','9','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-31-9-92','','','','list','article','{\"thumbnail\":\"\"}'), ('93','9','0','1','0','10000','直播特训班','2018','高老师','我的直播','0-31-9-93','','','','list','article','{\"thumbnail\":\"\"}'), ('94','9','0','1','0','10000','科学冲刺班','2018','高老师','我的面授','0-31-9-94','','','','list','article','{\"thumbnail\":\"\"}'), ('95','9','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-31-9-95','','','','list','article','{\"thumbnail\":\"\"}'), ('96','9','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-31-9-96','','','','list','article','{\"thumbnail\":\"\"}'), ('97','9','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-31-9-97','','','','list','article','{\"thumbnail\":\"\"}'), ('98','9','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-31-9-98','','','','list','article','{\"thumbnail\":\"\"}'), ('99','9','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-31-9-99','','','','list','article','{\"thumbnail\":\"\"}'), ('100','9','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-31-9-100','','','','list','article','{\"thumbnail\":\"\"}');
INSERT INTO `cmf_portal_category` VALUES ('101','9','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-31-9-101','','','','list','article','{\"thumbnail\":\"\"}'), ('102','9','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-31-9-102','','','','list','article','{\"thumbnail\":\"\"}'), ('103','9','0','1','0','10000','科学冲刺班','2018','李丛蔚','我的网课','0-31-9-103','','','','list','article','{\"thumbnail\":\"\"}'), ('104','9','0','1','0','10000','百学解题班','2018','李丛蔚','我的网课','0-31-9-104','','','','list','article','{\"thumbnail\":\"\"}'), ('105','9','0','1','0','10000','创学精讲班','2018','李丛蔚','我的网课','0-31-9-105','','','','list','article','{\"thumbnail\":\"\"}'), ('106','9','0','1','0','10000','奥学备考班','2018','李丛蔚','我的网课','0-31-9-106','','','','list','article','{\"thumbnail\":\"\"}'), ('107','38','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-31-38-107','','','','list','article','{\"thumbnail\":\"\"}'), ('108','38','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-31-38-108','','','','list','article','{\"thumbnail\":\"\"}'), ('109','38','0','1','0','10000','直播重难点','2018','王欣','我的直播','0-31-38-109','','','','list','article','{\"thumbnail\":\"\"}'), ('110','38','0','1','0','10000','直播特训班(','2018','王欣','我的直播','0-31-38-110','','','','list','article','{\"thumbnail\":\"\"}'), ('111','38','0','1','0','10000','创学精讲班','2018','郭成奎','我的面授','0-31-38-111','','','','list','article','{\"thumbnail\":\"\"}'), ('112','38','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-31-38-112','','','','list','article','{\"thumbnail\":\"\"}'), ('113','38','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-31-38-113','','','','list','article','{\"thumbnail\":\"\"}'), ('114','38','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-31-38-114','','','','list','article','{\"thumbnail\":\"\"}'), ('115','38','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-31-38-115','','','','list','article','{\"thumbnail\":\"\"}'), ('116','38','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-31-38-116','','','','list','article','{\"thumbnail\":\"\"}'), ('117','38','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-31-38-117','','','','list','article','{\"thumbnail\":\"\"}'), ('118','38','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-31-38-118','','','','list','article','{\"thumbnail\":\"\"}'), ('119','38','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-31-38-119','','','','list','article','{\"thumbnail\":\"\"}'), ('120','38','0','1','0','10000','科学冲刺班','2018','甘森','我的网课','0-31-38-120','','','','list','article','{\"thumbnail\":\"\"}'), ('121','38','0','1','0','10000','百学解题班','2018','甘森','我的网课','0-31-38-121','','','','list','article','{\"thumbnail\":\"\"}'), ('122','38','0','1','0','10000','创学精讲班','2018','甘森','我的网课','0-31-38-122','','','','list','article','{\"thumbnail\":\"\"}'), ('123','38','0','1','0','10000','奥学备考班','2018','甘森','我的网课','0-31-38-123','','','','list','article','{\"thumbnail\":\"\"}'), ('124','39','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-31-39-124','','','','list','article','{\"thumbnail\":\"\"}'), ('125','39','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-31-39-125','','','','list','article','{\"thumbnail\":\"\"}'), ('126','39','0','1','0','10000','直播重难点','2018','陈凤英','我的直播','0-31-39-126','','','','list','article','{\"thumbnail\":\"\"}'), ('127','39','0','1','0','10000','直播特训班','2018','陈凤英','我的直播','0-31-39-127','','','','list','article','{\"thumbnail\":\"\"}'), ('128','39','0','1','0','10000','科学冲刺班','2018','陈凤英','我的面授','0-31-39-128','','','','list','article','{\"thumbnail\":\"\"}'), ('129','39','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-31-39-129','','','','list','article','{\"thumbnail\":\"\"}'), ('130','39','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-31-39-130','','','','list','article','{\"thumbnail\":\"\"}'), ('131','39','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-31-39-131','','','','list','article','{\"thumbnail\":\"\"}'), ('132','39','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-31-39-132','','','','list','article','{\"thumbnail\":\"\"}'), ('133','39','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-31-39-133','','','','list','article','{\"thumbnail\":\"\"}'), ('134','39','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-31-39-134','','','','list','article','{\"thumbnail\":\"\"}'), ('135','39','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-31-39-135','','','','list','article','{\"thumbnail\":\"\"}'), ('136','39','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-31-39-136','','','','list','article','{\"thumbnail\":\"\"}'), ('137','39','0','1','0','10000','科学冲刺班','2018','宿吉南','我的网课','0-31-39-137','','','','list','article','{\"thumbnail\":\"\"}'), ('138','39','0','1','0','10000','百学解题班','2018','宿吉南','我的网课','0-31-39-138','','','','list','article','{\"thumbnail\":\"\"}'), ('139','39','0','1','0','10000','创学精讲班','2018','宿吉南','我的网课','0-31-39-139','','','','list','article','{\"thumbnail\":\"\"}'), ('140','39','0','1','0','10000','奥学备考班','2018','宿吉南','我的网课','0-31-39-140','','','','list','article','{\"thumbnail\":\"\"}'), ('141','10','0','1','0','10000','创学精讲班','2018','史良玉','我的网课','0-32-10-141','','','','list','article','{\"thumbnail\":\"\"}'), ('142','28','0','1','0','10000','创学冲刺班','2019',NULL,'我的网课','0-32-28-142','','','','list','article','{\"thumbnail\":\"\"}'), ('143','28','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-32-28-143','','','','list','article','{\"thumbnail\":\"\"}'), ('144','28','0','1','0','10000','奥学精讲班','2019',NULL,'我的网课','0-32-28-144','','','','list','article','{\"thumbnail\":\"\"}'), ('145','28','0','1','0','10000','创学精讲班','2018','李珊珊','我的网课','0-32-28-145','','','','list','article','{\"thumbnail\":\"\"}'), ('146','40','0','1','0','10000','创学精讲班','2018','史良玉','我的网课','0-32-40-146','','','','list','article','{\"thumbnail\":\"\"}'), ('147','41','0','1','0','10000','创学冲刺班','2019',NULL,'我的网课','0-32-41-147','','','','list','article','{\"thumbnail\":\"\"}'), ('148','41','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-32-41-148','','','','list','article','{\"thumbnail\":\"\"}'), ('149','41','0','1','0','10000','奥学精讲班','2019',NULL,'我的网课','0-32-41-149','','','','list','article','{\"thumbnail\":\"\"}'), ('150','41','0','1','0','10000','创学精讲班','2018','李珊珊','我的网课','0-32-41-150','','','','list','article','{\"thumbnail\":\"\"}'), ('151','42','0','1','0','10000','创学冲刺班(','2019',NULL,'我的网课','0-32-42-151','','','','list','article','{\"thumbnail\":\"\"}'), ('152','42','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-32-42-152','','','','list','article','{\"thumbnail\":\"\"}'), ('153','42','0','1','0','10000','奥学精讲班','2019',NULL,'我的网课','0-32-42-153','','','','list','article','{\"thumbnail\":\"\"}'), ('154','42','0','1','0','10000','创学精讲班','2018','李珊珊','我的网课','0-32-42-154','','','','list','article','{\"thumbnail\":\"\"}'), ('155','43','0','1','0','10000','创学冲刺班','2019',NULL,'我的网课','0-32-43-155','','','','list','article','{\"thumbnail\":\"\"}'), ('156','43','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-32-43-156','','','','list','article','{\"thumbnail\":\"\"}'), ('157','43','0','1','0','10000','奥学精讲班','2019',NULL,'我的网课','0-32-43-157','','','','list','article','{\"thumbnail\":\"\"}'), ('158','43','0','1','0','10000','精讲班','2018','李珊珊','我的网课','0-32-43-158','','','','list','article','{\"thumbnail\":\"\"}'), ('159','11','0','1','0','10000','法规+基础+实务','2018','何老师','我的网课','0-33-11-159','','','','list','article','{\"thumbnail\":\"\"}'), ('160','20','0','1','0','10000','法规+基础+实务','2018','禹老师','我的网课','0-33-20-160','','','','list','article','{\"thumbnail\":\"\"}'), ('161','26','0','1','0','10000','法规+基础+实务','2018','禹老师','我的网课','0-33-26-161','','','','list','article','{\"thumbnail\":\"\"}'), ('162','44','0','1','0','10000','法规+基础+实务','2018','禹老师','我的网课','0-33-44-162','','','','list','article','{\"thumbnail\":\"\"}'), ('163','45','0','1','0','10000','实务','2018','禹老师','我的网课','0-33-45-163','','','','list','article','{\"thumbnail\":\"\"}'), ('164','45','0','1','0','10000','基础','2018','禹老师','我的网课','0-33-45-164','','','','list','article','{\"thumbnail\":\"\"}'), ('165','45','0','1','0','10000','法规','2018','禹老师','我的网课','0-33-45-165','','','','list','article','{\"thumbnail\":\"\"}'), ('166','46','0','1','0','10000','基础+实务','2018','禹老师','我的网课','0-33-46-166','','','','list','article','{\"thumbnail\":\"\"}'), ('167','46','0','1','0','10000','法规','2018','汪老师','我的网课','0-33-46-167','','','','list','article','{\"thumbnail\":\"\"}'), ('168','47','0','1','0','10000','实务','2018','何老师','我的网课','0-33-47-168','','','','list','article','{\"thumbnail\":\"\"}'), ('169','47','0','1','0','10000','法规','2018','汪老师','我的网课','0-33-47-169','','','','list','article','{\"thumbnail\":\"\"}'), ('170','47','0','1','0','10000','基础','2018','万老师','我的网课','0-33-47-170','','','','list','article','{\"thumbnail\":\"\"}'), ('171','16','0','1','0','10000','1百科直播班','2019',NULL,'我的直播','0-34-16-171','','','','list','article','{\"thumbnail\":\"\"}'), ('172','16','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-16-172','','','','list','article','{\"thumbnail\":\"\"}'), ('173','16','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-16-173','','','','list','article','{\"thumbnail\":\"\"}'), ('174','16','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-16-174','','','','list','article','{\"thumbnail\":\"\"}'), ('175','16','0','1','0','10000','6教学应考班','2019',NULL,'我的网课','0-34-16-175','','','','list','article','{\"thumbnail\":\"\"}'), ('176','16','0','1','0','10000','4百学解题班','2019',NULL,'我的网课','0-34-16-176','','','','list','article','{\"thumbnail\":\"\"}'), ('177','16','0','1','0','10000','3创学精讲班','2019',NULL,'我的网课','0-34-16-177','','','','list','article','{\"thumbnail\":\"\"}'), ('178','16','0','1','0','10000','2奥学备考班','2019',NULL,'我的网课','0-34-16-178','','','','list','article','{\"thumbnail\":\"\"}'), ('179','16','0','1','0','10000','6奥创案例班','2018','李昌春','我的网课','0-34-16-179','','','','list','article','{\"thumbnail\":\"\"}'), ('180','16','0','1','0','10000','5教学应考班','2018','李昌春','我的网课','0-34-16-180','','','','list','article','{\"thumbnail\":\"\"}'), ('181','16','0','1','0','10000','4科学冲刺班','2018','李昌春','我的网课','0-34-16-181','','','','list','article','{\"thumbnail\":\"\"}'), ('182','16','0','1','0','10000','3百学解题班','2018','李昌春','我的网课','0-34-16-182','','','','list','article','{\"thumbnail\":\"\"}'), ('183','16','0','1','0','10000','2创学精讲班','2018','李昌春','我的网课','0-34-16-183','','','','list','article','{\"thumbnail\":\"\"}'), ('184','16','0','1','0','10000','1奥学备考班','2018','李昌春','我的网课','0-34-16-184','','','','list','article','{\"thumbnail\":\"\"}'), ('185','19','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-34-19-185','','','','list','article','{\"thumbnail\":\"\"}'), ('186','19','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-19-186','','','','list','article','{\"thumbnail\":\"\"}'), ('187','19','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-19-187','','','','list','article','{\"thumbnail\":\"\"}'), ('188','19','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-19-188','','','','list','article','{\"thumbnail\":\"\"}'), ('189','19','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-19-189','','','','list','article','{\"thumbnail\":\"\"}'), ('190','19','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-34-19-190','','','','list','article','{\"thumbnail\":\"\"}'), ('191','19','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-19-191','','','','list','article','{\"thumbnail\":\"\"}'), ('192','19','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-19-192','','','','list','article','{\"thumbnail\":\"\"}'), ('193','19','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-19-193','','','','list','article','{\"thumbnail\":\"\"}'), ('194','19','0','1','0','10000','6奥创案例班','2018','李想','我的网课','0-34-19-194','','','','list','article','{\"thumbnail\":\"\"}'), ('195','19','0','1','0','10000','5教学应考班','2018','李想','我的网课','0-34-19-195','','','','list','article','{\"thumbnail\":\"\"}'), ('196','19','0','1','0','10000','4科学冲刺班','2018','李想','我的网课','0-34-19-196','','','','list','article','{\"thumbnail\":\"\"}'), ('197','19','0','1','0','10000','3百学解题班','2018','李想','我的网课','0-34-19-197','','','','list','article','{\"thumbnail\":\"\"}'), ('198','19','0','1','0','10000','2创学精讲班','2018','李想','我的网课','0-34-19-198','','','','list','article','{\"thumbnail\":\"\"}'), ('199','19','0','1','0','10000','1奥学备考班','2018','李想','我的网课','0-34-19-199','','','','list','article','{\"thumbnail\":\"\"}'), ('200','48','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-34-48-200','','','','list','article','{\"thumbnail\":\"\"}');
INSERT INTO `cmf_portal_category` VALUES ('201','48','0','1','0','10000','真题解析','2018','徐奔','我的直播','0-34-48-201','','','','list','article','{\"thumbnail\":\"\"}'), ('202','48','0','1','0','10000','商务密训班','2019',NULL,'我的面授','0-34-48-202','','','','list','article','{\"thumbnail\":\"\"}'), ('203','48','0','1','0','10000','商务特训班','2019',NULL,'我的面授','0-34-48-203','','','','list','article','{\"thumbnail\":\"\"}'), ('204','48','0','1','0','10000','商务集训班','2019',NULL,'我的面授','0-34-48-204','','','','list','article','{\"thumbnail\":\"\"}'), ('205','48','0','1','0','10000','3科学冲刺班','2018','陈剑名','我的面授','0-34-48-205','','','','list','article','{\"thumbnail\":\"\"}'), ('206','48','0','1','0','10000','2创学精讲班','2018','陈剑名','我的面授','0-34-48-206','','','','list','article','{\"thumbnail\":\"\"}'), ('207','48','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-48-207','','','','list','article','{\"thumbnail\":\"\"}'), ('208','48','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-48-208','','','','list','article','{\"thumbnail\":\"\"}'), ('209','48','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-48-209','','','','list','article','{\"thumbnail\":\"\"}'), ('210','48','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-48-210','','','','list','article','{\"thumbnail\":\"\"}'), ('211','19','0','1','0','10000','科学冲刺班','2019','','我的网课','0-34-19-211','','','','list','article','{\"thumbnail\":\"\"}'), ('212','48','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-48-212','','','','list','article','{\"thumbnail\":\"\"}'), ('213','48','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-48-213','','','','list','article','{\"thumbnail\":\"\"}'), ('214','48','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-48-214','','','','list','article','{\"thumbnail\":\"\"}'), ('215','48','0','1','0','10000','6奥创案例班','2018','徐奔','我的网课','0-34-48-215','','','','list','article','{\"thumbnail\":\"\"}'), ('216','48','0','1','0','10000','5教学应考班','2018','徐奔','我的网课','0-34-48-216','','','','list','article','{\"thumbnail\":\"\"}'), ('217','48','0','1','0','10000','4科学冲刺班','2018','徐奔','我的网课','0-34-48-217','','','','list','article','{\"thumbnail\":\"\"}'), ('218','48','0','1','0','10000','3百学解题班','2018','徐奔','我的网课','0-34-48-218','','','','list','article','{\"thumbnail\":\"\"}'), ('219','48','0','1','0','10000','2创学精讲班','2018','彭磊','我的网课','0-34-48-219','','','','list','article','{\"thumbnail\":\"\"}'), ('220','48','0','1','0','10000','1奥学备考班','2018','徐奔','我的网课','0-34-48-220','','','','list','article','{\"thumbnail\":\"\"}'), ('221','49','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-34-49-221','','','','list','article','{\"thumbnail\":\"\"}'), ('222','49','0','1','0','10000','真题解析班','2018','何亮','我的直播','0-34-49-222','','','','list','article','{\"thumbnail\":\"\"}'), ('223','49','0','1','0','10000','3科学冲刺班','2018','何亮','我的面授','0-34-49-223','','','','list','article','{\"thumbnail\":\"\"}'), ('224','49','0','1','0','10000','2创学精讲班','2018','高晓岗','我的面授','0-34-49-224','','','','list','article','{\"thumbnail\":\"\"}'), ('225','49','0','1','0','10000','6育学密训班','2018','吕丛发','我的面授','0-34-49-225','','','','list','article','{\"thumbnail\":\"\"}'), ('226','49','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-49-226','','','','list','article','{\"thumbnail\":\"\"}'), ('227','49','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-49-227','','','','list','article','{\"thumbnail\":\"\"}'), ('228','49','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-49-228','','','','list','article','{\"thumbnail\":\"\"}'), ('229','49','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-49-229','','','','list','article','{\"thumbnail\":\"\"}'), ('230','49','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-34-49-230','','','','list','article','{\"thumbnail\":\"\"}'), ('231','49','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-49-231','','','','list','article','{\"thumbnail\":\"\"}'), ('232','49','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-49-232','','','','list','article','{\"thumbnail\":\"\"}'), ('233','49','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-49-233','','','','list','article','{\"thumbnail\":\"\"}'), ('234','49','0','1','0','10000','7奥创案例班','2018','董祥','我的网课','0-34-49-234','','','','list','article','{\"thumbnail\":\"\"}'), ('235','49','0','1','0','10000','5.教学应考班','2018','董祥','我的网课','0-34-49-235','','','','list','article','{\"thumbnail\":\"\"}'), ('236','49','0','1','0','10000','4科学冲刺班','2018','董祥','我的网课','0-34-49-236','','','','list','article','{\"thumbnail\":\"\"}'), ('237','49','0','1','0','10000','3百学解题班','2018','董祥','我的网课','0-34-49-237','','','','list','article','{\"thumbnail\":\"\"}'), ('238','49','0','1','0','10000','2创学精讲班','2018','董祥','我的网课','0-34-49-238','','','','list','article','{\"thumbnail\":\"\"}'), ('239','49','0','1','0','10000','1奥学备考班','2018','董祥','我的网课','0-34-49-239','','','','list','article','{\"thumbnail\":\"\"}'), ('240','50','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-34-50-240','','','','list','article','{\"thumbnail\":\"\"}'), ('241','50','0','1','0','10000','真题解析','2018','何亮','我的直播','0-34-50-241','','','','list','article','{\"thumbnail\":\"\"}'), ('242','50','0','1','0','10000','3科学冲刺班','2018','薛涛','我的面授','0-34-50-242','','','','list','article','{\"thumbnail\":\"\"}'), ('243','50','0','1','0','10000','2创学精讲班','2018','薛涛','我的面授','0-34-50-243','','','','list','article','{\"thumbnail\":\"\"}'), ('244','50','0','1','0','10000','6育学密训班','2018','薛涛','我的面授','0-34-50-244','','','','list','article','{\"thumbnail\":\"\"}'), ('245','50','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-50-245','','','','list','article','{\"thumbnail\":\"\"}'), ('246','50','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-50-246','','','','list','article','{\"thumbnail\":\"\"}'), ('247','50','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-50-247','','','','list','article','{\"thumbnail\":\"\"}'), ('248','50','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-50-248','','','','list','article','{\"thumbnail\":\"\"}'), ('249','50','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-34-50-249','','','','list','article','{\"thumbnail\":\"\"}'), ('250','50','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-50-250','','','','list','article','{\"thumbnail\":\"\"}'), ('251','50','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-50-251','','','','list','article','{\"thumbnail\":\"\"}'), ('252','50','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-50-252','','','','list','article','{\"thumbnail\":\"\"}'), ('253','50','0','1','0','10000','7奥创案例班','2018','魏国安','我的网课','0-34-50-253','','','','list','article','{\"thumbnail\":\"\"}'), ('254','50','0','1','0','10000','5教学应考班','2018','魏国安','我的网课','0-34-50-254','','','','list','article','{\"thumbnail\":\"\"}'), ('255','50','0','1','0','10000','4科学冲刺班','2018','魏国安','我的网课','0-34-50-255','','','','list','article','{\"thumbnail\":\"\"}'), ('256','50','0','1','0','10000','3百学解题班','2018','魏国安','我的网课','0-34-50-256','','','','list','article','{\"thumbnail\":\"\"}'), ('257','50','0','1','0','10000','2创学精讲班','2018','薛涛','我的网课','0-34-50-257','','','','list','article','{\"thumbnail\":\"\"}'), ('258','50','0','1','0','10000','1奥学备考班','2018','魏国安','我的网课','0-34-50-258','','','','list','article','{\"thumbnail\":\"\"}'), ('259','51','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-34-51-259','','','','list','article','{\"thumbnail\":\"\"}'), ('260','51','0','1','0','10000','真题解析班','2018','葛卫林','我的直播','0-34-51-260','','','','list','article','{\"thumbnail\":\"\"}'), ('261','51','0','1','0','10000','商务密训班','2019',NULL,'我的面授','0-34-51-261','','','','list','article','{\"thumbnail\":\"\"}'), ('262','51','0','1','0','10000','商务特训班','2019',NULL,'我的面授','0-34-51-262','','','','list','article','{\"thumbnail\":\"\"}'), ('263','51','0','1','0','10000','商务集训班','2019',NULL,'我的面授','0-34-51-263','','','','list','article','{\"thumbnail\":\"\"}'), ('264','51','0','1','0','10000','6育学密训班','2018','魏老师','我的面授','0-34-51-264','','','','list','article','{\"thumbnail\":\"\"}'), ('265','51','0','1','0','10000','2创学精讲班','2018','葛卫林','我的面授','0-34-51-265','','','','list','article','{\"thumbnail\":\"\"}'), ('266','51','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-51-266','','','','list','article','{\"thumbnail\":\"\"}'), ('267','51','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-51-267','','','','list','article','{\"thumbnail\":\"\"}'), ('268','51','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-51-268','','','','list','article','{\"thumbnail\":\"\"}'), ('269','51','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-51-269','','','','list','article','{\"thumbnail\":\"\"}'), ('270','51','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-34-51-270','','','','list','article','{\"thumbnail\":\"\"}'), ('271','51','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-51-271','','','','list','article','{\"thumbnail\":\"\"}'), ('272','51','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-51-272','','','','list','article','{\"thumbnail\":\"\"}'), ('273','51','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-51-273','','','','list','article','{\"thumbnail\":\"\"}'), ('274','51','0','1','0','10000','5教学应考班','2018','高晓强','我的网课','0-34-51-274','','','','list','article','{\"thumbnail\":\"\"}'), ('275','51','0','1','0','10000','4科学冲刺班','2018','高晓强','我的网课','0-34-51-275','','','','list','article','{\"thumbnail\":\"\"}'), ('276','51','0','1','0','10000','3百学解题班','2018','高晓强','我的网课','0-34-51-276','','','','list','article','{\"thumbnail\":\"\"}'), ('277','51','0','1','0','10000','2创学精讲班','2018','高晓强','我的网课','0-34-51-277','','','','list','article','{\"thumbnail\":\"\"}'), ('278','51','0','1','0','10000','1奥学备考班','2018','高晓强','我的网课','0-34-51-278','','','','list','article','{\"thumbnail\":\"\"}'), ('279','52','0','1','0','10000','百科直播班','2019',NULL,'我的网课','0-34-52-279','','','','list','article','{\"thumbnail\":\"\"}'), ('280','52','0','1','0','10000','真题解析','2018','张天宇','我的直播','0-34-52-280','','','','list','article','{\"thumbnail\":\"\"}'), ('281','52','0','1','0','10000','商务密训班','2019',NULL,'我的面授','0-34-52-281','','','','list','article','{\"thumbnail\":\"\"}'), ('282','52','0','1','0','10000','商务特训班','2019',NULL,'我的面授','0-34-52-282','','','','list','article','{\"thumbnail\":\"\"}'), ('283','52','0','1','0','10000','商务集训班','2019',NULL,'我的面授','0-34-52-283','','','','list','article','{\"thumbnail\":\"\"}'), ('284','52','0','1','0','10000','6育学密训班','2018','吕从发','我的面授','0-34-52-284','','','','list','article','{\"thumbnail\":\"\"}'), ('285','52','0','1','0','10000','2创学精讲班','2018','张天宇','我的面授','0-34-52-285','','','','list','article','{\"thumbnail\":\"\"}'), ('286','52','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-34-52-286','','','','list','article','{\"thumbnail\":\"\"}'), ('287','52','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-34-52-287','','','','list','article','{\"thumbnail\":\"\"}'), ('288','52','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-34-52-288','','','','list','article','{\"thumbnail\":\"\"}'), ('289','52','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-34-52-289','','','','list','article','{\"thumbnail\":\"\"}'), ('290','52','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-34-52-290','','','','list','article','{\"thumbnail\":\"\"}'), ('291','52','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-34-52-291','','','','list','article','{\"thumbnail\":\"\"}'), ('292','52','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-34-52-292','','','','list','article','{\"thumbnail\":\"\"}'), ('293','52','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-34-52-293','','','','list','article','{\"thumbnail\":\"\"}'), ('294','52','0','1','0','10000','5教学应考班','2018','宿吉南','我的网课','0-34-52-294','','','','list','article','{\"thumbnail\":\"\"}'), ('295','52','0','1','0','10000','4科学冲刺班','2018','宿吉南','我的网课','0-34-52-295','','','','list','article','{\"thumbnail\":\"\"}'), ('296','52','0','1','0','10000','3百学解题班','2018','宿吉南','我的网课','0-34-52-296','','','','list','article','{\"thumbnail\":\"\"}'), ('297','52','0','1','0','10000','1奥学备考班','2018','宿吉南','我的网课','0-34-52-297','','','','list','article','{\"thumbnail\":\"\"}'), ('298','17','0','1','0','10000','育学密训班','2019',NULL,'我的网课','0-35-17-298','','','','list','article','{\"thumbnail\":\"\"}'), ('299','17','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-17-299','','','','list','article','{\"thumbnail\":\"\"}'), ('300','17','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-17-300','','','','list','article','{\"thumbnail\":\"\"}');
INSERT INTO `cmf_portal_category` VALUES ('301','17','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-17-301','','','','list','article','{\"thumbnail\":\"\"}'), ('302','17','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-17-302','','','','list','article','{\"thumbnail\":\"\"}'), ('303','17','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-17-303','','','','list','article','{\"thumbnail\":\"\"}'), ('304','17','0','1','0','10000','创学精讲班','2018',NULL,'我的网课','0-35-17-304','','','','list','article','{\"thumbnail\":\"\"}'), ('305','18','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-18-305','','','','list','article','{\"thumbnail\":\"\"}'), ('306','18','0','1','0','10000','育学密训班','2018','李珊珊','我的直播','0-35-18-306','','','','list','article','{\"thumbnail\":\"\"}'), ('307','18','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-18-307','','','','list','article','{\"thumbnail\":\"\"}'), ('308','18','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-18-308','','','','list','article','{\"thumbnail\":\"\"}'), ('309','18','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-18-309','','','','list','article','{\"thumbnail\":\"\"}'), ('310','18','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-18-310','','','','list','article','{\"thumbnail\":\"\"}'), ('311','18','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-18-311','','','','list','article','{\"thumbnail\":\"\"}'), ('312','18','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-18-312','','','','list','article','{\"thumbnail\":\"\"}'), ('313','18','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-18-313','','','','list','article','{\"thumbnail\":\"\"}'), ('314','18','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-18-314','','','','list','article','{\"thumbnail\":\"\"}'), ('315','18','0','1','0','10000','案例特训班','2018','李想','我的网课','0-35-18-315','','','','list','article','{\"thumbnail\":\"\"}'), ('316','18','0','1','0','10000','教学应考班','2018','李想','我的网课','0-35-18-316','','','','list','article','{\"thumbnail\":\"\"}'), ('317','18','0','1','0','10000','科学冲刺班','2018','李想','我的网课','0-35-18-317','','','','list','article','{\"thumbnail\":\"\"}'), ('318','18','0','1','0','10000','百学解题班','2018','李想','我的网课','0-35-18-318','','','','list','article','{\"thumbnail\":\"\"}'), ('319','18','0','1','0','10000','创学精讲班','2018','李想','我的网课','0-35-18-319','','','','list','article','{\"thumbnail\":\"\"}'), ('320','18','0','1','0','10000','奥学备考班','2018','李想','我的网课','0-35-18-320','','','','list','article','{\"thumbnail\":\"\"}'), ('321','53','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-53-321','','','','list','article','{\"thumbnail\":\"\"}'), ('322','53','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-35-53-322','','','','list','article','{\"thumbnail\":\"\"}'), ('323','53','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-53-323','','','','list','article','{\"thumbnail\":\"\"}'), ('324','53','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-53-324','','','','list','article','{\"thumbnail\":\"\"}'), ('325','53','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-53-325','','','','list','article','{\"thumbnail\":\"\"}'), ('326','53','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-53-326','','','','list','article','{\"thumbnail\":\"\"}'), ('327','53','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-53-327','','','','list','article','{\"thumbnail\":\"\"}'), ('328','53','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-53-328','','','','list','article','{\"thumbnail\":\"\"}'), ('329','53','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-53-329','','','','list','article','{\"thumbnail\":\"\"}'), ('330','53','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-53-330','','','','list','article','{\"thumbnail\":\"\"}'), ('331','53','0','1','0','10000','奥创案例班','2018','李昌春','我的网课','0-35-53-331','','','','list','article','{\"thumbnail\":\"\"}'), ('332','53','0','1','0','10000','教学应考班','2018','李昌春','我的网课','0-35-53-332','','','','list','article','{\"thumbnail\":\"\"}'), ('333','53','0','1','0','10000','科学冲刺班','2018','李昌春','我的网课','0-35-53-333','','','','list','article','{\"thumbnail\":\"\"}'), ('334','53','0','1','0','10000','百学解题班','2018','李昌春','我的网课','0-35-53-334','','','','list','article','{\"thumbnail\":\"\"}'), ('335','53','0','1','0','10000','创学精讲班','2018','李昌春','我的网课','0-35-53-335','','','','list','article','{\"thumbnail\":\"\"}'), ('336','53','0','1','0','10000','奥学备考班','2018','李昌春','我的网课','0-35-53-336','','','','list','article','{\"thumbnail\":\"\"}'), ('337','54','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-54-337','','','','list','article','{\"thumbnail\":\"\"}'), ('338','54','0','1','0','10000','重难点','2018','徐奔','我的直播','0-35-54-338','','','','list','article','{\"thumbnail\":\"\"}'), ('339','54','0','1','0','10000','育学特训班','2018',NULL,'我的直播','0-35-54-339','','','','list','article','{\"thumbnail\":\"\"}'), ('340','54','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-35-54-340','','','','list','article','{\"thumbnail\":\"\"}'), ('341','54','0','1','0','10000','商务密训班','2019',NULL,'我的面授','0-35-54-341','','','','list','article','{\"thumbnail\":\"\"}'), ('342','54','0','1','0','10000','商务特训班','2019',NULL,'我的面授','0-35-54-342','','','','list','article','{\"thumbnail\":\"\"}'), ('343','54','0','1','0','10000','商务集训班','2019',NULL,'我的面授','0-35-54-343','','','','list','article','{\"thumbnail\":\"\"}'), ('344','54','0','1','0','10000','科学冲刺班','2018','徐奔','我的面授','0-35-54-344','','','','list','article','{\"thumbnail\":\"\"}'), ('345','54','0','1','0','10000','创学精讲班','2018','陈剑名','我的面授','0-35-54-345','','','','list','article','{\"thumbnail\":\"\"}'), ('346','54','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-54-346','','','','list','article','{\"thumbnail\":\"\"}'), ('347','54','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-54-347','','','','list','article','{\"thumbnail\":\"\"}'), ('348','54','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-54-348','','','','list','article','{\"thumbnail\":\"\"}'), ('349','54','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-54-349','','','','list','article','{\"thumbnail\":\"\"}'), ('350','54','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-54-350','','','','list','article','{\"thumbnail\":\"\"}'), ('351','54','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-54-351','','','','list','article','{\"thumbnail\":\"\"}'), ('352','54','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-54-352','','','','list','article','{\"thumbnail\":\"\"}'), ('353','54','0','1','0','10000','奥学备考班','2019','','我的网课','0-35-54-353','','','','list','article','{\"thumbnail\":\"\"}'), ('354','54','0','1','0','10000','奥创案例班','2018','城子老师','我的网课','0-35-54-354','','','','list','article','{\"thumbnail\":\"\"}'), ('355','54','0','1','0','10000','教学应考班','2018','城子老师','我的网课','0-35-54-355','','','','list','article','{\"thumbnail\":\"\"}'), ('356','54','0','1','0','10000','科学冲刺班','2018','王小江','我的网课','0-35-54-356','','','','list','article','{\"thumbnail\":\"\"}'), ('357','54','0','1','0','10000','百学解题班','2018','城子老师','我的网课','0-35-54-357','','','','list','article','{\"thumbnail\":\"\"}'), ('358','54','0','1','0','10000','创学精讲班','2018','王小江','我的网课','0-35-54-358','','','','list','article','{\"thumbnail\":\"\"}'), ('359','54','0','1','0','10000','奥学备考班','2018','王小江','我的网课','0-35-54-359','','','','list','article','{\"thumbnail\":\"\"}'), ('360','55','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-55-360','','','','list','article','{\"thumbnail\":\"\"}'), ('361','55','0','1','0','10000','重难点','2018','高晓岗','我的直播','0-35-55-361','','','','list','article','{\"thumbnail\":\"\"}'), ('362','55','0','1','0','10000','特训班','2018','何亮','我的直播','0-35-55-362','','','','list','article','{\"thumbnail\":\"\"}'), ('363','55','0','1','0','10000','育学密训班','2018','董雨佳','我的直播','0-35-55-363','','','','list','article','{\"thumbnail\":\"\"}'), ('364','55','0','1','0','10000','科学冲刺班','2018','吕从发','我的面授','0-35-55-364','','','','list','article','{\"thumbnail\":\"\"}'), ('365','55','0','1','0','10000','创学精讲班','2018','吕从发','我的面授','0-35-55-365','','','','list','article','{\"thumbnail\":\"\"}'), ('366','55','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-55-366','','','','list','article','{\"thumbnail\":\"\"}'), ('367','55','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-55-367','','','','list','article','{\"thumbnail\":\"\"}'), ('368','55','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-55-368','','','','list','article','{\"thumbnail\":\"\"}'), ('369','55','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-55-369','','','','list','article','{\"thumbnail\":\"\"}'), ('370','55','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-55-370','','','','list','article','{\"thumbnail\":\"\"}'), ('371','55','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-55-371','','','','list','article','{\"thumbnail\":\"\"}'), ('372','55','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-55-372','','','','list','article','{\"thumbnail\":\"\"}'), ('373','55','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-55-373','','','','list','article','{\"thumbnail\":\"\"}'), ('374','55','0','1','0','10000','奥创案例班','2018','董祥','我的网课','0-35-55-374','','','','list','article','{\"thumbnail\":\"\"}'), ('375','55','0','1','0','10000','教学应考班','2018','董祥','我的网课','0-35-55-375','','','','list','article','{\"thumbnail\":\"\"}'), ('376','55','0','1','0','10000','科学冲刺班','2018','董祥','我的网课','0-35-55-376','','','','list','article','{\"thumbnail\":\"\"}'), ('377','55','0','1','0','10000','百学解题班','2018','董祥','我的网课','0-35-55-377','','','','list','article','{\"thumbnail\":\"\"}'), ('378','55','0','1','0','10000','创学精讲班','2018','董祥','我的网课','0-35-55-378','','','','list','article','{\"thumbnail\":\"\"}'), ('379','55','0','1','0','10000','奥学备考班','2018','董祥','我的网课','0-35-55-379','','','','list','article','{\"thumbnail\":\"\"}'), ('380','56','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-56-380','','','','list','article','{\"thumbnail\":\"\"}'), ('381','56','0','1','0','10000','直播特训班','2018','何亮','我的直播','0-35-56-381','','','','list','article','{\"thumbnail\":\"\"}'), ('382','56','0','1','0','10000','直播重难点','2018','何亮','我的直播','0-35-56-382','','','','list','article','{\"thumbnail\":\"\"}'), ('383','56','0','1','0','10000','育学密训班','2018',NULL,'我的直播','0-35-56-383','','','','list','article','{\"thumbnail\":\"\"}'), ('384','56','0','1','0','10000','科学冲刺班','2018','何亮','我的面授','0-35-56-384','','','','list','article','{\"thumbnail\":\"\"}'), ('385','56','0','1','0','10000','创学精讲班','2018','薛涛','我的面授','0-35-56-385','','','','list','article','{\"thumbnail\":\"\"}'), ('386','56','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-56-386','','','','list','article','{\"thumbnail\":\"\"}'), ('387','56','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-56-387','','','','list','article','{\"thumbnail\":\"\"}'), ('388','56','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-56-388','','','','list','article','{\"thumbnail\":\"\"}'), ('389','56','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-56-389','','','','list','article','{\"thumbnail\":\"\"}'), ('390','56','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-56-390','','','','list','article','{\"thumbnail\":\"\"}'), ('391','56','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-56-391','','','','list','article','{\"thumbnail\":\"\"}'), ('392','56','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-56-392','','','','list','article','{\"thumbnail\":\"\"}'), ('393','56','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-56-393','','','','list','article','{\"thumbnail\":\"\"}'), ('394','56','0','1','0','10000','奥创案例班','2018','魏国安','我的网课','0-35-56-394','','','','list','article','{\"thumbnail\":\"\"}'), ('395','56','0','1','0','10000','教学应考班','2018','魏国安','我的网课','0-35-56-395','','','','list','article','{\"thumbnail\":\"\"}'), ('396','56','0','1','0','10000','科学冲刺班','2018','魏国安','我的网课','0-35-56-396','','','','list','article','{\"thumbnail\":\"\"}'), ('397','56','0','1','0','10000','百学解题班','2018','魏国安','我的网课','0-35-56-397','','','','list','article','{\"thumbnail\":\"\"}'), ('398','56','0','1','0','10000','创学精讲班','2018','魏国安','我的网课','0-35-56-398','','','','list','article','{\"thumbnail\":\"\"}'), ('399','56','0','1','0','10000','奥学备考班','2018','魏国安','我的网课','0-35-56-399','','','','list','article','{\"thumbnail\":\"\"}'), ('400','57','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-57-400','','','','list','article','{\"thumbnail\":\"\"}');
INSERT INTO `cmf_portal_category` VALUES ('401','57','0','1','0','10000','直播特训班','2018','刘凯歌','我的直播','0-35-57-401','','','','list','article','{\"thumbnail\":\"\"}'), ('402','57','0','1','0','10000','直播重难点','2018','刘凯歌','我的直播','0-35-57-402','','','','list','article','{\"thumbnail\":\"\"}'), ('403','57','0','1','0','10000','育学密训班','2018','刘凯歌','我的直播','0-35-57-403','','','','list','article','{\"thumbnail\":\"\"}'), ('404','57','0','1','0','10000','科学冲刺班','2018','刘凯歌','我的面授','0-35-57-404','','','','list','article','{\"thumbnail\":\"\"}'), ('405','57','0','1','0','10000','创学精讲班','2018','魏国安','我的面授','0-35-57-405','','','','list','article','{\"thumbnail\":\"\"}'), ('406','57','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-57-406','','','','list','article','{\"thumbnail\":\"\"}'), ('407','57','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-57-407','','','','list','article','{\"thumbnail\":\"\"}'), ('408','57','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-57-408','','','','list','article','{\"thumbnail\":\"\"}'), ('409','57','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-57-409','','','','list','article','{\"thumbnail\":\"\"}'), ('410','57','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-57-410','','','','list','article','{\"thumbnail\":\"\"}'), ('411','57','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-57-411','','','','list','article','{\"thumbnail\":\"\"}'), ('412','57','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-57-412','','','','list','article','{\"thumbnail\":\"\"}'), ('413','57','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-57-413','','','','list','article','{\"thumbnail\":\"\"}'), ('414','57','0','1','0','10000','教学应考班','2018','吕世尊','我的网课','0-35-57-414','','','','list','article','{\"thumbnail\":\"\"}'), ('415','57','0','1','0','10000','科学冲刺班','2018','吕世尊','我的网课','0-35-57-415','','','','list','article','{\"thumbnail\":\"\"}'), ('416','57','0','1','0','10000','百学解题班','2018','吕世尊','我的网课','0-35-57-416','','','','list','article','{\"thumbnail\":\"\"}'), ('417','57','0','1','0','10000','创学精讲班','2018','赵怀志','我的网课','0-35-57-417','','','','list','article','{\"thumbnail\":\"\"}'), ('418','57','0','1','0','10000','奥学备考班','2018','赵怀志','我的网课','0-35-57-418','','','','list','article','{\"thumbnail\":\"\"}'), ('419','58','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-58-419','','','','list','article','{\"thumbnail\":\"\"}'), ('420','58','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-58-420','','','','list','article','{\"thumbnail\":\"\"}'), ('421','58','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-58-421','','','','list','article','{\"thumbnail\":\"\"}'), ('422','58','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-58-422','','','','list','article','{\"thumbnail\":\"\"}'), ('423','58','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-58-423','','','','list','article','{\"thumbnail\":\"\"}'), ('424','58','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-58-424','','','','list','article','{\"thumbnail\":\"\"}'), ('425','58','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-58-425','','','','list','article','{\"thumbnail\":\"\"}'), ('426','58','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-58-426','','','','list','article','{\"thumbnail\":\"\"}'), ('427','58','0','1','0','10000','教学应考班','2018','高晓强','我的网课','0-35-58-427','','','','list','article','{\"thumbnail\":\"\"}'), ('428','58','0','1','0','10000','科学冲刺班','2018','高晓强','我的网课','0-35-58-428','','','','list','article','{\"thumbnail\":\"\"}'), ('429','58','0','1','0','10000','百学解题班','2018','高晓强','我的网课','0-35-58-429','','','','list','article','{\"thumbnail\":\"\"}'), ('430','58','0','1','0','10000','创学精讲班','2018','高晓强','我的网课','0-35-58-430','','','','list','article','{\"thumbnail\":\"\"}'), ('431','58','0','1','0','10000','奥学备考班','2018','高晓强','我的网课','0-35-58-431','','','','list','article','{\"thumbnail\":\"\"}'), ('432','58','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-58-432','','','','list','article','{\"thumbnail\":\"\"}'), ('433','58','0','1','0','10000','直播重难点','2018','葛卫林','我的直播','0-35-58-433','','','','list','article','{\"thumbnail\":\"\"}'), ('434','58','0','1','0','10000','直播特训班','2018','葛卫林','我的直播','0-35-58-434','','','','list','article','{\"thumbnail\":\"\"}'), ('435','58','0','1','0','10000','育学密训班','2018','葛卫林','我的直播','0-35-58-435','','','','list','article','{\"thumbnail\":\"\"}'), ('436','58','0','1','0','10000','新精讲班','2018',NULL,'我的面授','0-35-58-436','','','','list','article','{\"thumbnail\":\"\"}'), ('437','58','0','1','0','10000','科学冲刺班','2018','葛卫林','我的面授','0-35-58-437','','','','list','article','{\"thumbnail\":\"\"}'), ('438','58','0','1','0','10000','精讲班','2018','葛卫林','我的面授','0-35-58-438','','','','list','article','{\"thumbnail\":\"\"}'), ('439','59','0','1','0','10000','科学冲刺班','2018','张天宇','我的面授','0-35-59-439','','','','list','article','{\"thumbnail\":\"\"}'), ('440','59','0','1','0','10000','创学精讲班','2018','吕丛发','我的面授','0-35-59-440','','','','list','article','{\"thumbnail\":\"\"}'), ('441','59','0','1','0','10000','百科直播班','2019',NULL,'我的直播','0-35-59-441','','','','list','article','{\"thumbnail\":\"\"}'), ('442','59','0','1','0','10000','直播模考班','2018',NULL,'我的直播','0-35-59-442','','','','list','article','{\"thumbnail\":\"\"}'), ('443','59','0','1','0','10000','直播重难点','2018','张天宇','我的直播','0-35-59-443','','','','list','article','{\"thumbnail\":\"\"}'), ('444','59','0','1','0','10000','直播特训班','2018','关宇','我的直播','0-35-59-444','','','','list','article','{\"thumbnail\":\"\"}'), ('445','59','0','1','0','10000','育学密训班','2018','张天宇','我的直播','0-35-59-445','','','','list','article','{\"thumbnail\":\"\"}'), ('446','59','0','1','0','10000','百科案例班','2019',NULL,'我的网课','0-35-59-446','','','','list','article','{\"thumbnail\":\"\"}'), ('447','59','0','1','0','10000','百科密训班','2019',NULL,'我的网课','0-35-59-447','','','','list','article','{\"thumbnail\":\"\"}'), ('448','59','0','1','0','10000','育学特训班','2019',NULL,'我的网课','0-35-59-448','','','','list','article','{\"thumbnail\":\"\"}'), ('449','59','0','1','0','10000','教学应考班','2019',NULL,'我的网课','0-35-59-449','','','','list','article','{\"thumbnail\":\"\"}'), ('450','59','0','1','0','10000','科学冲刺班','2019',NULL,'我的网课','0-35-59-450','','','','list','article','{\"thumbnail\":\"\"}'), ('451','59','0','1','0','10000','百学解题班','2019',NULL,'我的网课','0-35-59-451','','','','list','article','{\"thumbnail\":\"\"}'), ('452','59','0','1','0','10000','创学精讲班','2019',NULL,'我的网课','0-35-59-452','','','','list','article','{\"thumbnail\":\"\"}'), ('453','59','0','1','0','10000','奥学备考班','2019',NULL,'我的网课','0-35-59-453','','','','list','article','{\"thumbnail\":\"\"}'), ('454','59','0','1','0','10000','教学应考班','2018','宿吉南','我的网课','0-35-59-454','','','','list','article','{\"thumbnail\":\"\"}'), ('455','59','0','1','0','10000','科学冲刺班','2018','宿吉南','我的网课','0-35-59-455','','','','list','article','{\"thumbnail\":\"\"}'), ('456','59','0','1','0','10000','百学解题班','2018','宿吉南','我的网课','0-35-59-456','','','','list','article','{\"thumbnail\":\"\"}'), ('457','59','0','1','0','10000','创学精讲班','2018','宿吉南','我的网课','0-35-59-457','','','','list','article','{\"thumbnail\":\"\"}'), ('458','59','0','1','0','10000','奥学备考班','2018','宿吉南','我的网课','0-35-59-458','','','','list','article','{\"thumbnail\":\"\"}');
INSERT INTO `cmf_portal_category_post` VALUES ('1','1','60','10000','1'), ('3','1','92','10000','1'), ('4','1','93','10000','1'), ('5','2','72','10000','1'), ('6','2','73','10000','1'), ('7','2','74','10000','1'), ('8','2','75','10000','1'), ('9','2','98','10000','1'), ('10','3','91','10000','1'), ('11','3','92','10000','1'), ('12','3','93','10000','1'), ('13','3','94','10000','1');
INSERT INTO `cmf_portal_post` VALUES ('1','0','1','1','1','0','1','0','0','0','0','0','0','1547625706','1547630454','1547625660','0','消防《全科》精讲试听','测试一下添加课程','课程简介。','测试一下添加课程','','&lt;p&gt;测试一下添加课程&lt;/p&gt;',NULL,'{\"thumbnail\":\"\"}','3500.00','3200.00','3200.00','3800.00','5000.00'), ('2','0','1','1','1','1','1','0','0','0','0','0','0','1547632236','1547633306','1547632140','0','2019、2018《综合能力》高级商务班','','课程体系全面，课程阶段涵盖精讲、习题、冲刺、应考、特训、密训、案例。适合自学时间较少，基础一般。本班次专为通过应试考试而设置。','','',NULL,NULL,'{\"thumbnail\":\"\"}','3100.00','3100.00','3100.00','3100.00','3100.00'), ('3','0','1','1','1','1','1','0','0','0','0','0','0','1547632326','1547633139','1547632260','0','BIM中级应用工程师《土建》','','测试','','',NULL,NULL,'{\"thumbnail\":\"\"}','6800.00','6800.00','6800.00','6800.00','6800.00');
INSERT INTO `cmf_portal_tag` VALUES ('1','1','0','0','网课'), ('2','1','0','0','直播'), ('3','1','0','0','测试一下添加课程');
INSERT INTO `cmf_portal_tag_post` VALUES ('1','3','1','1');
INSERT INTO `cmf_post_lession` VALUES ('13',NULL,NULL,'古老师《消防工程师》专业知识','http://vod.acbkjg.com/a8f74f90d7d24af285a12ebd1ead2c7f/0b53f3ea305d48fd97f1480739d05c36-3d730545d094877a5a426324c2b862e1-od-S00000001-100000.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','a8f74f90d7d24af285a12ebd1ead2c7f','2596.363'), ('12',NULL,NULL,'1、建设工程监理案例分析（备考导学班）01','http://vod.acbkjg.com/d06cc650872749a39fab4a7794837950/63337898707345ceb61c6c897a03bf53-73c79573b31fc10e2ee7d2014210692e-od-S00000001-100000.m3u8','1',NULL,NULL,'1547790793',NULL,'60','0-1-6-60',NULL,NULL), ('11',NULL,NULL,'3、建设工程监理案例分析（备考导学班）02','http://vod.acbkjg.com/f0c4b600546e41cf88a2bd02c45fc5dd/c9ec12e871f043ef8257a7399da5198e-f7575568d4807d30eec234950d151869-od-S00000001-100000.m3u8','1',NULL,NULL,'1547790793',NULL,'60','0-1-6-60',NULL,NULL), ('10',NULL,NULL,'2、建设工程监理案例分析（备考导学班）02','http://vod.acbkjg.com/f0c4b600546e41cf88a2bd02c45fc5dd/c9ec12e871f043ef8257a7399da5198e-f7575568d4807d30eec234950d151869-od-S00000001-100000.m3u8','1',NULL,NULL,'1547790793',NULL,'60','0-1-6-60',NULL,NULL), ('9',NULL,NULL,'1、建设工程监理案例分析（备考导学班）01','http://vod.acbkjg.com/d06cc650872749a39fab4a7794837950/63337898707345ceb61c6c897a03bf53-73c79573b31fc10e2ee7d2014210692e-od-S00000001-100000.m3u8','1',NULL,NULL,'1547790793',NULL,'60','0-1-6-60',NULL,NULL), ('14',NULL,NULL,'杨老师《学历》专业知识','http://vod.acbkjg.com/22a86452b1bd4235970a7864b0e75c7b/774f06fa8042432db5befbf7e1cbf4f2-0ab3a4b5e7985ee47746f2675db1a8c0-od-S00000001-100000.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','22a86452b1bd4235970a7864b0e75c7b','1113.861'), ('15',NULL,NULL,'谢老师《建造师》基本知识','http://vod.acbkjg.com/3438b6a1e78c484dbb468010da6e398b/3eec50dd27a847e780eb8f3162baa2bb-fdaf0e17f7cd8d1f384c245b904d485e-od-S00000001-100000.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','3438b6a1e78c484dbb468010da6e398b','2061.444'), ('16',NULL,NULL,'谢向阳《微信营销》分享','http://vod.acbkjg.com/1484d8e84e0d45ef82a12afc16bf7181/17cc03f8308a43c7894608fe78435ad8-0e80728879304c02d39196f8dfb36179-od-S00000001-100000.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','1484d8e84e0d45ef82a12afc16bf7181','384.244'), ('17',NULL,NULL,'路老师《湖南职称》专业知识','http://vod.acbkjg.com/545bc0aa41624321901f929c65590d1d/e2bb2f44a0694f76b76294aeb45f0c4d-103da984ad305f20082ffcc58668cbc7-od-S00000001-100000.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','545bc0aa41624321901f929c65590d1d','840.168'), ('18',NULL,NULL,'梁老师《二级建造师》专业知识','http://vod.acbkjg.com/e71eabe0f8b2472fabfc9be857917398/10476d71302e4113903c80caf89cc185-b84083e703284820e39dae62c9be80d9-sd.m3u8','1',NULL,NULL,'1547795723',NULL,'62','0-2-8-62','e71eabe0f8b2472fabfc9be857917398','2755.907');
INSERT INTO `cmf_role` VALUES ('1','0','1','1329633709','1329633709','0','超级管理员','拥有网站最高管理员权限！'), ('2','0','1','1329633709','1329633709','0','普通管理员','权限由最高管理员分配！');
INSERT INTO `cmf_route` VALUES ('1','5000','1','2','portal/List/index?id=1','监理工程师'), ('2','4999','1','2','portal/Article/index?cid=1','监理工程师/:id'), ('3','5000','1','2','portal/List/index?id=2','安全工程师'), ('4','4999','1','2','portal/Article/index?cid=2','安全工程师/:id'), ('5','5000','1','2','portal/List/index?id=3','2019 BIM'), ('6','4999','1','2','portal/Article/index?cid=3','2019 BIM/:id'), ('7','5000','1','2','portal/List/index?id=4','二级造价师'), ('8','4999','1','2','portal/Article/index?cid=4','二级造价师/:id'), ('9','5000','1','2','portal/List/index?id=5','一级造价师'), ('10','4999','1','2','portal/Article/index?cid=5','一级造价师/:id'), ('11','5000','1','2','portal/List/index?id=6','案例分析'), ('12','4999','1','2','portal/Article/index?cid=6','案例分析/:id'), ('13','5000','1','2','portal/List/index?id=7','进度控制'), ('14','4999','1','2','portal/Article/index?cid=7','进度控制/:id'), ('15','5000','1','2','portal/List/index?id=8','安全法规'), ('16','4999','1','2','portal/Article/index?cid=8','安全法规/:id'), ('23','5000','1','2','portal/List/index?id=12','工程管理'), ('24','4999','1','2','portal/Article/index?cid=12','工程管理/:id'), ('25','5000','1','2','portal/List/index?id=13','BIM应用工程师《建筑》'), ('26','4999','1','2','portal/Article/index?cid=13','BIM应用工程师《建筑》/:id'), ('27','5000','1','2','portal/List/index?id=14','BIM应用工程师《结构》'), ('28','4999','1','2','portal/Article/index?cid=14','BIM应用工程师《结构》/:id'), ('29','5000','1','2','portal/List/index?id=15','科目'), ('30','4999','1','2','portal/Article/index?cid=15','科目/:id'), ('41','5000','1','2','portal/List/index?id=21','管理'), ('42','4999','1','2','portal/Article/index?cid=21','管理/:id'), ('43','5000','1','2','portal/List/index?id=22','法规'), ('44','4999','1','2','portal/Article/index?cid=22','法规/:id'), ('45','5000','1','2','portal/List/index?id=23','造价管理'), ('46','4999','1','2','portal/Article/index?cid=23','造价管理/:id'), ('47','5000','1','2','portal/List/index?id=24','造价案例分析'), ('48','4999','1','2','portal/Article/index?cid=24','造价案例分析/:id'), ('49','5000','1','2','portal/List/index?id=25','建设工程计价'), ('50','4999','1','2','portal/Article/index?cid=25','建设工程计价/:id'), ('53','5000','1','2','portal/List/index?id=27','技术与计量（土建）'), ('54','4999','1','2','portal/Article/index?cid=27','技术与计量（土建）/:id'), ('57','5000','1','2','portal/List/index?id=29','BIM应用工程师《机电》'), ('58','4999','1','2','portal/Article/index?cid=29','BIM应用工程师《机电》/:id'), ('59','5000','1','2','portal/List/index?id=30','BIM'), ('60','4999','1','2','portal/Article/index?cid=30','BIM/:id'), ('61','5000','1','2','portal/List/index?id=31','一级消防工程师'), ('62','4999','1','2','portal/Article/index?cid=31','一级消防工程师/:id'), ('63','5000','1','2','portal/List/index?id=32','公路水运检测师'), ('64','4999','1','2','portal/Article/index?cid=32','公路水运检测师/:id'), ('65','5000','1','2','portal/List/index?id=33','湖南中级工程师'), ('66','4999','1','2','portal/Article/index?cid=33','湖南中级工程师/:id'), ('67','5000','1','2','portal/List/index?id=34','二级建造师'), ('68','4999','1','2','portal/Article/index?cid=34','二级建造师/:id'), ('69','5000','1','2','portal/List/index?id=35','一级建造师'), ('70','4999','1','2','portal/Article/index?cid=35','一级建造师/:id'), ('71','5000','1','2','portal/List/index?id=36','技术与计量（安装）'), ('72','4999','1','2','portal/Article/index?cid=36','技术与计量（安装）/:id'), ('73','5000','1','2','portal/List/index?id=37','BIM建模工程师'), ('74','4999','1','2','portal/Article/index?cid=37','BIM建模工程师/:id');
INSERT INTO `cmf_theme` VALUES ('1','0','0','0','0','simpleboot3','simpleboot3','1.0.2','http://demo.thinkcmf.com','','ThinkCMF','http://www.thinkcmf.com','zh-cn','ThinkCMF模板','ThinkCMF默认模板');
INSERT INTO `cmf_theme_file` VALUES ('1','0','10','simpleboot3','文章页','portal/Article/index','portal/article','文章页模板文件','{\"vars\":{\"hot_articles_category_id\":{\"title\":\"Hot Articles\\u5206\\u7c7bID\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}','{\"vars\":{\"hot_articles_category_id\":{\"title\":\"Hot Articles\\u5206\\u7c7bID\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}',NULL), ('2','0','10','simpleboot3','联系我们页','portal/Page/index','portal/contact','联系我们页模板文件','{\"vars\":{\"baidu_map_info_window_text\":{\"title\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57\",\"name\":\"baidu_map_info_window_text\",\"value\":\"ThinkCMF<br\\/><span class=\'\'>\\u5730\\u5740\\uff1a\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def2601\\u53f7<\\/span>\",\"type\":\"text\",\"tip\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57,\\u652f\\u6301\\u7b80\\u5355html\\u4ee3\\u7801\",\"rule\":[]},\"company_location\":{\"title\":\"\\u516c\\u53f8\\u5750\\u6807\",\"value\":\"\",\"type\":\"location\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_cn\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\",\"value\":\"\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def0001\\u53f7\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_en\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"NO.0001 Xie Tu Road, Shanghai China\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"email\":{\"title\":\"\\u516c\\u53f8\\u90ae\\u7bb1\",\"value\":\"catman@thinkcmf.com\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_cn\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\",\"value\":\"021 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_en\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"+8621 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"qq\":{\"title\":\"\\u8054\\u7cfbQQ\",\"value\":\"478519726\",\"type\":\"text\",\"tip\":\"\\u591a\\u4e2a QQ\\u4ee5\\u82f1\\u6587\\u9017\\u53f7\\u9694\\u5f00\",\"rule\":{\"require\":true}}}}','{\"vars\":{\"baidu_map_info_window_text\":{\"title\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57\",\"name\":\"baidu_map_info_window_text\",\"value\":\"ThinkCMF<br\\/><span class=\'\'>\\u5730\\u5740\\uff1a\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def2601\\u53f7<\\/span>\",\"type\":\"text\",\"tip\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57,\\u652f\\u6301\\u7b80\\u5355html\\u4ee3\\u7801\",\"rule\":[]},\"company_location\":{\"title\":\"\\u516c\\u53f8\\u5750\\u6807\",\"value\":\"\",\"type\":\"location\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_cn\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\",\"value\":\"\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def0001\\u53f7\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_en\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"NO.0001 Xie Tu Road, Shanghai China\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"email\":{\"title\":\"\\u516c\\u53f8\\u90ae\\u7bb1\",\"value\":\"catman@thinkcmf.com\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_cn\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\",\"value\":\"021 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_en\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"+8621 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"qq\":{\"title\":\"\\u8054\\u7cfbQQ\",\"value\":\"478519726\",\"type\":\"text\",\"tip\":\"\\u591a\\u4e2a QQ\\u4ee5\\u82f1\\u6587\\u9017\\u53f7\\u9694\\u5f00\",\"rule\":{\"require\":true}}}}',NULL), ('3','0','5','simpleboot3','首页','portal/Index/index','portal/index','首页模板文件','{\"vars\":{\"top_slide\":{\"title\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"admin\\/Slide\\/index\",\"multi\":false},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"tip\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"rule\":{\"require\":true}}},\"widgets\":{\"features\":{\"title\":\"\\u5feb\\u901f\\u4e86\\u89e3ThinkCMF\",\"display\":\"1\",\"vars\":{\"sub_title\":{\"title\":\"\\u526f\\u6807\\u9898\",\"value\":\"Quickly understand the ThinkCMF\",\"type\":\"text\",\"placeholder\":\"\\u8bf7\\u8f93\\u5165\\u526f\\u6807\\u9898\",\"tip\":\"\",\"rule\":{\"require\":true}},\"features\":{\"title\":\"\\u7279\\u6027\\u4ecb\\u7ecd\",\"value\":[{\"title\":\"MVC\\u5206\\u5c42\\u6a21\\u5f0f\",\"icon\":\"bars\",\"content\":\"\\u4f7f\\u7528MVC\\u5e94\\u7528\\u7a0b\\u5e8f\\u88ab\\u5206\\u6210\\u4e09\\u4e2a\\u6838\\u5fc3\\u90e8\\u4ef6\\uff1a\\u6a21\\u578b\\uff08M\\uff09\\u3001\\u89c6\\u56fe\\uff08V\\uff09\\u3001\\u63a7\\u5236\\u5668\\uff08C\\uff09\\uff0c\\u4ed6\\u4e0d\\u662f\\u4e00\\u4e2a\\u65b0\\u7684\\u6982\\u5ff5\\uff0c\\u53ea\\u662fThinkCMF\\u5c06\\u5176\\u53d1\\u6325\\u5230\\u4e86\\u6781\\u81f4\\u3002\"},{\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"group\",\"content\":\"ThinkCMF\\u5185\\u7f6e\\u4e86\\u7075\\u6d3b\\u7684\\u7528\\u6237\\u7ba1\\u7406\\u65b9\\u5f0f\\uff0c\\u5e76\\u53ef\\u76f4\\u63a5\\u4e0e\\u7b2c\\u4e09\\u65b9\\u7ad9\\u70b9\\u8fdb\\u884c\\u4e92\\u8054\\u4e92\\u901a\\uff0c\\u5982\\u679c\\u4f60\\u613f\\u610f\\u751a\\u81f3\\u53ef\\u4ee5\\u5bf9\\u5355\\u4e2a\\u7528\\u6237\\u6216\\u7fa4\\u4f53\\u7528\\u6237\\u7684\\u884c\\u4e3a\\u8fdb\\u884c\\u8bb0\\u5f55\\u53ca\\u5206\\u4eab\\uff0c\\u4e3a\\u60a8\\u7684\\u8fd0\\u8425\\u51b3\\u7b56\\u63d0\\u4f9b\\u6709\\u6548\\u53c2\\u8003\\u6570\\u636e\\u3002\"},{\"title\":\"\\u4e91\\u7aef\\u90e8\\u7f72\",\"icon\":\"cloud\",\"content\":\"\\u901a\\u8fc7\\u9a71\\u52a8\\u7684\\u65b9\\u5f0f\\u53ef\\u4ee5\\u8f7b\\u677e\\u652f\\u6301\\u4e91\\u5e73\\u53f0\\u7684\\u90e8\\u7f72\\uff0c\\u8ba9\\u4f60\\u7684\\u7f51\\u7ad9\\u65e0\\u7f1d\\u8fc1\\u79fb\\uff0c\\u5185\\u7f6e\\u5df2\\u7ecf\\u652f\\u6301SAE\\u3001BAE\\uff0c\\u6b63\\u5f0f\\u7248\\u5c06\\u5bf9\\u4e91\\u7aef\\u90e8\\u7f72\\u8fdb\\u884c\\u8fdb\\u4e00\\u6b65\\u4f18\\u5316\\u3002\"},{\"title\":\"\\u5b89\\u5168\\u7b56\\u7565\",\"icon\":\"heart\",\"content\":\"\\u63d0\\u4f9b\\u7684\\u7a33\\u5065\\u7684\\u5b89\\u5168\\u7b56\\u7565\\uff0c\\u5305\\u62ec\\u5907\\u4efd\\u6062\\u590d\\uff0c\\u5bb9\\u9519\\uff0c\\u9632\\u6cbb\\u6076\\u610f\\u653b\\u51fb\\u767b\\u9646\\uff0c\\u7f51\\u9875\\u9632\\u7be1\\u6539\\u7b49\\u591a\\u9879\\u5b89\\u5168\\u7ba1\\u7406\\u529f\\u80fd\\uff0c\\u4fdd\\u8bc1\\u7cfb\\u7edf\\u5b89\\u5168\\uff0c\\u53ef\\u9760\\uff0c\\u7a33\\u5b9a\\u7684\\u8fd0\\u884c\\u3002\"},{\"title\":\"\\u5e94\\u7528\\u6a21\\u5757\\u5316\",\"icon\":\"cubes\",\"content\":\"\\u63d0\\u51fa\\u5168\\u65b0\\u7684\\u5e94\\u7528\\u6a21\\u5f0f\\u8fdb\\u884c\\u6269\\u5c55\\uff0c\\u4e0d\\u7ba1\\u662f\\u4f60\\u5f00\\u53d1\\u4e00\\u4e2a\\u5c0f\\u529f\\u80fd\\u8fd8\\u662f\\u4e00\\u4e2a\\u5168\\u65b0\\u7684\\u7ad9\\u70b9\\uff0c\\u5728ThinkCMF\\u4e2d\\u4f60\\u53ea\\u662f\\u589e\\u52a0\\u4e86\\u4e00\\u4e2aAPP\\uff0c\\u6bcf\\u4e2a\\u72ec\\u7acb\\u8fd0\\u884c\\u4e92\\u4e0d\\u5f71\\u54cd\\uff0c\\u4fbf\\u4e8e\\u7075\\u6d3b\\u6269\\u5c55\\u548c\\u4e8c\\u6b21\\u5f00\\u53d1\\u3002\"},{\"title\":\"\\u514d\\u8d39\\u5f00\\u6e90\",\"icon\":\"certificate\",\"content\":\"\\u4ee3\\u7801\\u9075\\u5faaApache2\\u5f00\\u6e90\\u534f\\u8bae\\uff0c\\u514d\\u8d39\\u4f7f\\u7528\\uff0c\\u5bf9\\u5546\\u4e1a\\u7528\\u6237\\u4e5f\\u65e0\\u4efb\\u4f55\\u9650\\u5236\\u3002\"}],\"type\":\"array\",\"item\":{\"title\":{\"title\":\"\\u6807\\u9898\",\"value\":\"\",\"type\":\"text\",\"rule\":{\"require\":true}},\"icon\":{\"title\":\"\\u56fe\\u6807\",\"value\":\"\",\"type\":\"text\"},\"content\":{\"title\":\"\\u63cf\\u8ff0\",\"value\":\"\",\"type\":\"textarea\"}},\"tip\":\"\"}}},\"last_news\":{\"title\":\"\\u6700\\u65b0\\u8d44\\u8baf\",\"display\":\"1\",\"vars\":{\"last_news_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/Category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}','{\"vars\":{\"top_slide\":{\"title\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"admin\\/Slide\\/index\",\"multi\":false},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"tip\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"rule\":{\"require\":true}}},\"widgets\":{\"features\":{\"title\":\"\\u5feb\\u901f\\u4e86\\u89e3ThinkCMF\",\"display\":\"1\",\"vars\":{\"sub_title\":{\"title\":\"\\u526f\\u6807\\u9898\",\"value\":\"Quickly understand the ThinkCMF\",\"type\":\"text\",\"placeholder\":\"\\u8bf7\\u8f93\\u5165\\u526f\\u6807\\u9898\",\"tip\":\"\",\"rule\":{\"require\":true}},\"features\":{\"title\":\"\\u7279\\u6027\\u4ecb\\u7ecd\",\"value\":[{\"title\":\"MVC\\u5206\\u5c42\\u6a21\\u5f0f\",\"icon\":\"bars\",\"content\":\"\\u4f7f\\u7528MVC\\u5e94\\u7528\\u7a0b\\u5e8f\\u88ab\\u5206\\u6210\\u4e09\\u4e2a\\u6838\\u5fc3\\u90e8\\u4ef6\\uff1a\\u6a21\\u578b\\uff08M\\uff09\\u3001\\u89c6\\u56fe\\uff08V\\uff09\\u3001\\u63a7\\u5236\\u5668\\uff08C\\uff09\\uff0c\\u4ed6\\u4e0d\\u662f\\u4e00\\u4e2a\\u65b0\\u7684\\u6982\\u5ff5\\uff0c\\u53ea\\u662fThinkCMF\\u5c06\\u5176\\u53d1\\u6325\\u5230\\u4e86\\u6781\\u81f4\\u3002\"},{\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"group\",\"content\":\"ThinkCMF\\u5185\\u7f6e\\u4e86\\u7075\\u6d3b\\u7684\\u7528\\u6237\\u7ba1\\u7406\\u65b9\\u5f0f\\uff0c\\u5e76\\u53ef\\u76f4\\u63a5\\u4e0e\\u7b2c\\u4e09\\u65b9\\u7ad9\\u70b9\\u8fdb\\u884c\\u4e92\\u8054\\u4e92\\u901a\\uff0c\\u5982\\u679c\\u4f60\\u613f\\u610f\\u751a\\u81f3\\u53ef\\u4ee5\\u5bf9\\u5355\\u4e2a\\u7528\\u6237\\u6216\\u7fa4\\u4f53\\u7528\\u6237\\u7684\\u884c\\u4e3a\\u8fdb\\u884c\\u8bb0\\u5f55\\u53ca\\u5206\\u4eab\\uff0c\\u4e3a\\u60a8\\u7684\\u8fd0\\u8425\\u51b3\\u7b56\\u63d0\\u4f9b\\u6709\\u6548\\u53c2\\u8003\\u6570\\u636e\\u3002\"},{\"title\":\"\\u4e91\\u7aef\\u90e8\\u7f72\",\"icon\":\"cloud\",\"content\":\"\\u901a\\u8fc7\\u9a71\\u52a8\\u7684\\u65b9\\u5f0f\\u53ef\\u4ee5\\u8f7b\\u677e\\u652f\\u6301\\u4e91\\u5e73\\u53f0\\u7684\\u90e8\\u7f72\\uff0c\\u8ba9\\u4f60\\u7684\\u7f51\\u7ad9\\u65e0\\u7f1d\\u8fc1\\u79fb\\uff0c\\u5185\\u7f6e\\u5df2\\u7ecf\\u652f\\u6301SAE\\u3001BAE\\uff0c\\u6b63\\u5f0f\\u7248\\u5c06\\u5bf9\\u4e91\\u7aef\\u90e8\\u7f72\\u8fdb\\u884c\\u8fdb\\u4e00\\u6b65\\u4f18\\u5316\\u3002\"},{\"title\":\"\\u5b89\\u5168\\u7b56\\u7565\",\"icon\":\"heart\",\"content\":\"\\u63d0\\u4f9b\\u7684\\u7a33\\u5065\\u7684\\u5b89\\u5168\\u7b56\\u7565\\uff0c\\u5305\\u62ec\\u5907\\u4efd\\u6062\\u590d\\uff0c\\u5bb9\\u9519\\uff0c\\u9632\\u6cbb\\u6076\\u610f\\u653b\\u51fb\\u767b\\u9646\\uff0c\\u7f51\\u9875\\u9632\\u7be1\\u6539\\u7b49\\u591a\\u9879\\u5b89\\u5168\\u7ba1\\u7406\\u529f\\u80fd\\uff0c\\u4fdd\\u8bc1\\u7cfb\\u7edf\\u5b89\\u5168\\uff0c\\u53ef\\u9760\\uff0c\\u7a33\\u5b9a\\u7684\\u8fd0\\u884c\\u3002\"},{\"title\":\"\\u5e94\\u7528\\u6a21\\u5757\\u5316\",\"icon\":\"cubes\",\"content\":\"\\u63d0\\u51fa\\u5168\\u65b0\\u7684\\u5e94\\u7528\\u6a21\\u5f0f\\u8fdb\\u884c\\u6269\\u5c55\\uff0c\\u4e0d\\u7ba1\\u662f\\u4f60\\u5f00\\u53d1\\u4e00\\u4e2a\\u5c0f\\u529f\\u80fd\\u8fd8\\u662f\\u4e00\\u4e2a\\u5168\\u65b0\\u7684\\u7ad9\\u70b9\\uff0c\\u5728ThinkCMF\\u4e2d\\u4f60\\u53ea\\u662f\\u589e\\u52a0\\u4e86\\u4e00\\u4e2aAPP\\uff0c\\u6bcf\\u4e2a\\u72ec\\u7acb\\u8fd0\\u884c\\u4e92\\u4e0d\\u5f71\\u54cd\\uff0c\\u4fbf\\u4e8e\\u7075\\u6d3b\\u6269\\u5c55\\u548c\\u4e8c\\u6b21\\u5f00\\u53d1\\u3002\"},{\"title\":\"\\u514d\\u8d39\\u5f00\\u6e90\",\"icon\":\"certificate\",\"content\":\"\\u4ee3\\u7801\\u9075\\u5faaApache2\\u5f00\\u6e90\\u534f\\u8bae\\uff0c\\u514d\\u8d39\\u4f7f\\u7528\\uff0c\\u5bf9\\u5546\\u4e1a\\u7528\\u6237\\u4e5f\\u65e0\\u4efb\\u4f55\\u9650\\u5236\\u3002\"}],\"type\":\"array\",\"item\":{\"title\":{\"title\":\"\\u6807\\u9898\",\"value\":\"\",\"type\":\"text\",\"rule\":{\"require\":true}},\"icon\":{\"title\":\"\\u56fe\\u6807\",\"value\":\"\",\"type\":\"text\"},\"content\":{\"title\":\"\\u63cf\\u8ff0\",\"value\":\"\",\"type\":\"textarea\"}},\"tip\":\"\"}}},\"last_news\":{\"title\":\"\\u6700\\u65b0\\u8d44\\u8baf\",\"display\":\"1\",\"vars\":{\"last_news_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/Category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}',NULL), ('4','0','10','simpleboot3','文章列表页','portal/List/index','portal/list','文章列表模板文件','{\"vars\":[],\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}','{\"vars\":[],\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}',NULL), ('5','0','10','simpleboot3','单页面','portal/Page/index','portal/page','单页面模板文件','{\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}','{\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}',NULL), ('6','0','10','simpleboot3','搜索页面','portal/search/index','portal/search','搜索模板文件','{\"vars\":{\"varName1\":{\"title\":\"\\u70ed\\u95e8\\u641c\\u7d22\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\\u8fd9\\u662f\\u4e00\\u4e2atext\",\"rule\":{\"require\":true}}}}','{\"vars\":{\"varName1\":{\"title\":\"\\u70ed\\u95e8\\u641c\\u7d22\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\\u8fd9\\u662f\\u4e00\\u4e2atext\",\"rule\":{\"require\":true}}}}',NULL), ('7','1','0','simpleboot3','模板全局配置','public/Config','public/config','模板全局配置文件','{\"vars\":{\"enable_mobile\":{\"title\":\"\\u624b\\u673a\\u6ce8\\u518c\",\"value\":1,\"type\":\"select\",\"options\":{\"1\":\"\\u5f00\\u542f\",\"0\":\"\\u5173\\u95ed\"},\"tip\":\"\"}}}','{\"vars\":{\"enable_mobile\":{\"title\":\"\\u624b\\u673a\\u6ce8\\u518c\",\"value\":1,\"type\":\"select\",\"options\":{\"1\":\"\\u5f00\\u542f\",\"0\":\"\\u5173\\u95ed\"},\"tip\":\"\"}}}',NULL), ('8','1','1','simpleboot3','导航条','public/Nav','public/nav','导航条模板文件','{\"vars\":{\"company_name\":{\"title\":\"\\u516c\\u53f8\\u540d\\u79f0\",\"name\":\"company_name\",\"value\":\"ThinkCMF\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}','{\"vars\":{\"company_name\":{\"title\":\"\\u516c\\u53f8\\u540d\\u79f0\",\"name\":\"company_name\",\"value\":\"ThinkCMF\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}',NULL);
INSERT INTO `cmf_user` VALUES ('1','1','0','0','1547859556','0','0','0.00','1547518825','1','admin','###5146626d26115cad0e639cf68cfb08cb','admin','18364120@qq.com','','','','127.0.0.1','','',NULL), ('2','2','0','0','1547523363','0','0','0.00','1547523363','1','','###ae31e245e5d66ff0cf7b9fa86ba09e5d','','','','','','127.0.0.1','','13937174487',NULL);
INSERT INTO `cmf_user_action` VALUES ('1','1','1','1','2','1','用户登录','login','user','');
INSERT INTO `cmf_user_token` VALUES ('1','1','1563070884','1547518884','c460db8e9533534869629247a3a56341c460db8e9533534869629247a3a56341','web'), ('2','2','1563075363','1547523363','68f06ee238840ff0dbb0d720890ee33c68f06ee238840ff0dbb0d720890ee33c','web');
INSERT INTO `cmf_verification_code` VALUES ('1','1','1547523353','1547525153','944299','13937174487');
