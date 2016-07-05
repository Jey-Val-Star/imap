-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--
-- ����: 127.0.0.1:3306
-- ����� ��������: ��� 05 2016 �., 10:40
-- ������ �������: 5.5.41-log
-- ������ PHP: 5.4.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- ���� ������: `task_imap`
--

-- --------------------------------------------------------

--
-- ��������� ������� `mails`
--

CREATE TABLE IF NOT EXISTS `mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) DEFAULT NULL,
  `date_mail` varchar(255) NOT NULL DEFAULT '0',
  `message` text,
  `from_mail` varchar(255) NOT NULL DEFAULT '0',
  `dmarc` varchar(255) NOT NULL DEFAULT '0',
  `folder` varchar(255) NOT NULL DEFAULT '1',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '1-mail delete',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2165 ;

-- --------------------------------------------------------

--
-- ��������� ������� `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL DEFAULT '0',
  `password` varchar(255) NOT NULL DEFAULT '0',
  `salt` varchar(255) NOT NULL DEFAULT '0',
  `gmail_login` varchar(255) NOT NULL DEFAULT '0',
  `gmail_pass` varchar(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;
