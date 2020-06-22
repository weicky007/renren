<?php
pdo_query("

DROP TABLE IF EXISTS `ims_ewei_shop_express`;
CREATE TABLE `ims_ewei_shop_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `status` tinyint(1) DEFAULT '1',
  `displayorder` tinyint(3) unsigned DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  `coding` varchar(10) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_express` (`id`, `name`, `express`, `status`, `displayorder`, `code`, `coding`) VALUES 
(1, '顺丰速运', 'shunfeng', 1, 0, 'JH_014', 'SF'),
(2, '申通快递', 'shentong', 1, 0, 'JH_005', 'STO'),
(3, '韵达快运', 'yunda', 1, 0, 'JH_003', 'YD'),
(4, '天天快递', 'tiantian', 1, 0, 'JH_004', 'HHTT'),
(5, '圆通速递', 'yuantong', 1, 0, 'JH_002', 'YTO'),
(6, '中通快递', 'zhongtong', 1, 0, 'JH_006', 'ZTO'),
(7, 'ems快递', 'ems', 1, 0, 'JH_001', 'EMS'),
(8, '百世快递', 'huitongkuaidi', 1, 0, 'JH_012', 'HTKY'),
(9, '全峰快递', 'quanfengkuaidi', 1, 0, 'JH_009', ''),
(10, '宅急送', 'zhaijisong', 1, 0, 'JH_007', 'ZJS'),
(11, 'aae全球专递', 'aae', 1, 0, 'JHI_049', 'AAE'),
(12, '安捷快递', 'anjie', 1, 0, '', 'AJ'),
(13, '安信达快递', 'anxindakuaixi', 1, 0, 'JH_131', ''),
(14, '彪记快递', 'biaojikuaidi', 1, 0, '', ''),
(15, 'bht', 'bht', 1, 0, 'JHI_008', 'BHT'),
(16, '百福东方国际物流', 'baifudongfang', 1, 0, 'JH_062', ''),
(17, '中国东方（COE）', 'coe', 1, 0, 'JHI_038', ''),
(18, '长宇物流', 'changyuwuliu', 1, 0, '', ''),
(19, '大田物流', 'datianwuliu', 1, 0, 'JH_050', 'DTWL'),
(20, '德邦快递', 'debangwuliu', 1, 0, 'JH_011', 'DBL'),
(21, 'dhl', 'dhl', 1, 0, 'JHI_002', 'DHL'),
(22, 'dpex', 'dpex', 1, 0, 'JHI_011', 'DPEX'),
(23, 'd速快递', 'dsukuaidi', 1, 0, 'JH_049', 'DSWL'),
(24, '递四方', 'disifang', 1, 0, 'JHI_080', 'D4PX'),
(25, 'fedex（国外）', 'fedex', 1, 0, 'JHI_014', 'FEDEX_GJ'),
(26, '飞康达物流', 'feikangda', 1, 0, 'JH_088', 'FKD'),
(27, '凤凰快递', 'fenghuangkuaidi', 1, 0, '', ''),
(28, '飞快达', 'feikuaida', 1, 0, 'JH_151', ''),
(29, '国通快递', 'guotongkuaidi', 1, 0, 'JH_010', 'GTO'),
(30, '港中能达物流', 'ganzhongnengda', 1, 0, 'JH_033', ''),
(31, '广东邮政物流', 'guangdongyouzhengwuliu', 1, 0, 'JH_135', 'GDEMS'),
(32, '共速达', 'gongsuda', 1, 0, 'JH_039', 'GSD'),
(33, '恒路物流', 'hengluwuliu', 1, 0, 'JH_048', 'HLWL'),
(34, '华夏龙物流', 'huaxialongwuliu', 1, 0, 'JH_129', 'HXLWL'),
(35, '海红', 'haihongwangsong', 1, 0, 'JH_132', ''),
(36, '海外环球', 'haiwaihuanqiu', 1, 0, 'JHI_013', ''),
(37, '佳怡物流', 'jiayiwuliu', 1, 0, 'JH_035', 'JYWL'),
(38, '京广速递', 'jinguangsudikuaijian', 1, 0, 'JH_041', 'JGSD'),
(39, '急先达', 'jixianda', 1, 0, 'JH_040', 'JXD'),
(40, '佳吉物流', 'jiajiwuliu', 1, 0, 'JH_030', 'CNEX'),
(41, '加运美物流', 'jymwl', 1, 0, 'JH_054', 'JYM'),
(42, '金大物流', 'jindawuliu', 1, 0, 'JH_079', ''),
(43, '嘉里大通', 'jialidatong', 1, 0, 'JH_060', ''),
(44, '晋越快递', 'jykd', 1, 0, 'JHI_046', 'JYKD'),
(45, '快捷速递', 'kuaijiesudi', 1, 0, 'JH_008', ''),
(46, '联邦快递（国内）', 'lianb', 1, 0, 'JH_122', ''),
(47, '联昊通物流', 'lianhaowuliu', 1, 0, 'JH_021', 'LHT'),
(48, '龙邦物流', 'longbanwuliu', 1, 0, 'JH_019', 'LB'),
(49, '立即送', 'lijisong', 1, 0, 'JH_044', 'LJSKD'),
(50, '乐捷递', 'lejiedi', 1, 0, 'JH_043', ''),
(51, '民航快递', 'minghangkuaidi', 1, 0, 'JH_100', 'MHKD'),
(52, '美国快递', 'meiguokuaidi', 1, 0, 'JHI_044', ''),
(53, '门对门', 'menduimen', 1, 0, 'JH_036', 'MDM'),
(54, 'OCS', 'ocs', 1, 0, 'JHI_012', 'OCS'),
(55, '配思货运', 'peisihuoyunkuaidi', 1, 0, '', ''),
(56, '全晨快递', 'quanchenkuaidi', 1, 0, 'JH_055', 'QCKD'),
(57, '全际通物流', 'quanjitong', 1, 0, 'JH_127', ''),
(58, '全日通快递', 'quanritongkuaidi', 1, 0, 'JH_029', 'QRT'),
(59, '全一快递', 'quanyikuaidi', 1, 0, 'JH_020', 'UAPEX'),
(60, '如风达', 'rufengda', 1, 0, 'JH_017', 'RFD'),
(61, '三态速递', 'santaisudi', 1, 0, 'JH_065', ''),
(62, '盛辉物流', 'shenghuiwuliu', 1, 0, 'JH_066', ''),
(63, '速尔物流', 'suer', 1, 0, '', 'SURE'),
(64, '盛丰物流', 'shengfeng', 1, 0, 'JH_082', 'SFWL'),
(65, '赛澳递', 'saiaodi', 1, 0, 'JH_042', 'SAD'),
(66, '天地华宇', 'tiandihuayu', 1, 0, 'JH_018', 'HOAU'),
(67, 'tnt', 'tnt', 1, 0, 'JHI_003', 'TNT'),
(68, 'ups', 'ups', 1, 0, 'JHI_004', 'UPS'),
(69, '万家物流', 'wanjiawuliu', 1, 0, '', 'WJWL'),
(70, '文捷航空速递', 'wenjiesudi', 1, 0, '', ''),
(71, '伍圆', 'wuyuan', 1, 0, '', ''),
(72, '万象物流', 'wxwl', 1, 0, 'JH_115', 'WXWL'),
(73, '新邦物流', 'xinbangwuliu', 1, 0, 'JH_022', ''),
(74, '信丰物流', 'xinfengwuliu', 1, 0, 'JH_023', 'XFEX'),
(75, '亚风速递', 'yafengsudi', 1, 0, 'JH_075', 'YFSD'),
(76, '一邦速递', 'yibangwuliu', 1, 0, 'JH_064', ''),
(77, '优速物流', 'youshuwuliu', 1, 0, 'JH_013', 'UC'),
(78, '邮政快递包裹', 'youzhengguonei', 1, 0, 'JH_077', 'YZPY'),
(79, '邮政国际包裹挂号信', 'youzhengguoji', 1, 0, '', ''),
(80, '远成物流', 'yuanchengwuliu', 1, 0, 'JH_024', 'YCWL'),
(81, '源伟丰快递', 'yuanweifeng', 1, 0, 'JH_141', ''),
(82, '元智捷诚快递', 'yuanzhijiecheng', 1, 0, 'JH_126', ''),
(83, '运通快递', 'yuntongkuaidi', 1, 0, 'JH_145', 'YTKD'),
(84, '越丰物流', 'yuefengwuliu', 1, 0, 'JH_068', ''),
(85, '源安达', 'yad', 1, 0, 'JH_067', 'YADEX'),
(86, '银捷速递', 'yinjiesudi', 1, 0, 'JH_148', ''),
(87, '中铁快运', 'zhongtiekuaiyun', 1, 0, 'JH_015', 'ZTKY'),
(88, '中邮物流', 'zhongyouwuliu', 1, 0, 'JH_027', 'ZYKD'),
(89, '忠信达', 'zhongxinda', 1, 0, 'JH_086', ''),
(90, '芝麻开门', 'zhimakaimen', 1, 0, 'JH_026', ''),
(91, '安能物流', 'annengwuliu', 1, 0, 'JH_059', 'ANE'),
(92, '京东快递', 'jd', 1, 0, 'JH_046', 'JD'),
(93, '微特派', 'weitepai', 1, 0, '', 'WTP'),
(94, '九曳供应链', 'jiuyescm', 1, 0, '', 'JIUYE'),
(95, '跨越速运', 'kuayue', 1, 0, '', 'KYSY'),
(96, '德邦物流', 'debangkuaidi', 1, 0, '', 'DBLKY'),
(97, '中通快运', 'zhongtongkuaiyun', 1, 0, '', 'ZTOKY');

DROP TABLE IF EXISTS `ims_ewei_shop_exhelper_esheet`;
CREATE TABLE `ims_ewei_shop_exhelper_esheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `express` varchar(50) DEFAULT '',
  `code` varchar(20) NOT NULL DEFAULT '',
  `datas` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

INSERT INTO `ims_ewei_shop_exhelper_esheet` (`id`, `name`, `express`, `code`, `datas`) VALUES
(1, '顺丰', 'shunfeng', 'SF', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"三联210\";s:4:\"spec\";s:38:\"（宽100mm 高210mm 切点90/60/60）\";s:4:\"size\";s:3:\"210\";s:9:\"isdefault\";i:0;}}'),
(2, '百世快递', 'huitongkuaidi', 'HTKY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联183\";s:4:\"spec\";s:37:\"（宽100mm 高183mm 切点87/5/91）\";s:4:\"size\";s:3:\"183\";s:9:\"isdefault\";i:1;}}'),
(3, '韵达', 'yunda', 'YD', 'a:3:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:0;}i:1;a:4:{s:5:\"style\";s:9:\"二联203\";s:4:\"spec\";s:36:\"（宽100mm 高203mm 切点152/51）\";s:4:\"size\";s:3:\"203\";s:9:\"isdefault\";i:1;}i:2;a:4:{s:5:\"style\";s:9:\"一联130\";s:4:\"spec\";s:35:\"（宽76mm 高130mm 切点152/51）\";s:4:\"size\";s:3:\"130\";s:9:\"isdefault\";i:0;}}'),
(4, '申通', 'shentong', 'STO', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:35:\"（宽100mm 高150mm 切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:0;}}'),
(5, '圆通', 'yuantong', 'YTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(6, 'EMS', 'ems', 'EMS', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(7, '中通', 'zhongtong', 'ZTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:8:\"单联76\";s:4:\"spec\";s:17:\"(宽76mm高130mm)\";s:4:\"size\";s:2:\"76\";s:9:\"isdefault\";i:0;}}'),
(8, '德邦', 'debangwuliu', 'DBL', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联177\";s:4:\"spec\";s:34:\"（宽100mm高177mm切点107/70）\";s:4:\"size\";s:3:\"177\";s:9:\"isdefault\";i:1;}}'),
(9, '优速', 'youshuwuliu', 'UC', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(10, '宅急送', 'zhaijisong', 'ZJS', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联120\";s:4:\"spec\";s:33:\"（宽100mm高116mm切点98/18）\";s:4:\"size\";s:3:\"120\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:0;}}'),
(11, '京东', 'jd', 'JD', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联110\";s:4:\"spec\";s:33:\"（宽100mm高110mm切点60/50）\";s:4:\"size\";s:3:\"110\";s:9:\"isdefault\";i:1;}}'),
(12, '信丰', 'xinfengwuliu', 'XFEX', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(13, '全峰', 'quanfengkuaidi', 'QFKD', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(14, '跨越速运', 'kuayue', 'KYSY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联137\";s:4:\"spec\";s:34:\"（宽100mm高137mm切点101/36）\";s:4:\"size\";s:3:\"137\";s:9:\"isdefault\";i:1;}}'),
(15, '安能', 'annengwuliu', 'ANE', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"三联180\";s:4:\"spec\";s:37:\"（宽100mm高180mm切点110/30/40）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(16, '快捷', 'kuaijiesudi', 'FAST', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(17, '国通', 'guotongkuaidi', 'GTO', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(18, '天天', 'tiantian', 'HHTT', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(19, '中铁快运', 'zhongtiekuaiyun', 'ZTKY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(20, '邮政快递包裹', 'youzhengguonei', 'YZPY', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联180\";s:4:\"spec\";s:34:\"（宽100mm高180mm切点110/70）\";s:4:\"size\";s:3:\"180\";s:9:\"isdefault\";i:1;}}'),
(21, '邮政国内标快', 'youzhengguonei', 'YZBK', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(22, '全一快递', 'quanyikuaidi', 'UAPEX', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:32:\"（宽90mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(23, '速尔快递', 'sue', 'SURE', 'a:1:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}}'),
(24, '顺丰(陆运)', 'shunfeng', 'SF', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"三联210\";s:4:\"spec\";s:38:\"（宽100mm 高210mm 切点90/60/60）\";s:4:\"size\";s:3:\"210\";s:9:\"isdefault\";i:0;}}');

DROP TABLE IF EXISTS `ims_ewei_shop_member_message_template_type`;
CREATE TABLE `ims_ewei_shop_member_message_template_type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `typecode` varchar(255) DEFAULT NULL,
  `templatecode` varchar(255) DEFAULT NULL,
  `templateid` varchar(255) DEFAULT NULL,
  `templatename` varchar(255) DEFAULT NULL,
  `content` varchar(1000) DEFAULT NULL,
  `typegroup` varchar(255) DEFAULT '',
  `groupname` varchar(255) DEFAULT '',
  `showtotaladd` tinyint(1) DEFAULT '0',
  KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

INSERT INTO `ims_ewei_shop_member_message_template_type` (`id`, `name`, `typecode`, `templatecode`, `templateid`, `templatename`, `content`, `typegroup`, `groupname`, `showtotaladd`) VALUES
(1, '订单付款通知', 'saler_pay', 'OPENTM405584202', '', '订单付款通知', '{{first.DATA}}订单编号：{{keyword1.DATA}}商品名称：{{keyword2.DATA}}商品数量：{{keyword3.DATA}}支付金额：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(2, '自提订单提交成功通知', 'carrier', 'OPENTM201594720', '', '订单付款通知', '{{first.DATA}}自提码：{{keyword1.DATA}}商品详情：{{keyword2.DATA}}提货地址：{{keyword3.DATA}}提货时间：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(3, '订单取消通知', 'cancel', 'OPENTM201764653', '', '订单关闭提醒', '{{first.DATA}}订单商品：{{keyword1.DATA}}订单编号：{{keyword2.DATA}}下单时间：{{keyword3.DATA}}订单金额：{{keyword4.DATA}}关闭时间：{{keyword5.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(4, '订单即将取消通知', 'willcancel', 'OPENTM201764653', '', '订单关闭提醒', '{{first.DATA}}订单商品：{{keyword1.DATA}}订单编号：{{keyword2.DATA}}下单时间：{{keyword3.DATA}}订单金额：{{keyword4.DATA}}关闭时间：{{keyword5.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(5, '订单支付成功通知', 'pay', 'OPENTM405584202', '', '订单支付通知', '{{first.DATA}}订单编号：{{keyword1.DATA}}商品名称：{{keyword2.DATA}}商品数量：{{keyword3.DATA}}支付金额：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(6, '订单发货通知', 'send', 'OPENTM401874827', '', '订单发货通知', '{{first.DATA}}订单编号：{{keyword1.DATA}}快递公司：{{keyword2.DATA}}快递单号：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(7, '自动发货通知(虚拟物品及卡密)', 'virtualsend', 'OPENTM207793687', '', '自动发货通知', '{{first.DATA}}商品名称：{{keyword1.DATA}}订单号：{{keyword2.DATA}}订单金额：{{keyword3.DATA}}卡密信息：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(8, '订单状态更新(修改收货地址)(修改价格)', 'orderstatus', 'TM00017', '', '订单付款通知', '{{first.DATA}}订单编号:{{OrderSn.DATA}}订单状态:{{OrderStatus.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(9, '退款成功通知', 'refund1', 'TM00430', '', '退款成功通知', '{{first.DATA}}退款金额：{{orderProductPrice.DATA}}商品详情：{{orderProductName.DATA}}订单编号：{{orderName.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(10, '换货成功通知', 'refund3', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(11, '退款申请驳回通知', 'refund2', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(12, '充值成功通知', 'recharge_ok', 'OPENTM207727673', '', '充值成功提醒', '{{first.DATA}}充值金额：{{keyword1.DATA}}充值时间：{{keyword2.DATA}}账户余额：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(13, '提现成功通知', 'withdraw_ok', 'OPENTM207422808', '', '提现通知', '{{first.DATA}}申请提现金额：{{keyword1.DATA}}取提现手续费：{{keyword2.DATA}}实际到账金额：{{keyword3.DATA}}提现渠道：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(14, '会员升级通知(任务处理通知)', 'upgrade', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(15, '充值成功通知（后台管理员手动）', 'backrecharge_ok', 'OPENTM207727673', '', '充值成功提醒', '{{first.DATA}}充值金额：{{keyword1.DATA}}充值时间：{{keyword2.DATA}}账户余额：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(16, '积分变动提醒', 'backpoint_ok', 'OPENTM207509450', '', '积分变动提醒', '{{first.DATA}}获得时间：{{keyword1.DATA}}获得积分：{{keyword2.DATA}}获得原因：{{keyword3.DATA}}当前积分：{{keyword4.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(17, '换货发货通知', 'refund4', 'OPENTM401874827', '', '订单发货通知', '{{first.DATA}}订单编号：{{keyword1.DATA}}快递公司：{{keyword2.DATA}}快递单号：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(18, '砍价活动通知', 'bargain_message', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'bargain', '砍价消息通知', 0),
(19, '拼团活动通知', 'groups', NULL, NULL, NULL, NULL, 'groups', '拼团消息通知', 0),
(20, '人人分销通知', 'commission', NULL, NULL, NULL, NULL, 'commission', '分销消息通知', 0),
(21, '商品付款通知', 'saler_goodpay', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(22, '砍到底价通知', 'bargain_fprice', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'bargain', '砍价消息通知', 0),
(23, '订单收货通知(卖家)', 'saler_finish', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(24, '余额兑换成功通知', 'exchange_balance', 'OPENTM207727673', '', '充值成功提醒', '{{first.DATA}}充值金额：{{keyword1.DATA}}充值时间：{{keyword2.DATA}}账户余额：{{keyword3.DATA}}{{remark.DATA}}', 'exchange', '兑换中心消息通知', 0),
(25, '积分兑换成功通知', 'exchange_score', 'OPENTM207509450', '', '积分变动提醒', '{{first.DATA}}获得时间：{{keyword1.DATA}}获得积分：{{keyword2.DATA}}获得原因：{{keyword3.DATA}}当前积分：{{keyword4.DATA}}{{remark.DATA}}', 'exchange', '兑换中心消息通知', 0),
(26, '兑换中心余额充值通知', 'exchange_recharge', 'OPENTM207727673', '', '充值成功提醒', '{{first.DATA}}充值金额：{{keyword1.DATA}}充值时间：{{keyword2.DATA}}账户余额：{{keyword3.DATA}}{{remark.DATA}}', 'exchange', '兑换中心消息通知', 0),
(27, '游戏中心通知', 'lottery_get', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'lottery', '抽奖消息通知', 0),
(35, '库存预警通知', 'saler_stockwarn', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(36, '卖家核销商品核销通知', 'o2o_sverify', 'OPENTM409521536', '', '核销成功提醒', '{{first.DATA}}核销项目：{{keyword1.DATA}}核销时间：{{keyword2.DATA}}核销门店：{{keyword3.DATA}}{{remark.DATA}}', 'o2o', 'O2O消息通知', 0),
(37, '核销商品核销通知', 'o2o_bverify', 'OPENTM409521536', '', '核销成功提醒', '{{first.DATA}}核销项目：{{keyword1.DATA}}核销时间：{{keyword2.DATA}}核销门店：{{keyword3.DATA}}{{remark.DATA}}', 'o2o', 'O2O消息通知', 0),
(38, '卖家商品预约通知', 'o2o_snorder', 'OPENTM202447657', '', '预约成功提醒', '{{first.DATA}}预约项目：{{keyword1.DATA}}预约时间：{{keyword2.DATA}}{{remark.DATA}}', 'o2o', 'O2O消息通知', 0),
(39, '商品预约成功通知', 'o2o_bnorder', 'OPENTM202447657', '', '预约成功提醒', '{{first.DATA}}预约项目：{{keyword1.DATA}}预约时间：{{keyword2.DATA}}{{remark.DATA}}', 'o2o', 'O2O消息通知', 0),
(42, '商品下单通知', 'saler_goodsubmit', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(50, '维权订单通知', 'saler_refund', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(43, '任务接取通知', 'task_pick', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(44, '任务进度通知', 'task_progress', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(45, '任务完成通知', 'task_finish', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(46, '任务海报接取通知', 'task_poster_pick', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(47, '任务海报进度通知', 'task_poster_progress', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(48, '任务海报完成通知', 'task_poster_finish', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(49, '任务海报扫描通知', 'task_poster_scan', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'task', '任务中心消息通知', 0),
(52, '成为分销商通知', 'commission_become', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(53, '新增下线通知', 'commission_agent_new', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(54, '下级付款通知', 'commission_order_pay', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(55, '下级确认收货通知', 'commission_order_finish', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(56, '提现申请提交通知', 'commission_apply', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(57, '提现申请完成审核通知', 'commission_check', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(58, '佣金打款通知', 'commission_pay', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(59, '分销商等级升级通知', 'commission_upgrade', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(60, '成为股东通知', 'globonus_become', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'globonus', '股东消息通知', 0),
(61, '股东等级升级通知', 'globonus_upgrade', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'globonus', '股东消息通知', 0),
(62, '分红发放通知', 'globonus_pay', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'globonus', '股东消息通知', 0),
(63, '奖励发放通知', 'article', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'article', '文章营销消息通知', 0),
(64, '成为区域代理通知', 'abonus_become', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'abonus', '区域代理消息通知', 0),
(65, '省级代理等级升级通知', 'abonus_upgrade1', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'abonus', '区域代理消息通知', 0),
(66, '市级代理等级升级通知', 'abonus_upgrade2', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'abonus', '区域代理消息通知', 0),
(67, '区级代理等级升级通知', 'abonus_upgrade3', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'abonus', '区域代理消息通知', 0),
(68, '区域代理分红发放通知', 'abonus_pay', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'abonus', '区域代理消息通知', 0),
(69, '入驻申请通知', 'merch_apply', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'merch', '商家通知', 0),
(70, '提现申请提交通知', 'merch_applymoney', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'merch', '商家通知', 0),
(71, '社区会员评论通知', 'reply', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sns', '人人社区消息通知', 0),
(51, '社区会员升级通知', 'sns', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'sns', '人人社区消息通知', 0),
(74, '周期购定时发货通知', 'cycelbuy_timing', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'cycelbuy', '周期购消息通知', 0),
(73, '修改收货时间卖家通知', 'cycelbuy_seller_date', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'cycelbuy', '周期购消息通知', 0),
(72, '修改地址卖家通知', 'cycelbuy_seller_address', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'cycelbuy', '周期购消息通知', 0),
(75, '修改收货时间买家通知', 'cycelbuy_buyer_date', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'cycelbuy', '周期购消息通知', 0),
(76, '修改地址买家通知', 'cycelbuy_buyer_address', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'cycelbuy', '周期购消息通知', 0),
(77, '分销提现申请提醒', 'commission_applymoney', 'OPENTM207574677', NULL, '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0),
(80, '成为团长通知', 'dividend_become', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'dividend', '团队分红通知', 0),
(81, '成为团长通知(卖家)', 'dividend_become_saler', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'dividend', '团队分红通知', 0),
(82, '团员成为团长通知', 'dividend_downline_become', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'dividend', '团队分红通知', 0),
(83, '团长提现通知', 'dividend_apply', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'dividend', '团队分红通知', 0),
(84, '提现审核完成通知', 'dividend_check', 'OPENTM207574677', '', '业务处理通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}业务状态：{{keyword2.DATA}}业务内容：{{keyword3.DATA}}{{remark.DATA}}', 'dividend', '团队分红通知', 0),
(85, '好友瓜分券活动发起通知', 'friendcoupon_launch', 'OPENTM415477060', '', '业务处理结果通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}处理状态：{{keyword2.DATA}}处理内容：{{keyword3.DATA}}{{remark.DATA}}', 'friendcoupon', '好友瓜分券', 0),
(86, '好友瓜分券活动完成通知', 'friendcoupon_complete', 'OPENTM415477060', '', '业务处理结果通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}处理状态：{{keyword2.DATA}}处理内容：{{keyword3.DATA}}{{remark.DATA}}', 'friendcoupon', '好友瓜分券', 0),
(87, '好友瓜分券活动失败通知', 'friendcoupon_failed', 'OPENTM415477060', '', '业务处理结果通知', '{{first.DATA}}业务类型：{{keyword1.DATA}}处理状态：{{keyword2.DATA}}处理内容：{{keyword3.DATA}}{{remark.DATA}}', 'friendcoupon', '好友瓜分券', 0),
(89, '多商户审核成功通知', 'march_type_success', 'OPENTM411720444', '', '审核成功通知', '{{first.DATA}}审核状态：{{keyword1.DATA}}审核时间：{{keyword2.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(90, '多商户审核失败通知', 'march_type_fail', 'OPENTM413117348', '', '审核失败通知', '{{first.DATA}}审核状态：{{keyword1.DATA}}审核时间：{{keyword2.DATA}}{{remark.DATA}}', 'sys', '系统消息通知', 0),
(91, '申请成为分销商通知', 'commission_become_apply', 'OPENTM401202609', '', '申请成为分销商通知', '{{first.DATA}}申请名称：{{keyword1.DATA}}申请人：{{keyword2.DATA}}申请类型：{{keyword3.DATA}}申请时间：{{keyword4.DATA}}{{remark.DATA}}', 'commission', '分销消息通知', 0);

DROP TABLE IF EXISTS `ims_ewei_shop_plugin`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_plugin` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

INSERT INTO `ims_ewei_shop_plugin` (`id`, `displayorder`, `identity`, `category`, `name`, `version`, `author`, `status`, `thumb`, `desc`, `iscom`, `deprecated`, `isv2`) VALUES
(1, 1, 'qiniu', 'tool', '七牛存储', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/qiniu.jpg', NULL, 1, 0, 0),
(2, 2, 'taobao', 'tool', '商品助手', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/taobao.jpg', '', 0, 0, 0),
(3, 3, 'commission', 'biz', '人人分销', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/commission.jpg', '', 0, 0, 0),
(4, 4, 'poster', 'sale', '超级海报', '1.2', '官方', 1, '../addons/ewei_shopv2/static/images/poster.jpg', '', 0, 0, 0),
(5, 5, 'verify', 'biz', 'O2O核销', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/verify.jpg', NULL, 1, 0, 0),
(6, 6, 'tmessage', 'tool', '会员群发', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/tmessage.jpg', NULL, 1, 0, 0),
(7, 7, 'perm', 'help', '分权系统', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/perm.jpg', NULL, 1, 0, 0),
(8, 8, 'sale', 'sale', '营销宝', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/sale.jpg', NULL, 1, 0, 0),
(9, 9, 'designer', 'help', '店铺装修V1', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/designer.jpg', NULL, 0, 1, 0),
(10, 10, 'creditshop', 'biz', '积分商城', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/creditshop.jpg', '', 0, 0, 0),
(11, 11, 'virtual', 'biz', '虚拟物品', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/virtual.jpg', NULL, 1, 0, 0),
(12, 11, 'article', 'help', '文章营销', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/article.jpg', '', 0, 0, 0),
(13, 13, 'coupon', 'sale', '超级券', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/coupon.jpg', NULL, 1, 0, 0),
(14, 14, 'postera', 'sale', '活动海报', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/postera.jpg', '', 0, 0, 0),
(15, 16, 'system', 'help', '系统工具', '1.0', '官方', 0, '../addons/ewei_shopv2/static/images/system.jpg', NULL, 0, 1, 0),
(16, 15, 'diyform', 'help', '自定表单', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/diyform.jpg', '', 0, 0, 0),
(17, 16, 'exhelper', 'help', '快递助手', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/exhelper.jpg', '', 0, 0, 0),
(18, 19, 'groups', 'biz', '人人拼团', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/groups.jpg', '', 0, 0, 0),
(19, 20, 'diypage', 'help', '店铺装修', '2.0', '官方', 1, '../addons/ewei_shopv2/static/images/designer.jpg', '', 0, 0, 0),
(20, 22, 'globonus', 'biz', '全民股东', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/globonus.jpg', '', 0, 0, 0),
(21, 23, 'merch', 'biz', '多商户', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/merch.jpg', '', 0, 0, 1),
(22, 26, 'qa', 'help', '帮助中心', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/qa.jpg', '', 0, 0, 1),
(24, 27, 'sms', 'tool', '短信提醒', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/sms.jpg', '', 1, 0, 1),
(25, 29, 'sign', 'tool', '积分签到', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/sign.jpg', '', 0, 0, 1),
(26, 30, 'sns', 'sale', '全民社区', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/sns.jpg', '', 0, 0, 1),
(27, 33, 'wap', 'tool', '全网通', '1.0', '官方', 1, '', '', 1, 0, 1),
(28, 34, 'h5app', 'tool', 'H5APP', '1.0', '官方', 1, '', '', 1, 0, 1),
(29, 26, 'abonus', 'biz', '区域代理', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/abonus.jpg', '', 0, 0, 1),
(30, 33, 'printer', 'tool', '小票打印机', '1.0', '官方', 1, '', '', 1, 0, 1),
(31, 34, 'bargain', 'tool', '砍价活动', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/bargain.jpg', '', 0, 0, 1),
(32, 35, 'task', 'sale', '任务中心', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/task.jpg', '', 0, 0, 1),
(33, 36, 'cashier', 'biz', '收银台', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/cashier.jpg', '', 0, 0, 1),
(34, 37, 'messages', 'tool', '消息群发', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/messages.jpg', '', 0, 0, 1),
(35, 38, 'seckill', 'sale', '整点秒杀', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/seckill.jpg', '', 0, 0, 1),
(36, 39, 'exchange', 'biz', '兑换中心', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/exchange.jpg', '', 0, 0, 1),
(37, 65, 'wxcard', 'sale', '微信卡券', '1.0', '官方', 1, '', NULL, 1, 0, 1),
(38, 42, 'quick', 'biz', '快速购买', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/quick.jpg', '', 0, 0, 1),
(39, 43, 'mmanage', 'tool', '手机端商家管理中心', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/mmanage.jpg', '', 0, 0, 1),
(40, 44, 'polyapi', 'tool', '进销存-网店管家', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/polyapi.jpg', '', 0, 0, 1),
(41, 45, 'lottery', 'biz', '游戏营销', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/lottery.jpg', '', 0, 0, 1),
(42, 46, 'pc', 'sale', 'PC端', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/pc.jpg', '', 0, 0, 1),
(43, 47, 'live', 'sale', '互动直播', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/live.jpg', '', 0, 0, 1),
(44, 48, 'invitation', 'sale', '邀请卡', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/invitation.png', '', 0, 0, 1),
(45, 46, 'app', 'biz', '小程序', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/app.jpg', '', 0, 0, 1),
(46, 49, 'cycelbuy', 'biz', '周期购', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/cycelbuy.jpg', '', 0, 0, 1),
(47, 50, 'dividend', 'biz', '团队分红', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/dividend.jpg', '', 0, 0, 1),
(48, 51, 'merchmanage', 'tool', '多商户手机端管理中心', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/merchmanage.jpg', '', 0, 0, 1),
(49, 52, 'membercard', 'sale', '付费会员卡', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/membercard.png', NULL, 0, 0, 1),
(50, 53, 'friendcoupon', 'sale', '好友瓜分券', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/friendcoupon.png', NULL, 0, 0, 1),
(51, 54, 'open_messikefu', 'tool', '聚合客服', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/open_messikefu.jpg', NULL, 0, 0, 1),
(52, 55, 'goodscircle', 'tool', '好物圈', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/goodscircle.png', NULL, 0, 0, 1),
(53, 56, 'open_farm', 'tool', '人人农场', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/open_farm.png', NULL, 0, 0, 1),
(54, 57, 'wxlive', 'sale', '小程序直播', '1.0', '官方', 1, '../addons/ewei_shopv2/static/images/wxlive.png', NULL, 0, 0, 1);

DROP TABLE IF EXISTS `ims_ewei_shop_task_extension`;
CREATE TABLE IF NOT EXISTS `ims_ewei_shop_task_extension` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskname` varchar(255) NOT NULL DEFAULT '',
  `taskclass` varchar(25) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `classify` varchar(255) NOT NULL DEFAULT '',
  `classify_name` varchar(255) NOT NULL DEFAULT '',
  `verb` varchar(255) NOT NULL DEFAULT '',
  `unit` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

insert into `ims_ewei_shop_task_extension`(`id`,`taskname`,`taskclass`,`status`,`classify`,`classify_name`,`verb`,`unit`) values
('1', '推荐人数', 'commission_member', '1', 'number', 'commission', '推荐', '人'),
('2', '分销佣金', 'commission_money', '1', 'number', 'commission', '达到', '元'),
('3', '分销订单', 'commission_order', '1', 'number', 'commission', '达到', '笔'),
('4', '订单满额', 'cost_enough', '1', 'number', 'number', '满', '元'),
('5', '累计金额', 'cost_total', '1', 'number', 'number', '累计', '元'),
('6', '订单满额', 'cost_enough', '1', 'number', 'cost', '满', '元'),
('7', '累计金额', 'cost_total', '1', 'number', 'cost', '累计', '元'),
('8', '订单数量', 'cost_count', '1', 'number', 'cost', '达到', '单'),
('9', '指定商品', 'cost_goods', '1', 'select', 'cost', '购买指定商品', '件'),
('10', '商品评价', 'cost_comment', '1', 'number', 'cost', '评价订单', '次'),
('11', '累计充值', 'cost_rechargetotal', '1', 'number', 'cost', '达到', '元'),
('12', '充值满额', 'cost_rechargeenough', '1', 'number', 'cost', '满', '元'),
('13', '绑定手机', 'member_info', '1', 'boole', 'member', '绑定手机号（必须开启wap或小程序）', '');

DROP TABLE IF EXISTS `ims_ewei_shop_task_type`;
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

insert into `ims_ewei_shop_task_type`(`id`,`type_key`,`type_name`,`description`,`verb`,`numeric`,`unit`,`goods`,`theme`,`once`) values
('1','poster','任务海报','把生成的海报并分享给朋友，朋友扫描并关注公众号即可获得奖励。','转发海报并吸引','1','人关注','0','primary','0'),
('2','info_phone','绑定手机','在个人中心中，绑定手机号，即可完成任务获得奖励。','绑定手机','0','','0','warning','0'),
('3','order_first','首次购物','在商城中首次下单，即可获得奖励，必须确认收货。','首次在商城中下单购物','0','','0','warning','0'),
('4','recharge_full','单笔充值满额','在商城中充值余额，单笔充值满额，即可获得奖励。','单笔充值满','1','元','0','success','1'),
('5','order_full','单笔满额','在商城中下单，单笔满额即可获得奖励，必须确认收货。','单笔订单满','1','元','0','success','1'),
('6','order_all','累计消费','在商城中购物消费，累计满额即可获得奖励，无需确认收货。','购物总消费额达到','1','元','0','success','0'),
('7','pyramid_money','分销佣金','只有分销商可接此任务。累计分销佣金满额，即可完成任务。','分销商获得佣金金额达','1','元','0','primary','0'),
('8','pyramid_num','下级人数','只有分销商可接此任务。累计下级人数达标，即可完成任务。','分销商推荐下级人数达','1','人','0','primary','0'),
('9','comment','商品好评','任意给一个商品五星好评，即可完成任务获得奖励。','给商品好评','0','','0','warning','0'),
('10','post','社区发帖','在社区中发表指定篇帖子，即可完成任务获得奖励。','在论坛中发表','1','篇帖子','0','warning','0'),
('11','goods','购买指定商品','购买指定商品后即可完成任务，必须确认收货。','购买指定商品','0','','1','info','0'),
('12','recharge_count','累计充值满额','在商城中充值余额，累计充值满额，即可获得奖励。','累计充值满','1','元','0','success','0');
('13','child_agent','下级分销商人数','直属下级分销商人数，累计下级分销商人数达标，即可获得奖励。','直属下级分销商人数达','1','人','0','primary','0');

INSERT INTO `ims_ewei_shop_diypage_template` (`id`, `uniacid`, `type`, `name`, `data`, `preview`, `tplid`, `cate`, `deleted`, `merch`) VALUES
(16, 0, 4, '新分销中心', 'eyJwYWdlIjp7InR5cGUiOiI0IiwidGl0bGUiOiJcdTUyMDZcdTk1MDBcdTRlMmRcdTVmYzMiLCJuYW1lIjoiXHU1MjA2XHU5NTAwXHU0ZTJkXHU1ZmMzIiwiZGVzYyI6IiIsImljb24iOiIiLCJrZXl3b3JkIjoiIiwiYmFja2dyb3VuZCI6IiNmM2YzZjMiLCJkaXltZW51IjoiLTEiLCJmb2xsb3diYXIiOiIwIiwidmlzaXQiOiIwIiwidmlzaXRsZXZlbCI6eyJtZW1iZXIiOiIiLCJjb21taXNzaW9uIjoiIn0sIm5vdmlzaXQiOnsidGl0bGUiOiIiLCJsaW5rIjoiIn19LCJpdGVtcyI6eyJNMTQ3NTk3NjIxMDU0NiI6eyJwYXJhbXMiOnsic3R5bGUiOiJkZWZhdWx0MyIsInNldGljb24iOiJpY29uLXNldHRpbmdzIiwic2V0bGluayI6Ii5cL2luZGV4LnBocD9pPTEyJmM9ZW50cnkmbT1ld2VpX3Nob3B2MiZkbz1tb2JpbGUmcj1nb29kcyZpc3RpbWU9MSIsImxlZnRuYXYiOiJcdTYzZDBcdTczYjAxIiwibGVmdG5hdmxpbmsiOiIiLCJyaWdodG5hdiI6Ilx1NjNkMFx1NzNiMDIiLCJyaWdodG5hdmxpbmsiOiIiLCJjZW50ZXJuYXYiOiJcdTYzZDBcdTczYjAiLCJjZW50ZXJuYXZsaW5rIjoiIiwiaGlkZXVwIjoiMCJ9LCJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZlYTIzZCIsInRleHRjb2xvciI6IiNmZmZmZmYiLCJ0ZXh0bGlnaHQiOiIjZmZmZmZmIn0sImlkIjoibWVtYmVyYyJ9LCJNMTUyNjYyMTY5MzUxNyI6eyJzdHlsZSI6eyJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiIsInRleHRjb2xvciI6IiMwMDAwMDAiLCJpY29uY29sb3IiOiIjZmY4MDAwIn0sInBhcmFtcyI6eyJpY29uY2xhc3MiOiJpY29uLWxpbmsifSwidHlwZSI6IjQiLCJpZCI6ImNvbW1pc3Npb25fc2hhcmVjb2RlIn0sIk0xNTI2ODcwNDk3MjQwIjp7InN0eWxlIjp7ImJhY2tncm91bmQiOiIjZmZmZmZmIiwicHJpY2Vjb2xvciI6IiNmZjgwMDAiLCJ0ZXh0Y29sb3IiOiIjMDAwMDAwIiwiYnRuY29sb3IiOiIjZmY4MDAwIn0sInR5cGUiOiI0IiwibWF4IjoiMSIsImlkIjoiY29tbWlzc2lvbl9ibG9jayJ9LCJNMTUyNjYxNTc2ODY3MiI6eyJzdHlsZSI6eyJoZWlnaHQiOiIxMCIsImJhY2tncm91bmQiOiIjZjNmM2YzIn0sImlkIjoiYmxhbmsifSwiTTE0NzU5NzYyMTIzMDUiOnsicGFyYW1zIjp7InJvd251bSI6IjIiLCJuZXdzdHlsZSI6IjEifSwic3R5bGUiOnsiYmFja2dyb3VuZCI6IiNmZmZmZmYiLCJ0aXBjb2xvciI6IiNmZWIzMTIifSwiZGF0YSI6eyJDMTQ3NTk3NjIxMjMwNSI6eyJpY29uY2xhc3MiOiJpY29uLXFpYW4iLCJpY29uY29sb3IiOiIjZmViMzEyIiwidGV4dCI6Ilx1NTIwNlx1OTUwMFx1NGY2M1x1OTFkMSIsInRleHRjb2xvciI6IiM2NjY2NjYiLCJ0aXBudW0iOiIwLjAwIiwidGlwdGV4dCI6Ilx1NTE0MyJ9LCJDMTQ3NTk3NjIxMjMwNiI6eyJpY29uY2xhc3MiOiJpY29uLWRpbmdkYW4yIiwiaWNvbmNvbG9yIjoiIzUwYjZmZSIsInRleHQiOiJcdTRmNjNcdTkxZDFcdTY2MGVcdTdlYzYiLCJ0ZXh0Y29sb3IiOiIjNjY2NjY2IiwibGlua3VybCI6IiIsInRpcG51bSI6IjUwIiwidGlwdGV4dCI6Ilx1N2IxNCJ9LCJDMTQ3NTk3NjIxMjMwOCI6eyJpY29uY2xhc3MiOiJpY29uLXRpeGlhbjEiLCJpY29uY29sb3IiOiIjZmY3NDFkIiwidGV4dCI6Ilx1NjNkMFx1NzNiMFx1NjYwZVx1N2VjNiIsInRleHRjb2xvciI6IiM2NjY2NjYiLCJsaW5rdXJsIjoiIiwidGlwbnVtIjoiMTAiLCJ0aXB0ZXh0IjoiXHU3YjE0In0sIkMxNDc1OTc2MjEyMzA5Ijp7Imljb25jbGFzcyI6Imljb24taGVpbG9uZ2ppYW5ndHViaWFvMTEiLCJpY29uY29sb3IiOiIjZmY3NDFkIiwidGV4dCI6Ilx1NjIxMVx1NzY4NFx1NGUwYlx1N2ViZiIsInRleHRjb2xvciI6IiM2NjY2NjYiLCJsaW5rdXJsIjoiIiwidGlwbnVtIjoiMiIsInRpcHRleHQiOiJcdTRlYmEifX0sImlkIjoiYmxvY2tncm91cCJ9LCJNMTUyNjYxNDU1MzE1MSI6eyJzdHlsZSI6eyJtYXJnaW50b3AiOiIxMCIsImJhY2tncm91bmQiOiIjZmZmZmZmIiwiaWNvbmNvbG9yIjoiI2ZmODAwMCIsInRleHRjb2xvciI6IiMwMDAwMDAiLCJyZW1hcmtjb2xvciI6IiM4ODg4ODgifSwiZGF0YSI6eyJDMTUyNjYxNDU1MzE1MiI6eyJ0ZXh0IjoiXHU2M2E4XHU1ZTdmXHU0ZThjXHU3ZWY0XHU3ODAxIiwibGlua3VybCI6IiIsImljb25jbGFzcyI6Imljb24tZXJ3ZWltYTEiLCJyZW1hcmsiOiIiLCJkb3RudW0iOiIifX0sImlkIjoibGlzdG1lbnUifSwiTTE1MjY2MTQ1NzUyMTIiOnsic3R5bGUiOnsibWFyZ2ludG9wIjoiMTAiLCJiYWNrZ3JvdW5kIjoiI2ZmZmZmZiIsImljb25jb2xvciI6IiNmZjgwMDAiLCJ0ZXh0Y29sb3IiOiIjMDAwMDAwIiwicmVtYXJrY29sb3IiOiIjODg4ODg4In0sImRhdGEiOnsiQzE1MjY2MTQ1NzUyMTIiOnsidGV4dCI6Ilx1NWMwZlx1NWU5N1x1OGJiZVx1N2Y2ZSIsImxpbmt1cmwiOiIiLCJpY29uY2xhc3MiOiJpY29uLXNob3AiLCJyZW1hcmsiOiIiLCJkb3RudW0iOiIifX0sImlkIjoibGlzdG1lbnUifX19', '../addons/ewei_shopv2/plugin/diypage/static/template/commission/preview.png', 15, 0, 0, 0);
");


$res = pdo_fieldexists('mc_credits_record', 'presentcredit');
        if (empty($res)) {
            pdo_query("ALTER TABLE ".tablename('mc_credits_record')." ADD `presentcredit` decimal(10,2);");
        }
pdo_insert('ewei_shop_task_type', array('id'=>13,'type_key'=>'child_agent','type_name'=>'下级分销商人数','description'=>'直属下级分销商人数，累计下级分销商人数达标,即可获得奖励','verb'=>'直属下级分销商人数达','numeric'=>1,'unit'=>'人','goods'=>0,'theme'=>'primary','once'=>0), true);

$res = pdo_get('ewei_shop_member_message_template_type',array('templatecode'=>'OPENTM207266668','name'=>'积分变动提醒'));
    if (empty($res)) {
    pdo_update("ewei_shop_member_message_template_type", array('templatecode'=> 'OPENTM207266668'), array('name'=> '积分变动提醒'));
    }
$res2 = pdo_get('ewei_shop_member_message_template_type',array('templatecode'=>'OPENTM202137457','name'=>'订单状态更新(修改收货地址)(修改价格)'));
    if (empty($res2)) {
    pdo_update("ewei_shop_member_message_template_type", array('templatecode'=> 'OPENTM202137457'), array('name'=> '订单状态更新(修改收货地址)(修改价格)'));
    }
	
$res = pdo_fetchall("select * from ".tablename("ewei_shop_express")." where name='中通快运'");
        if (empty($res)) {
            pdo_insert('ewei_shop_express', array('name' => '中通快运', 'express' => 'zhongtongkuaiyun', 'status' => 1, 'displayorder' => 0, 'coding' => 'ZTOKY', 'code' => ''));
        }
$res = pdo_fetch('select * from '.tablename('ewei_shop_express').' where id=20');
        if ($res['name'] == '德邦物流') {
            pdo_fetch("update ".tablename('ewei_shop_express')." set name='德邦快递', coding='DBL' where id=20");
            pdo_fetch("update ".tablename('ewei_shop_express')." set name='德邦物流', coding='DBLKY' where id=96");
        }
$list = pdo_get('ewei_shop_exhelper_esheet',array('name'=>'顺丰(陆运)'));

        if (empty($list)){
            pdo_query("INSERT INTO ".tablename('ewei_shop_exhelper_esheet')."(`id`, `name`, `express`, `code`, `datas`) VALUES (25, '顺丰(陆运)', 'shunfeng', 'SF', 'a:2:{i:0;a:4:{s:5:\"style\";s:9:\"二联150\";s:4:\"spec\";s:33:\"（宽100mm高150mm切点90/60）\";s:4:\"size\";s:3:\"150\";s:9:\"isdefault\";i:1;}i:1;a:4:{s:5:\"style\";s:9:\"三联210\";s:4:\"spec\";s:38:\"（宽100mm 高210mm 切点90/60/60）\";s:4:\"size\";s:3:\"210\";s:9:\"isdefault\";i:0;}}');");
        }


        $list = pdo_get('ewei_shop_exhelper_esheet',array('name'=>'韵达'));


        if (!empty($list)){
            $temp = 'a:3:{i:0;a:4:{s:5:"style";s:9:"二联180";s:4:"spec";s:34:"（宽100mm高180mm切点110/70）";s:4:"size";s:3:"180";s:9:"isdefault";i:0;}i:1;a:4:{s:5:"style";s:9:"二联203";s:4:"spec";s:36:"（宽100mm 高203mm 切点152/51）";s:4:"size";s:3:"203";s:9:"isdefault";i:1;}i:2;a:4:{s:5:"style";s:9:"一联130";s:4:"spec";s:35:"（宽76mm 高130mm 切点152/51）";s:4:"size";s:3:"130";s:9:"isdefault";i:0;}}';

           pdo_update('ewei_shop_exhelper_esheet',array('datas'=>$temp),array('name'=>'韵达'));
        }		
pdo_update('ewei_shop_exhelper_esheet', array ('datas' =>'a:1:{i:0;a:4:{s:5:"style";s:8:"单联76";s:4:"spec";s:17:"(宽76mm高130mm)";s:4:"size";s:2:"76";s:9:"isdefault";i:0;}}'), array ('code' => 'ZTO'));
 pdo_update('ewei_shop_express',array ('coding'=>'YZPY'),array ('express'=>'youzhengguonei'));	
		
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/core/mobile/bargain/c.json');
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/static/images/wxcode.jpg');
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/static/images/wxcode_1.jpg');
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/static/images/wxcode_2.jpg');
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/core/wxapp/wxappOpemCrypt.php');
@unlink( IA_ROOT.'/addons/ewei_shopv2/plugin/app/core/wxapp/WxpayAPI.class.php');

