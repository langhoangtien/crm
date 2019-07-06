/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50721
Source Host           : localhost:3306
Source Database       : vcbs

Target Server Type    : MYSQL
Target Server Version : 50721
File Encoding         : 65001

Date: 2019-02-12 15:18:54
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for phppos_kpi_rate
-- ----------------------------
DROP TABLE IF EXISTS `phppos_kpi_rate`;
CREATE TABLE `phppos_kpi_rate` (
  `rate_start` varchar(25) DEFAULT NULL,
  `rate_end` varchar(25) DEFAULT NULL,
  `point` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of phppos_kpi_rate
-- ----------------------------
INSERT INTO `phppos_kpi_rate` VALUES ('0', '70', '60');
INSERT INTO `phppos_kpi_rate` VALUES ('70', '90', '80');
INSERT INTO `phppos_kpi_rate` VALUES ('90', '110', '100');
INSERT INTO `phppos_kpi_rate` VALUES ('110', '130', '120');
INSERT INTO `phppos_kpi_rate` VALUES ('130', '10000', '140');
INSERT INTO `phppos_kpi_rate` VALUES ('120', '10000000', '100');


-- ----------------------------
DROP TABLE IF EXISTS `phppos_task_log_notice`;
CREATE TABLE `phppos_task_log_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) DEFAULT NULL,
  `joins` varchar(200) DEFAULT NULL,
  `implements` varchar(200) DEFAULT NULL,
  `approved` varchar(200) DEFAULT NULL,
  `seens` varchar(200) DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `person_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;