<?php

// Module info
return [
    'name' => 'Snippets',
    'identifier' => 'snippets',
    'dependencies' => 'code',
    'description' => 'Executes PHP code and replaces the placeholders in your website content with the result.',
    'version' => '1.0.1',
    'for' => [
        '1.0',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'snippets',
    'manager_class' => 'Neoflow\\Module\\Snippets\\Manager',
    'backend_route' => 'tmod_snippets_backend_index',
    'is_core' => true,
];
