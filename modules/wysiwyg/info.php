<?php

// Module info
return [
    'name' => 'WYSIWYG',
    'identifier' => 'wysiwyg',
    'dependencies' => 'search',
    'description' => 'Page module for the presentation and editing of website content.',
    'version' => '1.1.0',
    'for' => [
        '1.0.1',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => '(c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'page',
    'folder_name' => 'wysiwyg',
    'manager_class' => 'Neoflow\\Module\\WYSIWYG\\Manager',
    'backend_route' => 'pmod_wysiwyg_backend_index',
    'frontend_route' => 'pmod_wysiwyg_frontend_index',
    'is_core' => true,
];
