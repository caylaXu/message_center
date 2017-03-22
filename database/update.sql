CREATE TABLE IF NOT EXISTS `MessageLogNew` (
  `Id` int(9) NOT NULL AUTO_INCREMENT,
  `Type` enum('app_push','sms','email') NOT NULL COMMENT 'app推送，短信，邮件',
  `ToUsers` varchar(2000) NOT NULL COMMENT '推送给',
  `Content` varchar(2000) NOT NULL COMMENT '消息内容',
  `Attr` varchar(2000) NOT NULL COMMENT '其他值',
  `ReceiptTime` int(11) NOT NULL COMMENT '推送时间',
  `SendTime` int(11) NOT NULL COMMENT '实际推送时间',
  `Response` varchar(2000) NOT NULL COMMENT '处理结果',
  PRIMARY KEY (`Id`),
  KEY `ReceiptTime` (`ReceiptTime`),
  KEY `Type` (`Type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息记录表' AUTO_INCREMENT=1 ;

CREATE TABLE `RltTimeTable` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `MinTime` int(11) NOT NULL COMMENT '最小时间',
  `MaxTime` int(11) NOT NULL COMMENT '最大时间',
  `TableName` varchar(16) NOT NULL DEFAULT '' COMMENT '表名',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='时间表名映射表';

