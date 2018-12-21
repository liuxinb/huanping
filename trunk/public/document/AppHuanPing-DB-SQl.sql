/*
Navicat MySQL Data Transfer

Source Server         : 192.168.199.100-hp.app
Source Server Version : 100131
Source Host           : 192.168.199.100:3306
Source Database       : huanping

Target Server Type    : MYSQL
Target Server Version : 100131
File Encoding         : 65001

Date: 2018-06-27 14:08:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `admin_auth_group`;
CREATE TABLE `admin_auth_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text COMMENT '规则id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of admin_auth_group
-- ----------------------------
INSERT INTO `admin_auth_group` VALUES ('1', '超级管理员', '1', '1,143,144,257,265,6,2,242,243,244,3,15,16,17,18,154,168,169,152,153,158,159,160,162,280,163,164,167,218,219,220,173,174,176,177,206,232,178,231,182,185,191,192,193,186,194,195,196,283,284,285,286,211,233,234,235,236,237,266,267,281,282,221,238,239,240,241,225,226,227,228,230,290,247,248,250,251,252,253,297,298,299');


-- ----------------------------
-- Table structure for admin_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `admin_auth_group_access`;
CREATE TABLE `admin_auth_group_access` (
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `group_id` int(11) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组明细表';

-- ----------------------------
-- Records of admin_auth_group_access
-- ----------------------------
INSERT INTO `admin_auth_group_access` VALUES ('1', '1');
INSERT INTO `admin_auth_group_access` VALUES ('99', '1');
INSERT INTO `admin_auth_group_access` VALUES ('99', '2');
INSERT INTO `admin_auth_group_access` VALUES ('99', '4');

-- ----------------------------
-- Table structure for admin_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `admin_auth_rule`;
CREATE TABLE `admin_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `condition` char(100) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证',
  `sort` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=300 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of admin_auth_rule
-- ----------------------------
INSERT INTO `admin_auth_rule` VALUES ('16', '3', 'Admin/Rule/add_group', '添加角色', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('17', '3', 'Admin/Rule/edit_group', '修改角色', '1', '1', '', '1');
INSERT INTO `admin_auth_rule` VALUES ('18', '3', 'Admin/Rule/delete_group', '删除角色', '1', '1', '', '2');
INSERT INTO `admin_auth_rule` VALUES ('230', '228', 'admin/order/enterprise_detail_order', '企业订单详情', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('265', '143', 'Admin/index/logout', '退出登录', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('244', '2', 'Admin/Rule/add', '添加权限', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('1', '0', 'Admin/Index/index', '管理设置', '1', '1', '', '6');
INSERT INTO `admin_auth_rule` VALUES ('152', '0', 'Admin/Category', '资源管理', '1', '1', '', '7');
INSERT INTO `admin_auth_rule` VALUES ('11', '10', 'Admin/Article/index', '文章管理', '1', '1', '', '8');
INSERT INTO `admin_auth_rule` VALUES ('12', '10', 'Admin/Posts/add_posts', '菜单管理', '1', '1', '', '9');
INSERT INTO `admin_auth_rule` VALUES ('13', '10', 'Admin/Posts/edit_posts', '日志管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('153', '152', 'Admin/Category/index', '分类管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('168', '154', 'Admin/user/add_user', '添加用户（管理员）', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('6', '0', 'Admin/Rule/', '权限控制', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('3', '6', 'Admin/Rule/rule_group', '角色管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('247', '0', 'admin/plug', '广告管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('243', '2', 'Admin/Rule/edit', '修改权限', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('2', '6', 'Admin/Rule/rule_list', '菜单管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('15', '3', 'Admin/Rule/rule_distribution', '角色分配权限', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('169', '154', 'Admin/user/edit_user', '修改用户', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('140', '11', 'Admin/Article/add_art', '新增文章', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('141', '11', 'Admin/Article/del_art', '删除文章', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('142', '11', 'Admin/Article/edi_art', '文章编辑', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('143', '1', 'Admin/User/my_center', '个人中心', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('144', '143', 'Admin/User/change_msg', '修改个人资料', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('240', '238', 'admin/msg/del', '删除用户发送消息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('154', '6', 'Admin/User/index', '管理员管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('241', '238', 'admin/msg/add_run', '发送消息执行', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('156', '46', 'ljklalla', 'C++', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('157', '59', 'blockchain', '区块链', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('158', '153', 'Admin/Category/add', '分类列表', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('159', '153', 'Admin/Category/edit', '修改分类', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('160', '153', 'Admin/Category/delete', '删除子类', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('162', '153', 'Admin/Category/status', '开启或禁止状态', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('163', '152', 'Admin/Flv/index', '视频管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('164', '163', 'Admin/Flv/add_flv', '添加视频', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('167', '163', 'Admin/flv/edit_run', '修改视频执行动作', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('239', '238', 'admin/msg/add', '发送消息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('172', '171', 'Admin/Username/add_userment', '用户添加', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('173', '0', 'Admin/teacher/', '教师管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('174', '173', 'Admin/teacher/index', '教师信息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('233', '211', 'Admin/firm/index', '企业信息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('176', '174', 'Admin/teacher/edit', '修改教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('177', '174', 'Admin/teacher/del', '删除教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('178', '0', 'Admin/Bill', '发票管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('231', '178', 'Admin/Bill/index', '发票信息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('180', '179', 'Admin/charge/index', '教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('181', '179', 'Admin/Charge/edit', '修改课程', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('182', '0', 'Admin/Wor/', '题库管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('238', '221', 'admin/msg/index', '消息管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('185', '182', 'Admin/Wor/single_index', '单选题管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('186', '182', 'Admin/Wor/multiple_index', '多选题管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('232', '174', 'Admin/teacher/add', '添加教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('284', '283', 'Admin/Wor/add', '判断题添加', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('286', '283', 'Admin/Wor/edit', '判断题修改', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('285', '283', 'Admin/Wor/del', '判断题删除', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('191', '185', 'Admin/Wor/edit_single', '单选题修改', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('192', '185', 'Admin/Wor/add_single', '单选题添加', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('193', '185', 'Admin/Wor/del_single', '单选题删除', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('194', '186', 'Admin/Wor/edit_mult', '修改多选题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('195', '186', 'Admin/Wor/add_mult', '添加多选题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('196', '186', 'Admin/wor/del_mult', '删除多选题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('197', '187', 'Admin/Wor/edit_fill', '修改填空题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('198', '187', 'Admin/Wor/add_fill', '添加填空题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('199', '187', 'Admin/Wor/del_fill', '删除填空题', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('200', '14', 'wqeqwe', 'ffffffffffffffff', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('221', '0', 'admin/msg', '通知管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('219', '163', 'admin/flv/edit', '修改视频', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('203', '202', 'admin/code/check', '验证码检测', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('204', '202', 'Admin/teacher/dels', '批量删除教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('206', '174', 'Admin/teacher/seach', '搜索教师', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('228', '227', 'admin/order/enterprise_index', '企业订单', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('227', '0', 'admin/order', '订单管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('226', '225', 'admin/Opinion/index', 'App意见反馈', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('225', '0', 'admin/Opinion', '帮助与反馈', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('211', '0', 'Admin/firm', '企业管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('237', '233', 'Admin/firm/del_firm', '删除企业信息数据', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('236', '233', 'Admin/firm/detail_firm', ' 企业信息查看详情', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('235', '233', 'Admin/firm/edit_firm_run', '修改企业信息数据执行动作', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('234', '233', 'Admin/firm/edit_firm', ' 企业信息修改', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('218', '163', 'Admin/Flv/add_flv_run', '添加视频执行', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('220', '163', 'Admin/flv/flv_delete', '删除视频', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('242', '2', 'Admin/Rule/delete', '删除权限', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('246', '245', 'srth', 'fghws', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('248', '247', 'admin/plug/index', '广告列表', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('297', '0', 'admin/userment', '用户管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('250', '248', 'Admin/Plug/add_adv', '添加广告操作', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('251', '248', 'Admin/Plug/update_adv', '修改广告信息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('252', '248', 'Admin/Plug/update_adv_run', '修改广告信息执行', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('253', '248', 'admin/plug/plug_list_delete', '广告列表删除', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('298', '297', 'admin/userment/index', '用户信息', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('299', '298', 'admin/userment/del_userment', '用户删除', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('257', '143', 'admin/Welcome/index', '首页', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('266', '211', 'admin/survey/index', '企业调查问卷管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('267', '266', 'admin/Survey/del', '问卷删除', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('275', '268', 'adrgawe', 'dfgaw', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('280', '153', 'Admin/Category/upload', ' 修改上传图片显示', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('281', '266', 'admin/Survey/add', '问卷添加', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('282', '266', 'admin/Survey/edit', '问卷修改', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('283', '182', 'Admin/Wor/index', '判断题管理', '1', '1', '', '0');
INSERT INTO `admin_auth_rule` VALUES ('290', '228', 'admin/order/recycle', '订单驳回', '1', '1', '', '0');

-- ----------------------------
-- Table structure for admin_certificate
-- ----------------------------
DROP TABLE IF EXISTS `admin_certificate`;
CREATE TABLE `admin_certificate` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `cid` int(11) DEFAULT NULL COMMENT '课程包Id',
  `url` varchar(255) DEFAULT NULL COMMENT '证书url',
  `create_time` int(6) DEFAULT NULL COMMENT '创建时间',
  `certificate_id` varchar(15) DEFAULT NULL COMMENT '证书编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_certificate
-- ----------------------------

-- ----------------------------
-- Table structure for admin_enterprise_order
-- ----------------------------
DROP TABLE IF EXISTS `admin_enterprise_order`;
CREATE TABLE `admin_enterprise_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(11) NOT NULL,
  `num` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '1' COMMENT '企业订单号',
  `price` double NOT NULL COMMENT '订单金额',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `order_status` varchar(255) NOT NULL DEFAULT '2' COMMENT '订单状态 0驳回 1已支付 2待付款 3支付中 4订单失效',
  `pay` varchar(255) NOT NULL DEFAULT '0' COMMENT '支付方式 0公司代付 1支付宝 2微信',
  `status` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '1' COMMENT '此数据状态',
  `count` int(11) NOT NULL DEFAULT '1' COMMENT '个数',
  `enterprise_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '企业名字',
  `note` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '备注',
  `category_id` int(11) NOT NULL COMMENT '视频包id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of admin_enterprise_order
-- ----------------------------

-- ----------------------------
-- Table structure for admin_evaluation
-- ----------------------------
DROP TABLE IF EXISTS `admin_evaluation`;
CREATE TABLE `admin_evaluation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `mid` int(11) DEFAULT NULL COMMENT '视频id',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `star` varchar(255) DEFAULT NULL COMMENT '几星',
  `content` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='用户评价视频表';

-- ----------------------------
-- Records of admin_evaluation
-- ----------------------------

-- ----------------------------
-- Table structure for admin_firm_hope
-- ----------------------------
DROP TABLE IF EXISTS `admin_firm_hope`;
CREATE TABLE `admin_firm_hope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL COMMENT '反馈意见',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_firm_hope
-- ----------------------------

-- ----------------------------
-- Table structure for admin_firmsign
-- ----------------------------
DROP TABLE IF EXISTS `admin_firmsign`;
CREATE TABLE `admin_firmsign` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL,
  `record_id` int(11) DEFAULT NULL COMMENT '企业档案',
  `phone` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_firmsign
-- ----------------------------

-- ----------------------------
-- Table structure for admin_firmsign_survey
-- ----------------------------
DROP TABLE IF EXISTS `admin_firmsign_survey`;
CREATE TABLE `admin_firmsign_survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(255) DEFAULT NULL,
  `A` varchar(255) DEFAULT NULL,
  `B` varchar(255) DEFAULT NULL,
  `C` varchar(255) DEFAULT NULL,
  `D` varchar(255) DEFAULT NULL,
  `countA` int(20) DEFAULT '0',
  `countB` int(20) DEFAULT '0',
  `countC` int(20) DEFAULT '0',
  `countD` int(20) DEFAULT '0',
  `create_time` int(20) DEFAULT NULL,
  `type` smallint(2) DEFAULT '1' COMMENT '1单选 2多选',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='企业调查问卷';

-- ----------------------------
-- Records of admin_firmsign_survey
-- ----------------------------
INSERT INTO `admin_firmsign_survey` VALUES ('1', '企业员工数量', '50人以下', '50-100人 ', '30—43人', '40人以上', '1', '0', '0', '0', '15254525', '1');
INSERT INTO `admin_firmsign_survey` VALUES ('2', '企业每年新入职员工人数', '1—10人', '10—25人', '25—40人 ', '40人以上', '1', '0', '0', '0', '1529554429', '1');
INSERT INTO `admin_firmsign_survey` VALUES ('3', '企业是否愿意接受付费培训', '不愿意', '如果培训内容好愿意 ', '', '', '1', '0', '0', '0', '383828382', '1');
INSERT INTO `admin_firmsign_survey` VALUES ('4', '企业可以接受的付费培训价格（按年计算）', '2K-4K ', '5K-7K ', '8K-10  K', '一万以上', '1', '0', '0', '0', '414528288', '1');
INSERT INTO `admin_firmsign_survey` VALUES ('6', '公司培训的频率是', '每月一次', '每季度一次', '每年一次', '完全没有培训', '1', '0', '0', '0', '1529553439', '1');
INSERT INTO `admin_firmsign_survey` VALUES ('9', '以下培训类型中，企业安排过的有（多选）', '单位内部讲师培训或师傅带徒弟', '聘请外部讲师培训', '安排学员到外部培训机构接收培训', '网络培训', '1', '1', '1', '0', '1529561142', '2');

-- ----------------------------
-- Table structure for admin_flv_category
-- ----------------------------
DROP TABLE IF EXISTS `admin_flv_category`;
CREATE TABLE `admin_flv_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `name` varchar(30) NOT NULL COMMENT '标志',
  `title` varchar(50) NOT NULL COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性 1:可见',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据状态,0禁止 1开启',
  `record` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '播放记录',
  `bag_price` float NOT NULL DEFAULT '0' COMMENT '视频包价格',
  `bag_img` varchar(255) NOT NULL DEFAULT '' COMMENT '视频包封面',
  `teacher_id` int(20) NOT NULL COMMENT '教师id',
  `hours` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=140 DEFAULT CHARSET=utf8 COMMENT='分类表';

-- ----------------------------
-- Records of admin_flv_category
-- ----------------------------
INSERT INTO `admin_flv_category` VALUES ('1', '课程包', '环境保护基础知识之一', '0', '1', '', '2018-06-20 09:06:36', '2018-06-26 10:13:05', '1', '1', '0.02', '/static/img/category/cat0.jpg', '0', '17');
INSERT INTO `admin_flv_category` VALUES ('2', '课程-第一部分', '环境影响评价发展历程', '1', '1', '', '2018-06-20 09:06:36', '2018-06-26 09:49:29', '1', '1', '0', '/static/img/category/cat1.jpg', '1', '2');
INSERT INTO `admin_flv_category` VALUES ('3', '课程-第二部分', '环境影响评价法律法规体系', '1', '1', '', '2018-06-20 09:06:36', '2018-06-25 09:13:23', '1', '1', '0', '/static/img/category/cat2.jpg', '2', '2');
INSERT INTO `admin_flv_category` VALUES ('4', '课程-第三部分', '环境影响评价基础       ', '1', '1', '', '2018-06-20 09:06:36', '2018-06-20 09:06:36', '1', '1', '0', '/static/img/category/cat3.jpg', '3', '2');
INSERT INTO `admin_flv_category` VALUES ('5', '课程-第四部分', '导则标准与技术方法     ', '1', '1', '', '2018-06-20 09:06:36', '2018-06-26 09:47:50', '1', '1', '0', '/static/img/category/cat4.jpg', '4', '2');
INSERT INTO `admin_flv_category` VALUES ('6', '课程-第五部分', '规划环境影响评价       ', '1', '1', '', '2018-06-20 09:06:36', '2018-06-20 09:06:36', '1', '1', '0', '/static/img/category/cat5.jpg', '5', '3');
INSERT INTO `admin_flv_category` VALUES ('7', '课程-第六部分', '工业类环境影响评价成果 ', '1', '1', '', '2018-06-20 09:06:36', '2018-06-26 09:47:35', '1', '1', '0', '/static/img/category/cat6.jpg', '6', '3');
INSERT INTO `admin_flv_category` VALUES ('8', '课程-第七部分', '生态类环境影响评价成果 ', '1', '1', '', '2018-06-20 09:06:36', '2018-06-25 15:46:19', '1', '1', '0', '/static/img/category/cat7.jpg', '7', '3');

-- ----------------------------
-- Table structure for admin_flv_movie
-- ----------------------------
DROP TABLE IF EXISTS `admin_flv_movie`;
CREATE TABLE `admin_flv_movie` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属分类',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '名称',
  `actors` char(120) DEFAULT NULL COMMENT '角色',
  `year` year(4) DEFAULT NULL COMMENT '年份',
  `cover_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '封面',
  `content` text COMMENT '介绍',
  `display` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '可见性',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `hour` int(10) DEFAULT NULL COMMENT '时长',
  `position` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否推荐 0：不推荐 1：推荐',
  `teacher_id` int(10) DEFAULT NULL COMMENT '对应教师id   admin_tea_teacher',
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `actors` (`actors`),
  KEY `year` (`year`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=27963 DEFAULT CHARSET=utf8 COMMENT='影片表';

-- ----------------------------
-- Records of admin_flv_movie
-- ----------------------------
INSERT INTO `admin_flv_movie` VALUES ('1', '6', '规划环境影响评价1', 'admin', '2018', '37', '规划环境影响评价1', '1', '2018-05-22 11:00:45', '2018-05-22 11:00:45', '1', '1580', '0', '1');
INSERT INTO `admin_flv_movie` VALUES ('2', '6', '规划环境影响评价2', 'admin', '2018', '38', '规划环境影响评价2', '1', '2018-05-22 11:38:02', '2018-05-22 11:38:02', '1', '1719', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('3', '6', '规划环境影响评价3', 'admin', '2018', '39', '规划环境影响评价3', '1', '2018-06-20 16:52:05', '2018-05-22 11:39:24', '1', '2626', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('4', '6', '规划环境影响评价4', 'admin', '2018', '40', '规划环境影响评价4', '1', '2018-05-22 11:48:10', '2018-05-22 11:48:10', '1', '1810', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('5', '6', '规划环境影响评价5', 'admin', '2018', '41', '规划环境影响评价5', '1', '2018-06-20 16:51:47', '2018-05-22 11:49:18', '1', '1588', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('6', '6', '规划环境影响评价6', 'admin', '2018', '42', '规划环境影响评价6', '1', '2018-06-20 16:51:27', '2018-05-22 11:50:14', '1', '1729', '0', '7');
INSERT INTO `admin_flv_movie` VALUES ('7', '6', '规划环境影响评价7', 'admin', '2018', '43', '规划环境影响评价7', '1', '2018-05-22 11:51:20', '2018-05-22 11:51:20', '1', '1910', '0', '1');
INSERT INTO `admin_flv_movie` VALUES ('8', '6', '规划环境影响评价8', 'admin', '2018', '44', '规划环境影响评价8', '1', '2018-06-20 16:51:12', '2018-05-22 11:52:14', '1', '1574', '1', '6');
INSERT INTO `admin_flv_movie` VALUES ('9', '6', '规划环境影响评价9', 'admin', '2018', '45', '规划环境影响评价9', '1', '2018-05-22 11:54:02', '2018-05-22 11:54:02', '1', '1855', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('10', '7', '工业类环境影响评价成果1', 'admin', '2018', '46', '工业类环境影响评价成果1', '1', '2018-06-20 16:50:57', '2018-05-22 11:55:06', '1', '1774', '0', '3');
INSERT INTO `admin_flv_movie` VALUES ('11', '7', '工业类环境影响评价成果2', 'admin', '2018', '47', '工业类环境影响评价成果2', '1', '2018-05-22 11:56:10', '2018-05-22 11:56:10', '1', '5588', '0', '1');
INSERT INTO `admin_flv_movie` VALUES ('12', '7', '工业类环境影响评价成果3', 'admin', '2018', '34', '工业类环境影响评价成果3', '1', '2018-05-22 11:44:16', '2018-05-22 10:56:42', '1', '2885', '1', '2');
INSERT INTO `admin_flv_movie` VALUES ('13', '7', '工业类环境影响评价成果4', 'admin', '2018', '35', '工业类环境影响评价成果4', '1', '2018-06-20 16:49:10', '2018-05-22 10:57:50', '1', '1893', '0', '5');
INSERT INTO `admin_flv_movie` VALUES ('14', '7', '工业类环境影响评价成果5', 'admin', '2018', '36', '工业类环境影响评价成果5', '1', '2018-06-20 16:49:21', '2018-05-22 10:58:51', '1', '1963', '1', '3');
INSERT INTO `admin_flv_movie` VALUES ('15', '8', '生态类环境影响评价成果1', 'admin', '2018', '50', '生态类环境影响评价成果1', '1', '2018-06-20 16:50:26', '2018-05-22 14:57:04', '1', '2696', '0', '4');
INSERT INTO `admin_flv_movie` VALUES ('16', '8', '生态类环境影响评价成果2', 'admin', '2018', '51', '生态类环境影响评价成果2', '1', '2018-05-22 14:59:47', '2018-05-22 14:59:47', '1', '2747', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('17', '8', '生态类环境影响评价成果3', 'admin', '2018', '49', '生态类环境影响评价成果3', '1', '2018-06-25 10:42:10', '2018-05-22 14:46:30', '1', '2469', '0', '5');
INSERT INTO `admin_flv_movie` VALUES ('18', '8', '生态类环境影响评价成果4', 'admin', '2018', '52', '生态类环境影响评价成果4', '1', '2018-06-25 10:30:03', '2018-05-22 15:02:35', '1', '4103', '1', '1');
INSERT INTO `admin_flv_movie` VALUES ('19', '5', '环境影响评价基础1', 'admin', '2018', '53', '环境影响评价基础1', '1', '2018-06-26 15:54:38', '2018-05-22 15:04:47', '1', '2580', '0', '2');
INSERT INTO `admin_flv_movie` VALUES ('20', '5', '环境影响评价基础2', 'admin', '2018', '54', '环境影响评价基础2', '1', '2018-06-25 10:29:29', '2018-05-22 15:16:22', '1', '2787', '0', '7');
INSERT INTO `admin_flv_movie` VALUES ('21', '4', '导则标准与技术方法1', 'admin', '2018', '55', '建设项目环境影响评价1', '1', '2018-06-22 17:24:13', '2018-05-22 15:36:38', '1', '3498', '0', '1');
INSERT INTO `admin_flv_movie` VALUES ('22', '4', '导则标准与技术方法2', 'admin', '2018', '56', '建设项目环境影响评价2', '1', '2018-06-20 16:45:55', '2018-05-22 15:39:53', '1', '2275', '0', '2');

-- ----------------------------
-- Table structure for admin_flv_movie_url
-- ----------------------------
DROP TABLE IF EXISTS `admin_flv_movie_url`;
CREATE TABLE `admin_flv_movie_url` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `movie_url` text NOT NULL,
  `movie_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `movie_id` (`movie_id`)
) ENGINE=MyISAM AUTO_INCREMENT=105 DEFAULT CHARSET=utf8 COMMENT='影片地址表';

-- ----------------------------
-- Records of admin_flv_movie_url
-- ----------------------------
INSERT INTO `admin_flv_movie_url` VALUES ('1', 'aa5c2e3b3aa127ef65f8624697635e4c_a', '1');
INSERT INTO `admin_flv_movie_url` VALUES ('2', 'aa5c2e3b3a9181ba3f109561fcfee7c7_a', '2');
INSERT INTO `admin_flv_movie_url` VALUES ('3', 'aa5c2e3b3a44821124ff92e45e524883_a', '3');
INSERT INTO `admin_flv_movie_url` VALUES ('4', 'aa5c2e3b3a7597b66271837c5f9ce2c5_a', '4');
INSERT INTO `admin_flv_movie_url` VALUES ('5', 'aa5c2e3b3a05951f632df5f8be290d41_a', '5');
INSERT INTO `admin_flv_movie_url` VALUES ('6', 'aa5c2e3b3a777220470188215a1dea2d_a', '6');
INSERT INTO `admin_flv_movie_url` VALUES ('7', 'aa5c2e3b3a01adf1b8645ce583f4cafa_a', '7');
INSERT INTO `admin_flv_movie_url` VALUES ('8', 'aa5c2e3b3ab0945dcfc864239b4f9a49_a', '8');
INSERT INTO `admin_flv_movie_url` VALUES ('9', 'aa5c2e3b3a5a8375cc8d13116170ebb9_a', '9');
INSERT INTO `admin_flv_movie_url` VALUES ('10', 'aa5c2e3b3a855cc5c418f7a782770fcd_a', '10');
INSERT INTO `admin_flv_movie_url` VALUES ('11', 'aa5c2e3b3ada08f5a9bf2fc6bba588fc_a', '11');
INSERT INTO `admin_flv_movie_url` VALUES ('12', 'aa5c2e3b3a93bcd15dfaf30e6a893a29_a', '12');
INSERT INTO `admin_flv_movie_url` VALUES ('13', 'aa5c2e3b3ab7f770b6b6b6335fcb1f28_a', '13');
INSERT INTO `admin_flv_movie_url` VALUES ('14', 'aa5c2e3b3a2382e56a93a8a0a245b126_a', '14');
INSERT INTO `admin_flv_movie_url` VALUES ('15', 'aa5c2e3b3a129a3064188da07305d025_a', '15');
INSERT INTO `admin_flv_movie_url` VALUES ('16', 'aa5c2e3b3a1120a0b288c02619bdd46f_a', '16');
INSERT INTO `admin_flv_movie_url` VALUES ('17', 'aa5c2e3b3aeb0eb538d0dc76711d5b7e_a', '17');
INSERT INTO `admin_flv_movie_url` VALUES ('18', 'aa5c2e3b3a9bebabb9c5cbcdaf2ad3a4_a', '18');
INSERT INTO `admin_flv_movie_url` VALUES ('19', 'aa5c2e3b3adf210bc5c304c29e465c7c_a', '19');
INSERT INTO `admin_flv_movie_url` VALUES ('20', 'aa5c2e3b3a51491c4d253f29ba851b3c_a', '20');
INSERT INTO `admin_flv_movie_url` VALUES ('21', 'aa5c2e3b3a3ea7a6feea78173eaada31_a', '21');
INSERT INTO `admin_flv_movie_url` VALUES ('22', 'aa5c2e3b3a894657b63b0240baf295f3_a', '22');

-- ----------------------------
-- Table structure for admin_flv_picture
-- ----------------------------
DROP TABLE IF EXISTS `admin_flv_picture`;
CREATE TABLE `admin_flv_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_flv_picture
-- ----------------------------

-- ----------------------------
-- Table structure for admin_invoice
-- ----------------------------
DROP TABLE IF EXISTS `admin_invoice`;
CREATE TABLE `admin_invoice` (
  `fpid` int(11) NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(255) NOT NULL COMMENT '单位名称',
  `unit_site` varchar(255) NOT NULL COMMENT '单位地址',
  `bank` varchar(255) NOT NULL COMMENT '开户行',
  `account` varchar(255) NOT NULL COMMENT '账号',
  `duty` varchar(255) NOT NULL COMMENT '税号',
  `phone` varchar(255) NOT NULL COMMENT '电话',
  `create_time` date NOT NULL,
  `price` decimal(10,2) NOT NULL COMMENT '价格',
  `qrcode` varchar(255) NOT NULL COMMENT '二维码',
  `enterId` int(11) unsigned NOT NULL COMMENT '所属企业id',
  PRIMARY KEY (`fpid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for admin_message
-- ----------------------------
DROP TABLE IF EXISTS `admin_message`;
CREATE TABLE `admin_message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `to_uid` int(11) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(11) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0系统消息,1用户消息',
  `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='消息表';

-- ----------------------------
-- Records of admin_message
-- ----------------------------

-- ----------------------------
-- Table structure for admin_notice
-- ----------------------------
DROP TABLE IF EXISTS `admin_notice`;
CREATE TABLE `admin_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `context` varchar(8000) NOT NULL,
  `img` varchar(200) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_notice
-- ----------------------------

-- ----------------------------
-- Table structure for admin_opinion
-- ----------------------------
DROP TABLE IF EXISTS `admin_opinion`;
CREATE TABLE `admin_opinion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `userid` int(11) NOT NULL COMMENT '关联用户id',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈表';

-- ----------------------------
-- Records of admin_opinion
-- ----------------------------

-- ----------------------------
-- Table structure for admin_order_order
-- ----------------------------
DROP TABLE IF EXISTS `admin_order_order`;
CREATE TABLE `admin_order_order` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `num` varchar(100) NOT NULL DEFAULT '0' COMMENT '订单号',
  `uid` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '总价',
  `count` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '商品（课程）数量',
  `note` char(255) DEFAULT NULL COMMENT '备注',
  `pay` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '支付方式 0:未支付；1支付宝 2微信 3代付支付宝 4代付微信 5代付',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '订单状态,0:驳回，1：已付款，2：待付款 3支付中',
  `date` datetime NOT NULL,
  `enterid` tinyint(255) unsigned DEFAULT '0' COMMENT '企业id',
  `username` varchar(255) NOT NULL,
  `enterorderid` int(11) DEFAULT '0' COMMENT '所属企业订单编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `one` (`num`) USING HASH
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_order_order
-- ----------------------------

-- ----------------------------
-- Table structure for admin_order_pay
-- ----------------------------
DROP TABLE IF EXISTS `admin_order_pay`;
CREATE TABLE `admin_order_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ordernum` varchar(200) NOT NULL COMMENT '订单编号(商户订单号）',
  `pay` int(11) NOT NULL COMMENT '支付方式：1支付宝 2微信',
  `price` float NOT NULL COMMENT '支付平台订单号',
  `tradenum` varchar(200) NOT NULL COMMENT '交易流水号',
  `paytime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '支付时间',
  `buyer_email` varchar(255) DEFAULT NULL,
  `buyer_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of admin_order_pay
-- ----------------------------

-- ----------------------------
-- Table structure for admin_plug_list
-- ----------------------------
DROP TABLE IF EXISTS `admin_plug_list`;
CREATE TABLE `admin_plug_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `plug_name` varchar(50) NOT NULL DEFAULT '' COMMENT '广告名称',
  `plug_pic` varchar(200) NOT NULL DEFAULT '' COMMENT '广告图片URL',
  `plug_url` varchar(200) NOT NULL DEFAULT '' COMMENT '广告链接',
  `plug_addtime` datetime NOT NULL COMMENT '添加时间',
  `plug_order` int(11) NOT NULL COMMENT '排序',
  `plug_status` int(11) NOT NULL DEFAULT '1' COMMENT '状态 0禁用 1启用',
  `plug_typeid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_plug_list
-- ----------------------------
INSERT INTO `admin_plug_list` VALUES ('10', '首页', '/static/img/index/banner2.jpg', 'http://taobao.com', '2018-06-22 17:59:49', '50', '1', '1');
INSERT INTO `admin_plug_list` VALUES ('11', '首页', '/static/img/index/banner.jpg', 'http://123456.com', '2018-06-22 17:59:54', '50', '1', '1');

-- ----------------------------
-- Table structure for admin_powers
-- ----------------------------
DROP TABLE IF EXISTS `admin_powers`;
CREATE TABLE `admin_powers` (
  `powerId` int(11) NOT NULL AUTO_INCREMENT,
  `powerName` char(24) NOT NULL COMMENT '权限名称',
  `pId` int(11) NOT NULL COMMENT '父级权限编号',
  `routhName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`powerId`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_powers
-- ----------------------------
INSERT INTO `admin_powers` VALUES ('1', '员工管理', '0', '/users');
INSERT INTO `admin_powers` VALUES ('2', '订单管理', '0', '/indent');
INSERT INTO `admin_powers` VALUES ('3', '发票管理', '0', '/invoice');
INSERT INTO `admin_powers` VALUES ('4', '我的帖子', '0', null);
INSERT INTO `admin_powers` VALUES ('5', '添加学员', '1', '/managementsystem/adminuser/add');
INSERT INTO `admin_powers` VALUES ('6', '批量导入', '1', '/managementsystem/adminuser/batch');
INSERT INTO `admin_powers` VALUES ('7', '待付款', '2', null);
INSERT INTO `admin_powers` VALUES ('8', '已付款', '2', null);
INSERT INTO `admin_powers` VALUES ('9', '打印发票', '3', '/managementsystem/invoice/details');
INSERT INTO `admin_powers` VALUES ('10', '发票', '4', '/invoice');
INSERT INTO `admin_powers` VALUES ('11', '学习管理', '0', '/student');
INSERT INTO `admin_powers` VALUES ('12', '修改学员', '11', '/managementsystem/adminuser/studentSave');
INSERT INTO `admin_powers` VALUES ('14', '修改学员', '1', '/managementsystem/adminuser/save');
INSERT INTO `admin_powers` VALUES ('19', '我的档案', '0', '/record');
INSERT INTO `admin_powers` VALUES ('20', '编辑档案', '19', '/managementsystem/record/update');
INSERT INTO `admin_powers` VALUES ('21', '添加档案', '19', '/managementsystem/record/add');

-- ----------------------------
-- Table structure for admin_record
-- ----------------------------
DROP TABLE IF EXISTS `admin_record`;
CREATE TABLE `admin_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firmname` varchar(40) NOT NULL COMMENT '企业名称',
  `province` varchar(120) NOT NULL COMMENT '省',
  `city` varchar(120) NOT NULL COMMENT '市',
  `county` varchar(120) NOT NULL COMMENT '县',
  `worksite` varchar(120) NOT NULL COMMENT '办公地址',
  `registersite` varchar(120) NOT NULL COMMENT '注册地址',
  `invoicename` varchar(40) NOT NULL COMMENT '发票名称',
  `identifynumber` varchar(40) NOT NULL COMMENT '纳税人识别号',
  `addressphone` char(11) NOT NULL COMMENT '地址电话',
  `openingnumber` varchar(40) NOT NULL COMMENT '开户行及账号',
  `name` varchar(10) NOT NULL COMMENT '联系人信息',
  `phone` char(11) NOT NULL COMMENT '联系人手机',
  `email` varchar(32) NOT NULL COMMENT '联系人邮箱',
  `cardfront` varchar(80) NOT NULL COMMENT '身份证正面',
  `cardside` varchar(80) NOT NULL COMMENT '身份证反面',
  `business` varchar(80) NOT NULL COMMENT '营业执照',
  `enterId` int(11) NOT NULL COMMENT '企业id',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_record
-- ----------------------------

-- ----------------------------
-- Table structure for admin_relation
-- ----------------------------
DROP TABLE IF EXISTS `admin_relation`;
CREATE TABLE `admin_relation` (
  `relationId` int(11) NOT NULL AUTO_INCREMENT COMMENT '关联编号',
  `levelId` tinyint(4) NOT NULL COMMENT '角色编号',
  `powerId` tinyint(4) NOT NULL COMMENT '权限编号',
  PRIMARY KEY (`relationId`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_relation
-- ----------------------------
INSERT INTO `admin_relation` VALUES ('241', '28', '6');
INSERT INTO `admin_relation` VALUES ('242', '28', '14');
INSERT INTO `admin_relation` VALUES ('243', '28', '7');
INSERT INTO `admin_relation` VALUES ('244', '28', '8');
INSERT INTO `admin_relation` VALUES ('245', '28', '9');
INSERT INTO `admin_relation` VALUES ('246', '28', '12');
INSERT INTO `admin_relation` VALUES ('247', '28', '2');
INSERT INTO `admin_relation` VALUES ('248', '28', '3');
INSERT INTO `admin_relation` VALUES ('249', '28', '11');

-- ----------------------------
-- Table structure for admin_tea_teacher
-- ----------------------------
DROP TABLE IF EXISTS `admin_tea_teacher`;
CREATE TABLE `admin_tea_teacher` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '专家，教师',
  `path` varchar(255) NOT NULL COMMENT '专家，教师图片地址',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '人物状态 0，禁止 1，开启',
  `qg` varchar(200) NOT NULL COMMENT '所在单位',
  `referral` varchar(200) NOT NULL COMMENT '人物介绍-职位',
  `create_time` int(20) NOT NULL COMMENT '创建时间',
  `phone` varchar(11) NOT NULL COMMENT '教师电话',
  `sex` int(2) NOT NULL DEFAULT '1' COMMENT '性别 0：女 1：男',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_tea_teacher
-- ----------------------------
INSERT INTO `admin_tea_teacher` VALUES ('1', '包存宽', '\\uploads\\20180622\\97bc5ed9288315e53b50adf71fc271b6.jpg', '1', '复旦大学', '教 授', '1529660110', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('2', '胡颖华', '\\uploads\\20180621\\62ad32a1448065193ed8136cfc55aecb.png', '1', '伊尔姆环境资源管理咨询(上海)有限公司', '高级工程师', '1527675327', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('3', '李 鱼', '\\uploads\\20180622\\8dd0af273f2b8a07f26e634fbcf77b55.jpg', '1', '华北电力大学', '教 授', '1529660024', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('4', '辜小安', '\\uploads\\20180621\\62ad32a1448065193ed8136cfc55aecb.png', '1', '中国铁道科学研究院', '教 授', '1527675327', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('5', '李 巍', '\\uploads\\20180621\\62ad32a1448065193ed8136cfc55aecb.png', '1', '北京师范大学', '教 授', '1527675327', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('6', '张 红', '\\uploads\\20180621\\62ad32a1448065193ed8136cfc55aecb.png', '1', '北京京诚嘉宇环境科技有限公司', '教授级高工', '1527675327', '15000000000', '1');
INSERT INTO `admin_tea_teacher` VALUES ('7', '贾生元', '\\uploads\\20180626\\291d84173f7a094fdbc67a289a4af08c.jpg', '1', '中国林业科学院', '教授级高工', '1529998913', '15000000000', '1');

-- ----------------------------
-- Table structure for admin_test_base
-- ----------------------------
DROP TABLE IF EXISTS `admin_test_base`;
CREATE TABLE `admin_test_base` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) DEFAULT NULL COMMENT '所属课程,对应amin_flv_movie的id',
  `content` varchar(255) NOT NULL DEFAULT '0' COMMENT '问题',
  `answer` varchar(10) NOT NULL DEFAULT '' COMMENT '答案,判断0：错误 1：正确  单选多选：0,1,2,3 ：A,B,C，D',
  `A` varchar(255) NOT NULL COMMENT 'A选项',
  `B` varchar(255) NOT NULL COMMENT 'B选项',
  `C` varchar(255) NOT NULL DEFAULT '0' COMMENT 'C选项',
  `D` varchar(255) NOT NULL COMMENT 'D选项',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '题目的类型：3--判断题,1--单选题,2--多选题,4--填空题',
  `option` varchar(255) NOT NULL COMMENT '选项',
  `create_time` int(20) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `title` (`answer`),
  KEY `actors` (`A`),
  KEY `year` (`B`)
) ENGINE=InnoDB AUTO_INCREMENT=232 DEFAULT CHARSET=utf8 COMMENT='题库管理';

-- ----------------------------
-- Records of admin_test_base
-- ----------------------------
INSERT INTO `admin_test_base` VALUES ('1', '1', '规划环评的基本特征有几项基本要求？', '2', '1', '6', '3', '4', '1', '{\"A\":\"1\",\"B\":\"6\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('2', '1', '三项基本要求里的核心要求是防范污染源再生是否正确？', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('3', '1', '推进质量改善算三项基本要求里的第几环节', '2', '2', '6', '3', '1', '1', '{\"A\":\"2\",\"B\":\"6\",\"C\":\"3\",\"D\":\"1\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('4', '1', '强化风险防范＋推进质量改善＋促进污染减排的排序是否正确？', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('5', '2', '规划环评的主要特征四大核心里的第一要素是什么？', '3', '规划的定位与规模 ', '规划的发展速度与时间', '规划的目标和方向', '规划的结构与布局', '1', '{\"A\":\"规划的定位与规模 \",\"B\":\"规划的发展速度与时间\",\"C\":\"规划的目标和方向\",\"D\":\"规划的结构与布局\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('6', '2', '确定规划的目标和方向才能完成规划发展活动的任务', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('7', '2', '天然气来替代燃煤是否正确', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('8', '2', '结构性污染治理的建议包含什么', '0', '总量控制', '数量控制', '部分控制', '个别控制', '1', '{\"A\":\"总量控制\",\"B\":\"数量控制\",\"C\":\"部分控制\",\"D\":\"个别控制\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('9', '3', '规划环评的技术原则——评价原则有几点', '3', '8点', '7点', '6点', '5点', '1', '{\"A\":\"8点\",\"B\":\"7点\",\"C\":\"6点\",\"D\":\"5点\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('10', '3', '规划环评的技术原则——评价一般原则有几点', '1', '4个', '5个', '6个', '7个', '1', '{\"A\":\"4个\",\"B\":\"5个\",\"C\":\"6个\",\"D\":\"7个\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('11', '4', '规划环评主体结构中的规划开发活动有几个关键点？', '2', '4点', '5点', '6点', '7点', '1', '{\"A\":\"4点\",\"B\":\"5点\",\"C\":\"6点\",\"D\":\"7点\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('12', '4', '规划环评主体结构中以能源重化工基地发展规划为例从几个问题导向出发去讲解课题？', '1', '3个', '4个', '5个', '6个', '1', '{\"A\":\"3个\",\"B\":\"4个\",\"C\":\"5个\",\"D\":\"6个\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('13', '4', '规划环评主体结构中——中观层次——导向因素', '0', '大气、水、生态', '空气、氧气、环境', '臭氧、污水、生态', '大气、氧气、生态', '1', '{\"A\":\"大气、水、生态\",\"B\":\"空气、氧气、环境\",\"C\":\"臭氧、污水、生态\",\"D\":\"大气、氧气、生态\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('14', '4', '规划环评有几大重点任务？', '2', '7大重点任务', '6大重点任务', '5大重点任务', '4大重点任务', '1', '{\"A\":\"7大重点任务\",\"B\":\"6大重点任务\",\"C\":\"5大重点任务\",\"D\":\"4大重点任务\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('15', '4', '中观层次要素导向包含', '2', '大气、水、土壤', '水、生态、空气', '大气、水、生态', '大气、污水、生态环境', '1', '{\"A\":\"大气、水、土壤\",\"B\":\"水、生态、空气\",\"C\":\"大气、水、生态\",\"D\":\"大气、污水、生态环境\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('16', '5', '规划环评有哪几大重点任务', '3', '6', '4', '3', '5', '1', '{\"A\":\"6\",\"B\":\"4\",\"C\":\"3\",\"D\":\"5\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('17', '5', '切断地下水污染途径是否正确', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('18', '5', '协调性分析除了规划要点梳理之外也是另一方面主要的内容', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('19', '6', '规划环评技术路线主要包括几部分内容？', '0', '3', '4', '5', '6', '1', '{\"A\":\"3\",\"B\":\"4\",\"C\":\"5\",\"D\":\"6\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('20', '6', '规划要点及协调性分析分为哪几个分析', '3', '规划目标与方向分析 ', '规划理论与实操分析', '规划目标与位置分析', '规划目标与定位分析', '1', '{\"A\":\"规划目标与方向分析 \",\"B\":\"规划理论与实操分析\",\"C\":\"规划目标与位置分析\",\"D\":\"规划目标与定位分析\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('21', '6', '宏观层次评价重点包含对宜居城市和创模的影响分析', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('22', '6', 'LCA指标体系就是生命周期指标体系？', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('23', '7', '当0.8≤C＜1时，土地可持续水平基本处于最优状态', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('24', '7', '当0.6≤C＜0.8，城市土地可持续利用水平相对较低', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('25', '7', '规划环境影响识别分为几个层次？', '1', '2', '3', '4', '5', '1', '{\"A\":\"2\",\"B\":\"3\",\"C\":\"4\",\"D\":\"5\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('26', '7', '规划环评主要指标体系有几个类型？', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('27', '8', 'CCP代表压力指数？', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('28', '8', 'CCS代表支持力指数？', '1', '错', '对', '', '', '3', '{\"A\":\"错\",\"B\":\"对\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('29', '8', '污染防治的对策需从几方面入手？', '0', '3', '4', '5', '6', '1', '{\"A\":\"3\",\"B\":\"4\",\"C\":\"5\",\"D\":\"6\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('30', '8', '生态保护的对策需从几方面入手？', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('31', '9', '承载力主要包括哪几大类', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('32', '9', '环境空间主要从几个方面体现', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('33', '9', '环境空间占用率越大越好', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('34', '9', '规划发展对承载力的利用水平，体现资源节约型和效益型发展的要求，其数值越大越好', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('35', '10', '污染源源强核算技术指南有几项环境保护标准？', '1', '4个', '5个', '6个', '7个', '1', '{\"A\":\"4个\",\"B\":\"5个\",\"C\":\"6个\",\"D\":\"7个\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('36', '10', '建设项目环境影响后评价管理办法主要内容有几条？', '2', '5条', '6条', '7条', '8条', '1', '{\"A\":\"5条\",\"B\":\"6条\",\"C\":\"7条\",\"D\":\"8条\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('37', '11', '《建设项目环境影响评价技术导则 总纲》是2017年1月1日开始实施的么？', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('38', '11', '建设项目的环境影响报告书内容总共分几条进行了讲解？', '3', '7条', '8条', '9条', '10条', '1', '{\"A\":\"7条\",\"B\":\"8条\",\"C\":\"9条\",\"D\":\"10条\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('39', '12', '环境影响评价是否应采用定量评价与定性评价相接结合的方法？', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('40', '12', '建设项目工程分心总共分几条进行了讲解？', '2', '6条', '5条', '4条', '3条', '1', '{\"A\":\"6条\",\"B\":\"5条\",\"C\":\"4条\",\"D\":\"3条\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('41', '12', '工程分析应应关注的内容——污染控制措施有几条？', '2', '7条', '6条', '5条', '4条', '1', '{\"A\":\"7条\",\"B\":\"6条\",\"C\":\"5条\",\"D\":\"4条\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('42', '13', '环境现状调查方法由环境要素环境影响评价技术导则下列哪一条不属于导则的具体规定？', '3', '自然环境现状调查预评价', '环境保护目标调查', '环境质量现状调查与评价', '整体污染源调查', '1', '{\"A\":\"自然环境现状调查预评价\",\"B\":\"环境保护目标调查\",\"C\":\"环境质量现状调查与评价\",\"D\":\"整体污染源调查\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('43', '13', '环境影响预测与评价中哪一条是视频中没有讲解过得内容？', '1', '基本要求', '环境影响结果与调查的主要内容', '环境影响预测与评价方法', '环境影响预测与评价内容', '1', '{\"A\":\"基本要求\",\"B\":\"环境影响结果与调查的主要内容\",\"C\":\"环境影响预测与评价方法\",\"D\":\"环境影响预测与评价内容\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('44', '14', '在流平衡示意图中，原料、辅料及燃料总代如硫量是多少t/a?', '0', '59352.49t/a', '69352.49t/a', '79352.49t/a', '89352.49t/a', '1', '{\"A\":\"59352.49t/a\",\"B\":\"69352.49t/a\",\"C\":\"79352.49t/a\",\"D\":\"89352.49t/a\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('45', '14', '在各种平衡分析中，钢铁导则强调在工程分析章节完成有哪四大平衡？', '2', '金属平衡、氧气平衡、水文平衡、硫平衡', '钢铁平衡、煤气平衡、水量平衡、硫平衡', '金属平衡、煤气平衡、水量平衡、硫平衡', '金属平衡、煤气平衡、水量平衡、镁平衡', '1', '{\"A\":\"金属平衡、氧气平衡、水文平衡、硫平衡\",\"B\":\"钢铁平衡、煤气平衡、水量平衡、硫平衡\",\"C\":\"金属平衡、煤气平衡、水量平衡、硫平衡\",\"D\":\"金属平衡、煤气平衡、水量平衡、镁平衡\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('46', '15', '生态学可否用哲学方法来研究生态', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('47', '15', '优势种在群落中是否起主导和控制作用', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('48', '15', '植被可等同于植物吗', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('49', '15', '1980年中国植被分类的单位有几种', '0', '3个基本单位，9级分类', '3个基本单位，8级分类', '4个基本单位，8级分类', '4个基本单位，9级分类', '1', '{\"A\":\"3个基本单位，9级分类\",\"B\":\"3个基本单位，8级分类\",\"C\":\"4个基本单位，8级分类\",\"D\":\"4个基本单位，9级分类\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('50', '15', '生物多样性化分了几个层次', '0', '3', '4', '6', '8', '1', '{\"A\":\"3\",\"B\":\"4\",\"C\":\"6\",\"D\":\"8\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('51', '16', '同种生物个体的集合体有几个基本特征', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('52', '16', '生态对环境的适应是否是自然选择的结果', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('53', '16', '绝大多数物种只能生活在确定的环境条件范围内是否正确', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('54', '17', '生态影响分析限制条件有几种', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('55', '17', '自然保护区、世界文化是否属于特殊生态敏感区', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('56', '17', '除特殊生态敏感区外，其他区域均属于一般区域是否正确', '1', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('57', '18', '生态现状调查方法中样方排列有几种排列', '0', '2', '4', '6', '8', '1', '{\"A\":\"2\",\"B\":\"4\",\"C\":\"6\",\"D\":\"8\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('58', '18', '环境敏感区中是环保部哪一号令提到的内容', '1', '40号', '44号', '46号', '48号', '1', '{\"A\":\"40号\",\"B\":\"44号\",\"C\":\"46号\",\"D\":\"48号\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('59', '18', '系统分析法是否属于生态现状评价法', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('60', '18', '一级评价基本图件有几个', '3', '6', '7', '8', '9', '1', '{\"A\":\"6\",\"B\":\"7\",\"C\":\"8\",\"D\":\"9\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('61', '18', '工程平面图是否属于三级评价的基本图件', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('62', '19', '环境影响评价中环境定义包含几大资源', '2', '1', '2', '3', '4', '1', '{\"A\":\"1\",\"B\":\"2\",\"C\":\"3\",\"D\":\"4\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('63', '19', '环境影响评价按环境要素分为几种', '3', '2', '3', '4', '5', '1', '{\"A\":\"2\",\"B\":\"3\",\"C\":\"4\",\"D\":\"5\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('64', '19', '环境影响评价必须客观、公开、公正是否正确', '0', '对', '错', '', '', '3', '{\"A\":\"对\",\"B\":\"错\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('65', '19', '环境标准有几大作用', '2', '1', '3', '6', '8', '1', '{\"A\":\"1\",\"B\":\"3\",\"C\":\"6\",\"D\":\"8\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('66', '19', '环境标准体系由几级几类构成', '1', '4级6类', '3级5类', '2级4类', '3级6类', '1', '{\"A\":\"4级6类\",\"B\":\"3级5类\",\"C\":\"2级4类\",\"D\":\"3级6类\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('67', '19', '环境空气功能可区分几类', '0', '2类', '3类', '5类', '7类', '1', '{\"A\":\"2类\",\"B\":\"3类\",\"C\":\"5类\",\"D\":\"7类\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('68', '19', '地表水水域是否适用于农业用水区域', '0', '是', '不是', '', '', '3', '{\"A\":\"是\",\"B\":\"不是\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('69', '19', '哪一年正式颁布了污染源强核算技术指南准则', '3', '2015年', '2016年', '2017年', '2018年', '1', '{\"A\":\"2015年\",\"B\":\"2016年\",\"C\":\"2017年\",\"D\":\"2018年\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('70', '20', '排入公共污水处理系统的水污染排放PH限值是多少', '0', '6.5—9', '7.5—9.5', '6—9', '7—9', '1', '{\"A\":\"6.5—9\",\"B\":\"7.5—9.5\",\"C\":\"6—9\",\"D\":\"7—9\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('71', '20', '工程沿线地区属于空气几类区域', '1', '一类', '二类', '三类', '四类', '1', '{\"A\":\"一类\",\"B\":\"二类\",\"C\":\"三类\",\"D\":\"四类\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('72', '20', '工程中弃渣按照指定地点消纳，是否会对周围环境产生明显生态影响和水土流失', '1', '会', '不会', '', '', '3', '{\"A\":\"会\",\"B\":\"不会\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('73', '21', '建设项目环境影响评价基础知识共分为几章内容？', '0', '7', '8', '9', '10', '1', '{\"A\":\"7\",\"B\":\"8\",\"C\":\"9\",\"D\":\"10\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('74', '21', '环境是否由环境要素构成？', '1', '是', '否', '', '', '3', '{\"A\":\"是\",\"B\":\"否\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('75', '21', '资质证书有效期届满，环评机构需要继续从事环境影响报告书编制工作的，应当在有效期满八十个工作日前申请资质延续？', '0', '是', '否', '', '', '3', '{\"A\":\"是\",\"B\":\"否\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('76', '21', '环保部于2015年几月28日以部令第36号下发了《建设项目环境影响评价资质管理办法》？', '2', '7', '8', '9', '10', '1', '{\"A\":\"7\",\"B\":\"8\",\"C\":\"9\",\"D\":\"10\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('77', '22', '依照《中华人民共和国环境影响评价法》的规定，由环境保护部降低其资质等级或者吊销其资质证书，并处所受费用十倍以上的罚款？', '0', '否', '是', '', '', '3', '{\"A\":\"否\",\"B\":\"是\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('78', '22', '环保部于2015年12月几日以部令第37号颁布了《建设项目环境影响后评价管理办法》？', '1', '9', '10', '11', '12', '1', '{\"A\":\"9\",\"B\":\"10\",\"C\":\"11\",\"D\":\"12\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('79', '22', '《控制污染物排放许可制实施方案》是否明确规定了：“通过实施排污许可制，落实企事业单位污染物排放总量控制要求，逐步实现由行政区域污染物排放总量控制向企事业单位污染物排放总量控制转变，控制的范围逐渐统一到固定污染源。”？', '0', '是', '否', '', '', '3', '{\"A\":\"是\",\"B\":\"否\",\"C\":\"\",\"D\":\"\"}', '123123123');
INSERT INTO `admin_test_base` VALUES ('80', '22', '排污许可制度不仅能有效地控制和改善环境质量，提高人们的生活质量，在污染防治和资源保护利用方面，更有其积极意义？', '1', '否', '是', '', '', '3', '{\"A\":\"否\",\"B\":\"是\",\"C\":\"\",\"D\":\"\"}', '123123123');

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `phone` char(11) NOT NULL COMMENT '手机号',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `status` enum('1','-1') NOT NULL DEFAULT '1' COMMENT '1:正常状态;-1:冻结',
  `token` varchar(255) NOT NULL COMMENT 'token',
  `qqopenid` varchar(255) NOT NULL COMMENT 'qq openid',
  `wxopenid` varchar(255) NOT NULL COMMENT 'wx openid',
  `enterprise_id` int(11) NOT NULL COMMENT '企业id',
  PRIMARY KEY (`uid`),
  KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES ('229', '17610392520', '550e1bafe077ff0b0b67f4e32f29d751', '-1', '1231243123213', '', '', '102');
INSERT INTO `admin_user` VALUES ('230', '17610392521', '25d55ad283aa400af464c76d713c07ad', '-1', 'e2131r2d3re223rf2r3f', '', '', '102');
INSERT INTO `admin_user` VALUES ('232', '17610392521', 'b104817435853a5b05aa64b8f2a937fa', '1', '3212131232132131', '', '', '1021');
INSERT INTO `admin_user` VALUES ('233', '15800000001', '63ee451939ed580ef3c4b6f0109d1fd0', '1', '1231242321', '', '', '1031');
INSERT INTO `admin_user` VALUES ('234', '15800000002', '63ee451939ed580ef3c4b6f0109d1fd0', '1', '123123123', '', '', '1031');
INSERT INTO `admin_user` VALUES ('235', '15800000003', '63ee451939ed580ef3c4b6f0109d1fd0', '1', '3213143123', '', '', '1031');
INSERT INTO `admin_user` VALUES ('236', '15800000004', '63ee451939ed580ef3c4b6f0109d1fd0', '1', '123123', '', '', '1031');
INSERT INTO `admin_user` VALUES ('237', '17610392521', 'f5bb0c8de146c67b44babbf4e6584cc0', '-1', '11', '', '', '1021');
INSERT INTO `admin_user` VALUES ('239', '17610392521', '9e5317e838cb5bd8e98a013fffc2b30e', '1', '71530060496', '', '', '1021');

-- ----------------------------
-- Table structure for admin_user_detail
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_detail`;
CREATE TABLE `admin_user_detail` (
  `udid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '昵称',
  `uid` int(11) unsigned NOT NULL COMMENT '对应用户uid',
  `idnumber` char(18) NOT NULL COMMENT '身份证号',
  `create_time` date NOT NULL COMMENT '入职时间',
  `enterprise_id` varchar(255) NOT NULL COMMENT '企业id',
  `wechat` varchar(255) NOT NULL COMMENT '微信',
  `qq` varchar(255) NOT NULL COMMENT 'QQ',
  `avatar` varchar(255) NOT NULL COMMENT '头像',
  `sex` varchar(255) NOT NULL DEFAULT '0' COMMENT '性别',
  PRIMARY KEY (`udid`),
  KEY `uid` (`uid`),
  CONSTRAINT `uid` FOREIGN KEY (`uid`) REFERENCES `admin_user` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_user_detail
-- ----------------------------
INSERT INTO `admin_user_detail` VALUES ('1', '刘欣', '229', '411525199710119333', '2018-06-26', '102', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('2', 'jellbool', '230', '411525199710119333', '2018-06-23', '1021', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('4', 'laravel', '232', '411525199710119333', '2018-05-30', '1021', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('5', 'wq张三', '233', '110110199910100001', '2018-06-26', '103', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('6', 'wq李四', '234', '110110199910100002', '2018-06-26', '103', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('7', 'wq王五', '235', '110110199910100003', '2018-06-26', '103', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('8', 'wq马六', '236', '110110199910100004', '2018-06-26', '103', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('9', '测试1', '237', '411525199710119333', '2018-06-20', '1021', '', '', '', '0');
INSERT INTO `admin_user_detail` VALUES ('11', 'overtrue', '239', '411525199710119333', '2018-06-14', '1021', '', '', '', '0');

-- ----------------------------
-- Table structure for admin_user_plan
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_plan`;
CREATE TABLE `admin_user_plan` (
  `jid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户id',
  `cid` int(11) NOT NULL COMMENT '课程包id',
  `mid` varchar(255) NOT NULL COMMENT '视频id',
  `progress` varchar(255) DEFAULT NULL COMMENT '进度',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  `complete` int(3) DEFAULT '0',
  PRIMARY KEY (`jid`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='学习进度表';

-- ----------------------------
-- Records of admin_user_plan
-- ----------------------------
INSERT INTO `admin_user_plan` VALUES ('36', '229', '6', '2', '56789', null, '1');
INSERT INTO `admin_user_plan` VALUES ('37', '229', '6', '3', '2143123', null, '1');

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码；mb_password加密',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像，相对于upload/avatar目录',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `email_code` varchar(60) DEFAULT NULL COMMENT '激活码',
  `phone` bigint(11) unsigned DEFAULT NULL COMMENT '手机号',
  `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '用户状态 0：禁用； 1：正常 ；2：未验证',
  `register_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `group_id` int(11) NOT NULL COMMENT '角色',
  PRIMARY KEY (`id`),
  KEY `user_login_key` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES ('1', 'sadmin', '552E428863A031F082D62B0E93807A4C', '', 'wangq@etlchina.net', 'hp.app@pwd.com', '15810153274', '1', '0', '', '0', '0');
INSERT INTO `admin_users` VALUES ('99', 'admin', '123456', '', '25449365@qq.com', null, '17801175267', '1', '0', '', '0', '0');

-- ----------------------------
-- Table structure for admin_version
-- ----------------------------
DROP TABLE IF EXISTS `admin_version`;
CREATE TABLE `admin_version` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '版本名',
  `num` varchar(11) DEFAULT NULL COMMENT '版本号',
  `type` varchar(255) DEFAULT NULL COMMENT '设备类型',
  `url` varchar(255) DEFAULT NULL COMMENT '文件路径',
  `mustupdate` int(10) DEFAULT '0' COMMENT '是否强制更新',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_version
-- ----------------------------
