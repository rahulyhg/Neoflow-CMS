<?php

// Module routes
return [
    'routes' => [
        ['pmod_code_backend_index', 'any', '/backend/module/code/edit/(section_id:any)', 'Backend@index'],
        ['pmod_code_backend_update', 'post', '/backend/module/code/update', 'Backend@update'],
        ['pmod_code_frontend', 'any', false, 'Frontend@index'],
    ],
    'namespace' => '\\Neoflow\\Module\\Code\\Controller\\',
];
