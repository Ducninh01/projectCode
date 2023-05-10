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

    $id = $_GET['id'];
    getCheckIdUrl("list_banner.php", $id);
    $record = getParamById($conn, "banners", $id, "list_banner.php");

    $oldImage = $record['image_url'];
    $imageData = '';
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

        // Check validate form title, content, and sort order
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

        // Check validate form image when add new image
        if ($imageUrl != '') {
            if (!in_array($fileExtension, $allowImageExtension)) {
                $errors['image_url'] = 'The file extension must be png, jpg, or jpeg.';
            }
            if ($imageSize > $maxFileSize) {
                $errors['image_url'] = 'File size must be less than 5MB.';
            } else {
                // Remove old image if new image is uploaded and the new image is different from the old image
                if (!file_exists($folder)) {
                    $oldImagePath = '../theme/upload/banner/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $imageData = $imageUrl;
                    move_uploaded_file($tempName, $folder);
                } else {
                    $errors['image_url'] = 'Image already exists on the server, not overwritten';
                }
            }
        } else {
            // When no add new image, use old image
            $imageData = $oldImage;
        }

        if (empty($errors)) {

            // Update value when change update new data
            $stmtUpdate = mysqli_prepare($conn, "UPDATE banners SET `title` = ?, `content` = ? ,`image_url` = ?,`sort_order` = ?, `active` = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "sssssi", $title, $content, $imageData, $sortOrder, $active, $id);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {

                if ($imageUrl == null) {

                    if ($title == $record['title']  && $content == $record['content'] && $imageData == $record['image_url'] && $sortOrder == $record['sort_order'] && $active == $record['active']) {
                        $_SESSION['flash_message'] = [
                            'type' => '',
                            'message' => 'No change data'
                        ];
                        header("location: list_banner.php");
                        exit;
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'message' => 'Update banner successfully'
                        ];
                        header("location: list_banner.php");
                        exit;
                    }
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Update banner successfully'
                    ];
                    header("location: list_banner.php");
                    exit;
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Update brand failed'
                ];
                header("location: list_banner.php");
                exit;
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
                                <i class="icon-user"></i>
                                <h3> Edit banners</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Title <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="title" name="title" value="<?php echo (isset($record['title'])) ? $record['title'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['title'])) ? $errors['title'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Content <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="content" name="content" value="<?php echo (isset($record['content'])) ? $record['content'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['content'])) ? $errors['content'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image_url" onchange="previewImage()"></br>
                                                <?php echo '<img id="preview" src="../theme/upload/banner/' . $record['image_url'] . ' "display: none" " width="100px"">' ?>
                                                <p class="error"><?php echo (isset($errors['image_url'])) ? $errors['image_url'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Sort order <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="sort_order" name="sort_order" value="<?php echo (isset($record['sort_order'])) ? $record['sort_order'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['sort_order'])) ? $errors['sort_order'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="active">Active</label>
                                            <div class="controls">
                                                <select class="span6" name="active">
                                                    <?php
                                                    if ($record['active'] == ACTIVE) {
                                                        echo ' <option value="1">Active</option>';
                                                        echo ' <option value="0">Inactive</option>';
                                                    } else {
                                                        echo ' <option value="0">Inactive</option>';
                                                        echo ' <option value="1">Active</option>';
                                                    }
                                                    ?>
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