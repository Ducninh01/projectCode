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
    getCheckIdUrl("list_product.php", $id);
    $record = getParamById($conn, "products", $id, "list_product.php");

    // Query name category and brand, if active = 1(show)
    $queryCategory = queryAll('categories', 'active =  ' . ACTIVE . ' ', 'id', 'DESC');
    $queryBrand = queryAll('brands', 'active =  ' . ACTIVE . ' ', 'id', 'DESC');

    $resultQueryCategory = mysqli_query($conn, $queryCategory);
    $resultQueryBrands = mysqli_query($conn, $queryBrand);

    $oldImage = $record['image']; // get old image

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get values form
        $categoryId = $_POST["category_id"];
        $brandId = $_POST["brand_id"];

        $name = $_POST["name"];
        $price = $_POST["price"];
        $oldPrice = $_POST["old_price"];
        $oldPriceEmpty = '';

        $description = strip_tags($_POST["description"]);
        $tags = $_POST['tags'];

        if (is_array($tags)) {
            // If tags is array -> implode string array
            $tagItString = implode(',', $tags);
        } else {
            // If tags is not array , save value tags
            $tagItString = $tags;
        }

        $isBestSell = $_POST["is_best_sell"];
        $isNew = $_POST["is_new"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and get set folder name, size and set max size image
        $image = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $folder = '../theme/upload/product/' . $image;
        $imageSize = $_FILES['image']['size'];
        $maxFileSize = 5 * 1024 * 1024;

        // Image format extension
        $allowImageExtension = [
            "png",
            "jpg",
            "jpeg"
        ];

        // Check validate form

        if (!checkRequire(trim($name))) {
            $errors["name"] = 'Please type name.';
        } else {
            if (!checkLength(strlen($name))) {
                $errors["name"] = 'Name must be more than 3 characters.';
            }
        }

        if (!checkRequire(trim($price))) {
            $errors["price"] = 'Please type price.';
        } else {
            if (checkInvalidNumber($sortOrder)) {
                $errors["price"] = 'Invalid price format.';
            } else {
                if ($oldPrice > 0) {
                    if ($price > $oldPrice) {
                        $errors["price"] = 'The price cannot be more than the old price.';
                    }
                }
            }
        }

        if (!checkRequire(trim($oldPrice))) {
            $oldPriceEmpty = 0;
        } else {
            if (checkInvalidNumber($oldPrice)) {
                $errors["old_price"] = 'Invalid old price format.';
            }
        }

        if (!checkRequire(trim($tags))) {
            $errors["tags"] = 'Please type tags.';
        } else {
            if (!checkLength(strlen($tags))) {
                $errors["tags"] = 'Tags must be more than 3 characters.';
            }
        }

        if (!checkRequire(trim($sortOrder))) {
            $errors["sort_order"] = 'Please type sort order.';
        } else {
            if (checkInvalidNumber($sortOrder)) {
                $errors["sort_order"] = 'Invalid sort order format.';
            }
        }

        if (!checkRequire(trim($description))) {
            $errors["description"] = 'Please type description.';
        }

        // Check validate form image when add new image
        if ($image != '') {
            if (!in_array($fileExtension, $allowImageExtension)) {
                $errors['image'] = 'The file extension must be png, jpg, or jpeg.';
            }
            if ($imageSize > $maxFileSize) {
                $errors['image'] = 'File size must be less than 5MB.';
            } else {
                // Remove old image if new image is uploaded and the new image is different from the old image
                if (!file_exists($folder)) {
                    $oldImagePath = '../theme/upload/product/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                    $imageData = $image;
                    move_uploaded_file($tempName, $folder);
                } else {
                    $errors['image'] = 'Image already exists on the server, not overwritten';
                }
            }
        } else {
            // When no add new image, use old image
            $imageData = $oldImage;
        }

        if (empty($errors)) {
            // Update value when change update new data
            $stmtUpdate = mysqli_prepare($conn, "UPDATE products SET `category_id` = ?, `brand_id` = ?, `name` = ?, `image` = ?, `price` = ?, `old_price` = ?, `description` = ?, `tags` = ?, `is_best_sell` = ?, `is_new` = ?, `sort_order` = ?, `active` = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "ssssssssssssi", $categoryId, $brandId, $name, $imageData, $price, $oldPrice, $description, $tagItString, $isBestSell, $isNew, $sortOrder, $active, $id);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {
                if ($image == null) {

                    if (
                        $categoryId == $record['category_id']  && $brandId == $record['brand_id'] && $name == $record['name'] && $imageData == $record['image'] && $price == $record['price']
                        && $oldPrice == $record['old_price'] && $description == $record['description'] & $tagItString == $record['tags']
                        && $isBestSell == $record['is_best_sell'] && $isNew == $record['is_new'] && $sortOrder == $record['sort_order']
                        && $active == $record['active']
                    ) {

                        $_SESSION['flash_message'] = [
                            'type' => '',
                            'message' => 'No change data'
                        ];
                        header("location: list_product.php");
                        exit;
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'message' => 'Update product successfully'
                        ];
                        header("location: list_product.php");
                        exit;
                    }
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Update product successfully'
                    ];
                    header("location: list_product.php");
                    exit;
                }
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Update product failed'
                ];
                header("location: list_product.php");
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
                                <i class="icon-table"></i>
                                <h3>Edit products</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="category_id">Category <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <select class="span6" name="category_id">
                                                    <?php
                                                    $numCategoies = mysqli_num_rows($resultQueryCategory);
                                                    if ($numCategoies > 0) {
                                                        echo '<option> ----- Category select -----</option>';

                                                        while ($row = mysqli_fetch_array($resultQueryCategory)) {

                                                            if ($record['category_id'] == $row['id']) {
                                                                echo '<option selected value="' . $row['id'] . '" >' . $row['name'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                                            }
                                                        }
                                                    } else {
                                                        echo '  <option>No category selected</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="brand_id">Brand <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <select class="span6" name="brand_id">
                                                    <?php
                                                    $numBrands = mysqli_num_rows($resultQueryBrands);
                                                    if ($numBrands > 0) {
                                                        echo '<option> ----- Brand select -----</option>';

                                                        while ($rowBrand = mysqli_fetch_array($resultQueryBrands)) {

                                                            if ($record['brand_id'] == $rowBrand['id']) {
                                                                echo '<option  selected value="' . $rowBrand['id'] . '" >' . $rowBrand['name'] . '</option>';
                                                            } else {
                                                                echo '<option value="' . $rowBrand['id'] . '">' . $rowBrand['name'] . '</option>';
                                                            }
                                                        }
                                                    } else {
                                                        echo '  <option> No brand selected</option>';
                                                    }

                                                    ?>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php echo isset($record['name']) ? $record['name'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="image">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image" onchange="previewImage()"> </br>
                                                <?php echo '<img id="preview" src="../theme/upload/product/' . $record['image'] . ' "display: none" " width="100px" "height="100px">' ?>
                                                <p class="error"><?php echo (isset($errors['image'])) ? $errors['image'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="price">Price <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="price" name="price" value="<?php echo isset($record['price']) ? $record['price'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['price'])) ? $errors['price'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="old_price">Old price <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="old_price" name="old_price" value="<?php echo isset($record['old_price']) ? $record['old_price'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['old_price'])) ? $errors['old_price'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="tags">Description</label>
                                            <div class="controls ckeditor span10">
                                                <textarea name="description" id="desc" class="span6">
                                                <?php echo (isset($record['description'])) ? $record['description'] : ''; ?>
                                                </textarea>
                                                <p class="error"><?php echo (isset($errors['description'])) ? $errors['description'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="myTags">Tags <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <ul class="span6" id="myTags"></ul>
                                                <input type="hidden" id="tagit-value" name="tags" value="<?php echo isset($record['tags']) ? $record['tags'] : ''; ?>">
                                                </br></br>
                                                <p class="error-edit-tags"><?php echo (isset($errors['tags'])) ? $errors['tags'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="is_best_sell">Is best sell</label>
                                            <div class="controls">
                                                <select class="span6" name="is_best_sell">
                                                    <?php if ($record['is_best_sell'] == ACTIVE) {
                                                        echo '  <option value="1">Yes</option>';
                                                        echo '  <option value="0">No</option>';
                                                    } else {
                                                        echo '  <option value="0">No</option>';
                                                        echo '  <option value="1">Yes</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="is_new">Is new</label>
                                            <div class="controls">
                                                <select class="span6" name="is_new">
                                                    <?php if ($record['is_new'] == ACTIVE) {
                                                        echo '  <option value="1">Yes</option>';
                                                        echo '  <option value="0">No</option>';
                                                    } else {
                                                        echo '  <option value="0">No</option>';
                                                        echo '  <option value="1">Yes</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Sort order <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="sort_order" name="sort_order" value="<?php echo isset($record['sort_order']) ? $record['sort_order'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['sort_order'])) ? $errors['sort_order'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="tags">Active</label>
                                            <div class="controls">
                                                <select class="span6" name="active">
                                                    <?php if ($record['active'] == ACTIVE) {
                                                        echo '  <option value="1">Active</option>';
                                                        echo '  <option value="0">Inctive</option>';
                                                    } else {
                                                        echo '  <option value="0">Inctive</option>';
                                                        echo '  <option value="1">Active</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary" id="add-tag">Save</button>
                                            <a href="list_product.php" class="btn">Cancel</a>
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
<script>
    CKEDITOR.replace('desc');

    $(document).ready(function() {
        $("#myTags").tagit();

        var tags = $("#tagit-value").val().split(",");
        for (var i = 0; i < tags.length; i++) {
            $("#myTags").tagit("createTag", tags[i]);
        }

        // Use button id add-tag
        $("#add-tag").on("click", function() {
            var tags = $("#myTags").tagit("assignedTags");
            $("#tagit-value").val(tags.join(","));
        });
    });
</script>
</html>