SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for tp_user
-- ----------------------------
DROP TABLE IF EXISTS `tp_user`;
CREATE TABLE `tp_user` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `pid` int(16) NOT NULL DEFAULT '0' COMMENT '一级推荐人id',
  `ppid` int(16) NOT NULL DEFAULT '0' COMMENT '二级推荐人id',
  `pppid` int(16) NOT NULL DEFAULT '0' COMMENT '三级推荐人id',
  `tel` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户电话号码',
  `name` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '用户昵称',
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `fmoney` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '冻结佣金',
  `money` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '可提现佣金',
  `vip` int(2) NOT NULL DEFAULT '0' COMMENT 'vip:0-普通用户；1-普通会员；2-高级会员',
  `uid` int(8) NOT NULL COMMENT '后台操作人',
  `add_time` datetime NOT NULL COMMENT '注册时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_vip_cate`;
CREATE TABLE `tp_vip_cate` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) CHARACTER SET utf8 NOT NULL COMMENT '分类标题',
  `addition` int(4) NOT NULL DEFAULT '0' COMMENT '分成比例(%)',
  `price` decimal(10,2) NOT NULL COMMENT '价格（元）',
  `img` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '分类会员图标',
  `day` int(8) NOT NULL COMMENT '会员生效日期（天）',
  `uid` int(8) NOT NULL COMMENT '后台操作人',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0-删除，1-正常显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_vip`;
CREATE TABLE `tp_vip` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL COMMENT '用户id',
  `cate_id` int(4) NOT NULL COMMENT '会员分类id',
  `end_time` datetime NOT NULL COMMENT '会员截止日期',
  `uid` int(8) NOT NULL COMMENT '后台操作人',
  `add_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-过期会员；1-会员正常；',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_admin`;
CREATE TABLE `tp_admin` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(8) NOT NULL COMMENT '用户名称',
  `password` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `uid` int(8) NOT NULL COMMENT '后台操作人',
  `add_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-删除；1-正常；',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_product`;
CREATE TABLE `tp_product` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `cate_id` int(8) NOT NULL COMMENT '所属分类id',
  `title` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品名称',
  `pro_no` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品编码',
  `keywords` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT '关键词',
  `desc` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '描述',
  `img` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品主图',
  `price` decimal(10,2) NOT NULL COMMENT '商品最低价格',
  `cost` decimal(10,2) NOT NULL COMMENT '商品原价',
  `pv` int(16) NOT NULL COMMENT '点击量',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品类型：0-虚拟商品；1-实物',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '状态：0-已删除，1-上架中',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_shop_cate`;
CREATE TABLE `tp_shop_cate` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `pid` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类',
  `weight` int(8) unsigned NOT NULL DEFAULT '100' COMMENT '权重：100-1000',
  `level` int(4) unsigned NOT NULL DEFAULT '1' COMMENT '分类等级：1-顶级分类,2-二级分类,3-三级分类',
  `title` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '分类名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT 'z状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_order`;
CREATE TABLE `tp_order` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(16) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户id',
  `pro_id` int(8) NOT NULL COMMENT '商品id',
  `tel` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '电话号码',
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT '收货人姓名',
  `money` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '订单金额',
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '收货人详细地址/收货人填写的备注',
  `type` int(4) NOT NULL DEFAULT '0' COMMENT '是否处理佣金问题：0-未处理；1-已处理;',
  `status` int(4) NOT NULL DEFAULT '0' COMMENT '状态：0-未发货；1-已发货; 2-已收货',
  `time` int(32) unsigned NOT NULL COMMENT '订单创建时间',
  `admin_id` int(4) NOT NULL COMMENT '操作人',
  `information` varchar(256) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品发货信息',
  `is_del` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已经删除：1-正常；0-已删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_image`;
CREATE TABLE `tp_image` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `pro_id` int(8) NOT NULL COMMENT '商品id',
  `img` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '图片路径',
  `admin_id` int(4) NOT NULL COMMENT '操作人',
  `add_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已经删除：1-正常；0-已删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_reward`;
CREATE TABLE `tp_reward` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `addition_one` int(4) NOT NULL DEFAULT '0' COMMENT '一级分成比例(%)',
  `addition_two` int(4) NOT NULL DEFAULT '0' COMMENT '二级分成比例(%)',
  `addition_three` int(4) NOT NULL DEFAULT '0' COMMENT '三级分成比例(%)',
  `uid` int(4) NOT NULL COMMENT '操作人',
  `add_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否已经删除：1-正常；0-已删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_address`;
CREATE TABLE `tp_address` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL,
  `address` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '收货地址',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_banner`;
CREATE TABLE `tp_banner` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `img` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'banner图地址',
  `url` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '跳转地址',
  `weight` int(8) NOT NULL DEFAULT '100' COMMENT '权重：排序使用',
  `uid` int(8) NOT NULL COMMENT '后台操作人',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：0-删除；1-正常',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_income`;
CREATE TABLE `tp_income` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `user_id` int(16) NOT NULL COMMENT '用户id',
  `order_id` int(16) NOT NULL COMMENT '生成佣金订单',
  `money` decimal(10,2) NOT NULL COMMENT '佣金',
  `uid` int(4) NOT NULL COMMENT '后台操作用户',
  `add_time` datetime NOT NULL COMMENT '订单生成时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型:1-正常；0-删除',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_pro_type`;
CREATE TABLE `tp_pro_type` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `pro_id` int(8) NOT NULL COMMENT '对应商品id',
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT '商品型号',
  `uid` int(4) NOT NULL COMMENT '操作人',
  `weight` int(4) NOT NULL DEFAULT '100' COMMENT '权重',
  `add_time` datetime NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `tp_collection`;
CREATE TABLE `tp_collection` (
  `id` int(16) NOT NULL,
  `user_id` int(16) NOT NULL COMMENT '用户id',
  `pro_id` int(8) NOT NULL COMMENT '商品id',
  `uid` int(4) NOT NULL COMMENT '后台操作人',
  `add_time` datetime NOT NULL COMMENT '新增时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
