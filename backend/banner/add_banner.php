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
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');
    include('../../configs/connect.php');
    include('../common/functions.php');

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $title = $_POST["title"];
        $content = $_POST["content"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and get set folder name, size and set max size image
        $imageUrl = $_FILES['image_url']['name'];
        $tempName = $_FILES['image_url']['tmp_name'];
        $fileExtension = pathinfo($_FILES["image_url"]["name"], PATHINFO_EXTENSION);
        $folder = '../theme/upload/banner/' . $imageUrl;
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

        if (!checkRequire(trim($title))) {
            $errors["title"] = 'Please type title.';
        } else {
            if (!checkLength(strlen($title))) {
                $errors["title"] = 'Title must be more than 3 characters.';
            }
        }

        if (!checkRequire(trim($content))) {
            $errors["content"] = 'Please type content.';
        } else {
            if (!checkLength(strlen($content))) {
                $errors["content"] = 'Content must be more than 3 characters.';
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

            // If isset name and active = 0, active = 1, active != 2 , then show error message name already exists
            $query = "SELECT * FROM banners WHERE active != " . DELETE_ACTIVE . " AND name = ? ";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {

                $fetchResult = mysqli_fetch_assoc($result);

                if ($fetchResult['image_url'] == $imageUrl) {
                    $errors["image_url"] = "Image already exists.";
                }
            } else {
                // Insert value in the table banners
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO banners (`title`,`content`,`image_url`,`sort_order`,`active`) VALUES (?,?,?,?,?)");
                mysqli_stmt_bind_param($stmtInsert, "sssss", $title, $content, $imageUrl, $sortOrder, $active);
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                if ($resultInsert) {
                    move_uploaded_file($tempName, $folder);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add banner successfully'
                    ];
                    header("location: list_banner.php");
                    exit;
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Error: Cannot add banner'
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
                                <i class="icon-folder-open"></i>
                                <h3>Add banners</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Title <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="title" name="title" value="<?php echo (isset($title)) ? $title : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['title'])) ? $errors['title'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Content <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="content" name="content" value="<?php echo (isset($content)) ? $content : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['content'])) ? $errors['content'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image_url" onchange="previewImage()">
                                                <img id="preview" alt="Preview Image" style="display: none;width:170px;">
                                                <p class="error"><?php echo (isset($errors['image_url'])) ? $errors['image_url'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Sort order <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="sort_order" name="sort_order" value="<?php echo (isset($sortOrder)) ? $sortOrder : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['sort_order'])) ? $errors['sort_order'] : ''; ?></p>

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
                                            <a href="list_banner.php" class="btn">Cancel</a>
                                        </div>

                                    </form>
                                </div>
                            </div>
                            </br>
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