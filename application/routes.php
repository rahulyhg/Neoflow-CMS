<?php

return [
// Frontend routes
[
    'routes' => [
        ['admin_redirect', 'any', '/admin', 'Frontend@adminRedirect'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\',
    'prefix' => 'frontend_',
],
// Backend routes
[
    'routes' => [
        ['index', 'any', '/backend', 'Backend'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\',
    'prefix' => 'backend_',
],
// Backend auth routes
[
    'routes' => [
        ['login', 'get', '/backend/login', 'Auth@login'],
        ['logout', 'get', '/backend/logout', 'Auth@logout'],
        ['authenticate', 'post', '/backend/auth', 'Auth@authenticate'],
        ['lost_password', 'get', '/backend/lost-password', 'Auth@lostPassword'],
        ['reset_password', 'post', '/backend/reset-password', 'Auth@resetPassword'],
        ['new_password', 'get', '/backend/new-password/(reset_key:string)', 'Auth@newPassword'],
        ['update_password', 'post', '/backend/update-password', 'Auth@updatePassword'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_auth_',
],
// Backend dashboard routes
[
    'routes' => [
        ['index', 'get', '/backend/dashboard', 'Dashboard'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_dashboard_',
],
// Backend page routes
[
    'routes' => [
        ['edit', 'get', '/backend/pages/edit/(id:num)', 'Page@edit'],
        ['delete', 'get', '/backend/pages/delete/(id:num)', 'Page@delete'],
        ['reorder', 'post', '/backend/pages/reorder', 'Page@reorder'],
        ['toggle_activation', 'get', '/backend/pages/toggle-activation/(id:num)', 'Page@toggleActivation'],
        ['update', 'post', '/backend/pages/update', 'Page@update'],
        ['create', 'post', '/backend/pages/create', 'Page@create'],
        ['index', 'get', '/backend/pages', 'Page@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_page_',
],
// Backend section routes
[
    'routes' => [
        ['reorder', 'post', '/backend/sections/reorder', 'Section@reorder'],
        ['edit', 'get', '/backend/sections/edit/(id:num)', 'Section@edit'],
        ['create', 'post', '/backend/sections/create', 'Section@create'],
        ['delete', 'get', '/backend/sections/delete/(id:num)', 'Section@delete'],
        ['update', 'post', '/backend/sections/update', 'Section@update'],
        ['toggle_activation', 'get', '/backend/sections/toggle-activation/(id:num)', 'Section@toggleActivation'],
        ['index', 'get', '/backend/pages/edit/(page_id:num)/sections', 'Section@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_section_',
],
// Backend user routes
[
    'routes' => [
        ['create', 'post', '/backend/users/create', 'User@create'],
        ['edit', 'get', '/backend/users/edit/(id:num)', 'User@edit'],
        ['delete', 'get', '/backend/users/delete/(id:num)', 'User@delete'],
        ['update', 'post', '/backend/users/update', 'User@update'],
        ['update_password', 'post', '/backend/users/update-password', 'User@updatePassword'],
        ['index', 'get', '/backend/users', 'User@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_user_',
],
// Backend role routes
[
    'routes' => [
        ['create', 'post', '/backend/roles/create', 'Role@create'],
        ['edit', 'get', '/backend/roles/edit/(id:num)', 'Role@edit'],
        ['delete', 'get', '/backend/roles/delete/(id:num)', 'Role@delete'],
        ['update', 'post', '/backend/roles/update', 'Role@update'],
        ['index', 'get', '/backend/roles', 'Role@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_role_',
],
// Backend module routes
[
    'routes' => [
        ['install', 'post', '/backend/modules/install', 'Module@install'],
        ['update', 'post', '/backend/modules/update', 'Module@update'],
        ['delete', 'get', '/backend/modules/delete/(id:num)', 'Module@delete'],
        ['view', 'get', '/backend/modules/view/(id:num)', 'Module@view'],
        ['reload_all', 'get', '/backend/modules/reload', 'Module@reload'],
        ['reload', 'get', '/backend/modules/reload/(id:num)', 'Module@reload'],
        ['toggle_activation', 'get', '/backend/modules/toggle-activation/(id:num)', 'Module@toggleActivation'],
        ['index', 'get', '/backend/modules', 'Module@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_module_',
],
// Backend media routes
[
    'routes' => [
        ['upload', 'post', '/backend/media/file/upload', 'Media@upload'],
        ['download', 'get', '/backend/media/file/download', 'Media@download'],
        ['delete_file', 'get', '/backend/media/file/delete', 'Media@deleteFile'],
        ['rename_file', 'get', '/backend/media/file/rename', 'Media@renameFile'],
        ['update_file', 'post', '/backend/media/file/update', 'Media@updateFile'],
        ['create_folder', 'post', '/backend/media/folder/create', 'Media@createFolder'],
        ['delete_folder', 'get', '/backend/media/folder/delete', 'Media@deleteFolder'],
        ['rename_folder', 'get', '/backend/media/folder/rename', 'Media@renameFolder'],
        ['update_folder', 'post', '/backend/media/folder/update', 'Media@updateFolder'],
        ['index', 'get', '/backend/media', 'Media@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_media_',
],
// Backend theme routes
[
    'routes' => [
        ['install', 'post', '/backend/themse/install', 'Theme@install'],
        ['delete', 'get', '/backend/themes/delete/(id:num)', 'Theme@delete'],
        ['reload_all', 'get', '/backend/themes/reload', 'Theme@reload'],
        ['reload', 'get', '/backend/themes/reload/(id:num)', 'Theme@reload'],
        ['view', 'get', '/backend/themes/view/(id:num)', 'Theme@view'],
        ['update', 'post', '/backend/themes/update', 'Theme@update'],
        ['index', 'get', '/backend/themes', 'Theme@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_theme_',
],
// Backend block routes
[
    'routes' => [
        ['create', 'post', '/backend/blocks/create', 'Block@create'],
        ['edit', 'get', '/backend/blocks/edit/(id:num)', 'Block@edit'],
        ['load', 'get', '/backend/blocks/load', 'Block@load'],
        ['update', 'post', '/backend/blocks/update', 'Block@update'],
        ['delete', 'get', '/backend/blocks/delete/(id:num)', 'Block@delete'],
        ['index', 'get', '/backend/blocks', 'Block@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_block_',
],
// Backend navigation routes
[
    'routes' => [
        ['create', 'post', '/backend/navigations/create', 'Navigation@create'],
        ['edit', 'get', '/backend/navigations/edit/(id:num)', 'Navigation@edit'],
        ['load', 'get', '/backend/navigations/load', 'Navigation@load'],
        ['update', 'post', '/backend/navigations/update', 'Navigation@update'],
        ['delete', 'get', '/backend/navigations/delete/(id:num)', 'Navigation@delete'],
        ['index', 'get', '/backend/navigations', 'Navigation@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_navigation_',
],
// Backend navitem routes
[
    'routes' => [
        ['index', 'get', '/backend/navigations/edit/(navigation_id:num)/navigation-items', 'Navitem@index'],
        ['reorder', 'post', '/backend/navigation-items/reorder', 'Navitem@reorder'],
        ['create', 'post', '/backend/navigation-items/create', 'Navitem@create'],
        ['edit', 'get', '/backend/navigation-items/edit/(id:num)', 'Navitem@edit'],
        ['update', 'post', '/backend/navigation-items/update', 'Navitem@update'],
        ['delete', 'get', '/backend/navigation-items/delete/(id:num)', 'Navitem@delete'],
        ['toggle_activation', 'get', '/backend/navigation-items/toggle-activation/(id:num)', 'Navitem@toggleActivation'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_navitem_',
],
// Backend setting routes
[
    'routes' => [
        ['index', 'get', '/backend/settings', 'Setting'],
        ['update', 'post', '/backend/settings/update', 'Setting@update'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_setting_',
],
// Backend maintenance routes
[
    'routes' => [
        ['index', 'get', '/backend/maintenance', 'Maintenance@index'],
        ['clear_cache', 'post', '/backend/maintenance/cache/cear', 'Maintenance@clearCache'],
        ['install_update', 'post', '/backend/maintenance/update/install', 'Maintenance@installUpdate'],
        ['delete_logfiles', 'post', '/backend/maintenance/logfiles/delete', 'Maintenance@deleteLogfiles'],
        ['reset_folder_permissions', 'get', '/backend/maintenance/folder-permissions/reset', 'Maintenance@resetFolderPermissions'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_maintenance_',
],
// Backend tool routes
[
    'routes' => [
        ['index', 'get', '/backend/tools', 'Tool@index'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_tool_',
],
// Add error route
[
    'routes' => [
        ['index', 'any', 'error', 'Error@index'],
        ['bad_request', 'any', 'bad-request', 'Error@badRequest'],
        ['unauthorized', 'any', 'unauthorized', 'Error@unauthorized'],
        ['forbidden', 'any', 'forbidden', 'Error@forbidden'],
        ['not_found', 'any', 'not-found', 'Error@notFound'],
        ['internal_server_error', 'any', 'internal-server-error', 'Error@internalServerError'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Frontend\\',
    'prefix' => 'error_',
],
// User profile route
[
    'routes' => [
        ['index', 'get', '/backend/profile', 'Profile@index'],
        ['update', 'post', '/backend/profile/update', 'Profile@update'],
        ['update_password', 'post', '/backend/profile/update-password', 'Profile@updatePassword'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Backend\\',
    'prefix' => 'backend_profile_',
],
// Add api route
[
    'routes' => [
        ['auth', 'post', '/api/user/auth', 'User@auth'],
        ['logout', 'get', '/api/user/logout', 'User@logout'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\Api\\',
    'prefix' => 'api_user_',
],
// Install route
[
    'routes' => [
        ['index', 'get', '/install', 'Install@index'],
        ['database_index', 'get', '/install/database', 'Install\\Database@index'],
        ['database_create', 'post', '/install/database/create', 'Install\\Database@create'],
        ['website_index', 'get', '/install/website', 'Install\\Website@index'],
        ['website_create', 'post', '/install/website/create', 'Install\\Website@create'],
        ['administrator_index', 'get', '/install/administrator', 'Install\\Administrator@index'],
        ['administrator_create', 'post', '/install/administrator/create', 'Install\\Administrator@create'],
        ['success', 'get', '/install/success', 'Install@success'],
    ],
    'namespace' => 'Neoflow\\CMS\\Controller\\',
    'prefix' => 'install_',
],
];
