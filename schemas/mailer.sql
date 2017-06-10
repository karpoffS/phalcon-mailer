-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 18 2016 г., 17:45
-- Версия сервера: 5.6.32
-- Версия PHP: 5.6.23-1+deprecated+dontuse+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `mailer`
--
CREATE DATABASE IF NOT EXISTS `mailer` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `mailer`;

-- --------------------------------------------------------

--
-- Структура таблицы `category_messages`
--

DROP TABLE IF EXISTS `category_messages`;
CREATE TABLE IF NOT EXISTS `category_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Имя категории',
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Описание категории',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `category_messages`:
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `category_messages`
--

TRUNCATE TABLE `category_messages`;
-- --------------------------------------------------------

--
-- Структура таблицы `email_confirmations`
--

DROP TABLE IF EXISTS `email_confirmations`;
CREATE TABLE IF NOT EXISTS `email_confirmations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `code` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  `modifiedAt` int(10) unsigned DEFAULT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `email_confirmations`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `email_confirmations`
--

TRUNCATE TABLE `email_confirmations`;
-- --------------------------------------------------------

--
-- Структура таблицы `email_groups`
--

DROP TABLE IF EXISTS `email_groups`;
CREATE TABLE IF NOT EXISTS `email_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `email_groups`:
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `email_groups`
--

TRUNCATE TABLE `email_groups`;
-- --------------------------------------------------------

--
-- Структура таблицы `email_imports`
--

DROP TABLE IF EXISTS `email_imports`;
CREATE TABLE IF NOT EXISTS `email_imports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(6) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `totals` int(64) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `email_imports`:
--   `groupId`
--       `email_groups` -> `id`
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `email_imports`
--

TRUNCATE TABLE `email_imports`;
-- --------------------------------------------------------

--
-- Структура таблицы `email_list`
--

DROP TABLE IF EXISTS `email_list`;
CREATE TABLE IF NOT EXISTS `email_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `groupId` int(11) NOT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `unsubscribe` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `email_list`:
--   `groupId`
--       `email_groups` -> `id`
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `email_list`
--

TRUNCATE TABLE `email_list`;
-- --------------------------------------------------------

--
-- Структура таблицы `failed_logins`
--

DROP TABLE IF EXISTS `failed_logins`;
CREATE TABLE IF NOT EXISTS `failed_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned DEFAULT NULL,
  `ipAddress` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `attempted` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `failed_logins`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `failed_logins`
--

TRUNCATE TABLE `failed_logins`;
-- --------------------------------------------------------

--
-- Структура таблицы `invite_code`
--

