<?php

return [
    'version' => '1.0.0-a2',
    'for' => [
        '1.0.0-a1',
    ],
    'sql' => false, // Old param (only needed for update from 1.0.0-a1 to 1.0.0-a2).
    'files' => false, // Old param (only needed for update from 1.0.0-a1 to 1.0.0-a2).
    'path' => [
        'sql' => '/delivery/changes.sql',
        'files' => '/delivery/files',
        'modules' => '/delivery/modules',
        'themes' => '/delivery/themes',
    ],
    'modules' => [
        'code' => 'code.zip',
        'codemirror' => 'codemirror.zip',
        'wysiwyg' => 'wysiwyg.zip',
        'tinymce' => 'tinymce.zip',
        'datetimepicker' => 'datetimepicker.zip',
        'dummy' => 'dummy.zip',
        'log-viewer' => 'log-viewer.zip',
        'robots' => 'robots.zip',
        'search' => 'search.zip',
        'sitemap' => 'sitemap.zip',
        'snippets' => 'snippets.zip',
    ],
    'themes' => [
        'flatly' => 'flatly.zip',
        'neoflow-backend' => 'neoflow-backend.zip',
    ],
];
