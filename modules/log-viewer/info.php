<?php

// Module info
return [
    'name' => 'Log Viewer',
    'identifier' => 'log-viewer',
    'dependencies' => '',
    'description' => 'User-friendly and simple viewer for in-depth analysis of log files.',
    'version' => '1.0.2',
    'for' => [
        '1.0.1',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => '(c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'log-viewer',
    'manager_class' => 'Neoflow\\Module\\LogViewer\\Manager',
    'backend_route' => 'tmod_log_viewer_backend_index',
];
