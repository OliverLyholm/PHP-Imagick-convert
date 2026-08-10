<?php

// Create new zip to add images
$zip = new ZipArchive();
$zipPath = tempnam(sys_get_temp_dir(), 'images_') . '.zip';

// Open zip
$zip->open($zipPath, ZipArchive::CREATE);

foreach($_FILES['uploaded_images']['tmp_name'] as $index => $tmPath) {
   
    if($_FILES['uploaded_images']['error'][$index] !== UPLOAD_ERR_OK){
        continue;
    }

    $newImage = new Imagick();
    $centerImage = new Imagick($tmPath);
    $backgroundImage = new Imagick($tmPath);

    $newImage->newImage(
        1080,
        1350,
        new ImagickPixel('transparent')
    );

    $width  = $newImage->getImageWidth();
    $height = $newImage->getImageHeight();

    // Blur background image
    $backgroundImage->blurImage(0, 10);

    // Crop background image
    $backgroundImage->cropThumbnailImage($width, $height);

    // Add background image to final image
    $newImage->compositeImage(
        $backgroundImage,
        Imagick::COMPOSITE_OVER,
        0,
        0
    );


    $CenterWidthPosition = ($width - $centerImage->getImageWidth()) / 2;
    $CenterHeightPosition = ($height - $centerImage->getImageHeight()) / 2;

    // Add and Center Album cover on final image
    $newImage->compositeImage(
        $centerImage,
        Imagick::COMPOSITE_OVER,
        (int) $CenterWidthPosition,
        (int) $CenterHeightPosition
    );

    // Set file format of final image
    $newImage->setImageFormat('png');

    // Export final image to Generated folder

    // $output = __DIR__ . "/generated/Image_{$index}.png";

    // $newImage->writeImage($output);

    // get image as data in memory
    $imageData = $newImage->getImagesBlob();

    // Add image data to zip file
    $zip->addFromString("Image_{$index}.png", $imageData);

    // Cleanup for next image
    $centerImage->clear();
    $backgroundImage->clear();
    $newImage->clear();
}

$zip->close();
header('content-Type: application/zip');
header('Content-Disposition: attachment; filename="converted_images.zip"');
header('Content-Length: ' . filesize($zipPath));

readfile($zipPath);

unlink($zipPath);

exit;

