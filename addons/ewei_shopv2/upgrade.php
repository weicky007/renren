<?php
if(!pdo_tableexists('ims_ewei_shop_live_goods')){
$sql1="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_goods` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`goodsid`  int(11) NOT NULL DEFAULT 0 ,
`liveid`  int(11) NOT NULL DEFAULT 0 ,
`liveprice`  decimal(10,2) NOT NULL DEFAULT 0.00 ,
`minliveprice`  decimal(10,2) NOT NULL DEFAULT 0.00 ,
`maxliveprice`  decimal(10,2) NOT NULL DEFAULT 0.00 ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql1);
}

if(!pdo_tableexists('ims_ewei_shop_live_goods_option')){
$sql2="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_goods_option` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`goodsid`  int(11) NOT NULL ,
`optionid`  int(11) NOT NULL DEFAULT 0 ,
`liveid`  int(11) NOT NULL DEFAULT 0 ,
`liveprice`  decimal(10,2) NOT NULL DEFAULT 0.00 ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql2);
}

if(!pdo_tableexists('ims_ewei_shop_live_status')){
$sql3="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_status` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`uniacid`  int(11) NOT NULL DEFAULT 0 ,
`roomid`  int(11) NOT NULL DEFAULT 0 ,
`starttime`  int(11) NOT NULL DEFAULT 0 ,
`endtime`  int(11) NOT NULL DEFAULT 0 ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql3);
}

if(!pdo_tableexists('ims_ewei_shop_sendticket')){
$sql4="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_sendticket` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `cpid` varchar(200) NOT NULL,
  `expiration` int(11) NOT NULL DEFAULT '0',
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '新人礼包',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql4);
}

if(!pdo_tableexists('ims_ewei_shop_sendticket_draw')){
$sql4="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_sendticket_draw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `cpid` varchar(50) NOT NULL,
  `openid` varchar(200) NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql4);
}

if(!pdo_tableexists('ims_ewei_shop_sendticket_share')){
$sql5="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_sendticket_share` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `sharetitle` varchar(255) NOT NULL,
  `shareicon` varchar(255) DEFAULT NULL,
  `sharedesc` varchar(255) DEFAULT NULL,
  `expiration` int(11) NOT NULL DEFAULT '0',
  `starttime` int(11) DEFAULT NULL,
  `endtime` int(11) DEFAULT NULL,
  `paycpid1` int(11) DEFAULT NULL,
  `paycpid2` int(11) DEFAULT NULL,
  `paycpid3` int(11) DEFAULT NULL,
  `paycpnum1` int(11) DEFAULT NULL,
  `paycpnum2` int(11) DEFAULT NULL,
  `paycpnum3` int(11) DEFAULT NULL,
  `sharecpid1` int(11) DEFAULT NULL,
  `sharecpid2` int(11) DEFAULT NULL,
  `sharecpid3` int(11) DEFAULT NULL,
  `sharecpnum1` int(11) DEFAULT NULL,
  `sharecpnum2` int(11) DEFAULT NULL,
  `sharecpnum3` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL,
  `order` int(11) DEFAULT NULL,
  `enough` decimal(10,2) DEFAULT NULL,
  `issync` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql5);
}

if(!pdo_tableexists('ims_ewei_shop_newstore_category')){
$sql6="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_newstore_category` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`uniacid`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql6);
}

if(!pdo_tableexists('ims_ewei_shop_live')){
$sql7="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `merchid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `livetype` tinyint(3) NOT NULL DEFAULT '0',
  `liveidentity` varchar(50) NOT NULL,
  `screen` tinyint(3) NOT NULL DEFAULT '0',
  `goodsid` varchar(255) NOT NULL,
  `category` int(11) NOT NULL DEFAULT '0',
  `url` varchar(1000) NOT NULL,
  `thumb` varchar(1000) NOT NULL,
  `hot` tinyint(3) NOT NULL DEFAULT '0',
  `recommend` tinyint(3) NOT NULL DEFAULT '0',
  `living` tinyint(3) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `livetime` int(10) NOT NULL DEFAULT '0',
  `lastlivetime` int(11) NOT NULL DEFAULT '0',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `introduce` text NOT NULL,
  `packetmoney` decimal(10,2) NOT NULL DEFAULT '0.00',
  `packettotal` int(11) NOT NULL DEFAULT '0',
  `packetprice` decimal(10,2) NOT NULL DEFAULT '0.00',
  `packetdes` varchar(255) NOT NULL,
  `couponid` varchar(255) NOT NULL,
  `share_title` varchar(255) NOT NULL,
  `share_icon` varchar(1000) NOT NULL,
  `share_desc` text NOT NULL,
  `share_url` varchar(1000) NOT NULL DEFAULT '',
  `subscribe` int(11) NOT NULL DEFAULT '0',
  `subscribenotice` tinyint(3) NOT NULL DEFAULT '0',
  `visit` int(11) NOT NULL DEFAULT '0',
  `video` varchar(1000) NOT NULL DEFAULT '',
  `covertype` tinyint(3) NOT NULL DEFAULT '0',
  `cover` varchar(1000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_merchid` (`merchid`),
  KEY `idx_category` (`category`),
  KEY `idx_hot` (`hot`),
  KEY `idx_recommend` (`recommend`),
  KEY `idx_living` (`living`),
  KEY `idx_status` (`status`),
  KEY `idx_livetime` (`livetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql7);
}

if(!pdo_tableexists('ims_ewei_shop_live_adv')){
$sql8="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_adv` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `merchid` int(11) NOT NULL DEFAULT '0',
  `advname` varchar(50) DEFAULT '',
  `link` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `displayorder` int(11) DEFAULT '0',
  `enabled` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_enabled` (`enabled`),
  KEY `idx_displayorder` (`displayorder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql8);
}

if(!pdo_tableexists('ims_ewei_shop_live_category')){
$sql9="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `name` varchar(50) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `enabled` tinyint(1) DEFAULT '1',
  `advimg` varchar(255) DEFAULT '',
  `advurl` varchar(500) DEFAULT '',
  `isrecommand` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_enabled` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql9);
}

if(!pdo_tableexists('ims_ewei_shop_live_coupon')){
$sql10="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `roomid` int(11) NOT NULL DEFAULT '0',
  `couponid` int(11) NOT NULL DEFAULT '0',
  `coupontotal` int(11) NOT NULL DEFAULT '0',
  `couponlimit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_roomid` (`roomid`),
  KEY `idx_couponid` (`couponid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql10);
}

if(!pdo_tableexists('ims_ewei_shop_live_favorite')){
$sql11="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `roomid` int(11) NOT NULL DEFAULT '0',
  `openid` tinytext NOT NULL,
  `deleted` tinyint(3) NOT NULL DEFAULT '0',
  `createtime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_roomid` (`roomid`),
  KEY `idx_deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql11);
}

if(!pdo_tableexists('ims_ewei_shop_live_setting')){
$sql12="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `ismember` tinyint(3) NOT NULL DEFAULT '0',
  `share_title` varchar(255) NOT NULL,
  `share_icon` varchar(1000) NOT NULL,
  `share_desc` varchar(255) NOT NULL,
  `share_url` varchar(255) NOT NULL,
  `livenoticetime` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_ismember` (`ismember`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql12);
}

