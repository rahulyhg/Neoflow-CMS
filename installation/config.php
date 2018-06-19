<?php

return [
    'app' => [
        'url' => '',
        'version' => '1.0.0-b1',
        'languages' => [
            'en',
            'de',
            'fr',
        ],
        'email' => '',
        'timezone' => date_default_timezone_get(),
    ],
    'session' => [
        'name' => ini_get('session.name'),
        'lifetime' => (int) ini_get('session.gc_maxlifetime'),
    ],
    'database' => [
        'host' => '',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'charset' => 'UTF8mb4',
    ],
    'cache' => [
        'type' => false,
        'for_orm' => true,
        'for_qb' => true,
    ],
    'logger' => [
        'extension' => 'txt',
        'prefix' => 'log_',
        'level' => 'WARNING',
        'stackTrace' => true,
    ],
    'translator' => [
        'notFoundPrefix' => 'NOT_FOUND:',
        'fallbackPrefix' => 'FALLBACK:',
    ],
    'services' => [
        'alert' => 'Neoflow\\CMS\\Service\\AlertService',
        'mail' => 'Neoflow\\CMS\\Service\\MailService',
        'navitem' => 'Neoflow\\CMS\\Service\\NavitemService',
        'section' => 'Neoflow\\CMS\\Service\\SectionService',
        'auth' => 'Neoflow\\CMS\\Service\\AuthService',
        'page' => 'Neoflow\\CMS\\Service\\PageService',
        'upload' => 'Neoflow\\CMS\\Service\\UploadService',
        'filesystem' => 'Neoflow\\CMS\\Service\\FilesystemService',
        'validation' => 'Neoflow\\CMS\\Service\\ValidationService',
        'install' => 'Neoflow\\CMS\\Service\\InstallService',
        'update' => 'Neoflow\\CMS\\Service\\UpdateService',
    ],
    'folders' => [
        'application' => [
            'path' => '/application',
            'permission' => 448,
        ],
        'vendor' => [
            'path' => '/vendor',
            'permission' => 448,
        ],
        'temp' => [
            'path' => '/temp',
            'permission' => 448,
        ],
        'logs' => [
            'path' => '/logs',
            'permission' => 448,
        ],
        'media' => [
            'path' => '/media',
            'permission' => 493,
        ],
        'modules' => [
            'path' => '/modules',
            'permission' => 493,
        ],
        'statics' => [
            'path' => '/statics',
            'permission' => 493,
        ],
        'themes' => [
            'path' => '/themes',
            'permission' => 493,
        ],
    ],
];
