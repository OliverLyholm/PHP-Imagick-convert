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

$imagesFolder = __DIR__ . "/uploads/$id";
$images = glob($imagesFolder . '/*');

if (count($images) === 1){
    $image = $images[0];

    header('Content-Type: ' . mime_content_type($image));
    header('Content-Disposition: attachment; filename="' . basename($image) . '"');
    header('Content-Length: ' . filesize($image));

    readfile($image);

    unlink($image);
    rmdir($imagesFolder);
    
    unlink($file);
    exit;
}


header('Content-Type: application/zip');
header(
    'Content-Disposition: attachment; filename="images_'
     . $id 
     .'.zip"'

     );
header('Content-Length: ' . filesize($file));

// Download the file
readfile($file);

// cleanup Delete files with id after download
unlink($file);



// Delete images from folder
foreach ($images as $image){

    if (is_file($image)){
        unlink($image);
    }
}

// delete images folder
rmdir($imagesFolder);


exit;