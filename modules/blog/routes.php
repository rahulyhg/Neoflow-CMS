<?php

// Module routes
return [
    [
        'routes' => [
            ['pmod_blog_backend_article_index', 'get', '/backend/module/blog/(section_id:num)', 'Article@index'],
            ['pmod_blog_backend_article_create', 'post', '/backend/module/blog/(section_id:num)/article/create', 'Article@create'],
            ['pmod_blog_backend_article_update', 'post', '/backend/module/blog/(section_id:num)/article/update', 'Article@update'],
            ['pmod_blog_backend_article_delete', 'get', '/backend/module/blog/(section_id:num)/article/delete/(id:num)', 'Article@delete'],
            ['pmod_blog_backend_category_index', 'get', '/backend/module/blog/(section_id:num)/categories', 'Category@index'],
            ['pmod_blog_backend_category_create', 'post', '/backend/module/blog/(section_id:num)/category/create', 'Category@create'],
            ['pmod_blog_backend_category_update', 'post', '/backend/module/blog/(section_id:num)/category/update', 'Category@update'],
            ['pmod_blog_backend_category_delete', 'get', '/backend/module/blog/(section_id:num)/category/delete/(id:num)', 'Category@delete'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\Backend\\',
    ],
    [
        'routes' => [
            ['pmod_blog_frontend_article_index', 'get', '{url:blog}', 'Frontend@index'],
            ['pmod_blog_frontend_article_index_category', 'get', '{url:blog}/(category_slug:any)', 'Frontend@index'],
            ['pmod_blog_frontend_article_show', 'get', '{url:blog}/(slug:any)', 'Frontend@show'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\',
    ],
];
