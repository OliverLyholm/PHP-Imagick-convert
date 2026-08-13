<?php

// check if there is any image uploaded
if (!isset($_FILES['uploaded_images']) OR 
    
    $_FILES['uploaded_images']['error'][0] === UPLOAD_ERR_NO_FILE){

    die('No image provided');
}



// check if JPEG compression is set
if(!isset($_POST['compression'])){
    die('jpeg compression not set');
}


// variables from form

// if no format is selected use jpeg as default
$formats = $_POST['formats'] ?? ['jpeg'];

if(isset($_POST['maxFileSize'])){

    $maxFileSize = $_POST['maxFileSize'] * 1024;

}

var_dump($maxFileSize);

$jpegCompression = $_POST['compression'];


// Create new zip to add images
$zip = new ZipArchive();
$zipPath = tempnam(sys_get_temp_dir(), 'images_') . '.zip';

// Open zip
$zip->open($zipPath, ZipArchive::CREATE);

$conversionId = uniqid('', true);

// Directories
$resultsDir = __DIR__ . '/uploads/' . $conversionId;
$zipsDir = __DIR__ . '/zips';

// Make sure the directories exist
if (!is_dir($resultsDir) && !mkdir($resultsDir, 0777, true)) {
    die('Could not create results directory: ' . $resultsDir);
}

if (!is_dir($zipsDir) && !mkdir($zipsDir, 0777, true)) {
    die('Could not create ZIP directory: ' . $zipsDir);
}




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
        $imageWidth = $centerImage->getImageWidth() + 250;
    }

    if (isset($_POST["imageHeight"]) && $_POST["imageHeight"] != 0 && $_POST["imageHeight"] != null){
        $imageHeight = $_POST["imageHeight"];
    } else {
        $imageHeight = $centerImage->getImageHeight() + 250;
    }

    // Use selected Center image height and width else use default of original image
        if (isset($_POST["centerImageWidth"]) && $_POST["centerImageWidth"] != 0 && $_POST["centerImageWidth"] != null){
        $centerImageWidth = $_POST["centerImageWidth"];
    } else {
        $centerImageWidth = $centerImage->getImageWidth();
    }

    if (isset($_POST["centerImageHeight"]) && $_POST["centerImageHeight"] != 0 && $_POST["centerImageHeight"] != null){
        $centerImageHeight = $_POST["centerImageHeight"];
    } else {
        $centerImageHeight = $centerImage->getImageHeight();
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

    // resize center image to the selected size 
    $centerImage->resizeImage(
        $centerImageWidth,
        $centerImageHeight,
        Imagick::FILTER_LANCZOS,
        1
    );

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


    // make a version of the image with every selected image format
    foreach ($formats as $imageFormat){

       

        if(!isset($imageFormat) OR $imageFormat == null){
            $imageFormat = 'jpeg';
        }

             //Create Image Path
            $imagePath = $resultsDir . '/Image_' . $index . '.' . $imageFormat;

            $outputImage = clone($newImage);

            switch($imageFormat){

                // check if image format is jpeg and compress
                case 'jpeg':

                $outputImage->setImageFormat('jpeg');

                $minQuality = 1;
                $maxQuality = 100;
                $bestQuality = $minQuality;

                // Guess 5 times max for end result
                for ($guess = 1; $guess <= 5; $guess++) {

                    // find midpoint between max and min quality
                    $quality = (int) floor(($minQuality + $maxQuality) / 2);

                    $outputImage->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $outputImage->setImageCompressionQuality($quality);

                    $outputImage->writeImage($imagePath);

                    clearstatcache(true, $imagePath);

                    $fileSize = filesize($imagePath);

                    // check if the filesize is bigger than the max file size
                    if ($fileSize <= $maxFileSize) {
                        $bestQuality = $quality;

                        // if quality fits check if there is something better
                        $minQuality = $quality + 1;
                    } else {
                       // if quality doesnt fit try lower
                        $maxQuality = $quality - 1;
                    }

                    // break if thre is nothing left to earch trough
                    if ($minQuality > $maxQuality) {
                        break;
                    }
                }

                // write image with best quality
                $outputImage->setImageCompression(Imagick::COMPRESSION_JPEG);
                $outputImage->setImageCompressionQuality($bestQuality);
                $outputImage->writeImage($imagePath);

                break;
            
                // check if image format is png and set format
                case 'png':
                    $outputImage->setImageFormat('png');
                    $outputImage->writeImage($imagePath);
                break;
            
                // check if image format is webp and compress
                case 'webp':
                    $outputImage->setImageFormat('webp');
                    $outputImage->setImageCompressionQuality($jpegCompression);
                    $outputImage->writeImage($imagePath);
                break;
            
                // check if image format is gif and set format
                case 'gif':
                    $outputImage->setImageFormat('gif');

                    $outputImage->writeImage($imagePath);
                break;



        }




    // Add temporary png path to zip
    $zip->addFile(
        $imagePath,
        "Image_{$index}.{$imageFormat}"
    );

    }

    // Cleanup for next image
    $centerImage->clear();
    $centerImage->destroy();

    $backgroundImage->clear();
    $backgroundImage->destroy();

    $newImage->clear();
    $newImage->destroy();
}   

// Close ZIP
$zip->close();

// Save ZIP permanently
$finalZipPath = $zipsDir . '/' . $conversionId . '.zip';

// Move temporary ZIP to permanent location
if (!rename($zipPath, $finalZipPath)) { die('Could not save ZIP file.'); }



// query for all data that gets sent to results.php
$dataQuery = http_build_query([
    'id' => $conversionId,
    'compression' => $jpegCompression,
    'imageFormat' => $formats
]);


// Redirect to results page
header(
    'Location: results.php?' . $dataQuery
);
exit;
