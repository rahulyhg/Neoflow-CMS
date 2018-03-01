<?php
return [
    'path' => ROOT_DIR,
    'cache' => false, // true (auto detection) | apc | apcu | file | false (disabled)
    'orm' => [
        'caching' => true, // true | false
    ],
    'queryBuilder' => [
        'caching' => true, // true | false
    ],
    'logger' => [
        'extension' => 'txt', // txt | log | ... | false (no extension)
        'prefix' => 'log_', // log_ | ... | false (no prefix)
        'level' => 'debug', // error | warning | info | debug | false (disabled)
    ],
    'translator' => [
        'notFoundPrefix' => 'NOT_FOUND:',
        'fallbackPrefix' => 'FALLBACK:',
    ],
    'services' => [
        '\\Neoflow\\CMS\\Service\\MailService',
        '\\Neoflow\\CMS\\Service\\NavitemService',
        '\\Neoflow\\CMS\\Service\\SectionService',
        '\\Neoflow\\CMS\\Service\\AuthService',
        '\\Neoflow\\CMS\\Service\\PageService',
        '\\Neoflow\\CMS\\Service\\StatsService',
        '\\Neoflow\\CMS\\Service\\UploadService',
        '\\Neoflow\\CMS\\Service\\FilesystemService',
        '\\Neoflow\\CMS\\Service\\ValidationService',
    ],
    'languages' => [// First language is default language
        'en', 'de', 'fr',
    ],
    'libs' => [
        'Alert',
        'Framework',
    ],
    'folders' => [
        'application' => [
            'path' => '/application',
            'permission' => 0700,
        ],
        'framework' => [
            'path' => '/framework',
            'permission' => 0700,
        ],
        'temp' => [
            'path' => '/temp',
            'permission' => 0700,
        ],
        'logs' => [
            'path' => '/logs',
            'permission' => 0700,
        ],
        'media' => [
            'path' => '/media',
            'permission' => 0755,
        ],
        'modules' => [
            'path' => '/modules',
            'permission' => 0755,
        ],
        'statics' => [
            'path' => '/statics',
            'permission' => 0755,
        ],
        'themes' => [
            'path' => '/themes',
            'permission' => 0755,
        ],
    ],
];
