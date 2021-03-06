<?php

// Module info
return [
    'name' => 'Robots',
    'identifier' => 'robots',
    'dependencies' => '',
    'description' => 'Editor to edit the content of robots.txt.',
    'version' => '1.0.2',
    'for' => [
        '1.0.1',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => '(c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'robots',
    'manager_class' => 'Neoflow\\Module\\Robots\\Manager',
    'backend_route' => 'tmod_robots_backend_index',
];