if(!pdo_tableexists('ims_ewei_shop_live_view')){
$sql13="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_live_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL,
  `roomid` int(11) NOT NULL DEFAULT '0',
  `viewing` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_roomid` (`roomid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql13);
}

if(!pdo_tableexists('ims_ewei_shop_goods_cards')){
$sql14="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_goods_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `card_id` varchar(255) DEFAULT NULL,
  `card_title` varchar(255) DEFAULT NULL,
  `card_brand_name` varchar(255) DEFAULT NULL,
  `card_totalquantity` int(11) DEFAULT NULL,
  `card_quantity` int(11) DEFAULT NULL,
  `card_logoimg` varchar(255) DEFAULT NULL,
  `card_logowxurl` varchar(255) DEFAULT NULL,
  `card_backgroundtype` tinyint(1) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `card_backgroundimg` varchar(255) DEFAULT NULL,
  `card_backgroundwxurl` varchar(255) DEFAULT NULL,
  `prerogative` varchar(255) DEFAULT NULL,
  `card_description` varchar(255) DEFAULT NULL,
  `freewifi` tinyint(1) DEFAULT NULL,
  `withpet` tinyint(1) DEFAULT NULL,
  `freepark` tinyint(1) DEFAULT NULL,
  `deliver` tinyint(1) DEFAULT NULL,
  `custom_cell1` tinyint(1) DEFAULT NULL,
  `custom_cell1_name` varchar(255) DEFAULT NULL,
  `custom_cell1_tips` varchar(255) DEFAULT NULL,
  `custom_cell1_url` varchar(255) DEFAULT NULL,
  `color2` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql14);
}

if(!pdo_tableexists('ims_ewei_shop_verifygoods')){
$sql15="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_verifygoods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `openid` varchar(255) DEFAULT NULL,
  `orderid` int(11) DEFAULT NULL,
  `ordergoodsid` int(11) DEFAULT NULL,
  `storeid` int(11) DEFAULT NULL,
  `starttime` int(11) DEFAULT NULL,
  `limitdays` int(11) DEFAULT NULL,
  `limitnum` int(11) DEFAULT NULL,
  `used` tinyint(1) DEFAULT '0',
  `verifycode` varchar(20) DEFAULT NULL,
  `codeinvalidtime` int(11) DEFAULT NULL,
  `invalid` tinyint(1) DEFAULT '0',
  `getcard` tinyint(1) DEFAULT '0',
  `activecard` tinyint(1) DEFAULT '0',
  `cardcode` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `verifycode` (`verifycode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql15);
}

if(!pdo_tableexists('ims_ewei_shop_verifygoods_log')){
$sql16="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_verifygoods_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT NULL,
  `verifygoodsid` int(11) DEFAULT NULL,
  `salerid` int(11) DEFAULT NULL,
  `storeid` int(11) DEFAULT NULL,
  `verifynum` int(11) DEFAULT NULL,
  `verifydate` int(11) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql16);
}

if(!pdo_tableexists('ims_ewei_shop_wxapp_tmessage')){
$sql17="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_wxapp_tmessage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `templateid` varchar(50) DEFAULT '',
  `datas` text,
  `emphasis_keyword` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql17);
}



if(!pdo_tableexists('ims_ewei_scratch_award')){
$sql18="
CREATE TABLE IF NOT EXISTS `ims_ewei_scratch_award` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `rid` int(11) DEFAULT '0',
  `fansID` int(11) DEFAULT '0',
  `from_user` varchar(50) DEFAULT '0' COMMENT '用户ID',
  `name` varchar(50) DEFAULT '' COMMENT '名称',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  `prizetype` varchar(10) DEFAULT '' COMMENT '类型',
  `award_sn` varchar(50) DEFAULT '' COMMENT 'SN',
  `createtime` int(10) DEFAULT '0',
  `consumetime` int(10) DEFAULT '0',
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_rid` (`rid`),
  KEY `indx_weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql18);
}

if(!pdo_tableexists('ims_ewei_scratch_fans')){
$sql19="
CREATE TABLE IF NOT EXISTS `ims_ewei_scratch_fans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) DEFAULT '0',
  `fansID` int(11) DEFAULT '0',
  `from_user` varchar(50) DEFAULT '' COMMENT '用户ID',
  `tel` varchar(20) DEFAULT '' COMMENT '登记信息(手机等)',
  `todaynum` int(11) DEFAULT '0',
  `totalnum` int(11) DEFAULT '0',
  `awardnum` int(11) DEFAULT '0',
  `last_time` int(10) DEFAULT '0',
  `createtime` int(10) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_rid` (`rid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql19);
}

if(!pdo_tableexists('ims_ewei_scratch_reply')){
$sql20="
CREATE TABLE IF NOT EXISTS `ims_ewei_scratch_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(10) unsigned DEFAULT '0',
  `weid` int(11) DEFAULT '0',
  `title` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `content` varchar(200) DEFAULT '',
  `start_picurl` varchar(200) DEFAULT '',
  `isshow` tinyint(1) DEFAULT '0',
  `ticket_information` varchar(200) DEFAULT '',
  `starttime` int(10) DEFAULT '0',
  `endtime` int(10) DEFAULT '0',
  `repeat_lottery_reply` varchar(50) DEFAULT '',
  `end_theme` varchar(50) DEFAULT '',
  `end_instruction` varchar(200) DEFAULT '',
  `end_picurl` varchar(200) DEFAULT '',
  `c_type_one` varchar(20) DEFAULT '',
  `c_name_one` varchar(50) DEFAULT '',
  `c_num_one` int(11) DEFAULT '0',
  `c_draw_one` int(11) DEFAULT '0',
  `c_rate_one` double DEFAULT '0',
  `c_type_two` varchar(20) DEFAULT '',
  `c_name_two` varchar(50) DEFAULT '',
  `c_num_two` int(11) DEFAULT '0',
  `c_draw_two` int(11) DEFAULT '0',
  `c_rate_two` double DEFAULT '0',
  `c_type_three` varchar(20) DEFAULT '',
  `c_name_three` varchar(50) DEFAULT '',
  `c_num_three` int(11) DEFAULT '0',
  `c_draw_three` int(11) DEFAULT '0',
  `c_rate_three` double DEFAULT '0',
  `c_type_four` varchar(20) DEFAULT '',
  `c_name_four` varchar(50) DEFAULT '',
  `c_num_four` int(11) DEFAULT '0',
  `c_draw_four` int(11) DEFAULT '0',
  `c_rate_four` double DEFAULT '0',
  `c_type_five` varchar(20) DEFAULT '',
  `c_name_five` varchar(50) DEFAULT '',
  `c_num_five` int(11) DEFAULT '0',
  `c_draw_five` int(11) DEFAULT '0',
  `c_rate_five` double DEFAULT '0',
  `c_type_six` varchar(20) DEFAULT '',
  `c_name_six` varchar(50) DEFAULT '',
  `c_num_six` int(11) DEFAULT '0',
  `c_draw_six` int(10) DEFAULT '0',
  `c_rate_six` double DEFAULT '0',
  `total_num` int(11) DEFAULT '0' COMMENT '总获奖人数(自动加)',
  `probability` double DEFAULT '0',
  `award_times` int(11) DEFAULT '0',
  `number_times` int(11) DEFAULT '0',
  `most_num_times` int(11) DEFAULT '0',
  `sn_code` tinyint(4) DEFAULT '0',
  `sn_rename` varchar(20) DEFAULT '',
  `tel_rename` varchar(20) DEFAULT '',
  `copyright` varchar(20) DEFAULT '',
  `show_num` tinyint(2) DEFAULT '0',
  `viewnum` int(11) DEFAULT '0',
  `fansnum` int(11) DEFAULT '0',
  `createtime` int(10) DEFAULT '0',
  `share_title` varchar(200) DEFAULT '',
  `share_desc` varchar(300) DEFAULT '',
  `share_url` varchar(100) DEFAULT '',
  `share_txt` text NOT NULL,
  `follow` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `indx_rid` (`rid`),
  KEY `indx_weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql20);
}

