<?php

// Module routes
return [
    [
        'routes' => [
            ['pmod_blog_backend_article_index', 'get', '/backend/module/blog/articles', 'Article@index'],
            ['pmod_blog_backend_article_edit', 'get', '/backend/module/blog/articles/edit/(id:num)', 'Article@edit'],
            ['pmod_blog_backend_article_create', 'post', '/backend/module/blog/articles/create', 'Article@create'],
            ['pmod_blog_backend_article_update', 'post', '/backend/module/blog/articles/update', 'Article@update'],
            ['pmod_blog_backend_article_update_website', 'post', '/backend/module/blog/articles/update/website', 'Article@updateWebsite'],
            ['pmod_blog_backend_article_delete', 'get', '/backend/module/blog/articles/delete/(id:num)', 'Article@delete'],
            ['pmod_blog_backend_category_index', 'get', '/backend/module/blog/categories', 'Category@index'],
            ['pmod_blog_backend_category_edit', 'get', '/backend/module/blog/categories/edit/(id:num)', 'Category@edit'],
            ['pmod_blog_backend_category_create', 'post', '/backend/module/blog/categories/create', 'Category@create'],
            ['pmod_blog_backend_category_update', 'post', '/backend/module/blog/categories/update', 'Category@update'],
            ['pmod_blog_backend_category_delete', 'get', '/backend/module/blog/categories/delete/(id:num)', 'Category@delete'],
            ['pmod_blog_backend_setting_index', 'get', '/backend/module/blog/settings', 'Setting@index'],
            ['pmod_blog_backend_setting_update', 'post', '/backend/module/blog/settings/update', 'Setting@create'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\Backend\\',
    ],
    [
        'routes' => [
            ['pmod_blog_frontend_article_index_category', 'get', '/(page:uri)/c/(title_slug:uri)', 'Frontend@index'],
            ['pmod_blog_frontend_article_show', 'get', '(page:uri)/(title_slug:any)', 'Frontend@show'],
        ],
        'namespace' => 'Neoflow\\Module\\Blog\\Controller\\',
    ],
];
