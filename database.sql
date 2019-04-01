/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isadmin` tinyint(4) NOT NULL DEFAULT '0',
  `username` tinytext NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  `site` int(11) NOT NULL,
  `displayname` tinytext NOT NULL,
  `disabled` tinyint(4) NOT NULL DEFAULT '0',
  KEY `index` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `configuration` (
  `companyname` tinytext,
  `logo` longblob,
  `logonmessage` text,
  `logoimagetype` tinytext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `session` (
  `token` text NOT NULL,
  `accountid` int(11) NOT NULL,
  `expire` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` tinytext NOT NULL,
  `companyname` tinytext NOT NULL,
  `address1` tinytext,
  `address2` tinytext,
  `address3` tinytext,
  `city` tinytext,
  `state` tinytext,
  `postcode` tinytext,
  `contactnumber` tinytext,
  `sitemanager` tinytext,
  `pinnumber` tinytext NOT NULL,
  `siteimage` longblob,
  `siteimagetype` tinytext,
  `sitemessage` text,
  KEY `Index` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT '0',
  `firstname` tinytext NOT NULL,
  `lastname` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `jobtitle` tinytext NOT NULL,
  `signedin` bit(1) NOT NULL DEFAULT b'0',
  `thumbnail` longblob,
  KEY `Index` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `visitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` int(11) NOT NULL DEFAULT '0',
  `firstname` tinytext NOT NULL,
  `lastname` tinytext NOT NULL,
  `title` tinytext NOT NULL,
  `company` tinytext NOT NULL,
  `vehiclereg` tinytext NOT NULL,
  `signedin` bit(1) NOT NULL DEFAULT b'0',
  KEY `Index` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