DROP TABLE IF EXISTS `invite_code`;
CREATE TABLE IF NOT EXISTS `invite_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL COMMENT 'Id пользователя использовавшего код',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email приглашонного',
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'код приглашения',
  `createAt` int(11) NOT NULL,
  `modifyAt` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT 'статус кода',
  PRIMARY KEY (`id`),
  KEY `code_idx` (`code`),
  KEY `email_idx` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `invite_code`:
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `invite_code`
--

TRUNCATE TABLE `invite_code`;
-- --------------------------------------------------------

--
-- Структура таблицы `messages_templates`
--

DROP TABLE IF EXISTS `messages_templates`;
CREATE TABLE IF NOT EXISTS `messages_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` int(11) NOT NULL,
  `modifyAt` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `messages_templates`:
--   `categoryId`
--       `category_messages` -> `id`
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `messages_templates`
--

TRUNCATE TABLE `messages_templates`;
-- --------------------------------------------------------

--
-- Структура таблицы `password_changes`
--

DROP TABLE IF EXISTS `password_changes`;
CREATE TABLE IF NOT EXISTS `password_changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `ipAddress` char(15) COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` text COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `password_changes`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `password_changes`
--

TRUNCATE TABLE `password_changes`;
-- --------------------------------------------------------

--
-- Структура таблицы `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profilesId` int(10) unsigned NOT NULL,
  `resource` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profilesId` (`profilesId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=113 ;

--
-- СВЯЗИ ТАБЛИЦЫ `permissions`:
--   `profilesId`
--       `profiles` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Дамп данных таблицы `permissions`
--

INSERT IGNORE INTO `permissions` (`id`, `profilesId`, `resource`, `action`) VALUES
(1, 1, 'users', 'index'),
(2, 1, 'users', 'search'),
(3, 1, 'users', 'edit'),
(4, 1, 'users', 'create'),
(5, 1, 'users', 'delete'),
(6, 1, 'users', 'changePassword'),
(7, 1, 'profiles', 'index'),
(8, 1, 'profiles', 'search'),
(9, 1, 'profiles', 'edit'),
(10, 1, 'profiles', 'create'),
(11, 1, 'profiles', 'delete'),
(12, 1, 'permissions', 'index'),
(13, 2, 'importings', 'parse'),
(14, 2, 'todo', 'index'),
(15, 2, 'profile', 'index'),
(16, 2, 'categories', 'edit'),
(17, 2, 'categories', 'index'),
(18, 2, 'categories', 'create'),
(19, 2, 'messages', 'delete'),
(20, 2, 'messages', 'edit'),
(21, 2, 'messages', 'create'),
(22, 2, 'messages', 'index'),
(23, 2, 'mailings', 'delete'),
(24, 2, 'mailings', 'command'),
(25, 2, 'mailings', 'index'),
(26, 2, 'importings', 'pause'),
(27, 2, 'importings', 'delete'),
(28, 2, 'importings', 'create'),
(29, 2, 'importings', 'index'),
(30, 2, 'groups', 'delete'),
(31, 2, 'groups', 'edit'),
(32, 2, 'groups', 'create'),
(33, 2, 'groups', 'index'),
(34, 2, 'queuing', 'list'),
(35, 2, 'queuing', 'play'),
(36, 2, 'queuing', 'delete'),
(37, 2, 'queuing', 'create'),
(38, 2, 'queuing', 'index'),
(39, 2, 'permissions', 'index'),
(40, 2, 'profiles', 'delete'),
(41, 2, 'profiles', 'create'),
(42, 2, 'profiles', 'edit'),
(43, 2, 'profiles', 'search'),
(44, 2, 'profiles', 'index'),
(45, 2, 'users', 'changePassword'),
(46, 2, 'users', 'delete'),
(47, 2, 'users', 'create'),
(48, 2, 'users', 'edit'),
(49, 2, 'users', 'search'),
(50, 2, 'users', 'index'),
(51, 2, 'invitings', 'index'),
(52, 2, 'invitings', 'create'),
(53, 2, 'invitings', 'mailing'),
(54, 2, 'invitings', 'delete'),
(55, 3, 'messages', 'delete'),
(56, 3, 'categories', 'index'),
(57, 3, 'categories', 'create'),
(58, 3, 'messages', 'edit'),
(59, 3, 'messages', 'create'),
(60, 3, 'messages', 'index'),
(61, 3, 'mailings', 'delete'),
(62, 3, 'mailings', 'command'),
(63, 3, 'mailings', 'index'),
(64, 3, 'importings', 'parse'),
(65, 3, 'importings', 'pause'),
(66, 3, 'importings', 'delete'),
(67, 3, 'importings', 'create'),
(68, 3, 'importings', 'index'),
(69, 3, 'groups', 'delete'),
(70, 3, 'groups', 'edit'),
(71, 3, 'groups', 'create'),
(72, 3, 'groups', 'index'),
(73, 3, 'queuing', 'list'),
(74, 3, 'queuing', 'play'),
(75, 3, 'queuing', 'delete'),
(76, 3, 'queuing', 'create'),
(77, 3, 'queuing', 'index'),
(78, 3, 'users', 'changePassword'),
(79, 3, 'users', 'create'),
(80, 3, 'users', 'edit'),
(81, 3, 'users', 'search'),
(82, 3, 'users', 'index'),
(83, 3, 'categories', 'edit'),
(84, 3, 'categories', 'delete'),
(85, 3, 'profile', 'index'),
(86, 3, 'todo', 'index'),
(87, 3, 'invitings', 'index'),
(88, 4, 'queuing', 'index'),
(89, 4, 'queuing', 'create'),
(90, 4, 'queuing', 'delete'),
(91, 4, 'queuing', 'play'),
(92, 4, 'queuing', 'list'),
(93, 4, 'groups', 'index'),
(94, 4, 'groups', 'create'),
(95, 4, 'groups', 'edit'),
(96, 4, 'groups', 'delete'),
(97, 4, 'importings', 'index'),
(98, 4, 'importings', 'create'),
(99, 4, 'importings', 'delete'),
(100, 4, 'importings', 'pause'),
(101, 4, 'importings', 'parse'),
(102, 4, 'mailings', 'index'),
(103, 4, 'mailings', 'command'),
(104, 4, 'mailings', 'delete'),
(105, 4, 'messages', 'index'),
(106, 4, 'messages', 'create'),
(107, 4, 'messages', 'edit'),
(108, 4, 'messages', 'delete'),
(109, 4, 'categories', 'index'),
(110, 4, 'categories', 'edit'),
(111, 4, 'categories', 'delete'),
(112, 4, 'profile', 'index');

-- --------------------------------------------------------

--
-- Структура таблицы `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `hiding` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Очистить таблицу перед добавлением данных `profiles`
--

TRUNCATE TABLE `profiles`;
--
-- Дамп данных таблицы `profiles`
--

INSERT IGNORE INTO `profiles` (`id`, `name`, `hiding`, `active`) VALUES
(1, 'System', 1, 1),
(2, 'Administrators', 0, 1),
(3, 'Moderators', 0, 1),
(4, 'Users', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `queue_list`
--

DROP TABLE IF EXISTS `queue_list`;
CREATE TABLE IF NOT EXISTS `queue_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jobId` int(64) NOT NULL DEFAULT '0' COMMENT 'Beanstalk id задания в очереди',
  `workerPid` int(11) NOT NULL DEFAULT '0' COMMENT 'pid процесса',
  `queueId` int(11) NOT NULL COMMENT 'Id очереди',
  `userId` int(11) NOT NULL COMMENT 'Пользователь',
  `messageId` int(11) NOT NULL COMMENT 'Сообщение',
  `categoryId` int(11) NOT NULL COMMENT 'Категория письма',
  `groupId` int(11) NOT NULL COMMENT 'Группа рассылки',
  `emailId` int(11) NOT NULL COMMENT 'Id email адреса',
  `createAt` int(64) NOT NULL COMMENT 'Дата создания очереди',
  `modifyAt` int(64) NOT NULL COMMENT 'дата отправки',
  `attempts` tinyint(1) NOT NULL COMMENT 'кол-во попыток',
  `errors` text COLLATE utf8_unicode_ci COMMENT 'Ошибки отправки',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - свободно, 1 - заблокирован',
  `status` int(1) NOT NULL COMMENT '0 - в очереди, 1 - отправлено, 2 - снято с очереди',
  PRIMARY KEY (`id`),
  KEY `jobId` (`jobId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `queue_list`:
--   `categoryId`
--       `category_messages` -> `id`
--   `emailId`
--       `email_list` -> `id`
--   `groupId`
--       `email_groups` -> `id`
--   `messageId`
--       `messages_templates` -> `id`
--   `queueId`
--       `queuing` -> `id`
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `queue_list`
--

TRUNCATE TABLE `queue_list`;
-- --------------------------------------------------------

--
-- Структура таблицы `queuing`
--

DROP TABLE IF EXISTS `queuing`;
CREATE TABLE IF NOT EXISTS `queuing` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id очереди',
  `userId` int(11) NOT NULL COMMENT 'Пользователь',
  `categoryId` int(11) NOT NULL COMMENT 'Категория рассылки',
  `messageId` int(11) NOT NULL COMMENT 'Сообщение',
  `groupId` int(11) NOT NULL COMMENT 'Группа адресов',
  `current` int(11) NOT NULL COMMENT 'Кол-во выполненых заданий',
  `totals` int(11) NOT NULL COMMENT 'Всего заданий',
  `status` tinyint(1) NOT NULL COMMENT '0 - приостановлена, 1 - выполняется, 2 - выполнена',
  PRIMARY KEY (`id`),
  KEY `userid` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Очередь рассылки' AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `queuing`:
--   `categoryId`
--       `category_messages` -> `id`
--   `groupId`
--       `email_groups` -> `id`
--   `messageId`
--       `messages_templates` -> `id`
--   `userId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `queuing`
--

TRUNCATE TABLE `queuing`;
-- --------------------------------------------------------

--
-- Структура таблицы `remember_tokens`
--

DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `token` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` text COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `remember_tokens`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `remember_tokens`
--

TRUNCATE TABLE `remember_tokens`;
-- --------------------------------------------------------

--
-- Структура таблицы `reset_passwords`
--

DROP TABLE IF EXISTS `reset_passwords`;
CREATE TABLE IF NOT EXISTS `reset_passwords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `code` varchar(48) COLLATE utf8_unicode_ci NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  `modifiedAt` int(10) unsigned DEFAULT NULL,
  `reset` char(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `reset_passwords`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `reset_passwords`
--

TRUNCATE TABLE `reset_passwords`;
-- --------------------------------------------------------

--
-- Структура таблицы `success_logins`
--

DROP TABLE IF EXISTS `success_logins`;
CREATE TABLE IF NOT EXISTS `success_logins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `ipAddress` char(15) COLLATE utf8_unicode_ci NOT NULL,
  `userAgent` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- СВЯЗИ ТАБЛИЦЫ `success_logins`:
--   `usersId`
--       `users` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `success_logins`
--

TRUNCATE TABLE `success_logins`;
-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(60) COLLATE utf8_unicode_ci NOT NULL,
  `mustChangePassword` tinyint(1) NOT NULL DEFAULT '0',
  `profilesId` int(10) unsigned NOT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `profilesId` (`profilesId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- СВЯЗИ ТАБЛИЦЫ `users`:
--   `profilesId`
--       `profiles` -> `id`
--

--
-- Очистить таблицу перед добавлением данных `users`
--

TRUNCATE TABLE `users`;
--
-- Дамп данных таблицы `users`
--

INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `mustChangePassword`, `profilesId`, `banned`, `suspended`, `active`) VALUES
(1, 'System', 'system@example.com', '54b53072540eeeb8f8e9343e71f28176', 0, 1, 0, 0, 1),
(2, 'Карпов Сергей Михайлович', 'karpoff.sm@yandex.ru', '54b53072540eeeb8f8e9343e71f28176', 0, 2, 0, 0, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
