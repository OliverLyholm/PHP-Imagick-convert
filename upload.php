<?php

// Create new zip to add images
$zip = new ZipArchive();
$zipPath = tempnam(sys_get_temp_dir(), 'images_') . '.zip';

// Open zip
$zip->open($zipPath, ZipArchive::CREATE);

// Foreach loop to process each of the images 
foreach($_FILES['uploaded_images']['tmp_name'] as $index => $tmPath) {
   
    if($_FILES['uploaded_images']['error'][$index] !== UPLOAD_ERR_OK){
        continue;
    }

    // Variables for image creation
    $newImage = new Imagick();
    $centerImage = new Imagick($tmPath);
    $backgroundImage = new Imagick($tmPath);

    // Use the selected height and width if any are set else use default values

    if (isset($_POST["imageWidth"]) && $_POST["imageWidth"] != 0 && $_POST["imageWidth"] != null){
        $imageWidth = $_POST["imageWidth"];
    } else {
        $imageWidth = 1080;
    }

    if (isset($_POST["imageHeight"]) && $_POST["imageHeight"] != 0 && $_POST["imageHeight"] != null){
        $imageHeight = $_POST["imageHeight"];
    } else {
        $imageHeight = 1350;
    }

    // blank base image
    $newImage->newImage(
        (int) $imageWidth,
        (int) $imageHeight,
        new ImagickPixel('transparent')
    );

    // Variables for getting height and width of base image
    $width  = $newImage->getImageWidth();
    $height = $newImage->getImageHeight();



    // Crop background image
    $backgroundImage->cropThumbnailImage($width, $height);

    // Blur background image
    $backgroundImage->blurImage(0, 10);

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

    // Compress png image
    $newImage->setImageCompression(Imagick::COMPRESSION_ZIP);
    $newImage->setImageCompressionQuality(6);

    // Temporary png path
    $pngPath = tempnam(sys_get_temp_dir(), 'image_') . '.png';

    $newImage->writeImage($pngPath);

    // Add temporary png path to zip
    $zip->addFile(
        $pngPath,
        "Image_{$index}.png"
    );


    // Cleanup for next image
    $centerImage->clear();
    $centerImage->destroy();

    $backgroundImage->clear();
    $backgroundImage->destroy();

    $newImage->clear();
    $newImage->destroy();
}

$zip->close();
header('content-Type: application/zip');
header('Content-Disposition: attachment; filename="converted_images.zip"');
header('Content-Length: ' . filesize($zipPath));

readfile($zipPath);

unlink($zipPath);

exit;

