<?php

// Module info
return [
    'name' => 'Code',
    'identifier' => 'code',
    'dependencies' => '',
    'description' => 'Page module for PHP code editing of website content.',
    'version' => '1.0.1',
    'for' => [
        '1.0',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'page',
    'folder_name' => 'code',
    'manager_class' => 'Neoflow\\Module\\Code\\Manager',
    'backend_route' => 'pmod_code_backend_index',
    'frontend_route' => 'pmod_code_frontend',
    'is_core' => true,
];
