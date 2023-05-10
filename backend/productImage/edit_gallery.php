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

    $id = $_GET['id'] ? $_GET['id'] : '';
    $productId = $_GET['product_id'] ? $_GET['product_id'] : '' ;

    $checkProductId = checkProductIdUrl($conn, "product_images", $productId, "../product/list_product.php");
    $record = getParamById($conn, "product_images", $id, "../product/list_product.php");

    $oldImage = $record['image_url'];
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        $imageUrl = $_FILES['image_url']['name'];
        $tempName = $_FILES['image_url']['tmp_name'];
        $fileExtension = pathinfo($_FILES["image_url"]["name"], PATHINFO_EXTENSION);
        $folder = '../theme/upload/gallery/' . $imageUrl;
        $imageSize = $_FILES['image_url']['size'];
        $maxFileSize = 5 * 1024 * 1024;

        // Image format extension
        $allowImageExtension = [
            "png",
            "jpg",
            "jpeg"
        ];

        // Validate form sort order and image

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
                    $oldImagePath = '../theme/upload/gallery/' . $oldImage;
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
            $stmtUpdate = mysqli_prepare($conn, "UPDATE product_images SET `image_url` = ?, `sort_order` = ? ,`active` = ? WHERE id = ?");
            if (!$stmtUpdate) {
            }
            mysqli_stmt_bind_param($stmtUpdate, "sssi", $imageData, $sortOrder, $active, $id);
            mysqli_stmt_execute($stmtUpdate);

            if ($imageUrl == null) {
                if (
                    $imageData == $record['image_url'] && $sortOrder == $record['sort_order'] &&
                    $active == $record['active'] && $productId == $record['product_id']
                ) {
                    $_SESSION['flash_message'] = [
                        'type' => '',
                        'message' => 'No change data'
                    ];
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Update product images successfully'
                    ];
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Update product images successfully'
                ];
            }

            header("location: list_gallery.php?product_id=" . $_GET['product_id']);
            exit;
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Update product images failed'
            ];
            header("location: list_gallery.php?product_id=" . $_GET['product_id']);
            exit;
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
                                <h3>Edit product images</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="image">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image_url" onchange="previewImage()"></br>
                                                <?php echo '<img id="preview" src="../theme/upload/gallery/' . $record['image_url'] . ' "display: none" " width="100px" "height="100px">' ?>
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
                                            <a href="list_gallery.php?product_id=<?php echo $_GET['product_id']; ?>" class="btn">Cancel</a>
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