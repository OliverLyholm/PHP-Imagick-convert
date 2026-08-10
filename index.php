<?php

$newImage = new Imagick();

$overlay = new Imagick(__DIR__ . '/Assets/To-the-unknown-750.jpg');



$newImage->newImage(

    1080,
    1350,
    new ImagickPixel('transparent')

);







$x = ($newImage->getImageWidth() - $overlay->getImageWidth()) / 2;
$y = ($newImage->getImageHeight() - $overlay->getImageHeight()) / 2;

$newImage->compositeImage(
    $overlay,
    Imagick::COMPOSITE_OVER,
    (int) $x,
    (int) $y
);

$newImage->setImageFormat('png');
$newImage->writeImage(__DIR__ . '/output.png');