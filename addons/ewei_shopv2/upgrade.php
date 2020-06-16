<?php
pdo_query("

DROP TABLE IF EXISTS `ims_ewei_shop_exhelper_esheet`;
CREATE TABLE `ims_ewei_shop_exhelper_esheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `datas` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('1', '顺丰', '', 'SF', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"三联210\";s:4:\"spec\";s:38:\"（宽100mm 高210mm 切点90/60/60）\";s:4:\"size\";s:3:\"210\";s:9:\"isdefault\";i:0;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('2', '百世快递', '', 'HTKY', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:0;}i:1;a:4:{s:5:\"style\";s:9:\"二联183\";s:4:\"spec\";s:37:\"（宽100mm 高183mm 切点87/5/91）\";s:4:\"size\";s:3:\"183\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('3', '韵达', '', 'YD', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:0;}i:1;a:4:{s:5:\"style\";s:9:\"二联203\";s:4:\"spec\";s:36:\"（宽100mm 高203mm 切点152/51）\";s:4:\"size\";s:3:\"203\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('4', '申通', '', 'STO', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:35:\"（宽100mm 高150mm 切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:0;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('5', '圆通', '', 'YTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('6', 'EMS', '', 'EMS', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('7', '中通', '', 'ZTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('8', '德邦', '', 'DBL', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联177\";s:4:\"spec\";s:34:\"（宽100mm高177mm切点107/70）\";s:4:\"size\";s:3:\"177\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('9', '优速', '', 'UC', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('10', '宅急送', '', 'ZJS', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联120\";s:4:\"spec\";s:33:\"（宽100mm高116mm切点98/10）\";s:4:\"size\";s:3:\"120\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('11', '京东', '', 'JD', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联110\";s:4:\"spec\";s:33:\"（宽100mm高110mm切点60/50）\";s:4:\"size\";s:3:\"110\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('12', '信丰', '', 'XFEX', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('13', '全峰', '', 'QFKD', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('14', '跨越速运', '', 'KYSY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联137\";s:4:\"spec\";s:34:\"（宽100mm高137mm切点101/36）\";s:4:\"size\";s:3:\"137\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('15', '安能', '', 'ANE', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"三联180\";s:4:\"spec\";s:37:\"（宽100mm高180mm切点110/30/40）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('16', '快捷', '', 'FAST', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('17', '国通', '', 'GTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('18', '天天', '', 'HHTT', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('19', '中铁快运', '', 'ZTKY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}');
INSERT INTO `ims_ewei_shop_exhelper_esheet` VALUES ('20', '邮政快递包裹', '', 'YZPY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}');

DROP TABLE IF EXISTS `ims_ewei_shop_plugin`;
CREATE TABLE `ims_ewei_shop_plugin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayorder` int(11) DEFAULT '0',
  `identity` varchar(50) DEFAULT '',
  `category` varchar(255) DEFAULT '',
  `name` varchar(50) DEFAULT '',
  `version` varchar(10) DEFAULT '',
  `author` varchar(20) DEFAULT '',
  `status` int(11) DEFAULT '0',
  `thumb` varchar(255) DEFAULT '',
  `desc` text,
  `iscom` tinyint(3) DEFAULT '0',
  `deprecated` tinyint(3) DEFAULT '0',
  `isv2` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_displayorder` (`displayorder`),
  KEY `idx_identity` (`identity`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_plugin` VALUES ('1', '1', 'qiniu', 'tool', '七牛存储', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/qiniu.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('2', '2', 'taobao', 'tool', '商品助手', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/taobao.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('3', '3', 'commission', 'biz', '人人分销', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/commission.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('4', '4', 'poster', 'sale', '超级海报', '1.2', '官方', '1', '../addons/ewei_shopv2/static/images/poster.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('5', '5', 'verify', 'biz', 'O2O核销', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/verify.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('6', '6', 'tmessage', 'tool', '会员群发', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/tmessage.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('7', '7', 'perm', 'help', '分权系统', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/perm.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('8', '8', 'sale', 'sale', '营销宝', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/sale.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('9', '9', 'designer', 'help', '店铺装修V1', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/designer.jpg', null, '0', '1', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('10', '10', 'creditshop', 'biz', '积分商城', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/creditshop.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('11', '11', 'virtual', 'biz', '虚拟物品', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/virtual.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('12', '11', 'article', 'help', '文章营销', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/article.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('13', '13', 'coupon', 'sale', '超级券', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/coupon.jpg', null, '1', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('14', '14', 'postera', 'sale', '活动海报', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/postera.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('15', '16', 'system', 'help', '系统工具', '1.0', '官方', '0', '../addons/ewei_shopv2/static/images/system.jpg', null, '0', '1', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('16', '15', 'diyform', 'help', '自定表单', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/diyform.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('17', '16', 'exhelper', 'help', '快递助手', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/exhelper.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('18', '19', 'groups', 'biz', '人人拼团', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/groups.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('19', '20', 'diypage', 'help', '店铺装修', '2.0', '官方', '1', '../addons/ewei_shopv2/static/images/designer.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('20', '22', 'globonus', 'biz', '全民股东', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/globonus.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('21', '23', 'merch', 'biz', '多商户', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/merch.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('22', '26', 'qa', 'help', '帮助中心', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/qa.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('24', '27', 'sms', 'tool', '短信提醒', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/sms.jpg', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('25', '29', 'sign', 'tool', '积分签到', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/sign.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('26', '30', 'sns', 'sale', '全民社区', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/sns.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('27', '33', 'wap', 'tool', '全网通', '1.0', '官方', '1', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('28', '34', 'h5app', 'tool', 'H5APP', '1.0', '官方', '1', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('29', '26', 'abonus', 'biz', '区域代理', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/abonus.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('30', '33', 'printer', 'tool', '小票打印机', '1.0', '官方', '1', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('31', '34', 'bargain', 'tool', '砍价活动', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/bargain.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('32', '35', 'task', 'sale', '任务中心', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/task.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('33', '36', 'cashier', 'biz', '收银台', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/cashier.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('34', '37', 'messages', 'tool', '消息群发', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/messages.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('35', '38', 'seckill', 'sale', '整点秒杀', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/seckill.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('36', '39', 'exchange', 'biz', '兑换中心', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/exchange.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('37', '40', 'lottery', 'biz', '游戏营销', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/lottery.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('38', '41', 'wxcard', 'sale', '微信卡券', '1.0', '官方', '1', '', '', '1', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('39', '42', 'quick', 'biz', '快速购买', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/quick.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('40', '43', 'mmanage', 'tool', '手机端商家管理中心', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/mmanage.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('41', '44', 'pc', 'tool', 'PC端', '1.0', '二开', '1', '../addons/ewei_shopv2/static/images/pc.jpg', '', '0', '0', '0');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('42', '45', 'live', 'sale', '互动直播', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/live.jpg', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('43', '46', 'invitation', 'sale', '邀请卡', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/invitation.png', '', '0', '0', '1');
INSERT INTO `ims_ewei_shop_plugin` VALUES ('44', '47', 'app', 'help', '小程序', '1.0', '官方', '1', '../addons/ewei_shopv2/static/images/app.jpg', '', '0', '0', '1');

");

