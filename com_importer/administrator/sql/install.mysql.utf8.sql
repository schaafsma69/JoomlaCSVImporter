CREATE TABLE IF NOT EXISTS `#__importer_tasks` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`name` VARCHAR(255)  NOT NULL ,
`description` TEXT NOT NULL ,
`definition` TEXT NOT NULL ,
`last_run` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