if(!pdo_tableexists('ims_ewei_scratch_sysset')){
$sql21="
CREATE TABLE IF NOT EXISTS `ims_ewei_scratch_sysset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `weid` int(11) DEFAULT '0',
  `appid` varchar(255) DEFAULT '',
  `appsecret` varchar(255) DEFAULT '',
  `appid_share` varchar(255) DEFAULT '',
  `appsecret_share` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `indx_weid` (`weid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql21);
}

if(!pdo_tableexists('ims_ewei_shop_wxapp_page')){
$sql22="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_wxapp_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `data` mediumtext,
  `createtime` int(11) NOT NULL DEFAULT '0',
  `lasttime` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `isdefault` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `idx_isdefault` (`isdefault`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql22);
}

if(!pdo_tableexists('ims_ewei_shop_exhelper_esheet')){
$sql23="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_esheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `datas` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql23);
}

if(!pdo_tableexists('ims_ewei_shop_exhelper_esheet_temp')){
$sql24="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_exhelper_esheet_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) DEFAULT '0',
  `esheetid` int(11) NOT NULL DEFAULT '0',
  `esheetname` varchar(255) NOT NULL DEFAULT '',
  `customername` varchar(50) NOT NULL DEFAULT '',
  `customerpwd` varchar(50) NOT NULL DEFAULT '',
  `monthcode` varchar(50) NOT NULL DEFAULT '',
  `sendsite` varchar(50) NOT NULL DEFAULT '',
  `paytype` tinyint(3) NOT NULL DEFAULT '1',
  `templatesize` varchar(10) NOT NULL DEFAULT '',
  `isnotice` tinyint(3) NOT NULL DEFAULT '0',
  `merchid` int(11) NOT NULL DEFAULT '0',
  `issend` tinyint(3) NOT NULL DEFAULT '1',
  `isdefault` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_isdefault` (`isdefault`),
  KEY `idx_uniacid` (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql24);
}

if(!pdo_tableexists('ims_ewei_shop_task_list')){
$sql25="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_list` (
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `displayorder` int(11) NOT NULL DEFAULT '0',
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `title` char(50) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(50) NOT NULL DEFAULT '',
  `starttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `demand` int(11) NOT NULL DEFAULT '0',
  `requiregoods` text NOT NULL,
  `picktype` tinyint(1) NOT NULL DEFAULT '0',
  `stop_type` tinyint(1) NOT NULL DEFAULT '0',
  `stop_limit` int(11) NOT NULL DEFAULT '0',
  `stop_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stop_cycle` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_type` tinyint(1) NOT NULL DEFAULT '0',
  `repeat_interval` int(11) NOT NULL DEFAULT '0',
  `repeat_cycle` tinyint(1) NOT NULL DEFAULT '0',
  `reward` text NOT NULL,
  `followreward` text NOT NULL,
  `goods_limit` int(11) NOT NULL DEFAULT '0',
  `notice` text NOT NULL,
  `design_data` text NOT NULL,
  `design_bg` varchar(255) NOT NULL DEFAULT '',
  `native_data` text NOT NULL,
  `native_data2` text,
  `native_data3` text,
  `reward2` text,
  `reward3` text,
  `level2` int(11) NOT NULL DEFAULT '0',
  `level3` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_uniacid` (`uniacid`),
  KEY `idx_passive` (`picktype`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql25);
}

if(!pdo_tableexists('ims_ewei_shop_task_qr')){
$sql26="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `openid` varchar(100) NOT NULL DEFAULT '',
  `recordid` int(11) NOT NULL DEFAULT '0',
  `sceneid` varchar(255) NOT NULL DEFAULT '',
  `mediaid` varchar(255) NOT NULL DEFAULT '',
  `ticket` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recordid` (`recordid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql26);
}

if(!pdo_tableexists('ims_ewei_shop_task_record')){
$sql27="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `taskid` int(11) NOT NULL DEFAULT '0',
  `tasktitle` varchar(255) NOT NULL,
  `taskimage` varchar(255) NOT NULL DEFAULT '',
  `tasktype` varchar(50) NOT NULL DEFAULT '',
  `task_progress` int(11) NOT NULL DEFAULT '0',
  `task_demand` int(11) NOT NULL DEFAULT '0',
  `openid` char(50) NOT NULL DEFAULT '',
  `nickname` varchar(255) NOT NULL DEFAULT '',
  `picktime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `stoptime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finishtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reward_data` text NOT NULL,
  `followreward_data` text NOT NULL,
  `design_data` text NOT NULL,
  `design_bg` varchar(255) NOT NULL DEFAULT '',
  `require_goods` varchar(255) NOT NULL DEFAULT '',
  `level1` int(11) NOT NULL DEFAULT '0',
  `reward_data1` text NOT NULL,
  `level2` int(11) NOT NULL DEFAULT '0',
  `reward_data2` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `taskid` (`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql27);
}

if(!pdo_tableexists('ims_ewei_shop_task_reward')){
$sql28="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `taskid` int(11) NOT NULL DEFAULT '0',
  `tasktitle` char(50) NOT NULL DEFAULT '',
  `tasktype` varchar(50) NOT NULL DEFAULT '',
  `taskowner` char(50) NOT NULL DEFAULT '',
  `ownernickname` char(50) NOT NULL DEFAULT '',
  `recordid` int(11) NOT NULL DEFAULT '0',
  `nickname` char(50) NOT NULL DEFAULT '',
  `headimg` varchar(255) NOT NULL DEFAULT '',
  `openid` char(50) NOT NULL DEFAULT '',
  `reward_type` char(10) NOT NULL DEFAULT '',
  `reward_title` char(50) NOT NULL DEFAULT '',
  `reward_data` decimal(10,2) NOT NULL DEFAULT '0.00',
  `get` tinyint(1) NOT NULL DEFAULT '0',
  `sent` tinyint(1) NOT NULL DEFAULT '0',
  `gettime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `senttime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isjoiner` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `level` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniacid` (`uniacid`),
  KEY `recordid` (`recordid`),
  KEY `taskid` (`taskid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql28);
}

if(!pdo_tableexists('ims_ewei_shop_task_set')){
$sql29="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_set` (
  `uniacid` int(11) NOT NULL DEFAULT '0',
  `entrance` tinyint(1) NOT NULL DEFAULT '0',
  `keyword` varchar(10) NOT NULL DEFAULT '',
  `cover_title` varchar(20) NOT NULL DEFAULT '',
  `cover_img` varchar(255) NOT NULL DEFAULT '',
  `cover_desc` varchar(255) NOT NULL DEFAULT '',
  `msg_pick` text NOT NULL,
  `msg_progress` text NOT NULL,
  `msg_finish` text NOT NULL,
  `msg_follow` text NOT NULL,
  `isnew` tinyint(1) NOT NULL DEFAULT '0',
  `bg_img` varchar(255) NOT NULL DEFAULT '../addons/ewei_shopv2/plugin/task/static/images/sky.png',
  PRIMARY KEY (`uniacid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql29);
}

if(!pdo_tableexists('ims_ewei_shop_task_type')){
$sql30="
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_type` (
  `id` int(11) NOT NULL,
  `type_key` char(20) NOT NULL DEFAULT '',
  `type_name` char(10) NOT NULL DEFAULT '',
  `description` char(30) NOT NULL DEFAULT '',
  `verb` char(11) NOT NULL DEFAULT '',
  `numeric` tinyint(1) NOT NULL DEFAULT '0',
  `unit` char(10) NOT NULL DEFAULT '',
  `goods` tinyint(1) NOT NULL DEFAULT '0',
  `theme` char(10) NOT NULL DEFAULT '',
  `once` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
pdo_run($sql30);
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'repeat')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `repeat` TINYINT(1) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'koulingstart')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `koulingstart` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'koulingend')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `koulingend` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'kouling')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `kouling` TINYINT(1) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'chufa')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `chufa` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'chufaend')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `chufaend` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo', 'tid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." add `tid` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_peerpay_payinfo', 'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_peerpay_payinfo')." add `openid` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_system_plugingrant_plugin', 'name')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_system_plugingrant_plugin')." add `name` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_system_plugingrant_plugin', 'plugintype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_system_plugingrant_plugin')." add `plugintype` TINYINT(3) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('ewei_shop_coupon', 'quickget')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon')." add `quickget` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_merch_user', 'maxgoods')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_merch_user')." add `maxgoods` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'isnewstore')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `isnewstore` TINYINT(3) NOT NULL;");
}


