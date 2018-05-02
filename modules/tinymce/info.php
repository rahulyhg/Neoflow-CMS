<?php
// Module info
return [
    'name' => 'TinyMCE',
    'identifier' => 'tinymce',
    'dependencies' => 'wysiwyg',
    'description' => 'HTML text editor, designed to simplify website content creation. Extends the WYSIWYG editor module.',
    'version' => '4.7.11',
    'for' => [
        '4.7.1'
    ],
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'library',
    'folder_name' => 'tinymce',
    'manager_class' => '\Neoflow\Module\TinyMCE\Manager'
];
