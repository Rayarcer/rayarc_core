-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 11, 2019 at 02:35 PM
-- Server version: 5.5.21
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `rayarcca_demo`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`rayarcca_root`@`localhost` PROCEDURE `sp_populate`()
BEGIN

            DECLARE iUserId INTEGER (11) UNSIGNED;
            DECLARE iLastXp INTEGER (11) UNSIGNED;
            DECLARE iLastRank INTEGER (11) UNSIGNED;
            DECLARE iInitXp INTEGER (11) UNSIGNED;
            DECLARE iInitRank INTEGER (11) UNSIGNED;
            DECLARE iDone INTEGER (11) UNSIGNED;
            
            DECLARE iVal01 INTEGER (11) UNSIGNED;
            DECLARE iVal02 INTEGER (11) UNSIGNED;

            -- this cursor returns all user ids that do not have a valid entry for their profile foreign key.
            DECLARE cUserIterator CURSOR FOR
                SELECT userId FROM `User`;

            DECLARE CONTINUE HANDLER FOR NOT FOUND SET iDone = 1;

             -- Step 01: Creating empty profile sets for users that do not yet have a profile
            SET iDone = 0;
            OPEN cUserIterator;

            lUserIterator: LOOP
                FETCH cUserIterator INTO iUserId;
                IF 1 = iDone THEN
                    LEAVE lUserIterator;
                END IF;

                SELECT rank, xp INTO iLastRank, iLastXp FROM UserTotalStat WHERE updateDate = '2010-01-02' AND userId = iUserId;
                SELECT rank, xp INTO iInitRank, iInitXp FROM UserTotalStat WHERE updateDate = '2010-01-01' AND userId = iUserId;

                SET iVal01 = ABS(iLastRank - iInitRank);
                SET iVal02 = ABS(iLastXp - iInitXp);
                IF iVal02 IS NOT NULL THEN
                    INSERT INTO `UserWeekStat` (`userId`, `rank` , `xp`) VALUES (iUserId, iVal01, iVal02);
                END IF;

            END LOOP lUserIterator;

            CLOSE cUserIterator;

        END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `email` varchar(128) NOT NULL,
  `comment` text NOT NULL,
  `object_id` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_parent` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `event_key` varchar(8) NOT NULL,
  `event_name` varchar(64) NOT NULL,
  `event_short_name` varchar(32) DEFAULT NULL,
  `event_desc` varchar(256) DEFAULT NULL,
  `event_image_path` varchar(255) DEFAULT NULL,
  `event_tn_path` varchar(255) DEFAULT NULL,
  `event_itinerary` varchar(256) DEFAULT NULL,
  `event_details` varchar(256) DEFAULT NULL,
  `event_start_datetime` datetime NOT NULL,
  `event_end_datetime` datetime DEFAULT NULL,
  `event_reoccurring` tinyint(1) DEFAULT NULL,
  `event_every` varchar(16) DEFAULT NULL,
  `starting_on` date DEFAULT NULL,
  `event_coordinator` varchar(64) DEFAULT NULL,
  `event_performances_by` varchar(512) DEFAULT NULL,
  `event_hosted_by` varchar(256) DEFAULT NULL,
  `event_in_support_of` varchar(64) DEFAULT NULL,
  `event_contact_email` varchar(128) DEFAULT NULL,
  `event_contact_number1` varchar(32) DEFAULT NULL,
  `event_contact_number2` varchar(32) DEFAULT NULL,
  `venue_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `venue_id` (`venue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_item`
--

CREATE TABLE IF NOT EXISTS `event_item` (
  `event_id` smallint(5) unsigned NOT NULL,
  `item_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `event_ticket_holder`
--

CREATE TABLE IF NOT EXISTS `event_ticket_holder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(8) NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `no_of_tickets` tinyint(255) NOT NULL,
  `address` varchar(128) NOT NULL,
  `city` varchar(32) NOT NULL,
  `contact_number` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid` tinyint(1) DEFAULT '0',
  `event_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_ticket_info`
--

CREATE TABLE IF NOT EXISTS `event_ticket_info` (
  `event_id` smallint(5) unsigned NOT NULL,
  `ticket_price_door` decimal(10,2) NOT NULL,
  `ticket_price_advance` decimal(10,2) DEFAULT NULL,
  `ticket_price_early` decimal(10,2) DEFAULT NULL,
  `ticket_early_enddate` datetime NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE IF NOT EXISTS `genre` (
  `genre_id` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE IF NOT EXISTS `item` (
  `item_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `item_key` varchar(8) NOT NULL,
  `item_title` varchar(128) DEFAULT NULL,
  `item_desc` varchar(256) DEFAULT NULL,
  `item_long_desc` text,
  `item_image` varchar(255) DEFAULT NULL,
  `item_duration` smallint(5) unsigned NOT NULL DEFAULT '0',
  `item_content_source` varchar(255) DEFAULT NULL,
  `item_download_source` varchar(255) DEFAULT NULL,
  `item_status` varchar(16) DEFAULT NULL,
  `item_access` char(1) DEFAULT NULL,
  `item_on_feature` tinyint(1) DEFAULT '0',
  `item_downloadable` tinyint(1) DEFAULT NULL,
  `item_date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `item_expires` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `item_parent_id` smallint(5) unsigned DEFAULT NULL,
  `profile_id` smallint(5) unsigned NOT NULL,
  `item_category_id` tinyint(1) unsigned DEFAULT NULL,
  `item_content_type_id` char(1) DEFAULT NULL,
  `item_content_provider_id` char(2) DEFAULT 'ry',
  `item_tag` varchar(256) DEFAULT NULL,
  `points` bigint(20) DEFAULT '0',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_key` (`item_key`),
  UNIQUE KEY `item_title` (`item_title`,`item_date_added`,`profile_id`),
  KEY `item_content_type_id` (`item_content_type_id`),
  KEY `profile_id` (`profile_id`),
  KEY `item_content_provider_id` (`item_content_provider_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=104 ;

-- --------------------------------------------------------

--
-- Table structure for table `item_child`
--

CREATE TABLE IF NOT EXISTS `item_child` (
  `item_child_id` smallint(5) unsigned NOT NULL,
  `item_id` smallint(5) unsigned NOT NULL,
  `sequence_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`item_child_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_content_provider`
--

CREATE TABLE IF NOT EXISTS `item_content_provider` (
  `provider_id` char(2) NOT NULL,
  `provider_name` varchar(64) NOT NULL,
  PRIMARY KEY (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_content_type`
--

CREATE TABLE IF NOT EXISTS `item_content_type` (
  `item_content_type_id` char(1) NOT NULL DEFAULT '',
  `item_content_type_name` varchar(32) NOT NULL,
  PRIMARY KEY (`item_content_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_genre`
--

CREATE TABLE IF NOT EXISTS `item_genre` (
  `item_id` smallint(5) unsigned NOT NULL,
  `genre_id` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`item_id`,`genre_id`),
  KEY `genre_id` (`genre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `item_stats`
--

CREATE TABLE IF NOT EXISTS `item_stats` (
  `stat_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `processing_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avg_item_sum_like_value` mediumint(8) unsigned NOT NULL,
  `max_item_sum_like_value` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_by_ip_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_by_ip_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_by_session_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_by_session_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_start_all_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_start_all_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_by_ip_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_by_ip_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_by_session_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_by_session_count` mediumint(8) unsigned NOT NULL,
  `avg_item_view_end_all_count` mediumint(8) unsigned NOT NULL,
  `max_item_view_end_all_count` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `list_item`
--

CREATE TABLE IF NOT EXISTS `list_item` (
  `list_id` smallint(5) unsigned DEFAULT '0',
  `list_type_id` tinyint(3) unsigned NOT NULL,
  `item_id` smallint(5) unsigned NOT NULL,
  `list_item_date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `list_id` (`list_id`,`list_type_id`,`item_id`),
  KEY `list_type_id` (`list_type_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `member_item_access`
--

CREATE TABLE IF NOT EXISTS `member_item_access` (
  `access_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(5) unsigned NOT NULL,
  `access_type` char(1) DEFAULT NULL,
  `access_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` varchar(64) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL,
  `session_member_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`access_id`),
  KEY `item_id` (`item_id`),
  KEY `session_member_id` (`session_member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=172 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_item_like`
--

CREATE TABLE IF NOT EXISTS `member_item_like` (
  `like_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` smallint(5) unsigned NOT NULL,
  `like_value` tinyint(4) NOT NULL,
  `like_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_id` varchar(64) DEFAULT NULL,
  `ip_address` varchar(32) DEFAULT NULL,
  `session_member_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`like_id`),
  KEY `item_id` (`item_id`),
  KEY `session_member_id` (`session_member_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `object`
--

CREATE TABLE IF NOT EXISTS `object` (
  `object_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_type` varchar(32) NOT NULL,
  `object_key` int(11) unsigned NOT NULL,
  PRIMARY KEY (`object_id`),
  UNIQUE KEY `object_type` (`object_type`,`object_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `subscribable`
--

CREATE TABLE IF NOT EXISTS `subscribable` (
  `subscribable_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `tag` varchar(128) NOT NULL,
  PRIMARY KEY (`subscribable_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscriber`
--

CREATE TABLE IF NOT EXISTS `subscriber` (
  `subscriber_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(128) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `status` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `firstname` varchar(32) DEFAULT NULL,
  `lastname` varchar(32) DEFAULT NULL,
  `member_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`subscriber_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE IF NOT EXISTS `subscription` (
  `subscription_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(64) NOT NULL,
  `subscribable_id` smallint(5) unsigned NOT NULL,
  `subscriber_id` smallint(5) unsigned NOT NULL,
  `status` char(1) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`subscription_id`),
  UNIQUE KEY `unique_id` (`unique_id`),
  UNIQUE KEY `sub_ids` (`subscribable_id`,`subscriber_id`),
  KEY `subscriber_id` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `voting_session`
--

CREATE TABLE IF NOT EXISTS `voting_session` (
  `vs_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `start_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_datetime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_item_quota` smallint(6) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`vs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `vs_item`
--

CREATE TABLE IF NOT EXISTS `vs_item` (
  `vs_id` mediumint(8) unsigned NOT NULL,
  `item_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`vs_id`,`item_id`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`object_id`) REFERENCES `object` (`object_id`),
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`object_id`) REFERENCES `object` (`object_id`);

--
-- Constraints for table `event_item`
--
ALTER TABLE `event_item`
  ADD CONSTRAINT `event_item_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `event_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `event_item_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`),
  ADD CONSTRAINT `event_item_ibfk_4` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

--
-- Constraints for table `genre`
--
ALTER TABLE `genre`
  ADD CONSTRAINT `genre_ibfk_1` FOREIGN KEY (`genre_id`) REFERENCES `rayarcca_admin`.`genre` (`genre_id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`item_content_provider_id`) REFERENCES `item_content_provider` (`provider_id`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`item_content_provider_id`) REFERENCES `item_content_provider` (`provider_id`);

--
-- Constraints for table `item_child`
--
ALTER TABLE `item_child`
  ADD CONSTRAINT `item_child_ibfk_1` FOREIGN KEY (`item_child_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `item_child_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

--
-- Constraints for table `item_genre`
--
ALTER TABLE `item_genre`
  ADD CONSTRAINT `item_genre_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `item_genre_ibfk_2` FOREIGN KEY (`genre_id`) REFERENCES `rayarcca_admin`.`genre` (`genre_id`),
  ADD CONSTRAINT `item_genre_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `item_genre_ibfk_4` FOREIGN KEY (`genre_id`) REFERENCES `rayarcca_admin`.`genre` (`genre_id`);

--
-- Constraints for table `list_item`
--
ALTER TABLE `list_item`
  ADD CONSTRAINT `list_item_ibfk_1` FOREIGN KEY (`list_type_id`) REFERENCES `rayarcca_admin`.`list_type` (`list_type_id`),
  ADD CONSTRAINT `list_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `list_item_ibfk_3` FOREIGN KEY (`list_type_id`) REFERENCES `rayarcca_admin`.`list_type` (`list_type_id`),
  ADD CONSTRAINT `list_item_ibfk_4` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

--
-- Constraints for table `vs_item`
--
ALTER TABLE `vs_item`
  ADD CONSTRAINT `vs_item_ibfk_1` FOREIGN KEY (`vs_id`) REFERENCES `voting_session` (`vs_id`),
  ADD CONSTRAINT `vs_item_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`),
  ADD CONSTRAINT `vs_item_ibfk_3` FOREIGN KEY (`vs_id`) REFERENCES `voting_session` (`vs_id`),
  ADD CONSTRAINT `vs_item_ibfk_4` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
