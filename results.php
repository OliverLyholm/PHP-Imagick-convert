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
        
        class="download-button"
    >
        Delete & return
    </a>

    

</div>

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
