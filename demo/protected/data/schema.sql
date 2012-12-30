/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
DROP TABLE IF EXISTS `authassignment`;
CREATE TABLE IF NOT EXISTS `authassignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`itemname`,`userid`),
  CONSTRAINT `authassignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `authassignment` DISABLE KEYS */;
INSERT INTO `authassignment` (`itemname`, `userid`, `bizrule`, `data`) VALUES
	('authenticated', '1', NULL, 'N;'),
	('post.*', '1', NULL, 'N;'),
	('postAdmin', '1', NULL, 'N;');
/*!40000 ALTER TABLE `authassignment` ENABLE KEYS */;
DROP TABLE IF EXISTS `authitem`;
CREATE TABLE IF NOT EXISTS `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `authitem` DISABLE KEYS */;
INSERT INTO `authitem` (`name`, `type`, `description`, `bizrule`, `data`) VALUES
	('authenticated', 2, 'Authenticated', NULL, 'N;'),
	('comment.*', 0, 'Comment wildcard', NULL, 'N;'),
	('comment.create', 0, 'Create comments', NULL, 'N;'),
	('comment.index', 0, 'List comments', NULL, 'N;'),
	('comment.update', 0, 'Update comments', NULL, 'N;'),
	('commentAdmin', 1, 'Administer comments', NULL, 'N;'),
	('post.*', 0, 'Post wildcard', NULL, 'N;'),
	('post.admin', 0, 'Manage posts', NULL, 'N;'),
	('post.create', 0, 'Create posts', NULL, 'N;'),
	('post.delete', 0, 'Delete posts', NULL, 'N;'),
	('post.index', 0, 'List posts', NULL, 'N;'),
	('post.update', 0, 'Update posts', NULL, 'N;'),
	('post.view', 0, 'View posts', NULL, 'N;'),
	('postAdmin', 1, 'Administer posts', NULL, 'N;');
/*!40000 ALTER TABLE `authitem` ENABLE KEYS */;
DROP TABLE IF EXISTS `authitemchild`;
CREATE TABLE IF NOT EXISTS `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `authitemchild` DISABLE KEYS */;
INSERT INTO `authitemchild` (`parent`, `child`) VALUES
	('commentAdmin', 'comment.create'),
	('commentAdmin', 'comment.index'),
	('commentAdmin', 'comment.update'),
	('authenticated', 'commentAdmin'),
	('postAdmin', 'post.create'),
	('postAdmin', 'post.delete'),
	('postAdmin', 'post.index'),
	('postAdmin', 'post.update'),
	('postAdmin', 'post.view'),
	('authenticated', 'postAdmin');
/*!40000 ALTER TABLE `authitemchild` ENABLE KEYS */;
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `name`) VALUES
	(1, 'demo');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;