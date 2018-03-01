<?php

/**
 * Normalize array of files from post upload.
 *
 * @param array $files
 *
 * @return array
 */
function normalize_post_files(array $files)
{
    $newFiles = [];
    $numberOfFiles = count($files['name']);
    $keys = array_keys($files);
    for ($i = 0; $i < $numberOfFiles; ++$i) {
        foreach ($keys as $key) {
            $newFiles[$i][$key] = $files[$key][$i];
        }
    }

    return $newFiles;
}
