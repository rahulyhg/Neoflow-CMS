<?php

// Module info
return [
    'name' => 'Blog',
    'identifier' => 'blog',
    'dependencies' => 'wysiwyg,datetimepicker,search',
    'description' => 'Page module with an article system for blogs and news.',
    'version' => '1.0.0',
    'for' => [],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => '(c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'page',
    'folder_name' => 'blog',
    'manager_class' => 'Neoflow\\Module\\Blog\\Manager',
    'backend_route' => 'pmod_blog_backend_article_index',
    'frontend_route' => 'pmod_blog_frontend_article_index',
];
