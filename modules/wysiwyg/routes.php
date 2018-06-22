<?php

// Module routes
// @todo Remove _fisch-routes when blog module is finished
return [
    'routes' => [
        ['pmod_wysiwyg_backend_index', 'any', '/backend/module/wysiwyg/(section_id:num)', 'Backend@index'],
        ['pmod_wysiwyg_backend_update', 'post', '/backend/module/wysiwyg/update', 'Backend@update'],
        ['pmod_wysiwyg_fisch', 'get', '{url:wysiwyg}/fisch/(bla:any)', 'Frontend@fisch'],
        ['pmod_wysiwyg_fisch', 'get', '{url:wysiwyg}/fisch', 'Frontend@fisch'],
        ['pmod_wysiwyg_frontend_index', 'any', false, 'Frontend@index'],
    ],
    'namespace' => 'Neoflow\\Module\\WYSIWYG\\Controller\\',
];
