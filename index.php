<?php
require_once "header.php";
require_once "config.php";

if ($_SERVER['REQUEST_METHOD'] = "POST" && isset($_POST['form1'])) {

    $uploadedImages = [];
    foreach ($_FILES['images']['name'] as $key => $images) {

        $image = [
            'name'      => $images,
            'tmp_name'  => $_FILES['images']['tmp_name'][$key],
            'size'      => $_FILES['images']['size'][$key],
        ];


        $name       = $image['name'];
        $tmp_name   = $image['tmp_name'];
        $size       = $image['size'];

        $allowExtension = ['jpg', 'png', 'jpeg'];
        $maxFileSize    = 1024 * 1024;
        $extension      = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowExtension)) {
            throw new Exception('Only jpg, jpeg and png images are supported.');
        }

        if ($size > $maxFileSize) {
            throw new Exception("Image size is too large.");
        }

        $image = time() . uniqid() . "." . $extension;
        $uploadPath = "./upload/$image";


        if (!move_uploaded_file($tmp_name, $uploadPath)) {
            throw new Exception("Failed to move uploaded file.");
        }

        $uploadedImages[] = $image;
    }

    $images = json_encode($uploadedImages);

    $sql = $conn->prepare("INSERT INTO image (images) VALUES (:images)");
    $sql->bindValue(':images', $images);
    $sql->execute();

    $conn = null;
}


?>




<div class="main">
    <h2 class="mb_10">Registration</h2>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" enctype="multipart/form-data">
        <table class="t2">

            <tr>
                <td>Images Upload</td>
                <td><input type="file" name="images[]" multiple autocomplete="off"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" value="Submit" name="form1"></td>
            </tr>
        </table>
    </form>
</div>
</div>
<?php require_once "footer.php"; ?>