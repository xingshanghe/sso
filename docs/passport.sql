-- ----------------------------
-- Table structure for accounts
-- ----------------------------
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增(索贝账号ID)',
  `username` varchar(64) NOT NULL COMMENT '登陆名称',
  `mobile` varchar(16) NOT NULL COMMENT '登陆手机号码',
  `email` varchar(64) NOT NULL COMMENT '登陆email',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(12)  NOT NULL DEFAULT '' COMMENT '密码混淆码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0表示未激活，1表示已激活',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号表';

-- ----------------------------
-- Table structure for account_infos
-- ----------------------------
DROP TABLE IF EXISTS `account_infos`;
CREATE TABLE `account_infos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `account_id` int unsigned NOT NULL DEFAULT 0 COMMENT '账号id(索贝账号ID)',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `birthday` varchar(64) DEFAULT NULL COMMENT '生日',
  `sex` enum('male','female') NOT NULL DEFAULT 'male' COMMENT '性别，默认男',
  `organization` varchar(128)  NOT NULL DEFAULT '' COMMENT '公司信息',
  `login_ip` int NOT NULL DEFAULT 0 COMMENT '上次登陆ip',
  `login_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '上次登陆时间',
  `create_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '记录录创建时间',
  `modified_time` datetime NOT NULL DEFAULT '2003-01-01 00:00:00' COMMENT '记录修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号基本信息表';

-- ----------------------------
-- Table structure for account_info_fix
-- ----------------------------
DROP TABLE IF EXISTS `account_info_fix`;
CREATE TABLE `account_info_fix` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `account_id` int unsigned NOT NULL DEFAULT 0 COMMENT '账号id(索贝账号ID)',
  `old_uid` int unsigned NOT NULL DEFAULT 0 COMMENT '老系统uid',
  `source` tinyint(2) NOT NULL DEFAULT 0 COMMENT '数据来源:0:系统;1,索贝学院;2一起编;',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='账号基本信息表';

-- ----------------------------
-- Table structure for services
-- ----------------------------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id 自增',
  `name` varchar(64) NOT NULL COMMENT '服务(业务系统)名称',
  `icon` varchar(256) NOT NULL COMMENT '服务(业务系统)图标名称',
  `url` varchar(256) NOT NULL COMMENT '服务(业务系统)访问地址',
  `description` TEXT NOT NULL COMMENT '服务描述',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0表示异常，1表示正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='索贝服务';