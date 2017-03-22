/*
Date: 2016-02-18 09:17:00
*/

CREATE DATABASE IF NOT EXISTS Message DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

grant select,insert,update,delete on Message.* to 'message'@'%' identified by 'zar3Is#k';
flush privileges;

SET FOREIGN_KEY_CHECKS=0;

use Message;

-- ----------------------------
-- Table structure for Telephone
-- ----------------------------
CREATE TABLE IF NOT EXISTS `Telephone` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '电话号码标识',
  `Telephone` char(11) NOT NULL DEFAULT '0' COMMENT '电话号码',
  `Owner` varchar(63) NOT NULL DEFAULT '' COMMENT '所属人',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Telephone` (`Telephone`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='电话号码表';

-- ----------------------------
-- Table structure for TelTpl
-- ----------------------------
CREATE TABLE IF NOT EXISTS `TelTpl` (
  `TelephoneId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '电话号码标识',
  `TemplateId` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '消息模板标识',
  PRIMARY KEY (`TelephoneId`,`TemplateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='电话消息关联表';

-- ----------------------------
-- Table structure for Template
-- ----------------------------
CREATE TABLE IF NOT EXISTS `Template` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '短信模板标识',
  `Name` varchar(63) NOT NULL DEFAULT '' COMMENT '模板名称',
  `Message` varchar(255) NOT NULL DEFAULT '' COMMENT '消息模板',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Name` (`Name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='消息模板表';

insert into Template values(1, 'template', '{#0}');