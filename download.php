<?php

// Check if id is set
if (!isset($_GET['id'])){
    die("No ID provided");
}

$id = $_GET['id'];

// Check if the id is valid
if (!preg_match('/^[a-zA-Z0-9.]+$/', $id)) {
    die("Invalid ID");
}

$file = __DIR__ . "/zips/$id.zip";

// check if the file exists
if (!file_exists($file)){
    die("Zip File Not Found");
}


header('Content-Type: application/zip');
header(
    'Content-Disposition: attachment; filename="images_' . $id .'.zip"'
);
header('Content-Length: ' . filesize($file));

// Download the file
readfile($file);

// cleanup Delete files with id after download
unlink($file);

$imagesFolder = __DIR__ . "/uploads/$id";
$images = glob($imagesFolder . '/*');

foreach ($images as $image){

    if (is_file($image)){
        unlink($image);
    }
}

rmdir($imagesFolder);


exit;