<?php
// Module info
return [
    'name' => 'Robots',
    'identifier' => 'robots',
    'dependencies' => '',
    'description' => 'Editor to edit the content of robots.txt.',
    'version' => '1.0.1',
    'for' => [
        '1.0',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) 2017-2018 Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'robots',
    'manager_class' => '\\Neoflow\\Module\\Robots\\Manager',
    'backend_route' => 'tmod_robots_backend_index',
];
