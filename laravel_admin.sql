/*
 Navicat Premium Data Transfer

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : laravel_admin

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 28/05/2020 03:25:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `permission_id` int(11) NOT NULL COMMENT '权限ID',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员和权限的关系表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (2, 1, '2020-05-28 03:14:45', '2020-05-28 03:14:45');

-- ----------------------------
-- Table structure for admins
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密码',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '姓名',
  `sex` tinyint(3) NOT NULL DEFAULT 0 COMMENT '性别:0=保密,1=男,2=女',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '头像',
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '电子邮箱',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态:1=正常,2=禁用',
  `remember_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT 'token',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admins
-- ----------------------------
INSERT INTO `admins` VALUES (1, 'admin', '$2y$10$f5Q/qz1Xd1FVClIzJruvXu9U/tBRMKTob0I8ozxrv.Qtp5kYhy72e', '默认管理员', 1, NULL, '1196974868@qq.com', 1, NULL, '2020-05-27 18:15:26', '2020-05-28 03:12:25', NULL);
INSERT INTO `admins` VALUES (2, 'demo', '$2y$10$c6LVVOA/MJP52YPmKUQcHOsV/lXPMQ0jQnHfx9fE5y9DI4hNe2vR2', 'Demo', 1, NULL, 'demo@example.com', 1, NULL, '2020-05-28 03:13:08', '2020-05-28 03:14:45', NULL);

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `variable` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '变量名',
  `type` tinyint(3) NOT NULL DEFAULT 1 COMMENT '类型:1=单行文本框,2=多行文本框,3=单选按钮,4=复选框,5=下拉菜单',
  `item` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '可选项',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序(升序)',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of configs
-- ----------------------------
INSERT INTO `configs` VALUES (1, '网站是否开启', 'siteOpen', 3, '开启,关闭', '开启', 1, '2020-05-28 03:15:45', '2020-05-28 03:24:10', NULL);
INSERT INTO `configs` VALUES (2, '网站标题', 'siteTitle', 1, NULL, '使用Laravel搭建的网站', 2, '2020-05-28 03:15:57', '2020-05-28 03:24:10', NULL);
INSERT INTO `configs` VALUES (3, '网站关键字', 'siteKeyWords', 1, NULL, 'Laravel,网站', 3, '2020-05-28 03:16:27', '2020-05-28 03:24:10', NULL);
INSERT INTO `configs` VALUES (4, '网站描述', 'siteDescription', 2, NULL, '为了快速开发而搭建的一套网站后台模板。使用Laravel框架，选用AdminLTE作为后台模板，为了提高RBAC权限性能而使用了Redis缓存技术。', 4, '2020-05-28 03:16:43', '2020-05-28 03:24:10', NULL);
INSERT INTO `configs` VALUES (5, '备案，版权信息', 'footerInfo', 2, NULL, '版权是个什么东东？', 5, '2020-05-28 03:17:22', '2020-05-28 03:24:10', NULL);

-- ----------------------------
-- Table structure for operation_logs
-- ----------------------------
DROP TABLE IF EXISTS `operation_logs`;
CREATE TABLE `operation_logs`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `method` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类型',
  `path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '路径',
  `input` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '数据',
  `admin_id` int(11) NOT NULL COMMENT '管理员ID(操作的人)',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '操作日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父ID',
  `title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '标题',
  `slug` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '标识',
  `icon` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '图标',
  `is_menu` tinyint(3) NOT NULL DEFAULT 2 COMMENT '菜单:1=是,2=否',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态:1=正常,2=禁用',
  `remark` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `sort` int(11) NULL DEFAULT NULL COMMENT '排序(升序)',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '权限表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (1, 0, '清空缓存', 'admin.clear_cache', 'fa-circle-o', 2, 1, NULL, 10, '2020-05-27 18:16:01', '2020-05-27 18:22:10', NULL);
INSERT INTO `permissions` VALUES (2, 18, '用户管理', 'admin.admin', 'fa-circle-o', 1, 1, NULL, 10, '2020-05-27 18:16:01', '2020-05-27 18:18:21', NULL);
INSERT INTO `permissions` VALUES (3, 2, '用户添加', 'admin.admin.create', 'fa-circle-o', 2, 1, NULL, 11, '2020-05-27 18:16:01', '2020-05-27 18:18:50', NULL);
INSERT INTO `permissions` VALUES (4, 2, '用户查看', 'admin.admin.show', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:19:34', NULL);
INSERT INTO `permissions` VALUES (5, 2, '用户修改', 'admin.admin.update', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:19:45', NULL);
INSERT INTO `permissions` VALUES (6, 2, '用户删除', 'admin.admin.delete', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:20:01', NULL);
INSERT INTO `permissions` VALUES (7, 18, '角色管理', 'admin.role', 'fa-circle-o', 1, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:20:21', NULL);
INSERT INTO `permissions` VALUES (8, 7, '角色添加', 'admin.role.create', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:20:37', NULL);
INSERT INTO `permissions` VALUES (9, 7, '角色查看', 'admin.role.show', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:20:49', NULL);
INSERT INTO `permissions` VALUES (10, 7, '角色修改', 'admin.role.update', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:21:03', NULL);
INSERT INTO `permissions` VALUES (11, 7, '角色删除', 'admin.role.delete', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:21:13', NULL);
INSERT INTO `permissions` VALUES (12, 18, '权限管理', 'admin.permission', 'fa-circle-o', 1, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:21:24', NULL);
INSERT INTO `permissions` VALUES (13, 12, '权限查看', 'admin.permission.show', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:01', '2020-05-27 18:21:48', NULL);
INSERT INTO `permissions` VALUES (14, 12, '权限修改', 'admin.permission.update', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:02', '2020-05-27 18:22:39', NULL);
INSERT INTO `permissions` VALUES (15, 19, '系统设置', 'admin.setting', 'fa-circle-o', 1, 1, NULL, 100, '2020-05-27 18:16:02', '2020-05-27 18:23:31', NULL);
INSERT INTO `permissions` VALUES (16, 19, '日志列表', 'admin.operation_log', 'fa-circle-o', 1, 1, NULL, 100, '2020-05-27 18:16:02', '2020-05-27 18:23:48', NULL);
INSERT INTO `permissions` VALUES (17, 16, '日常查看', 'admin.operation_log.show', 'fa-circle-o', 2, 1, NULL, 100, '2020-05-27 18:16:02', '2020-05-27 18:23:58', NULL);
INSERT INTO `permissions` VALUES (18, 0, '管理员管理', NULL, 'fa-group', 1, 1, NULL, 10, '2020-05-27 18:17:10', '2020-05-27 18:17:10', NULL);
INSERT INTO `permissions` VALUES (19, 0, '系统管理', NULL, 'fa-gears', 1, 1, NULL, 11, '2020-05-27 18:17:37', '2020-05-27 18:17:37', NULL);

-- ----------------------------
-- Table structure for role_admins
-- ----------------------------
DROP TABLE IF EXISTS `role_admins`;
CREATE TABLE `role_admins`  (
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `admin_id` int(11) NOT NULL COMMENT '管理员ID',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色和管理员的关系表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_admins
-- ----------------------------
INSERT INTO `role_admins` VALUES (1, 2, '2020-05-28 03:14:45', '2020-05-28 03:14:45');

-- ----------------------------
-- Table structure for role_permissions
-- ----------------------------
DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions`  (
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `permission_id` int(11) NOT NULL COMMENT '权限ID',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色和权限的关系表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of role_permissions
-- ----------------------------
INSERT INTO `role_permissions` VALUES (1, 2, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 7, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 12, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 16, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 17, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 18, '2020-05-28 03:14:13', '2020-05-28 03:14:13');
INSERT INTO `role_permissions` VALUES (1, 19, '2020-05-28 03:14:13', '2020-05-28 03:14:13');

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名称',
  `status` tinyint(3) NOT NULL DEFAULT 1 COMMENT '状态:1=正常,2=禁用',
  `remark` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `deleted_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, '观察者', 1, '仅有查看权限', '2020-05-28 03:14:01', '2020-05-28 03:14:01', NULL);

SET FOREIGN_KEY_CHECKS = 1;
