<?php

// checks if id is set
if (!isset($_GET["id"])){

die("No ID provided");

}


$id = $_GET['id'];

$images = glob(__DIR__ . "/uploads/$id/*");

?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Converted Images</title>

<style>
    .download-container {
        margin-bottom: 30px;
    }

    .download-button {
        display: inline-block;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

        .delete-button {
        display: inline-block;
        padding: 12px 20px;
        background-color: #ff000d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
    }

    .download-button:hover {
        background-color: #0056b3;
    }

    .images {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .image-card {
        width: 250px;
    }

    .image-card img {
        width: 100%;
        display: block;
    }

    .quality-container {
    margin-bottom: 30px;
    font-size: 18px;
}
</style>


</head>

<body>


<h1>Converted Images</h1>

<!-- Download all images -->
<div class="download-container">

    <a 
        href="download.php?id=<?= urlencode($id); ?>"
        
        class="download-button"
    >
        Download All Images
    </a>

        <a 
        href="delete.php?id=<?= urlencode($id); ?>"
        
        class="delete-button"
    >
        Delete & return
    </a>

</div>

<?php 
// check if image format is a jpeg and show image quality
if(isset($_GET['imageFormat']) && $_GET['imageFormat'] === "jpeg"){

    $imageFormat = $_GET['imageFormat'];
    $jpegCompression = $_GET['compression'];

 ?>
<!-- JPEG Quality -->
<div class="quality-container">
    <strong>JPEG Quality:</strong>
    <span><?= htmlspecialchars($jpegCompression); ?></span>
</div>

<?php } ?>

<!-- Images -->
<div class="images">


<!-- loop trough every image and display them -->
<?php foreach($images as $image){

 $filename = basename($image);

 ?>

    <div class="image-card">
        <img src="<?= "uploads/$id/" . htmlspecialchars($filename) ?>" alt="Image">

    </div>

<?php } ?>
</div>


</body>
</html>



</body>
</html>
