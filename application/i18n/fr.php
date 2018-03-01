<?php

$translations = fetch_translations(__DIR__.'/translations.csv', 3);

return [
    'dateFormat' => 'm/d/Y',
    'dateTimeFormat' => 'm/d/Y H:i',
    'translation' => $translations,
];
