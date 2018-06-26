<?php

// Module routes
return [
    [
        'routes' => [
            ['pmod_blog_backend_article_index', 'get', '/backend/module/blog/(section_id:num)/articles', 'Article@index'],
            ['pmod_blog_backend_article_edit', 'get', '/backend/module/blog/(section_id:num)/articles/edit/(id:num)', 'Article@edit'],
            ['pmod_blog_backend_article_create', 'post', '/backend/module/blog/articles/create', 'Article@create'],
            ['pmod_blog_backend_article_update', 'post', '/backend/module/blog/articles/update', 'Article@update'],
            ['pmod_blog_backend_article_delete', 'get', '/backend/module/blog/(section_id:num)/articles/delete/(id:num)', 'Article@delete'],
            ['pmod_blog_backend_category_index', 'get', '/backend/module/blog/(section_id:num)/categories', 'Category@index'],
            ['pmod_blog_backend_category_edit', 'get', '/backend/module/blog/(section_id:num)/categories/edit/(id:num)', 'Category@edit'],
            ['pmod_blog_backend_category_create', 'post', '/backend/module/blog/categories/create', 'Category@create'],
            ['pmod_blog_backend_category_update', 'post', '/backend/module/blog/categories/update', 'Category@update'],
            ['pmod_blog_backend_category_delete', 'get', '/backend/module/blog/(section_id:num)/categories/delete/(id:num)', 'Category@delete'],
            ['pmod_blog_backend_setting_index', 'get', '/backend/module/blog/(section_id:num)/settings', 'Setting@index'],
            ['pmod_blog_backend_setting_update', 'post', '/backend/module/blog/(section_id:num)/settings/update', 'Setting@create'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\Backend\\',
    ],
    [
        'routes' => [
            ['pmod_blog_frontend_article_index', 'get', '/{blog:uri}', 'Frontend@index'],
            ['pmod_blog_frontend_article_index_category', 'get', '/(page:uri)/c/(slug:any)', 'Frontend@index'],
            ['pmod_blog_frontend_article_show', 'get', '{blog:uri}/(slug:any)', 'Frontend@show'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\',
    ],
];
