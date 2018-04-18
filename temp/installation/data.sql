SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

INSERT INTO `roles` VALUES(1, 'Administrator', 'Role with all permissions.');

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

INSERT INTO `blocks` VALUES(1, 'content', 'Content Default');
INSERT INTO `blocks` VALUES(2, 'content-primary', 'Content Primary');

INSERT INTO `modules` VALUES(1, 'Log Viewer', 'log-viewer', 'log-viewer', 'tmod_log_viewer_backend_index', NULL, '\\Neoflow\\Module\\LogViewer\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'User-friendly and simple viewer for in-depth analysis of log files.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '', 0);
INSERT INTO `modules` VALUES(2, 'Snippets', 'snippets', 'snippets', 'tmod_snippets_backend_index', NULL, '\\Neoflow\\Module\\Snippets\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Executes PHP code and replaces the placeholders in your website content with the result.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', 'code', 1);
INSERT INTO `modules` VALUES(3, 'WYSIWYG', 'wysiwyg', 'wysiwyg', 'pmod_wysiwyg_backend_index', 'pmod_wysiwyg_frontend', '\\Neoflow\\Module\\WYSIWYG\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Page module for the presentation and editing of website content.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'page', '', 1);
INSERT INTO `modules` VALUES(4, 'TinyMCE', 'tinymce', 'tinymce', NULL, NULL, '\\Neoflow\\Module\\TinyMCE\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'HTML text editor, designed to simplify website content creation. Extends the WYSIWYG editor module.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '4.7.1', 1, 'library', 'wysiwyg', 0);
INSERT INTO `modules` VALUES(5, 'Code', 'code', 'code', 'pmod_code_backend_index', 'pmod_code_frontend', '\\Neoflow\\Module\\Code\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Page module for PHP code editing of website content.', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'page', '', 1);
INSERT INTO `modules` VALUES(6, 'CodeMirror', 'codemirror', 'codemirror', NULL, NULL, '\\Neoflow\\Module\\CodeMirror\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Code editor with syntax highlighting. Extends the code editor module.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '5.27.4', 1, 'library', 'code', 0);
INSERT INTO `modules` VALUES(7, 'Sitemap', 'sitemap', 'sitemap', 'tmod_sitemap_backend_index', NULL, '\\Neoflow\\Module\\Sitemap\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Automated creation of sitemap (sitemap. xml) for search engines.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '', 1);
INSERT INTO `modules` VALUES(8, 'Robots', 'robots', 'robots', 'tmod_robots_backend_index', NULL, '\\Neoflow\\Module\\Robots\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Editor to edit the content of robots.txt.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '1.0', 1, 'tool', '', 0);
INSERT INTO `modules` VALUES(9, 'DateTimePicker', 'datetimepicker', 'datetimepicker', NULL, NULL, '\\Neoflow\\Module\\DateTimePicker\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Datetimepicker, datepicker or timepicker dropdown to your forms.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow', 'MIT', '5.27.4', 1, 'library', '', 1);
INSERT INTO `modules` VALUES(10, 'Dummy', 'dummy', 'dummy', 'tmod_dummy_backend_index', NULL, '\\Neoflow\\Module\\Dummy\\Manager', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Dummy module as demo example for interested developers.', 'Copyright (c) 2017 Jonathan Nessier, Neoflow.ch', 'MIT', '2.0.0', 1, 'tool', '', 0);

INSERT INTO `navigations` VALUES(1, 'Page tree', 'Navigation of complete page tree. DO NOT CHANGE IT.', 'page-tree');

INSERT INTO `languages` VALUES(1, 'en', 'English', 'gb');
INSERT INTO `languages` VALUES(2, 'de', 'German', 'de');
INSERT INTO `languages` VALUES(3, 'fr', 'French', 'fr');

INSERT INTO `themes` VALUES(1, 'Neoflow Backend', 'neoflow-backend', 'backend', 'MIT', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Official backend theme of the Neoflow CMS', 'Copyright (c) 2016-2017 Jonathan Nessier, Neoflow', '1.0', 'sequential', 'neoflow-backend');
INSERT INTO `themes` VALUES(2, 'Flatly', 'flatly', 'frontend', 'MIT', 'Thomas Park,Jonathan Nessier', 'Bootstrap-based free template of Bootswatch, modified for Neoflow CMS.', '2012-2018 Thomas Park', '1.0', 'sequential', 'flatly');

INSERT INTO `settings` VALUES (1,'','','','',1,1,10,1800,1,'','','jpeg,jpg,doc,docx,xls,xlsx,ppt,pptx,pdf,gif,png,bmp,psd,tiff,zip,tar,rar,txt',1,2,'',0,'',0,'UTC');

INSERT INTO `settings_languages` VALUES (1,1,1);
INSERT INTO `settings_languages` VALUES (2,1,2);
INSERT INTO `settings_languages` VALUES (3,1,3);

INSERT INTO `mod_sitemap_settings` VALUES(1, 'monthly', '1', 72, 1);

INSERT INTO `mod_snippets` VALUES(1, 'Dummy', 'return \'Just a snippet\';', 'dummy', 'Dummy snippet....', '');
INSERT INTO `mod_snippets` VALUES(2, 'Google Analytics', 'return \'<script>\r\n	(function (i, s, o, g, r, a, m) {\r\n		i[\"GoogleAnalyticsObject\"] = r;\r\n		i[r] = i[r] || function () {\r\n			(i[r].q = i[r].q || []).push(arguments)\r\n		}, i[r].l = 1 * new Date();\r\n		a = s.createElement(o),\r\n				m = s.getElementsByTagName(o)[0];\r\n		a.async = 1;\r\n		a.src = g;\r\n		m.parentNode.insertBefore(a, m);\r\n	})(window, document, \"script\", \"https://www.google-analytics.com/analytics.js\", \"ga\");\r\n\r\n	ga(\"create\", \"\' . $id . \'\", \"auto\");\r\n	ga(\"send\", \"pageview\");\r\n</script>\';', 'GoogleAnalytics', 'Creates the JavaScript code for Google Analytics based on an custom ID which passed by a parameter.', 'id');

INSERT INTO `pages` VALUES (1, 'Welcome', 'welcome', 'Willkommensseite des Neoflow CMS.', 'Welcome, Neoflow, CMS', 1, 1, 1, 0, '/welcome', 1, 1, 1524048810, 1524047489);
INSERT INTO `pages` VALUES (2, 'Willkommen', 'willkommen', 'Willkommensseite des Neoflow CMS.', 'Willkommen, Neoflow, CMS', 2, 1, 1, 0, '/willkommen', 1, 1, 1524048810, 1524047489);

INSERT INTO `navitems` VALUES (1, 'Welcome', 2, NULL, 1, 1, 1, 1);
INSERT INTO `navitems` VALUES (2, 'Willkommen', 2, NULL, 1, 2, 1, 1);

COMMIT;