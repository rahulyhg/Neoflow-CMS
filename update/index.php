<?php
require_once '../../vendor/neoflow/framework/functions/rrmdir.php';

rrmdir('../../framework');

function copyFilesAndFolders($src, $dst)
{
    $dir = opendir($src);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if (is_dir($src . '/' . $file)) {
                copyFilesAndFolders($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
copyFilesAndFolders('../delivery/files', '../..');
