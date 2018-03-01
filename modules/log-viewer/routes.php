<?php

// Module routes
return [
    'routes' => [
        ['tmod_log_viewer_backend_index', 'any', '/backend/module/log-viewer', 'Backend@index'],
        ['tmod_log_viewer_backend_show', 'get', '/backend/module/log-viewer/show/(logfile:string)', 'Backend@show'],
        ['tmod_log_viewer_backend_get', 'get', '/backend/module/log-viewer/get/(logfile:string)', 'Backend@get'],
    ],
    'namespace' => '\\Neoflow\\Module\\LogViewer\\Controller\\',
];
