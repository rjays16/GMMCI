/*
SQLyog Ultimate v11.33 (64 bit)
MySQL - 5.6.12-log : Database - mediquest
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`mediquest` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `mediquest`;

/*Table structure for table `seg_inventory_reptbl` */

DROP TABLE IF EXISTS `seg_inventory_reptbl`;

CREATE TABLE `seg_inventory_reptbl` (
  `rep_nr` int(4) unsigned NOT NULL AUTO_INCREMENT,
  `rep_name` varchar(80) NOT NULL,
  `rep_script` varchar(64) NOT NULL,
  PRIMARY KEY (`rep_nr`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `seg_inventory_reptbl` */

insert  into `seg_inventory_reptbl`(`rep_nr`,`rep_name`,`rep_script`) values (1,'Inventory list of Meds','list_meds'),(2,'List of Expiry Drugs','list_expiry'),(3,'Daily Supplies Release','daily_supply_release'),(4,'Monthly Stock Position Report of Medicines','list_positionmonthly'),(5,'Slow and Fast Moving Items','slowfast_items'),(6,'Daily Replenishment Report','daily_replenish'),(7,'Adjustment Report','adjustment'),(8,'Oxygen Utilization Report','oxygen_usage'),(11,'Inventory List of Supplies','list_supplies');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
