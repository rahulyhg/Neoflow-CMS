INSERT INTO `roles` VALUES(1, 'Administrator', 'Role with all permissions.');

INSERT INTO `permissions` VALUES(1, 'manage_pages', 'Page', 'Manage pages and page content');
INSERT INTO `permissions` VALUES(2, 'manage_navigations', 'Navigation', 'Manage navigations');
INSERT INTO `permissions` VALUES(3, 'manage_modules', 'Module', 'Manage modules');
INSERT INTO `permissions` VALUES(4, 'manage_themes', 'Theme', 'Manage themes');
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

INSERT INTO `navigations` VALUES(1, 'Page tree', 'Navigation of complete page tree. DO NOT CHANGE IT.', 'page-tree');

INSERT INTO `languages` VALUES(1, 'en', 'English', 'gb');
INSERT INTO `languages` VALUES(2, 'de', 'German', 'de');
INSERT INTO `languages` VALUES(3, 'fr', 'French', 'fr');

INSERT INTO `themes` VALUES(1, 'Neoflow Backend', 'neoflow-backend', 'backend', 'MIT', 'Jonathan Nessier <jonathan.nessier@neoflow.ch>', 'Official backend theme of the Neoflow CMS.', 'Copyright (c) Jonathan Nessier, Neoflow.ch', '1.0.1', 'sequential', 'neoflow-backend');

INSERT INTO `settings` VALUES (1,'','','','',1,1,10,1800,0,'','','jpeg,jpg,doc,docx,xls,xlsx,ppt,pptx,pdf,gif,png,bmp,psd,tiff,zip,tar,rar,txt',0,2,'',0,'',0,'UTC');

INSERT INTO `settings_languages` VALUES (1,1,1);
INSERT INTO `settings_languages` VALUES (2,1,2);
INSERT INTO `settings_languages` VALUES (3,1,3);

INSERT INTO `mod_sitemap_settings` VALUES(1, 'monthly', '1', 72, 1);

INSERT INTO `mod_search_entities` VALUES (1, '\\Neoflow\\Module\\WYSIWYG\\Model');

INSERT INTO `mod_search_settings` VALUES (1, '/search', 1);

INSERT INTO `mod_snippets` VALUES(1, 'Dummy', 'return \'Just a snippet\';', 'dummy', 'Dummy snippet....', '');
INSERT INTO `mod_snippets` VALUES(2, 'Google Analytics', 'return \'<script>\r\n	(function (i, s, o, g, r, a, m) {\r\n		i[\"GoogleAnalyticsObject\"] = r;\r\n		i[r] = i[r] || function () {\r\n			(i[r].q = i[r].q || []).push(arguments)\r\n		}, i[r].l = 1 * new Date();\r\n		a = s.createElement(o),\r\n				m = s.getElementsByTagName(o)[0];\r\n		a.async = 1;\r\n		a.src = g;\r\n		m.parentNode.insertBefore(a, m);\r\n	})(window, document, \"script\", \"https://www.google-analytics.com/analytics.js\", \"ga\");\r\n\r\n	ga(\"create\", \"\' . $id . \'\", \"auto\");\r\n	ga(\"send\", \"pageview\");\r\n</script>\';', 'GoogleAnalytics', 'Creates the JavaScript code for Google Analytics based on an custom ID which passed by a parameter.', 'id');