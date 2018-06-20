/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Version : 50621
 Source Host           : localhost
 Source Database       : passport

 Target Server Version : 50621
 File Encoding         : utf-8

 Date: 06/02/2015 16:57:25 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `account_info_fix`
-- ----------------------------
DROP TABLE IF EXISTS `account_info_fix`;
CREATE TABLE `account_info_fix` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `account_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '账号id(索贝账号ID)',
  `old_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '老系统uid',
  `source` tinyint(2) NOT NULL DEFAULT '0' COMMENT '数据来源:0:系统;1,索贝学院;2一起编;',
  `salt` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '密码混淆码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号基本信息表';

-- ----------------------------
--  Table structure for `account_infos`
-- ----------------------------
DROP TABLE IF EXISTS `account_infos`;
CREATE TABLE `account_infos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `account_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '账号id(索贝账号ID)',
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '真实姓名',
  `birthday` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sex` enum('male','female') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'male' COMMENT '性别，默认男',
  `organization` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '公司信息',
  `login_ip` int(11) NOT NULL DEFAULT '0' COMMENT '上次登陆ip',
  `login_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '上次登陆时间',
  `create_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '记录录创建时间',
  `modified_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '记录修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号基本信息表';

-- ----------------------------
--  Records of `account_infos`
-- ----------------------------
BEGIN;
INSERT INTO `account_infos` VALUES ('1', '1', null, '邢尚合1', '1985-01-22', 'male', '索贝数码科技股份有限公司', '0', '2003-01-01 00:00:00', '2003-01-01 00:00:00', '2015-05-28 14:21:34');
COMMIT;

-- ----------------------------
--  Table structure for `accounts`
-- ----------------------------
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增(索贝账号ID)',
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '登陆名称',
  `mobile` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT '登陆手机号码',
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '登陆email',
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0表示未激活，1表示已激活',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号表';

-- ----------------------------
--  Records of `accounts`
-- ----------------------------
BEGIN;
INSERT INTO `accounts` VALUES ('1', 'admin', '18010636836', 'xingshanghe@sobey.com', 'admin', '', '1'), ('7', 'xingshanghe', '18010636837', 'xsh@sobey.com', '123456', '', '0'), ('9', 'test', '18010636899', 'xshasd1@sobey.com', '123456', '', '0');
COMMIT;

-- ----------------------------
--  Table structure for `services`
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '服务(业务系统)名称',
  `icon` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT '服务(业务系统)图标名称',
  `url` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT '服务(业务系统)访问地址',
  `description` text COLLATE utf8_unicode_ci NOT NULL COMMENT '服务描述',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0表示异常，1表示正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='索贝服务';

-- ----------------------------
--  Records of `services`
-- ----------------------------
BEGIN;
INSERT INTO `services` VALUES ('1', '索贝云官网', 'https://account.xiaomi.com/static/res/8323d19/passport/acc-2014/img/icon_service.png', 'http://www.sobeyyun.com', '索贝云服务官方网站', '1'), ('2', '索贝云商城', 'https://account.xiaomi.com/static/res/8323d19/passport/acc-2014/img/icon_service.png', 'http://mall.sobey.com', '索贝商城，索贝产品正品销售网站', '1'), ('3', '索贝学院', 'https://account.xiaomi.com/static/res/8323d19/passport/acc-2014/img/icon_service.png', 'http://college.sobey.com', '索贝学院，索贝非编工具培训基地', '1'), ('4', '一起编', 'https://account.xiaomi.com/static/res/8323d19/passport/acc-2014/img/icon_service.png', 'http://www.yiqibian.com', '全国最大的视频社交平台', '1'), ('5', 'MediaX官网', 'https://account.xiaomi.com/static/res/8323d19/passport/acc-2014/img/icon_service.png', 'http://www.imediax.cn', 'MediaX官方网站', '1');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
