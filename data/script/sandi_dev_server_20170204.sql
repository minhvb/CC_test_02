/*
Navicat MySQL Data Transfer

Source Server         : Sanroap
Source Server Version : 50717
Source Host           : 10.17.72.189:3306
Source Database       : sandi_dev

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-02-04 18:33:31
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `action`
-- ----------------------------
DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of action
-- ----------------------------

-- ----------------------------
-- Table structure for `authenticate`
-- ----------------------------
DROP TABLE IF EXISTS `authenticate`;
CREATE TABLE `authenticate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleId` int(11) NOT NULL,
  `actionId` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0. active, 1. inactive',
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of authenticate
-- ----------------------------

-- ----------------------------
-- Table structure for `policy`
-- ----------------------------
DROP TABLE IF EXISTS `policy`;
CREATE TABLE `policy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bureauId` smallint(6) DEFAULT NULL,
  `departmentId` smallint(6) DEFAULT NULL,
  `divisionId` smallint(6) DEFAULT NULL,
  `shortName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `recruitmentFlag` tinyint(1) DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agencyImplement` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `attentionFlag` tinyint(1) DEFAULT NULL,
  `recruitmentForm` tinyint(1) DEFAULT NULL,
  `purpose` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pdfFile` varchar(2048) COLLATE utf8_unicode_ci DEFAULT NULL,
  `supportArea` smallint(6) DEFAULT NULL,
  `detailOfSupportArea` varchar(1024) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detailRecruitmentTime` text COLLATE utf8_unicode_ci,
  `publishStartdate` int(11) DEFAULT NULL,
  `publishEnddate` int(11) DEFAULT NULL,
  `emailNotificationFlag` smallint(6) DEFAULT NULL,
  `createBy` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updateBy` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  `isDraft` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of policy
-- ----------------------------

-- ----------------------------
-- Table structure for `policy_attributes`
-- ----------------------------
DROP TABLE IF EXISTS `policy_attributes`;
CREATE TABLE `policy_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attributeType` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of policy_attributes
-- ----------------------------

-- ----------------------------
-- Table structure for `policy_attribute_mapping`
-- ----------------------------
DROP TABLE IF EXISTS `policy_attribute_mapping`;
CREATE TABLE `policy_attribute_mapping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policyId` int(11) DEFAULT NULL,
  `attributesPolicyId` smallint(6) DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of policy_attribute_mapping
-- ----------------------------

-- ----------------------------
-- Table structure for `role`
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO role VALUES ('1', '管理者', 'Administrator', null, null);
INSERT INTO role VALUES ('2', '施策情報入力者', 'User input', null, null);
INSERT INTO role VALUES ('3', '都職員', '都職員', null, null);
INSERT INTO role VALUES ('4', '関係団体職員', '関係団体職員', null, null);
INSERT INTO role VALUES ('5', '金融機関行員', '金融機関行員', null, null);
INSERT INTO role VALUES ('6', '一般', '一般', null, null);

-- ----------------------------
-- Table structure for `security_question`
-- ----------------------------
DROP TABLE IF EXISTS `security_question`;
CREATE TABLE `security_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of security_question
-- ----------------------------
INSERT INTO security_question VALUES ('1', '好きな食べ物は？', null, null);
INSERT INTO security_question VALUES ('2', '好きな映画は？', null, null);
INSERT INTO security_question VALUES ('3', '子どもの頃のニックネームは？', null, null);

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `roleId` int(11) NOT NULL,
  `userName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `bureauId` int(11) DEFAULT NULL,
  `departmentId` int(11) DEFAULT NULL,
  `divisionId` int(11) DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'encrypted by md5',
  `business` int(11) DEFAULT NULL,
  `companySizeId` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` int(11) DEFAULT NULL,
  `passwordUpdateDate` int(11) DEFAULT NULL,
  `lastLoginFail` int(11) DEFAULT NULL,
  `totalLoginFail` int(11) DEFAULT '0',
  `passwordHistory` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  `questionId` int(11) DEFAULT NULL,
  `answer` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `token` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tokenExpireDate` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO user VALUES ('1', '2', 'user001', '1', '1', '1', 'abc@gmail.com', '25d55ad283aa400af464c76d713c07ad', null, null, null, '1486104353', '1486107516', '0', '[\"25d55ad283aa400af464c76d713c07ad\"]', null, null, '1', 'manh', null, null, '0');
INSERT INTO user VALUES ('3', '1', 'user002', '1', '1', '1', 'abc@gmail.com', '25f9e794323b453885f5181f1b624d0b', null, null, null, '1486196041', '1486107516', '0', '[\"25d55ad283aa400af464c76d713c07ad\",\"25f9e794323b453885f5181f1b624d0b\"]', null, null, '1', 'manh', null, null, '0');
INSERT INTO user VALUES ('5', '6', 'abcd@gmail.com', null, null, null, 'abcd@gmail.com', '25d55ad283aa400af464c76d713c07ad', null, null, null, '1486207409', null, null, '[\"25d55ad283aa400af464c76d713c07ad\"]', '1486207409', '1486207409', '2', '123', 'qBL48x', '1486293809', '0');

-- ----------------------------
-- Table structure for `user_history`
-- ----------------------------
DROP TABLE IF EXISTS `user_history`;
CREATE TABLE `user_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `roleId` int(11) NOT NULL,
  `loginDate` int(11) DEFAULT NULL,
  `createDate` int(11) DEFAULT NULL,
  `updateDate` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of user_history
-- ----------------------------
