<?php

// Check if id is set
if (!isset($_GET['id'])){
    header("Location: index.html");
}

$id = $_GET['id'];

// Check if the id is valid
if (!preg_match('/^[a-zA-Z0-9.]+$/', $id)) {
    header("Location: index.html");
}

$file = __DIR__ . "/zips/$id.zip";

// check if the file exists
if (!file_exists($file)){
    header("Location: index.html");
}


// Delete files
unlink($file);

$imagesFolder = __DIR__ . "/uploads/$id";
$images = glob($imagesFolder . '/*');

// Delete all images inside the folder
foreach ($images as $image){

    if (is_file($image)){
        unlink($image);
    }
}

rmdir($imagesFolder);

// return to main page
header("Location: index.html");