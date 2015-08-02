create database `mydb` default character set utf8 COLLATE utf8_general_ci;

use `mydb`;

CREATE TABLE IF NOT EXISTS `country` (
	`id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`country` VARCHAR(60) NOT NULL,
	UNIQUE INDEX `country_index` (`country`)
) Engine=InnoDB;

CREATE TABLE IF NOT EXISTS `city` (
	`id` INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`city` VARCHAR(100) NOT NULL,
	`country_id` INTEGER,
	FOREIGN KEY(`country_id`) REFERENCES `country`(`id`) ON DELETE SET NULL
	ON UPDATE CASCADE,
	UNIQUE INDEX `city_index` (`city`, `country_id`) 
) Engine=InnoDB;

CREATE TABLE IF NOT EXISTS `users` (
	`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`name` VARCHAR(60) NOT NULL,
	`email` CHAR(255) NOT NULL,
	`password` CHAR(32) NOT NULL,
	`birth_date` DATE DEFAULT NULL,
	`photo` TEXT DEFAULT NULL,
	`city_id` INTEGER,
	`gender` enum('male','female') NOT NULL DEFAULT 'male',
	FOREIGN KEY(`city_id`) REFERENCES `city`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
	UNIQUE INDEX `index1` (`email`)
) Engine=InnoDB;