<?php

// Module info
return [
    'name' => 'Search',
    'identifier' => 'search',
    'dependencies' => '',
    'description' => 'Provides a dynamic search.',
    'version' => '1.0.1',
    'for' => [
        '1.0.0',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => '(c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'search',
    'manager_class' => 'Neoflow\\Module\\Search\\Manager',
    'backend_route' => 'tmod_search_backend_index',
    'is_core' => true,
];