if(!pdo_fieldexists('ewei_shop_goods', 'islive')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `islive` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'liveprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `liveprice` decimal(10,2) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('ewei_shop_goods_option', 'islive')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_option')." add `islive` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods_option', 'liveprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods_option')." add `liveprice` decimal(10,2) NOT NULL DEFAULT '0';");
}

if(!pdo_fieldexists('ewei_shop_member', 'membercardid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `membercardid` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'membercardcode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `membercardcode` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'membershipnumber')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `membershipnumber` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'membercardactive')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `membercardactive` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'liveid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `liveid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'opencard')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `opencard` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'cardid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `cardid` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'verifygoodsnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `verifygoodsnum` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'verifygoodsdays')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `verifygoodsdays` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'iscoupon')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `iscoupon` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_member_cart', 'isnewstore')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." add `isnewstore` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_plugin', 'name')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_plugin')." add `name` VARCHAR(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_plugin', 'category')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_plugin')." add `category` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_plugin', 'thumb')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_plugin')." add `thumb` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'username')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `username` VARCHAR(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'pwd')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `pwd` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'salt')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `salt` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'lastvisit')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `lastvisit` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'lastip')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `lastip` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'isfounder')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `isfounder` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'mobile')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `mobile` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'getmessage')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `getmessage` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'getnotice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `getnotice` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_saler', 'roleid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_saler')." add `roleid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'ordersn_trade')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `ordersn_trade` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'tradestatus')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `tradestatus` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'tradepaytype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `tradepaytype` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'tradepaytime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `tradepaytime` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'prohibitrefund')) {
	pdo_query("ALTER TABLE ".tablename('shop_order_goods')." add `prohibitrefund` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'canbuyagain')) {
	pdo_query("ALTER TABLE ".tablename('shop_order_goods')." add `canbuyagain` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_sms_set', 'aliyun')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_sms_set')." add `aliyun` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_sms_set', 'aliyun_appcode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_sms_set')." add `aliyun_appcode` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'banner')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `banner` TEXT NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'perms')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `perms` TEXT NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'diypage_list')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `diypage_list` TEXT NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'diypage_ispage')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `diypage_ispage` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_store', 'opensend')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `opensend` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_store', 'classify')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `classify` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_store', 'diypage')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `diypage` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'storegroupid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `storegroupid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'label')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `label` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'tag')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `tag` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'citycode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `citycode` VARCHAR(20) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'province')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `province` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'city')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `city` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'area')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `area` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'provincecode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `provincecode` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'areacode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `areacode` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_coupon_data', 'shareident')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon_data')." add `shareident` VARCHAR(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_coupon_data', 'textkey')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon_data')." add `textkey` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_diyform_type', 'savedata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_diyform_type')." add `savedata` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'verifygoodslimittype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `verifygoodslimittype` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'verifygoodslimitdate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `verifygoodslimitdate` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'minliveprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `minliveprice` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'maxliveprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `maxliveprice` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'dowpayment')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `dowpayment` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'tempid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `tempid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'isstoreprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `isstoreprice` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'beforehours')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `beforehours` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'nestable')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `nestable` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'tabs')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `tabs` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'invitation_id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `invitation_id` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'showlevels')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `showlevels` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'showgroups')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `showgroups` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'showcommission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `showcommission` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'jurisdiction_url')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `jurisdiction_url` VARCHAR(1000) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'jurisdictionurl_show')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `jurisdictionurl_show` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_live', 'notice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `notice` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'notice_url')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `notice_url` VARCHAR(1000) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'followqrcode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `followqrcode` VARCHAR(1000) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_live', 'coupon_num')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_live')." add `coupon_num` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_printer_template', 'goodssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_printer_template')." add `goodssn` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_member_printer_template', 'productsn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_printer_template')." add `productsn` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'dowpayment')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `dowpayment` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'betweenprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `betweenprice` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'isshare')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `isshare` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'storeid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `storeid` VARCHAR(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'trade_time')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `trade_time` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'optime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `optime` VARCHAR(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'tdate_time')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `tdate_time` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'dowpayment')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `dowpayment` decimal(10,2) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'peopleid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `peopleid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'cates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `cates` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_verifygoods', 'limittype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_verifygoods')." add `limittype` TINYINT(1) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_verifygoods', 'limitdate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_verifygoods')." add `limitdate` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_cashier_user', 'notice_openids')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_cashier_user')." add `notice_openids` VARCHAR(500) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exchange_setting', 'no_qrimg')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_setting')." add `no_qrimg` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'newgoods')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `newgoods` TINYINT(3) DEFAULT 0 ;");
}

