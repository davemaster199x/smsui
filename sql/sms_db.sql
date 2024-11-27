/*
 Navicat Premium Data Transfer

 Source Server         : Local_DB
 Source Server Type    : MySQL
 Source Server Version : 100411 (10.4.11-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : sms_db

 Target Server Type    : MySQL
 Target Server Version : 100411 (10.4.11-MariaDB)
 File Encoding         : 65001

 Date: 18/07/2023 07:23:04
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for contact
-- ----------------------------
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact`  (
  `contact_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp,
  `updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL,
  `first_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `company` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `phone` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`contact_id`) USING BTREE,
  INDEX `created`(`created` ASC) USING BTREE,
  INDEX `updated`(`updated` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `phone`(`phone` ASC) USING BTREE,
  CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of contact
-- ----------------------------
INSERT INTO `contact` VALUES (1, '2023-07-10 14:35:19', '2023-07-10 14:35:19', 1, 'Test', 'Dave', 'dave@gmail.com', NULL, NULL);
INSERT INTO `contact` VALUES (2, '2023-07-11 10:28:18', '2023-07-11 10:28:18', 1, 'ddddf', 'dd', 'fdfdf@gmail.com', NULL, NULL);
INSERT INTO `contact` VALUES (4, '2023-07-11 10:30:24', '2023-07-11 10:30:24', 1, 'sass', 'asds', 'sass@gmail.com', 'sdfds', '23123');
INSERT INTO `contact` VALUES (5, '2023-07-11 11:08:19', '2023-07-11 11:08:19', 1, 'Dave', 'Flores', 'dave@gmail.com', 'Test company', '123456');

-- ----------------------------
-- Table structure for did
-- ----------------------------
DROP TABLE IF EXISTS `did`;
CREATE TABLE `did`  (
  `did_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp,
  `updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL,
  `did` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `api_username` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `api_password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`did_id`) USING BTREE,
  INDEX `created`(`created` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `did`(`did` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of did
-- ----------------------------
INSERT INTO `did` VALUES (1, '2023-07-10 10:00:22', '2023-07-10 10:00:22', 1, 'test', 'test', 'test');

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message`  (
  `message_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp,
  `updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  `src` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `dst` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `type` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `relayed` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`) USING BTREE,
  INDEX `created`(`created` ASC) USING BTREE,
  INDEX `updated`(`updated` ASC) USING BTREE,
  INDEX `src`(`src` ASC) USING BTREE,
  INDEX `dst`(`dst` ASC) USING BTREE,
  INDEX `relayed`(`relayed` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of message
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp,
  `updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `email` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `password` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `first_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `last_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `hash` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE,
  INDEX `created`(`created` ASC) USING BTREE,
  INDEX `active`(`active` ASC) USING BTREE,
  INDEX `username`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, '2023-07-10 07:34:25', '2023-07-18 06:59:53', 1, 'test@gmail.com', '$2y$10$PPYE2E4d6LqUaDCH06OUsOMCa3fDlsb9yrdUlLqC1x4Gwl4x5P/7i', 'Test', 'Name', '84f1d0422ac76a0fe0b4d92825ee58f2');

-- ----------------------------
-- Table structure for xref_user_did
-- ----------------------------
DROP TABLE IF EXISTS `xref_user_did`;
CREATE TABLE `xref_user_did`  (
  `created` timestamp NOT NULL DEFAULT current_timestamp,
  `updated` timestamp NOT NULL DEFAULT current_timestamp ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL,
  `did_id` int UNSIGNED NOT NULL,
  INDEX `created`(`created` ASC) USING BTREE,
  INDEX `updated`(`updated` ASC) USING BTREE,
  INDEX `user_id`(`user_id` ASC) USING BTREE,
  INDEX `did_id`(`did_id` ASC) USING BTREE,
  CONSTRAINT `xref_user_did_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `xref_user_did_ibfk_2` FOREIGN KEY (`did_id`) REFERENCES `did` (`did_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of xref_user_did
-- ----------------------------
INSERT INTO `xref_user_did` VALUES ('2023-07-10 10:00:39', '2023-07-10 10:00:39', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
