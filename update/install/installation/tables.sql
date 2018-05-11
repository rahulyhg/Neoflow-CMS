SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `blocks`;
CREATE TABLE IF NOT EXISTS `blocks` (
  `block_id` int(11) NOT NULL AUTO_INCREMENT,
  `block_key` varchar(50) DEFAULT '',
  `title` varchar(50) DEFAULT '',
  PRIMARY KEY (`block_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `flag_code` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `folder_name` varchar(50) NOT NULL,
  `identifier` varchar(50) NOT NULL,
  `backend_route` varchar(50) DEFAULT NULL,
  `frontend_route` varchar(50) DEFAULT NULL,
  `manager_class` varchar(100) DEFAULT NULL,
  `author` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `copyright` varchar(100) DEFAULT NULL,
  `license` varchar(100) DEFAULT NULL,
  `version` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `type` enum('page','library','tool') NOT NULL,
  `dependencies` varchar(200) NOT NULL,
  `is_core` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_code`;
CREATE TABLE IF NOT EXISTS `mod_code` (
  `code_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text DEFAULT NULL,
  `section_id` int(11) NOT NULL,
  PRIMARY KEY (`code_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_search_settings`;
CREATE TABLE `mod_search_settings` (
  `setting_id` INT NOT NULL AUTO_INCREMENT,
  `url_path` VARCHAR(200) NOT NULL DEFAULT "/search" ,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_search_entities`;
CREATE TABLE `mod_search_entities` (
  `entity_id` INT NOT NULL AUTO_INCREMENT,
  `entity_class` VARCHAR(255) NOT NULL,
PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_sitemap_settings`;
CREATE TABLE IF NOT EXISTS `mod_sitemap_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `default_changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'monthly',
  `default_priority` varchar(5) NOT NULL DEFAULT '0.5',
  `sitemap_lifetime` int(11) NOT NULL DEFAULT 72,
  `automated_creation` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_sitemap_urls`;
CREATE TABLE IF NOT EXISTS `mod_sitemap_urls` (
  `url_id` int(11) NOT NULL AUTO_INCREMENT,
  `loc` varchar(255) NOT NULL,
  `lastmod` varchar(20) NOT NULL,
  `changefreq` enum('always','hourly','daily','weekly','monthly','yearly','never') NOT NULL DEFAULT 'monthly',
  `priority` varchar(5) NOT NULL,
  PRIMARY KEY (`url_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_snippets`;
CREATE TABLE IF NOT EXISTS `mod_snippets` (
  `snippet_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `code` text NOT NULL,
  `placeholder` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parameters` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`snippet_id`) USING BTREE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `mod_wysiwyg`;
CREATE TABLE IF NOT EXISTS `mod_wysiwyg` (
  `wysiwyg_id` int(11) NOT NULL AUTO_INCREMENT,
  `content` longtext DEFAULT NULL,
  `section_id` int(11) NOT NULL,
  PRIMARY KEY (`wysiwyg_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `navigations`;
CREATE TABLE IF NOT EXISTS `navigations` (
  `navigation_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `navigation_key` varchar(50) DEFAULT '',
  PRIMARY KEY (`navigation_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `navitems`;
CREATE TABLE IF NOT EXISTS `navitems` (
  `navitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) DEFAULT NULL,
  `page_id` int(11) DEFAULT NULL,
  `parent_navitem_id` int(11) DEFAULT NULL,
  `navigation_id` int(11) DEFAULT NULL,
  `language_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`navitem_id`),
  KEY `fk_navitems_page_id_idx` (`page_id`),
  KEY `fk_navitems_navitem_id_idx` (`parent_navitem_id`),
  KEY `fk_navitems_navigation_id_idx` (`navigation_id`),
  KEY `fk_navitems_language_id_idx` (`language_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `pages`;
CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `keywords` varchar(255) DEFAULT '',
  `language_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `has_custom_slug` tinyint(1) NOT NULL DEFAULT 0,
  `only_logged_in_users` tinyint(1) DEFAULT 0,
  `url` text DEFAULT NULL,
  `is_startpage` tinyint(1) NOT NULL DEFAULT 0,
  `author_user_id` int(11) DEFAULT NULL,
  `modified_when` int(11) DEFAULT NULL,
  `created_when` int(11) DEFAULT NULL,
  PRIMARY KEY (`page_id`),
  KEY `fk_pages_language_id_idx` (`language_id`),
  KEY `fk_pages_user_id_idx` (`author_user_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `pages_roles`;
CREATE TABLE IF NOT EXISTS `pages_roles` (
  `page_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`page_role_id`),
  KEY `fk_pages_users_page_id_idx` (`page_id`),
  KEY `fk_pages_roles_role_id_idx` (`role_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_key` varchar(50) NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`permission_id`),
  UNIQUE KEY `tag` (`permission_key`),
  UNIQUE KEY `title_UNIQUE` (`title`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  `description` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `roles_permissions`;
CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `role_permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_permission_id`),
  KEY `fk_roles_permissions_role_id_idx` (`role_id`),
  KEY `fk_roles_permissions_permission_id_idx` (`permission_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `block_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`section_id`),
  KEY `fk_page_id_idx` (`page_id`),
  KEY `fk_module_id_idx` (`module_id`),
  KEY `fk_sections_block_id_idx` (`block_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `website_title` varchar(50) DEFAULT NULL,
  `website_description` varchar(150) DEFAULT NULL,
  `website_keywords` varchar(255) DEFAULT NULL,
  `website_author` varchar(50) DEFAULT NULL,
  `backend_theme_id` int(11) NOT NULL,
  `default_language_id` int(11) NOT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 10,
  `session_lifetime` int(11) NOT NULL DEFAULT 1800,
  `show_debugbar` tinyint(1) NOT NULL DEFAULT 0,
  `emailaddress` varchar(100) NOT NULL,
  `session_name` varchar(50) NOT NULL,
  `allowed_file_extensions` varchar(250) NOT NULL,
  `show_error_details` tinyint(1) NOT NULL DEFAULT 0,
  `theme_id` int(11) NOT NULL,
  `custom_js` text NOT NULL,
  `show_custom_js` tinyint(1) NOT NULL DEFAULT 1,
  `custom_css` text NOT NULL,
  `show_custom_css` tinyint(1) NOT NULL DEFAULT 1,
  `timezone` varchar(100) NOT NULL,
  PRIMARY KEY (`setting_id`),
  KEY `fk_settings_default_language_id_idx` (`default_language_id`),
  KEY `fk_settings_frontend_theme_id_idx` (`theme_id`),
  KEY `fk_settings_backend_theme_id_idx` (`backend_theme_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `settings_languages`;
CREATE TABLE IF NOT EXISTS `settings_languages` (
  `setting_language_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`setting_language_id`),
  KEY `fk_settings_languages_setting_id_idx` (`setting_id`),
  KEY `fk_settings_languages_language_id_idx` (`language_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `themes`;
CREATE TABLE IF NOT EXISTS `themes` (
  `theme_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `folder_name` varchar(50) NOT NULL,
  `type` enum('frontend','backend') NOT NULL DEFAULT 'frontend',
  `license` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `copyright` varchar(100) NOT NULL,
  `version` varchar(50) NOT NULL,
  `block_handling` enum('grouped','sequential') NOT NULL DEFAULT 'grouped',
  `identifier` varchar(50) NOT NULL,
  PRIMARY KEY (`theme_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `reset_key` varchar(255) DEFAULT NULL,
  `reseted_when` int(11) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `failed_logins` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `fk_user_role_id_idx` (`role_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE IF NOT EXISTS `visitors` (
  `visitor_id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `last_activity` int(11) NOT NULL,
  `user_agent` varchar(200) NOT NULL,
  PRIMARY KEY (`visitor_id`)
) ENGINE=InnoDB;

ALTER TABLE `navitems`
  ADD CONSTRAINT `fk_navitems_language_id` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_navigation_id` FOREIGN KEY (`navigation_id`) REFERENCES `navigations` (`navigation_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_navitem_id` FOREIGN KEY (`parent_navitem_id`) REFERENCES `navitems` (`navitem_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_navitems_page_id` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `pages`
  ADD CONSTRAINT `fk_pages_language_id` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_pages_user_id` FOREIGN KEY (`author_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE NO ACTION;

ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `fk_roles_permissions_permission_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`permission_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_roles_permissions_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `sections`
  ADD CONSTRAINT `fk_sections_block_id` FOREIGN KEY (`block_id`) REFERENCES `blocks` (`block_id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sections_module_id` FOREIGN KEY (`module_id`) REFERENCES `modules` (`module_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_sections_page_id` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `settings`
  ADD CONSTRAINT `fk_settings_backend_theme_id` FOREIGN KEY (`backend_theme_id`) REFERENCES `themes` (`theme_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_settings_default_language_id` FOREIGN KEY (`default_language_id`) REFERENCES `languages` (`language_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_settings_frontend_theme_id` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`theme_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

SET FOREIGN_KEY_CHECKS = 1;