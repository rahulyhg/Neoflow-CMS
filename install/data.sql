SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `roles` VALUES(1, 'Administrator', '...');

INSERT INTO `permissions` VALUES(1, 'manage_pages', 'Page', 'Manage pages and page content');
INSERT INTO `permissions` VALUES(2, 'manage_navigations', 'Navigation', 'Manage navigations');
INSERT INTO `permissions` VALUES(3, 'manage_modules', 'Module', 'Manage modules');
INSERT INTO `permissions` VALUES(4, 'manage_templates', 'Theme', 'Manage themes');
INSERT INTO `permissions` VALUES(5, 'manage_media', 'Media', 'Manage media files and folders');
INSERT INTO `permissions` VALUES(6, 'settings', 'Setting', 'Update website settings');
INSERT INTO `permissions` VALUES(7, 'manage_users', 'User', 'Manage user accounts');
INSERT INTO `permissions` VALUES(8, 'manage_roles', 'Role', 'Manage roles and permissions');
INSERT INTO `permissions` VALUES(9, 'maintenance', 'Maintenance', 'Maintain website and system');
INSERT INTO `permissions` VALUES(10, 'manage_blocks', 'Block', 'Manage blocks');
INSERT INTO `permissions` VALUES(11, 'run_tools', 'Tool', 'Additional backend functions');

INSERT INTO `roles_permissions` VALUES(1, 1, 1);
INSERT INTO `roles_permissions` VALUES(2, 1, 2);
INSERT INTO `roles_permissions` VALUES(3, 1, 3);
INSERT INTO `roles_permissions` VALUES(4, 1, 4);
INSERT INTO `roles_permissions` VALUES(5, 1, 5);
INSERT INTO `roles_permissions` VALUES(6, 1, 6);
INSERT INTO `roles_permissions` VALUES(7, 1, 7);
INSERT INTO `roles_permissions` VALUES(8, 1, 8);
INSERT INTO `roles_permissions` VALUES(9, 1, 9);
INSERT INTO `roles_permissions` VALUES(10, 1, 10);
INSERT INTO `roles_permissions` VALUES(11, 1, 11);

INSERT INTO `blocks` VALUES(1, 'content', 'Inhalt');

INSERT INTO `modules` VALUES(1, 'Log Viewer', 'log-viewer', 'log-viewer', 'tmod_log_viewer_backend_index', NULL, '\\Neoflow\\Module\\LogViewer\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'User-friendly and simple viewer for in-depth analysis of log files.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '');
INSERT INTO `modules` VALUES(2, 'Snippets', 'snippets', 'snippets', 'tmod_snippets_backend_index', NULL, '\\Neoflow\\Module\\Snippets\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Executes PHP code and replaces the placeholders in your website content with the result.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', 'code');
INSERT INTO `modules` VALUES(3, 'WYSIWYG', 'wysiwyg', 'wysiwyg', 'pmod_wysiwyg_backend_index', 'pmod_wysiwyg_frontend', '\\Neoflow\\Module\\WYSIWYG\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Page module for the presentation and editing of website content.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'page', '');
INSERT INTO `modules` VALUES(4, 'TinyMCE', 'tinymce', 'tinymce', NULL, NULL, '\\Neoflow\\Module\\TinyMCE\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'HTML text editor, designed to simplify website content creation. Extends the WYSIWYG editor module.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '4.7.1', 1, 'library', 'wysiwyg');
INSERT INTO `modules` VALUES(5, 'Code', 'code', 'code', 'pmod_code_backend_index', 'pmod_code_frontend', '\\Neoflow\\Module\\Code\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Page module for PHP code editing of website content.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'page', '');
INSERT INTO `modules` VALUES(6, 'CodeMirror', 'codemirror', 'codemirror', NULL, NULL, '\\Neoflow\\Module\\CodeMirror\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Code editor with syntax highlighting. Extends the code editor module.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '5.27.4', 1, 'library', 'code');
INSERT INTO `modules` VALUES(7, 'Sitemap', 'sitemap', 'sitemap', 'tmod_sitemap_backend_index', NULL, '\\Neoflow\\Module\\Sitemap\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Automated creation of sitemap (sitemap. xml) for search engines.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '');
INSERT INTO `modules` VALUES(8, 'Robots', 'robots', 'robots', 'tmod_robots_backend_index', NULL, '\\Neoflow\\Module\\Robots\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Editor to edit the content of robots.txt.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '');
INSERT INTO `modules` VALUES(9, 'DateTimePicker', 'datetimepicker', 'datetimepicker', NULL, NULL, '\\Neoflow\\Module\\DateTimePicker\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Datetimepicker, datepicker or timepicker dropdown to your forms.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '5.27.4', 1, 'library', '');

INSERT INTO `navigations` VALUES(1, 'Page tree', 'Navigation of complete page tree. DO NOT CHANGE IT.', 'page-tree');

INSERT INTO `languages` VALUES(1, 'en', 'English', 'gb');
INSERT INTO `languages` VALUES(2, 'de', 'German', 'de');
INSERT INTO `languages` VALUES(3, 'fr', 'French', 'fr');

INSERT INTO `themes` VALUES(1, 'Neoflow Backend', 'neoflow-backend', 'backend', 'MIT', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Official backend theme of the Neoflow CMS', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', '1.0', 'sequential', 'neoflow-backend');
INSERT INTO `themes` VALUES(2, 'Cloudy', 'cloudy', 'frontend', 'MIT', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Lorem ipsum', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', '1.0', 'grouped', 'cloudy');

INSERT INTO `settings` VALUES (1,'','','','',1,1,10,1800,1,'','','jpeg,jpg,doc,docx,xls,xlsx,ppt,pptx,pdf,gif,png,bmp,psd,tiff,zip,tar,rar,txt',1,2,'',0,'',0,'UTC', '1.0.0-dev');

INSERT INTO `settings_languages` VALUES (1,1,1);
INSERT INTO `settings_languages` VALUES (2,1,2);
INSERT INTO `settings_languages` VALUES (3,1,3);

INSERT INTO `mod_sitemap_settings` VALUES(1, 'monthly', '1', 72, 1);

INSERT INTO `mod_snippets` VALUES(1, 'Dummy', 'return \'8988\';', 'dummy', 'Dummy snippet....');
INSERT INTO `mod_snippets` VALUES(2, 'Google Analytics', 'return \'this is google analytics\';', 'GoogleAnalytics', 'Platziert den JavaScript Code für Google Analytics. this is google analytics');
INSERT INTO `mod_snippets` VALUES(3, '456345', '', '89089', '');

COMMIT;