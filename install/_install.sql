----------------------------------------------------------
--Win64 10.2.7-MariaDB - mariadb.org binary distribution--
----------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- create chall
CREATE DATABASE IF NOT EXISTS `chall` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `chall`;

-- create three tables.
CREATE TABLE IF NOT EXISTS `chal` (
  `challenge_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `challenge_name` varchar(100) DEFAULT NULL,
  `challenge_desc` text DEFAULT NULL,
  `challenge_score` mediumint(9) DEFAULT NULL,
  `challenge_flag` varchar(255) DEFAULT NULL,
  `challenge_rate` float NOT NULL DEFAULT 0,
  `challenge_solve_count` mediumint(9) DEFAULT 0,
  `challenge_is_open` tinyint(1) DEFAULT 0,
  `challenge_by` varchar(255) DEFAULT 'stypr',
  PRIMARY KEY (`challenge_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `log` (
  `log_no` int(255) NOT NULL AUTO_INCREMENT,
  `log_id` varchar(100) DEFAULT NULL,
  `log_challenge` varchar(255) DEFAULT NULL,
  `log_type` varchar(64) DEFAULT NULL,
  `log_date` datetime DEFAULT NULL,
  `log_info` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`log_no`)
) ENGINE=MyISAM AUTO_INCREMENT=554 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user` (
  `user_no` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL,
  `user_pw` varchar(40) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `user_score` int(10) unsigned NOT NULL DEFAULT 0,
  `user_join_date` datetime NOT NULL,
  `user_auth_date` datetime DEFAULT NULL,
  `user_join_ip` varchar(15) NOT NULL,
  `user_auth_ip` varchar(15) DEFAULT NULL,
  `user_last_solved` datetime DEFAULT NULL,
  `user_comment` varchar(255) DEFAULT NULL,
  `user_permission` tinyint(1) unsigned zerofill NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_no`)
) ENGINE=MyISAM AUTO_INCREMENT=1550 DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
