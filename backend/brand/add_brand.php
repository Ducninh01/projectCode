<?php
    session_start();
    ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
    include "../common/header_lib.php";
?>
<body>
<?php
    include('../../configs/constant.php');
    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $name = $_POST["name"];
        $link = $_POST["link"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and set folder name, size and set max size image
        $imageUrl = $_FILES['image_url']['name'];
        $tempName = $_FILES['image_url']['tmp_name'];
        $fileExtension = pathinfo($_FILES["image_url"]["name"], PATHINFO_EXTENSION);
        $folder = '../theme/upload/brand/' . $imageUrl;
        $imageSize = $_FILES['image_url']['size'];
        $maxFileSize = 5 * 1024 * 1024;

        // Image format extension
        $allowImageExtension = [
            "png",
            "jpg",
            "jpeg"
        ];

        // Check validate form name, image, link, and sort order
        if (!checkRequire($imageUrl)) {
            $errors['image_url'] = 'Please select an image.';
        } else {
            if (!in_array($fileExtension, $allowImageExtension)) {
                $errors['image_url'] = 'The file extension must be png, jpg, or jpeg.';
            }
        }

        if ($imageSize > $maxFileSize) {
            $errors['image_url'] = 'File size must be less than 5MB.';
        }

        if (!checkRequire(trim($name))) {
            $errors["name"] = 'Please type name.';
        } else {
            if (!checkLength(strlen($name))) {
                $errors["name"] = 'Name must be more than 3 characters.';
            }
        }

        if (!checkRequire(trim($link))) {
            $errors["link"] = 'Please type link url.';
        } else {
            if (checkInvalidLink($link)) {
                $errors["link"] = 'Invalid link url.';
            }
        }

        if (!checkRequire(trim($sortOrder))) {
            $errors["sort_order"] = 'Please type sort order.';
        } else {
            if (checkInvalidNumber($sortOrder)) {
                $errors["sort_order"] = 'Invalid sort order format.';
            }
        }

        if (empty($errors)) {
            // If exist name and active = 0, active = 1, active != 2 , then show error message name already exists
            $query = "SELECT * FROM brands WHERE active != " . DELETE_ACTIVE . " AND name = ? ";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If isset result
            if (mysqli_num_rows($result) > 0) {

                $fetchResult = mysqli_fetch_assoc($result);

                if ($fetchResult['name'] == $name) {
                    $errors["name"] = 'Name already exists.';
                }
            } else {
                // Insert value in the table brands
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO brands (`name`,`image_url`,`link`,`sort_order`,`active`) VALUES (?,?,?,?,?)");
                mysqli_stmt_bind_param($stmtInsert, "sssss", $name, $imageUrl, $link, $sortOrder, $active);
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                if ($resultInsert) {
                    move_uploaded_file($tempName, $folder);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add brands successfully'
                    ];
                    header("location: list_brand.php");
                    exit;
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Error: Cannot add brands product'
                    ];
                }
            }
        }
    }
?>
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-th-list"></i>
                                <h3>Add brands</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php echo (isset($name)) ? $name : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="fileInput">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image_url" onchange="previewImage()">
                                                <img id="preview" alt="Preview Image" style="display: none;width:180px;">
                                                <p class="error"><?php echo (isset($errors['image_url'])) ? $errors['image_url'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="link">Link <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="link" name="link" value="<?php echo (isset($link)) ? $link : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['link'])) ? $errors['link'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Sort order <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="sort_order" name="sort_order" value="<?php echo (isset($sortOrder)) ? $sortOrder : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['sort_order'])) ?  $errors['sort_order'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="active">Active</label>
                                            <div class="controls">
                                                <select class="span6" name="active">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="list_brand.php" class="btn">Cancel</a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
    ob_end_flush();
    include "../common/extra.php";
    include "../common/footer.php";
    include "../common/footer_lib.php";
?>
</body>
</html>