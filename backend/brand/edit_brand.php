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
    getCheckIdUrl("list_brand.php", $id);
    $record = getParamById($conn, "brands", $id, "list_brand.php");

    $oldImage = $record['image_url'];
    $imageData = '';
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $name = $_POST["name"];
        $link = $_POST["link"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and get set folder name, size and set max size image
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

        // Check validate form name, link, and sort order
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

        // Check validate form image when add new image
        if ($imageUrl != '') {

            if (!in_array($fileExtension, $allowImageExtension)) {
                $errors['image_url'] = 'The file extension must be png, jpg, or jpeg.';
            }
            if ($imageSize > $maxFileSize) {
                $errors['image_url'] = 'File size must be less than 5MB.';
            } else {
                if (!file_exists($folder)) {
                    $oldImagePath = '../theme/upload/brand/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $imageData = $imageUrl;
                    move_uploaded_file($tempName, $folder);
                } else {
                    $errors['image_url'] = 'Image already exists on the server, not overwritten';
                }
            }
            // When no add image, use old image
        } else {
            $imageData = $oldImage;
        }

        if (empty($errors)) {

            // Update value when change update new data
            $stmtUpdate = mysqli_prepare($conn, "UPDATE brands SET `name` = ?, `image_url` = ?, `link` = ?,`sort_order` = ?,`active` = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "sssssi", $name, $imageData, $link, $sortOrder, $active, $id);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {
                if ($imageUrl == null) {

                    if ($name == $record['name'] && $imageData == $record['image_url'] && $link == $record['link'] && $sortOrder == $record['sort_order'] && $active == $record['active']) {
                        $_SESSION['flash_message'] = [
                            'type' => '',
                            'message' => 'No change data'
                        ];
                        header("location: list_brand.php");
                        exit;
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'message' => 'Update brand successfully'
                        ];
                        header("location: list_brand.php");
                        exit;
                    }
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Update brand successfully'
                    ];
                    header("location: list_brand.php");
                    exit;
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Update brand failed'
                ];
                header("location: list_brand.php");
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
                                <i class="icon-th-list"></i>
                                <h3> Edit brands</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php echo (isset($record['name'])) ? $record['name'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="fileInput">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image_url" onchange="previewImage()"> </br>
                                                <?php echo '<img id="preview" src="../theme/upload/brand/' . $record['image_url'] . ' "display: none" " width="150px" "height="100px">' ?>
                                                <p class="error"><?php echo (isset($errors['image_url'])) ? $errors['image_url'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="link">Link <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="link" name="link" value="<?php echo (isset($record['link'])) ? $record['link'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['link'])) ? $errors['link'] : ''; ?></p>
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

                                        </br>
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