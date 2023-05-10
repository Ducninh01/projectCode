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
    include('../../configs/connect.php');
    include('../../configs/constant.php');
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');
    include('../common/functions.php');

    // Check product_id url
    $productId = '';

    if (isset($_GET['product_id']) && is_numeric($_GET['product_id']) && $_GET['product_id'] > 0) {
        $productId = $_GET['product_id'];
    } else {
        header("location: ../product/list_product.php");
        exit;
    }

    // $check = checkProductIdUrl($conn, "product_images",$productId,"../product/list_product.php");

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and set folder name, size and set max size image
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

        // Validate form image and sort order

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

        if (!checkRequire(trim($sortOrder))) {
            $errors["sort_order"] = 'Please type sort order.';
        } else {
            if (checkInvalidNumber($sortOrder)) {
                $errors["sort_order"] = 'Invalid sort order format.';
            }
        }

        if (empty($errors)) {
            // Query name and image , check exists
            $query = "SELECT * FROM product_images WHERE `image_url` = ? ";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "s", $imageUrl);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If exists , show error message
            if (mysqli_num_rows($result) > 0) {

                $fetchResult = mysqli_fetch_assoc($result);

                if ($fetchResult['image_url'] == $imageUrl) {
                    $errors["image_url"] = "Image already exists.";
                }
            } else {
                // If exist id of table product , then insert value in the table product_images
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO product_images (`image_url`,`sort_order`,`active`,`product_id`) VALUES (?,?,?,?)");
                mysqli_stmt_bind_param($stmtInsert, "ssii", $imageUrl, $sortOrder, $active, $productId);
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                // Check success or error
                if ($resultInsert) {
                    move_uploaded_file($tempName, $folder);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add product images successfully'
                    ];
                    header("location: list_gallery.php?product_id=" . $_GET['product_id']);
                    exit;
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Error: Cannot add gallery'
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
                                <i class="icon-picture"></i>
                                <h3>Add product images</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="image">Image <span class="color-red">*</span> </label>
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
                                            <label class="control-label" for="tags">Active</label>
                                            <div class="controls">
                                                <select class="span6" name="active">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
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