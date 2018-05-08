<?php

// Module info
return [
    'name' => 'Sitemap',
    'identifier' => 'sitemap',
    'dependencies' => '',
    'description' => 'Automated creation of sitemap (sitemap. xml) for search engines.',
    'version' => '1.0.1',
    'for' => [
        '1.0',
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool',
    'folder_name' => 'sitemap',
    'manager_class' => '\\Neoflow\\Module\\Sitemap\\Manager',
    'backend_route' => 'tmod_sitemap_backend_index',
    'is_core' => true,
];
