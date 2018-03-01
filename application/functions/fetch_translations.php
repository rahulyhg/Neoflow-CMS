<?php

/**
 * Fetch translation from CSV file.
 *
 * @param string $translationFilePath
 * @param int    $position
 *
 * @return array
 */
function fetch_translations(string $translationFilePath, int $position = 1): array
{
    $translations = [];
    if (is_string($translationFilePath)) {
        foreach (file($translationFilePath) as $translationRow) {
            $translationColumns = str_getcsv($translationRow, ';');
            if (isset($translationColumns[0]) && isset($translationColumns[$position])) {
                $translations[$translationColumns[0]] = $translationColumns[$position];
            }
        }
    }

    return $translations;
}
