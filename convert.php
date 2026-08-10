<?php

$images = glob(__DIR__ . '/Assets/*.{jpg,jpeg,png}', GLOB_BRACE);




foreach($images as $image){

    $newImage = new Imagick();
    $centerImage = new Imagick($image);
    $BackgroundImage = new Imagick($image);
    $imageName = pathinfo($image, PATHINFO_FILENAME);

    // Create new blank image
    $newImage->newImage(

        1080,
        1350,
        new ImagickPixel('transparent')

    );

    
    $width  = $newImage->getImageWidth();
    $height = $newImage->getImageHeight();

    // Blur background image
    $BackgroundImage->blurImage(0, 10);

    // Crop background image
    $BackgroundImage->cropThumbnailImage($width, $height);

    // Add background image to final image
    $newImage->compositeImage(
        $BackgroundImage,
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

    $output = __DIR__ . "/Generated/{$imageName}_Converted.png";

    $newImage->writeImage($output);

    // Cleanup for next image
    $centerImage->clear();
    $BackgroundImage->clear();
    $newImage->clear();

}