<?php
// Define excludes
$excludes = [
    'update',
    'temp',
    'vendor',
    'application/templates',
    'application/views'
];
foreach (glob('./{modules,themes}/*/{templates,views}', GLOB_BRACE + GLOB_ONLYDIR) as $folder) {
    $excludes[] = str_replace('./', '', $folder);
}

// Create finder
$finder = PhpCsFixer\Finder::create()
    ->exclude($excludes)
    ->in(__DIR__);

// Create config
return PhpCsFixer\Config::create()
        ->setRiskyAllowed(true)
        ->setRules([
            '@PSR1' => true,
            '@PSR2' => true,
            '@Symfony' => true,
            'phpdoc_to_comment' => false,
        ])
        ->setFinder($finder);
