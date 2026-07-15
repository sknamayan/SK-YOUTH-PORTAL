<?php
// Temporary script to create symbolic link on Hostinger
$target = '/home/u334873024/domains/dodgerblue-fox-693317.hostingersite.com/public_html/storage/app/public';
$link = '/home/u334873024/domains/dodgerblue-fox-693317.hostingersite.com/public_html/public/storage';

header('Content-Type: text/html; charset=utf-8');

if (file_exists($link)) {
    echo "Existing path found at $link. Attempting to delete...<br>";
    if (is_link($link)) {
        unlink($link);
        echo "Deleted existing symlink.<br>";
    } else {
        // If it is an empty dir or broken symlink
        @rmdir($link);
        @unlink($link);
        echo "Cleaned existing directory/file.<br>";
    }
}

echo "Creating symbolic link...<br>";
if (@symlink($target, $link)) {
    echo "<strong>SUCCESS:</strong> Symbolic link created successfully using PHP symlink()!";
} else {
    echo "PHP symlink() failed. Trying shell execution...<br>";
    @shell_exec("ln -s " . escapeshellarg($target) . " " . escapeshellarg($link));
    if (file_exists($link)) {
        echo "<strong>SUCCESS:</strong> Symbolic link created successfully using shell command!";
    } else {
        echo "<strong>FAILED:</strong> Unable to create symbolic link. Please contact administrator.";
    }
}
