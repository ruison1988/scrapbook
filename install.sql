/*
SQLyog Ultimate v12.08 (64 bit)
MySQL - 5.7.26 : Database - scrapbook
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`scrapbook` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `scrapbook`;

/*Table structure for table `sb_section` */

DROP TABLE IF EXISTS `sb_section`;

CREATE TABLE `sb_section` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `book` varchar(50) NOT NULL COMMENT '书本名称',
  `index` int(10) NOT NULL COMMENT '章节索引',
  `title` varchar(200) DEFAULT NULL COMMENT '文章标题',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
