-- phpMyAdmin SQL Dump
-- version 3.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 10, 2010 at 04:41 PM
-- Server version: 5.1.47
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `osw`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of file',
  `hash` varchar(255) NOT NULL COMMENT 'hash of file - access from web only via hash',
  `user` int(10) unsigned NOT NULL COMMENT 'id of user which uploaded file',
  `real_name` text NOT NULL COMMENT 'name of file in filesystem',
  `uploaded` datetime NOT NULL COMMENT 'time of uploading',
  `expire` datetime NOT NULL COMMENT 'time of expiring',
  PRIMARY KEY (`id`,`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table with uploaded files and all needed info' AUTO_INCREMENT=12 ;

--
-- Dumping data for table `files`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id of user',
  `role` enum('master','normal') NOT NULL DEFAULT 'normal' COMMENT 'level of permissions',
  `username` varchar(255) NOT NULL COMMENT 'username used for login',
  `password` text NOT NULL COMMENT 'encrypted password in sha1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Table with users' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `password`) VALUES
(1, 'master', 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997');
