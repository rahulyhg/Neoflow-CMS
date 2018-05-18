<?php

// Module routes
return [
    'routes' => [
        ['pmod_wysiwyg_backend_index', 'any', '/backend/module/wysiwyg/edit/(section_id:num)', 'Backend@index'],
        ['pmod_wysiwyg_backend_update', 'post', '/backend/module/wysiwyg/update', 'Backend@update'],
        ['pmod_wysiwyg_backend_api_pages', 'get', '/backend/module/wysiwyg/api/pages', 'Api@pages'],
        ['pmod_wysiwyg_backend_api_files', 'get', '/backend/module/wysiwyg/api/(id:any)/files', 'Api@files'],
        ['pmod_wysiwyg_backend_api_file_upload', 'post', '/backend/module/wysiwyg/api/(id:any)/file/upload', 'Api@uploadFile'],
        ['pmod_wysiwyg_backend_api_file_delete', 'get', '/backend/module/wysiwyg/(id:any)/file/delete', 'Api@deleteFile'],
        ['pmod_wysiwyg_fisch', 'get', '{url:wysiwyg}/fisch/(bla:any)', 'Frontend@fisch'],
        ['pmod_wysiwyg_fisch', 'get', '{url:wysiwyg}/fisch', 'Frontend@fisch'],
        ['pmod_wysiwyg_frontend', 'any', false, 'Frontend@index'],
    ],
    'namespace' => 'Neoflow\\Module\\WYSIWYG\\Controller\\',
];
