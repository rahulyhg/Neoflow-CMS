<?php

// Module info
return [
    'name' => 'Dummy',
    'identifier' => 'dummy', // Unique module identifier
    'dependencies' => '', // List of identifiers for required modules
    'description' => 'Dummy module as demo example for interested developers.',
    'version' => '2.0.0',
    'for' => [
        '1.0.0',
    ], // list of older versions (for updates)
    'author' => 'Jonathan Nessier <jonathan.nessier@neoflow.ch>',
    'copyright' => 'Copyright (c) 2017 Jonathan Nessier, Neoflow.ch',
    'license' => 'MIT',
    'type' => 'tool', // page, library or tool
    'folder_name' => 'dummy', // Mostly like the identifier, but without special chars or spaces
    'manager_class' => '\\Neoflow\\Module\\Dummy\\Manager',
    'backend_route' => 'tmod_dummy_backend_index',
];
