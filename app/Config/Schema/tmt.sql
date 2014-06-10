# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.29)
# Database: themusictank
# Generation Time: 2014-06-10 20:53:19 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table album_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `album_review_snapshots`;

CREATE TABLE `album_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`album_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_ALBUM_ID` (`album_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `albums`;

CREATE TABLE `albums` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`artist_id` int(10) unsigned NOT NULL,
	`name` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
	`slug` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
	`image_src` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
	`image` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
	`release_date` int(10) unsigned DEFAULT NULL,
	`release_date_text` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
	`duration` int(6) unsigned DEFAULT NULL,
	`is_newrelease` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`notability` int(2) unsigned DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_SLUG` (`slug`),
	KEY `FK_ALBUMS_ARTISTS` (`artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table artist_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `artist_review_snapshots`;

CREATE TABLE `artist_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`artist_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`),
	KEY `UNIQUE_ARTIST_ID` (`artist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table artists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `artists`;

CREATE TABLE `artists` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL DEFAULT '',
	`slug` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	KEY `UNIQUE_SLUG` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table configs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `configs`;

CREATE TABLE `configs` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(100) NOT NULL DEFAULT '',
	`value` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table facebook_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `facebook_users`;

CREATE TABLE `facebook_users` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`facebook_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_USER_ID` (`user_id`),
	UNIQUE KEY `UNIQUE_FACEBOOK_ID` (`facebook_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lastfm_albums
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lastfm_albums`;

CREATE TABLE `lastfm_albums` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`album_id` int(11) unsigned NOT NULL,
	`mbid` varchar(36) DEFAULT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`wiki` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_ALBUM_ID` (`album_id`),
	UNIQUE KEY `UNIQUE_MBID` (`mbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lastfm_artists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lastfm_artists`;

CREATE TABLE `lastfm_artists` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`artist_id` int(11) unsigned DEFAULT NULL,
	`mbid` varchar(36) DEFAULT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`is_popular` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`image` varchar(255) DEFAULT NULL,
	`image_src` varchar(255) DEFAULT NULL,
	`url` varchar(255) DEFAULT NULL,
	`biography` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_ARTIST_ID` (`artist_id`),
	UNIQUE KEY `UNIQUE_MBID` (`mbid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table lastfm_tracks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lastfm_tracks`;

CREATE TABLE `lastfm_tracks` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`track_id` int(11) unsigned NOT NULL,
	`mbid` varchar(36) DEFAULT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`wiki` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_ALBUM_ID` (`track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table notifications
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;

CREATE TABLE `notifications` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`created` int(11) unsigned DEFAULT NULL,
	`is_viewed` tinyint(1) unsigned DEFAULT NULL,
	`title` varchar(255) NOT NULL DEFAULT '',
	`type` varchar(255) NOT NULL DEFAULT '',
	`related_model_id` int(11) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table rdio_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rdio_users`;

CREATE TABLE `rdio_users` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned DEFAULT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`key` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '',
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_USER_ID` (`user_id`),
	UNIQUE KEY `UNIQUE_KEY` (`lastsync`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table review_frames
# ------------------------------------------------------------

DROP TABLE IF EXISTS `review_frames`;

CREATE TABLE `review_frames` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`artist_id` int(11) unsigned NOT NULL,
	`album_id` int(11) unsigned NOT NULL,
	`track_id` int(11) unsigned NOT NULL,
	`user_id` int(11) unsigned NOT NULL,
	`review_id` varchar(13) NOT NULL DEFAULT '',
	`groove` float NOT NULL,
	`starpowering` int(1) unsigned NOT NULL,
	`suckpowering` int(1) unsigned NOT NULL,
	`multiplier` int(1) NOT NULL,
	`position` int(6) unsigned NOT NULL,
	`created` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `FK_REVIEW_FRAMES_ARTISTS` (`artist_id`),
	KEY `FK_REVIEW_FRAMES_ALBUMS` (`album_id`),
	KEY `FK_REVIEW_FRAMES_TRACKS` (`track_id`),
	KEY `FK_REVIEW_FRAMES_USERS` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table subscribers_album_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscribers_album_review_snapshots`;

CREATE TABLE `subscribers_album_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`album_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table subscribers_track_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscribers_track_review_snapshots`;

CREATE TABLE `subscribers_track_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`track_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table track_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `track_review_snapshots`;

CREATE TABLE `track_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`track_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_TRACK_ID` (`track_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tracks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tracks`;

CREATE TABLE `tracks` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`album_id` int(10) unsigned NOT NULL,
	`title` varchar(255) NOT NULL DEFAULT '',
	`slug` varchar(255) NOT NULL DEFAULT '',
	`track_num` int(11) unsigned DEFAULT NULL,
	`duration` int(6) unsigned DEFAULT NULL,
	`is_challenge` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`wavelength` text,
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_SLUG` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_achievements
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_achievements`;

CREATE TABLE `user_achievements` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned DEFAULT NULL,
	`achievement_id` int(11) unsigned DEFAULT NULL,
	`created` int(11) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `INDEX_USER_ID` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_activities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_activities`;

CREATE TABLE `user_activities` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`type` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
	`related_model_id` int(11) unsigned NOT NULL,
	`created` int(11) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table user_album_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_album_review_snapshots`;

CREATE TABLE `user_album_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`album_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) unsigned NOT NULL DEFAULT '0',
	`liking` int(11) unsigned NOT NULL DEFAULT '0',
	`liking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`disliking` int(11) unsigned NOT NULL DEFAULT '0',
	`disliking_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`neutral` int(11) unsigned NOT NULL DEFAULT '0',
	`neutral_pct` int(3) unsigned NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_followers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_followers`;

CREATE TABLE `user_followers` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`follower_id` int(11) unsigned NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table user_track_review_snapshots
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_track_review_snapshots`;

CREATE TABLE `user_track_review_snapshots` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(11) unsigned NOT NULL,
	`track_id` int(11) unsigned NOT NULL,
	`lastsync` int(11) unsigned DEFAULT NULL,
	`total` int(11) NOT NULL DEFAULT '0',
	`liking` int(11) NOT NULL DEFAULT '0',
	`liking_pct` int(3) NOT NULL DEFAULT '0',
	`disliking` int(11) NOT NULL DEFAULT '0',
	`disliking_pct` int(3) NOT NULL DEFAULT '0',
	`neutral` int(11) NOT NULL DEFAULT '0',
	`neutral_pct` int(3) NOT NULL DEFAULT '0',
	`curve` text,
	`ranges` text,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`firstname` varchar(255) DEFAULT NULL,
	`lastname` varchar(255) DEFAULT NULL,
	`username` varchar(255) DEFAULT NULL,
	`password` varchar(250) DEFAULT NULL,
	`role` varchar(10) DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`updated` datetime DEFAULT NULL,
	`slug` varchar(255) DEFAULT NULL,
	`image_src` varchar(255) DEFAULT NULL,
	`image` varchar(255) DEFAULT NULL,
	`location` varchar(255) DEFAULT NULL,
	`preferred_player_api` int(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE_SLUG` (`slug`),
	UNIQUE KEY `UNIQUE_USERNAME` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
