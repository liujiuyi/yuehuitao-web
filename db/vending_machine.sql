CREATE TABLE `vem_admin_user` (

	`id` int(6) NOT NULL AUTO_INCREMENT,

	`username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',

	`password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',

	`type` int(1) NOT NULL DEFAULT '2' COMMENT '1，管理员2，加盟商',

	`last_login_ip` varchar(255) NOT NULL DEFAULT '0' COMMENT '最后登录IP',

	`last_login_date` varchar(10) NOT NULL DEFAULT '0' COMMENT '最后登录日期',

	`create_date` varchar(50) DEFAULT '' COMMENT '创建时间',

	PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='系统用户表';


CREATE TABLE `vem_delivery_user` (

	`id` int(6) NOT NULL AUTO_INCREMENT,

	`admin_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '加盟商Id',

	`device_ids` varchar(50) NOT NULL DEFAULT '' COMMENT '设备id，逗号分隔',

	`username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',

	`password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',

	`last_login_ip` varchar(255) NOT NULL DEFAULT '0' COMMENT '最后登录IP',

	`last_login_date` varchar(10) NOT NULL DEFAULT '0' COMMENT '最后登录日期',

	`create_date` varchar(50) DEFAULT '' COMMENT '创建时间',

	PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统用户表';


CREATE TABLE `vem_device` (

	`id` int(11) NOT NULL AUTO_INCREMENT,

	`admin_user_id` int(11) DEFAULT NULL COMMENT '加盟商Id',

	`device_code` varchar(20) NOT NULL DEFAULT '' COMMENT '设备标识',

	`device_name` varchar(100) NOT NULL DEFAULT '' COMMENT '设备名',

	`device_address` varchar(255) DEFAULT NULL COMMENT '设备地址',

	`box_number` int(4) NOT NULL DEFAULT '64' COMMENT '格子数',

	PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

CREATE TABLE `vem_device_box` (

	`id` int(11) NOT NULL AUTO_INCREMENT,

	`box_number` int(11) NOT NULL DEFAULT '0' COMMENT '格子号码',

	`device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备Id',

	`goods_name` varchar(255) DEFAULT '' COMMENT '商品名称',

	`goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',

	`goods_image` varchar(50) DEFAULT NULL COMMENT '商品图片',

	`status` int(1) NOT NULL DEFAULT '0' COMMENT '盒子状态：1，正常，0，无货',

	PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=672 DEFAULT CHARSET=utf8;

CREATE TABLE `vem_order_list` (

	`id` int(11) NOT NULL AUTO_INCREMENT,

	`order_id` varchar(50) NOT NULL DEFAULT '0' COMMENT '订单Id',

	`order_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单金额',

	`status` int(1) NOT NULL DEFAULT '0' COMMENT '是否支付 0未支付 1支付',

	`is_open` int(1) NOT NULL DEFAULT '0' COMMENT '是否开门',

	`create_date` varchar(20) NOT NULL COMMENT '交易时间',

	PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8;

CREATE TABLE `vem_order_goods` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`order_id` varchar(50) NOT NULL DEFAULT '0' COMMENT '订单Id',
	`device_id` int(11) NOT NULL DEFAULT '0' COMMENT '设备Id',
	`box_id` int(11) NOT NULL DEFAULT '0' COMMENT '格子Id',
	`goods_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
	`goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=295 DEFAULT CHARSET=utf8;