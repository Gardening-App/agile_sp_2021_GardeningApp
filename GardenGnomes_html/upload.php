<?php
date_default_timezone_set('America/Chicago');
if (!session_id()) {
    session_start();
}

require 'shopItems.php';
require 'sanitize.php';
require 'callQuery.php';
?>

<html>

<head>

    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/upload.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Indie+Flower&family=Roboto+Slab&family=Yellowtail&display=swap" rel="stylesheet">
    <title>Upload</title>
</head>
<?php
include("css/header.php");
?>

<body>
    <div class="parent">
        <?php
        require 'dbConnect.php';

        if (!session_id()) {
            session_start();
        }

        $target_dir = "images/profilepictures/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $file_temp = $_FILES['fileToUpload']['tmp_name'];
            $check = getimagesize($file_temp);

            // $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }

        // Check if file already exists
        // if (file_exists($target_file)) {
        //   echo "Sorry, file already exists.";
        //   $uploadOk = 0;
        // }

        // // Check file size
        // if ($_FILES["fileToUpload"]["size"] > 500000) {
        //   echo "Sorry, your file is too large.";
        //   $uploadOk = 0;
        // }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

                updateProfilePicture($pdo);

                echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }

        function updateProfilePicture($pdo)
        {

            $target_dir = "images/profilepictures/";
            $userID = $_SESSION['userID'];
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

            $sql = "UPDATE user SET profilepicture = '" . $target_file . "' WHERE userID = '" . $userID . "';";

            $preppedSql = $pdo->prepare($sql);

            $preppedSql->execute();
        }
        ?>


        <a href="profile.php" class="previous">&laquo; Profile</a>
    </div>
</body>

</html>