if(!pdo_fieldexists('ewei_shop_order', 'officcode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `officcode` VARCHAR(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'wxapp_prepay_id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `wxapp_prepay_id` VARCHAR(100) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_virtual_data', 'createtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_virtual_data')." add `createtime` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_virtual_type', 'recycled')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_virtual_type')." add `recycled` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_message_mass_task', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_message_mass_task')." add `id` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_message_mass_template', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_message_mass_template')." add `id` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'article_keyword2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `article_keyword2` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'article_report')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `article_report` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `uniacid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'network_attachment')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `network_attachment` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'article_rule_credittotal')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `article_rule_credittotal` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article', 'article_advance')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article')." add `article_advance` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article_category', 'displayorder')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article_category')." add `displayorder` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article_category', 'uniacid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article_category')." add `uniacid` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article_sys', 'article_source')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article_sys')." add `article_source` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_article_sys', 'article_temp')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_article_sys')." add `article_temp` INT(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_cashier_pay_log', 'payopenid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_cashier_pay_log')." add `payopenid` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_cashier_pay_log', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_cashier_pay_log')." add `paytype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_cashier_pay_log', 'nosalemoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_cashier_pay_log')." add `nosalemoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_category', 'level')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_category')." add `level` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_category', 'advimg')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_category')." add `advimg` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_apply', 'alipay1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_apply')." add `alipay1` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_apply', 'alipay1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_apply')." add `alipay1` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_apply', 'repurchase')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_apply')." add `repurchase` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_apply', 'sendmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_apply')." add `sendmoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_level', 'ordermoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." add `ordermoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_level', 'ordercount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." add `ordercount` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_level', 'downcount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." add `downcount` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_level', 'commissionmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." add `commissionmoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_commission_level', 'goodsids')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_commission_level')." add `goodsids` varchar(1000) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_coupon', 'pwdkey2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon')." add `pwdkey2` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_coupon', 'pwdsuc')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon')." add `pwdsuc` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_coupon', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_coupon')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'transid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `transid` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'storeid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `storeid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'address')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `address` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'remarksaler')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `remarksaler` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_dispatch', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_dispatch')." add `id` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exchange_group', 'repeat')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exchange_group')." add `repeat` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_sys', 'ip_cloud')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_sys')." add `ip_cloud` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_sys', 'port')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_sys')." add `port` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_sys', 'port_cloud')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_sys')." add `port_cloud` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'tcate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `tcate` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'type')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `type` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'isdiscount_title')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `isdiscount_title` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'isrecommand')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `isrecommand` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'commission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `commission` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'score')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `score` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'noticetype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `noticetype` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'followurl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `followurl` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'followtip')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `followtip` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'deduct')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `deduct` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'shorttitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `shorttitle` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'virtual')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `virtual` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'detail_logo')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `detail_logo` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'detail_totaltitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `detail_totaltitle` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'detail_btntext1')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `detail_btntext1` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'cates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `cates` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'deduct2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `deduct2` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'edareas')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `edareas` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'edmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `edmoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'diyformtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `diyformtype` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'saleupdate37975')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `saleupdate37975` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'catesinit3')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `catesinit3` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'catesinit3')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `catesinit3` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'keywords')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `keywords` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'goodssn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `goodssn` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'title')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `title` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'category')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `category` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'showstock')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `showstock` tinyint(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'stock')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `stock` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'goodsnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `goodsnum` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'purchaselimit')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `purchaselimit` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'singleprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `singleprice` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'units')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `units` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'dispatchtype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `dispatchtype` tinyint(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'freight')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `freight` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'isindex')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `isindex` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'deleted')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `deleted` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'followurl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `followurl` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'share_title')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `share_title` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'deduct')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `deduct` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'thumb_url')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `thumb_url` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'rights')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `rights` tinyint(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'gid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `gid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'credit')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `credit` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'price')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `price` decimal(11,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'dispatchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `dispatchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'goodid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `goodid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'discount')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `discount` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'starttime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `starttime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'canceltime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `canceltime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'endtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `endtime` int(45) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'finishtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `finishtime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'success')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `success` int(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'deleted')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `deleted` int(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `id` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_set', 'followqrcode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `followqrcode` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_set', 'groupsurl')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `groupsurl` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_set', 'share_url')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `share_url` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_set', 'groups_description')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `groups_description` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_set', 'creditdeduct')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `creditdeduct` tinyint(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'agentnotupgrade')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `agentnotupgrade` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'inviter')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `inviter` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'agentselectgoods')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `agentselectgoods` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'username')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `username` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'fixagentid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `fixagentid` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diymemberdataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diymemberdataid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diymemberdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diymemberdata` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diycommissionid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diycommissionid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diycommissiondataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diycommissiondataid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diycommissiondata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diycommissiondata` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'isblack')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `isblack` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diymemberfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diymemberfields` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'diycommissionfields')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `diycommissionfields` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'commission_total')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `commission_total` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_cart', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_cart', 'selectedadd')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." add `selectedadd` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_cart', 'isnewstore')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_cart')." add `isnewstore` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'transid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `transid` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'gives')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `gives` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'isborrow')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `isborrow` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'realmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `realmoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'remark')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `remark` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'verifytime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `verifytime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'verifycode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `verifycode` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'verifystoreid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `verifystoreid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'closereason')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `closereason` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'printstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `printstate` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'remarkclose')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `remarkclose` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'invoicename')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `invoicename` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'ismerch')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `ismerch` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'liveid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `liveid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'ordersn_trade')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `ordersn_trade` varchar(32) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'tradepaytype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `tradepaytype` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_comment', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_comment')." add `id` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformdata')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `diyformdata` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformdataid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `diyformdataid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `openid` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'diyformid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `diyformid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'rstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `rstate` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'printstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `printstate` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_refund', 'realprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." add `realprice` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_refund', 'refundtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." add `refundtime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_refund', 'orderprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." add `orderprice` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_refund', 'rexpress')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_refund')." add `rexpress` varchar(100) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_log', 'ip')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_log')." add `ip` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_log', 'createtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_log')." add `createtime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_role', 'perms2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_role')." add `perms2` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_role', 'deleted')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_role')." add `deleted` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_user', 'perms2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_user')." add `perms2` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_user', 'deleted')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_user')." add `deleted` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_user', 'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_user')." add `openid` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'keyword2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `keyword2` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'times')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `times` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'resptype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `resptype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'resptitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `resptitle` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'resptitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `resptitle` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'scantext')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `scantext` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `paytype` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'subpaycontent')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `subpaycontent` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'templateid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `templateid` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'entrytext')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `entrytext` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'resptext11')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `resptext11` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'ismembergroup')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `ismembergroup` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'membergroupid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `membergroupid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'keyword2')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `keyword2` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'isdefault')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `isdefault` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'resptype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `resptype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'resptitle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `resptitle` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'testflag')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `testflag` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'reward_totle')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `reward_totle` varchar(500) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster_qr', 'scenestr')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_qr')." add `scenestr` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster_qr', 'posterid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster_qr')." add `posterid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_refund_address', 'openid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_refund_address')." add `openid` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_refund_address', 'title')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_refund_address')." add `title` varchar(20) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_refund_address', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_refund_address')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_sign_records', 'id')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_sign_records')." add `id` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'type')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `type` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'realname')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `realname` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_store', 'logo')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_store')." add `logo` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_abonus_bill', 'ceshi')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_abonus_bill')." add `ceshi` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'mobile')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `mobile` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_creditshop_log', 'time_send')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_creditshop_log')." add `time_send` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'istime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `istime` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'ednum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `ednum` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'saleupdate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `saleupdate` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'isdiscount_discounts')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `isdiscount_discounts` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'allcates')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `allcates` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'video')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `video` varchar(521) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'sharebtn')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `sharebtn` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'thumb_first')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `thumb_first` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_goods', 'catch_url')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_goods')." add `catch_url` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'groupsprice')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `groupsprice` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'groupnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `groupnum` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_goods', 'headstype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_goods')." add `headstype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'status')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `status` int(9) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'is_team')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `is_team` int(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'creditmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `creditmoney` decimal(11,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'address')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `address` varchar(1000) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_groups_order', 'refundstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_groups_order')." add `refundstate` tinyint(2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'commission')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `commission` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'content')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `content` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member', 'idnumber')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member')." add `idnumber` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'deductionmoney')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `deductionmoney` decimal(10,2) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'remark')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `remark` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_member_log', 'alipay')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_member_log')." add `alipay` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'address_send')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `address_send` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'remarkclose')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `remarkclose` text NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order', 'ismr')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order')." add `ismr` int(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'rstate')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `rstate` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'merchid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `merchid` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'merchsale')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `merchsale` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_user', 'openid_wa')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_user')." add `openid_wa` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_perm_user', 'member_nick')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_perm_user')." add `member_nick` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_poster', 'createtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_poster')." add `createtime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_postera', 'createtime')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_postera')." add `createtime` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_message_mass_template', 'miniprogram')) {
	pdo_query("ALTER TABLE ".tablename('ewei_message_mass_template')." add `miniprogram` tinyint(1) NOT NULL;");
}

