<?php

// Module routes
return [
    'routes' => [
        ['lmod_tinymce_backend_api_pages', 'get', '/backend/module/tinymce/api/pages', 'Api@pages'],
        ['lmod_tinymce_backend_api_files', 'get', '/backend/module/tinymce/api/files', 'Api@files'],
        ['lmod_tinymce_backend_api_file_upload', 'post', '/backend/module/tinymce/api/file/upload', 'Api@uploadFile'],
        ['lmod_tinymce_backend_api_file_delete', 'get', '/backend/module/tinymce/file/delete', 'Api@deleteFile'],
    ],
    'namespace' => 'Neoflow\\Module\\TinyMCE\\Controller\\',
];
