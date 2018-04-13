<?php
return [
    'app' => [
        'url' => '',
    ],
    'database' => [
        'host' => '',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'charset' => 'UTF8',
    ],
    'cache' => [
        'type' => 'auto',
        'for_orm' => true,
        'for_qb' => true,
    ],
    'logger' => [
        'extension' => 'txt',
        'prefix' => 'log_',
        'level' => 'warning',
        'stackTrace' => true,
    ],
    'translator' => [
        'notFoundPrefix' => 'NOT_FOUND:',
        'fallbackPrefix' => 'FALLBACK:',
    ],
    'services' => [
        '\Neoflow\CMS\Service\MailService',
        '\Neoflow\CMS\Service\NavitemService',
        '\Neoflow\CMS\Service\SectionService',
        '\Neoflow\CMS\Service\AuthService',
        '\Neoflow\CMS\Service\PageService',
        '\Neoflow\CMS\Service\StatsService',
        '\Neoflow\CMS\Service\UploadService',
        '\Neoflow\CMS\Service\FilesystemService',
        '\Neoflow\CMS\Service\ValidationService',
        '\Neoflow\CMS\Service\InstallService',
    ],
    'folders' => [
        'application' => [
            'path' => '/application',
            'permission' => 448,
        ],
        'framework' => [
            'path' => '/framework',
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