if(!pdo_fieldexists('ewei_message_mass_template', 'appid')) {
	pdo_query("ALTER TABLE ".tablename('ewei_message_mass_template')." add `appid` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_message_mass_template', 'pagepath')) {
	pdo_query("ALTER TABLE ".tablename('ewei_message_mass_template')." add `pagepath` varchar(255) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_senduser', 'province')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_senduser')." add `province` varchar(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_senduser', 'city')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_senduser')." add `city` varchar(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_senduser', 'area')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_senduser')." add `area` varchar(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_sys', 'ebusiness')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_sys')." add `ebusiness` varchar(20) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_exhelper_sys', 'apikey')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_exhelper_sys')." add `apikey` varchar(50) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'esheetprintnum')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `esheetprintnum` int(11) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_order_goods', 'ordercode')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_order_goods')." add `ordercode` varchar(30) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_payment', 'paytype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_payment')." add `paytype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_payment', 'alitype')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_payment')." add `alitype` tinyint(3) NOT NULL;");
}

if(!pdo_fieldexists('ewei_shop_payment', 'alipay_sec')) {
	pdo_query("ALTER TABLE ".tablename('ewei_shop_payment')." add `alipay_sec` text NOT NULL;");
}

//12-27 3.1.8


$sql999="


ALTER TABLE `ims_ewei_shop_abonus_bill` 
	CHANGE `ceshi` `ceshi` int(11)   NULL after `confirmtime` ;
	
ALTER TABLE `ims_ewei_shop_article` 
	CHANGE `article_report` `article_report` int(1)   NOT NULL DEFAULT 0 after `article_keyword` , 
	CHANGE `network_attachment` `network_attachment` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `article_state` , 
	CHANGE `uniacid` `uniacid` int(11)   NOT NULL DEFAULT 0 after `network_attachment` , 
	CHANGE `article_rule_credittotal` `article_rule_credittotal` int(11)   NULL DEFAULT 0 after `uniacid` , 
	CHANGE `article_keyword2` `article_keyword2` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' after `displayorder` , 
	CHANGE `article_advance` `article_advance` int(11)   NULL DEFAULT 0 after `article_keyword2` , 
	ADD KEY `idx_article_content`(`article_content`(10)) , COMMENT='营销文章' ;
	
ALTER TABLE `ims_ewei_shop_commission_level` 
	CHANGE `commissionmoney` `commissionmoney` decimal(10,2)   NULL DEFAULT 0.00 after `commission3` , 
	CHANGE `ordermoney` `ordermoney` decimal(10,2)   NULL DEFAULT 0.00 after `commissionmoney` , 
	CHANGE `downcount` `downcount` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `ordermoney` , 
	CHANGE `ordercount` `ordercount` int(11)   NULL DEFAULT 0 after `downcount` , 
	ADD COLUMN `withdraw` decimal(10,2)   NULL DEFAULT 0.00 after `ordercount` , 
	ADD COLUMN `repurchase` decimal(10,2)   NULL DEFAULT 0.00 after `withdraw` , 
	CHANGE `goodsids` `goodsids` varchar(1000)  COLLATE utf8_general_ci NULL DEFAULT '' after `repurchase` ;
	
ALTER TABLE `ims_ewei_shop_designer` 
	ADD KEY `idx_keyword`(`keyword`) ;






ALTER TABLE `ims_ewei_shop_goods` 
	CHANGE `type` `type` tinyint(1)   NULL DEFAULT 1 after `ccate` , 
	CHANGE `isrecommand` `isrecommand` tinyint(1)   NULL DEFAULT 0 after `isdiscount` , 
	CHANGE `score` `score` decimal(10,2)   NULL DEFAULT 0.00 after `commission3_pay` , 
	CHANGE `tcate` `tcate` int(11)   NULL DEFAULT 0 after `noticeopenid` , 
	CHANGE `noticetype` `noticetype` text  COLLATE utf8_general_ci NULL after `tcate` , 
	CHANGE `followtip` `followtip` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `needfollow` , 
	CHANGE `followurl` `followurl` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `followtip` , 
	CHANGE `deduct` `deduct` decimal(10,2)   NULL DEFAULT 0.00 after `followurl` , 
	DROP COLUMN `virtual` , 
	ADD COLUMN `virtual` int(11)   NULL DEFAULT 0 after `deduct` , 
	CHANGE `cates` `cates` text  COLLATE utf8_general_ci NULL after `tcates` , 
	CHANGE `detail_logo` `detail_logo` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `artid` , 
	CHANGE `detail_btntext1` `detail_btntext1` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `detail_shopname` , 
	CHANGE `detail_totaltitle` `detail_totaltitle` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `detail_btnurl2` , 
	ADD COLUMN `saleupdate42392` tinyint(3)   NULL DEFAULT 0 after `detail_totaltitle` , 
	CHANGE `deduct2` `deduct2` decimal(10,2)   NULL DEFAULT 0.00 after `saleupdate42392` , 
	CHANGE `edmoney` `edmoney` decimal(10,2)   NULL DEFAULT 0.00 after `ednum` , 
	CHANGE `edareas` `edareas` text  COLLATE utf8_general_ci NULL after `edmoney` , 
	CHANGE `diyformtype` `diyformtype` tinyint(1)   NULL DEFAULT 0 after `edareas` , 
	CHANGE `shorttitle` `shorttitle` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `manydeduct` , 
	CHANGE `isdiscount_title` `isdiscount_title` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `shorttitle` , 
	CHANGE `commission` `commission` text  COLLATE utf8_general_ci NULL after `isdiscount_discounts` , 
	CHANGE `saleupdate37975` `saleupdate37975` tinyint(3)   NULL DEFAULT 0 after `commission` , 
	ADD COLUMN `minpriceupdated` tinyint(1)   NULL DEFAULT 0 after `subtitle` , 
	CHANGE `sharebtn` `sharebtn` tinyint(1)   NOT NULL DEFAULT 0 after `minpriceupdated` , 
	CHANGE `video` `video` varchar(521)  COLLATE utf8_general_ci NOT NULL DEFAULT '' after `sharebtn` , 
	CHANGE `catesinit3` `catesinit3` text  COLLATE utf8_general_ci NULL after `video` , 
	CHANGE `merchid` `merchid` int(11)   NULL DEFAULT 0 after `showtotaladd` , 
	CHANGE `keywords` `keywords` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `merchsale` , 
	ADD COLUMN `saleupdate40170` tinyint(3)   NULL DEFAULT 0 after `catch_source` , 
	ADD COLUMN `saleupdate35843` tinyint(3)   NULL DEFAULT 0 after `saleupdate40170` , 
	CHANGE `labelname` `labelname` text  COLLATE utf8_general_ci NULL after `saleupdate35843` , 
	ADD COLUMN `saleupdate33219` tinyint(3)   NULL DEFAULT 0 after `cannotrefund` , 
	CHANGE `bargain` `bargain` int(11)   NULL DEFAULT 0 after `saleupdate33219` , 
	ADD COLUMN `saleupdate32484` tinyint(3)   NULL DEFAULT 0 after `buyagain_commission` , 
	ADD COLUMN `saleupdate36586` tinyint(3)   NULL DEFAULT 0 after `saleupdate32484` , 
	CHANGE `diypage` `diypage` int(11)   NULL after `saleupdate36586` , 
	ADD COLUMN `saleupdate53481` tinyint(3)   NULL DEFAULT 0 after `cashier` , 
	ADD COLUMN `saleupdate30424` tinyint(3)   NULL DEFAULT 0 after `saleupdate53481` , 
	CHANGE `isendtime` `isendtime` tinyint(3)   NOT NULL DEFAULT 0 after `saleupdate30424` , 
	CHANGE `saleupdate` `saleupdate` tinyint(3)   NULL DEFAULT 0 after `beforehours` , 
	CHANGE `newgoods` `newgoods` tinyint(3)   NOT NULL DEFAULT 0 after `saleupdate` , 
	ADD FULLTEXT KEY `idx_buygroups`(`buygroups`) , 
	ADD FULLTEXT KEY `idx_buylevels`(`buylevels`) , 
	ADD FULLTEXT KEY `idx_showgroups`(`showgroups`) , 
	ADD KEY `idx_tcate`(`tcate`) ;


ALTER TABLE `ims_ewei_shop_groups_goods` 
	CHANGE `title` `title` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' after `uniacid` , 
	CHANGE `category` `category` tinyint(3) unsigned   NOT NULL DEFAULT 0 after `title` , 
	CHANGE `stock` `stock` int(11)   NOT NULL DEFAULT 0 after `category` , 
	CHANGE `singleprice` `singleprice` decimal(10,2)   NULL DEFAULT 0.00 after `groupsprice` , 
	CHANGE `goodsnum` `goodsnum` int(11)   NOT NULL DEFAULT 1 after `singleprice` , 
	CHANGE `units` `units` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '件' after `goodsnum` , 
	CHANGE `freight` `freight` decimal(10,2)   NULL DEFAULT 0.00 after `units` , 
	ADD COLUMN `ishot` tinyint(3)   NOT NULL DEFAULT 0 after `status` , 
	CHANGE `deleted` `deleted` tinyint(3)   NOT NULL DEFAULT 0 after `ishot` , 
	CHANGE `share_title` `share_title` varchar(255)  COLLATE utf8_general_ci NULL after `followtext` , 
	CHANGE `goodssn` `goodssn` varchar(50)  COLLATE utf8_general_ci NULL after `share_desc` , 
	CHANGE `showstock` `showstock` tinyint(2)   NOT NULL after `productsn` , 
	CHANGE `purchaselimit` `purchaselimit` int(11)   NOT NULL DEFAULT 0 after `showstock` , 
	CHANGE `dispatchtype` `dispatchtype` tinyint(2)   NOT NULL after `single` , 
	CHANGE `isindex` `isindex` tinyint(3)   NOT NULL DEFAULT 0 after `dispatchid` , 
	CHANGE `followurl` `followurl` varchar(255)  COLLATE utf8_general_ci NULL after `isindex` , 
	CHANGE `deduct` `deduct` decimal(10,2)   NOT NULL DEFAULT 0.00 after `followurl` , 
	CHANGE `rights` `rights` tinyint(2)   NOT NULL DEFAULT 1 after `deduct` , 
	CHANGE `thumb_url` `thumb_url` text  COLLATE utf8_general_ci NULL after `rights` , 
	CHANGE `gid` `gid` int(11)   NULL DEFAULT 0 after `thumb_url` , 
	ADD KEY `idx_istop`(`isindex`) ;


ALTER TABLE `ims_ewei_shop_groups_order` 
	CHANGE `id` `id` int(11)   NOT NULL auto_increment first , 
	CHANGE `price` `price` decimal(11,2)   NULL DEFAULT 0.00 after `paytime` , 
	CHANGE `goodid` `goodid` int(11)   NOT NULL after `pay_type` , 
	CHANGE `starttime` `starttime` int(11)   NOT NULL after `heads` , 
	CHANGE `endtime` `endtime` int(45)   NOT NULL after `starttime` , 
	CHANGE `success` `success` int(2)   NOT NULL DEFAULT 0 after `createtime` , 
	ADD COLUMN `delete` int(2)   NOT NULL DEFAULT 0 after `success` , 
	CHANGE `credit` `credit` int(11)   NULL DEFAULT 0 after `delete` , 
	CHANGE `dispatchid` `dispatchid` int(11)   NULL after `creditmoney` , 
	CHANGE `discount` `discount` decimal(10,2)   NULL DEFAULT 0.00 after `address` , 
	CHANGE `canceltime` `canceltime` int(11)   NOT NULL DEFAULT 0 after `discount` , 
	CHANGE `finishtime` `finishtime` int(11)   NOT NULL DEFAULT 0 after `canceltime` , 
	CHANGE `deleted` `deleted` int(2)   NOT NULL DEFAULT 0 after `message` ;


ALTER TABLE `ims_ewei_shop_groups_set` 
	CHANGE `groupsurl` `groupsurl` varchar(255)  COLLATE utf8_general_ci NULL after `followurl` , 
	CHANGE `groups_description` `groups_description` text  COLLATE utf8_general_ci NULL after `share_desc` , 
	CHANGE `followqrcode` `followqrcode` varchar(255)  COLLATE utf8_general_ci NULL after `description` , 
	CHANGE `share_url` `share_url` varchar(255)  COLLATE utf8_general_ci NULL after `followqrcode` , 
	CHANGE `creditdeduct` `creditdeduct` tinyint(2)   NOT NULL DEFAULT 0 after `share_url` ;


ALTER TABLE `ims_ewei_shop_member` 
	ADD COLUMN `commission_pay` decimal(10,2)   NULL DEFAULT 0.00 after `content` , 
	CHANGE `idnumber` `idnumber` varchar(255)  COLLATE utf8_general_ci NULL after `commission_pay` , 
	CHANGE `createtime` `createtime` int(10)   NULL DEFAULT 0 after `idnumber` , 
	CHANGE `inviter` `inviter` int(11)   NULL DEFAULT 0 after `childtime` , 
	CHANGE `agentnotupgrade` `agentnotupgrade` int(11)   NULL DEFAULT 0 after `inviter` , 
	CHANGE `agentselectgoods` `agentselectgoods` tinyint(3)   NULL DEFAULT 0 after `agentnotupgrade` , 
	CHANGE `fixagentid` `fixagentid` tinyint(3)   NULL DEFAULT 0 after `agentblack` , 
	CHANGE `diymemberfields` `diymemberfields` text  COLLATE utf8_general_ci NULL after `diymemberid` , 
	CHANGE `diymemberdata` `diymemberdata` text  COLLATE utf8_general_ci NULL after `diymemberfields` , 
	CHANGE `diymemberdataid` `diymemberdataid` int(11)   NULL DEFAULT 0 after `diymemberdata` , 
	CHANGE `diycommissionid` `diycommissionid` int(11)   NULL DEFAULT 0 after `diymemberdataid` , 
	CHANGE `diycommissionfields` `diycommissionfields` text  COLLATE utf8_general_ci NULL after `diycommissionid` , 
	CHANGE `diycommissiondata` `diycommissiondata` text  COLLATE utf8_general_ci NULL after `diycommissionfields` , 
	CHANGE `diycommissiondataid` `diycommissiondataid` int(11)   NULL DEFAULT 0 after `diycommissiondata` , 
	CHANGE `isblack` `isblack` int(11)   NULL DEFAULT 0 after `diycommissiondataid` , 
	CHANGE `username` `username` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `isblack` , 
	CHANGE `commission_total` `commission_total` decimal(10,2)   NULL DEFAULT 0.00 after `username` , 
	CHANGE `commission` `commission` decimal(10,2)   NULL DEFAULT 0.00 after `membercardactive` ;


ALTER TABLE `ims_ewei_shop_member_cart` 
	CHANGE `selectedadd` `selectedadd` tinyint(1)   NULL DEFAULT 1 after `selected` , 
	CHANGE `merchid` `merchid` int(11)   NULL DEFAULT 0 after `selectedadd` , 
	CHANGE `isnewstore` `isnewstore` tinyint(3)   NOT NULL DEFAULT 0 after `merchid` ;


ALTER TABLE `ims_ewei_shop_member_log` 
	CHANGE `gives` `gives` decimal(10,2)   NULL after `rechargetype` , 
	CHANGE `transid` `transid` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `couponid` , 
	CHANGE `realmoney` `realmoney` decimal(10,2)   NULL DEFAULT 0.00 after `transid` , 
	CHANGE `isborrow` `isborrow` tinyint(3)   NULL DEFAULT 0 after `deductionmoney` , 
	CHANGE `remark` `remark` varchar(255)  COLLATE utf8_general_ci NOT NULL DEFAULT '' after `borrowopenid` ;


ALTER TABLE `ims_ewei_shop_order` 
	CHANGE `verifycode` `verifycode` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `verifyopenid` , 
	CHANGE `verifytime` `verifytime` int(11)   NULL DEFAULT 0 after `verifycode` , 
	CHANGE `verifystoreid` `verifystoreid` int(11)   NULL DEFAULT 0 after `verifytime` , 
	CHANGE `printstate` `printstate` tinyint(1)   NULL DEFAULT 0 after `storeid` , 
	CHANGE `closereason` `closereason` text  COLLATE utf8_general_ci NULL after `refundstate` , 
	CHANGE `remarkclose` `remarkclose` text  COLLATE utf8_general_ci NULL after `remarksaler` , 
	CHANGE `invoicename` `invoicename` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `verifycodes` , 
	CHANGE `merchid` `merchid` int(11)   NULL DEFAULT 0 after `invoicename` , 
	CHANGE `ismerch` `ismerch` tinyint(1)   NULL DEFAULT 0 after `merchid` , 
	ADD COLUMN `authorid` int(11)   NULL DEFAULT 0 after `buyagainprice` , 
	ADD COLUMN `isauthor` tinyint(1)   NULL DEFAULT 0 after `authorid` , 
	CHANGE `ispackage` `ispackage` tinyint(3)   NULL DEFAULT 0 after `isauthor` , 
	CHANGE `liveid` `liveid` int(11)   NULL after `isnewstore` , 
	CHANGE `ordersn_trade` `ordersn_trade` varchar(32)  COLLATE utf8_general_ci NULL after `liveid` , 
	CHANGE `tradepaytype` `tradepaytype` tinyint(1)   NULL after `tradestatus` , 
	DROP KEY `idx_ordersn` ;



ALTER TABLE `ims_ewei_shop_order_goods` 
	CHANGE `diyformid` `diyformid` int(11)   NULL DEFAULT 0 after `commissions` , 
	CHANGE `diyformdataid` `diyformdataid` int(11)   NULL DEFAULT 0 after `diyformid` , 
	CHANGE `diyformdata` `diyformdata` text  COLLATE utf8_general_ci NULL after `diyformdataid` , 
	CHANGE `openid` `openid` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `diyformfields` , 
	CHANGE `printstate` `printstate` int(11)   NOT NULL DEFAULT 0 after `openid` , 
	CHANGE `rstate` `rstate` tinyint(3)   NULL DEFAULT 0 after `printstate2` , 
	CHANGE `merchid` `merchid` int(11)   NULL DEFAULT 0 after `refundtime` , 
	ADD COLUMN `is_make` tinyint(1)   NULL DEFAULT 0 after `seckill_timeid` , 
	CHANGE `sendtype` `sendtype` tinyint(3)   NOT NULL DEFAULT 0 after `is_make` , 
	CHANGE `esheetprintnum` `esheetprintnum` int(11)   NOT NULL DEFAULT 0 after `peopleid` ;




ALTER TABLE `ims_ewei_shop_perm_log` 
	CHANGE `createtime` `createtime` int(11)   NULL DEFAULT 0 after `op` , 
	CHANGE `ip` `ip` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `createtime` , 
	ADD FULLTEXT KEY `idx_op`(`op`) , 
	ADD FULLTEXT KEY `idx_type`(`type`) ;


ALTER TABLE `ims_ewei_shop_perm_plugin` 
	ADD KEY `idx_acid`(`acid`) ;



ALTER TABLE `ims_ewei_shop_poster_log` 
	ADD KEY `idx_from_openid`(`from_openid`) ;


ALTER TABLE `ims_ewei_shop_poster_qr` 
	CHANGE `scenestr` `scenestr` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `qrimg` , 
	CHANGE `posterid` `posterid` int(11)   NULL DEFAULT 0 after `scenestr` , 
	ADD KEY `idx_openid`(`openid`) ;


ALTER TABLE `ims_ewei_shop_poster_scan` 
	ADD KEY `idx_openid`(`openid`) ;


ALTER TABLE `ims_ewei_shop_postera` 
	CHANGE `isdefault` `isdefault` tinyint(3)   NULL DEFAULT 0 after `keyword` , 
	CHANGE `resptitle` `resptitle` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `isdefault` , 
	CHANGE `resptype` `resptype` tinyint(3)   NULL DEFAULT 0 after `endtext` , 
	CHANGE `testflag` `testflag` tinyint(1)   NULL DEFAULT 0 after `resptext` , 
	CHANGE `keyword2` `keyword2` varchar(255)  COLLATE utf8_general_ci NULL DEFAULT '' after `testflag` , 
	CHANGE `reward_totle` `reward_totle` varchar(500)  COLLATE utf8_general_ci NULL DEFAULT '' after `keyword2` ;


ALTER TABLE `ims_ewei_shop_postera_log` 
	ADD KEY `idx_from_openid`(`from_openid`) ;


";
pdo_run($sql999);