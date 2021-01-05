/*
 Navicat MySQL Data Transfer

 Source Server         : 宁国腾百度服务器
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : 106.12.219.170:3306
 Source Schema         : notengt

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 05/01/2021 15:55:56
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for content
-- ----------------------------
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `type_id` int(11) DEFAULT '0' COMMENT '导航（分类）id',
  `title` varchar(255) DEFAULT NULL COMMENT '笔记标题',
  `content` longtext COMMENT '笔记内容',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  `upload_type` tinyint(4) DEFAULT '1' COMMENT '1笔记 2附件',
  PRIMARY KEY (`id`),
  KEY `type外键` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for type
-- ----------------------------
DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT '0',
  `title` varchar(255) DEFAULT NULL COMMENT '导航（分类）',
  `parent_id` int(11) DEFAULT '0' COMMENT '父id',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1' COMMENT '1有效  0删除',
  `top` tinyint(4) DEFAULT '0' COMMENT '置顶 1是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `nick_name` varchar(255) DEFAULT NULL COMMENT '昵称',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `create_time` int(11) DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
