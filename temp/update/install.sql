ALTER TABLE `settings` CHANGE `author` `website_author` VARCHAR(50) DEFAULT NULL;
ALTER TABLE `settings` CHANGE `keywords` `website_keywords` VARCHAR(250) DEFAULT NULL;
ALTER TABLE `settings` CHANGE `sender_emailaddress` `emailaddress` VARCHAR(100) DEFAULT NULL;

DROP `visitors`;

UPDATE `permissions` SET `permission_key` = 'manage_themes' WHERE `permissions`.`permission_id` = 4;