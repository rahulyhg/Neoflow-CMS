<?php

$translations = fetch_translations(__DIR__.'/translations.csv', 1);

return [
    'dateFormat' => 'd/m/Y',
    'dateTimeFormat' => 'd/m/Y H:i',
    'translation' => $translations,
];
