CREATE TABLE `user_refresh_tokens` (
	`user_refresh_tokenID` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`urf_userID` INT(11) UNSIGNED NOT NULL,
	`urf_token` VARCHAR(1000) NOT NULL,
	`urf_ip` VARCHAR(50) NOT NULL,
	`urf_user_agent` VARCHAR(1000) NOT NULL,
	`urf_created` DATETIME NOT NULL COMMENT 'UTC',
	PRIMARY KEY (`user_refresh_tokenID`)
);
