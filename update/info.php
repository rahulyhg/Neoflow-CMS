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
        'packages' => '/delivery/packages',
    ],
    'modules' => [
        'code' => 'code.zip',
        'codemirror' => 'codemirror.zip',
        'datetimepicker' => 'datetimepicker.zip',
        'dummy' => 'dummy.zip',
        'log-viewer' => 'log-viewer.zip',
        'robots' => 'robots.zip',
        'search' => 'search.zip',
        'sitemap' => 'sitemap.zip',
        'snippets' => 'snippets.zip',
        'tinymce' => 'tinymce.zip',
        'wysiwyg' => 'wysiwyg.zip',
    ],
    'themes' => [
        'flatly' => 'flatly.zip',
        'neoflow-backend' => 'neoflow-backend.zip',
    ],
];
