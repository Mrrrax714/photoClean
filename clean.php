<?php
session_start();
function scanit($dir)
{
$Directory = new RecursiveDirectoryIterator( $dir );
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+\.(jp*g|gif|png|webp|gif)/i', RecursiveRegexIterator::GET_MATCH);
foreach($Regex as $f)
{
 $fs[]=$f[0];
}
return $fs;
}

function deleteSmallPhotos($dir)
{
    foreach (scanit(rtrim($dir, "/") . "") as $file) {
        if (is_dir($file)) {
            deleteSmallPhotos($file);
        } else {
            $image_info = getimagesize($file);
            $width = $image_info[0];
            $height = $image_info[1];
            if ($width * $height < 1000000) {
                // 1 megapixel = 1000000 pixels
                unlink($file); // delete the file
                echo "Deleted: $file\n";
            }
        }
    }
}

function deleteDuplicateFiles($dir)
{
    $files = [];
    foreach (scanit(rtrim($dir, "/") . "") as $file) {
        if (is_dir($file)) {
            deleteDuplicateFiles($file);
        } else {
            $hash = md5_file($file);
            if (isset($files[$hash])) {
                // Found a duplicate file
                echo "Deleting duplicate file: $file\n";
                unlink($file); // Delete the duplicate file
            } else {
                $files[$hash] = $file;
            }
        }
    }
}


$directory = "/storage/emulated/0/www/public/media/";
deleteSmallPhotos($directory);
deleteDuplicateFiles($directory);
