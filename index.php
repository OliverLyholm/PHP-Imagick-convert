<?php

$image = new Imagick();



$image->newImage(

    1080,
    1350,
    new ImagickPixel('transparent')

);

$image->setImageFormat('png');

var_dump($image);