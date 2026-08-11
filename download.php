<?php

// Check if id is set
if (!isset($_GET['id'])){
    die("No ID provided");
}

$id = $_GET['id'];

$file = __DIR__ . "/zips/$id.zip";

var_dump($file);

if (!file_exists($file)){
    die("Zip File Not Found");
}

var_dump($id);

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="images.zip"');
header('Content-Length: ' . filesize($file));

readfile($file);

// exit;