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

    // Query name category and brand, where active = 1(show)
    $queryCategory = queryAll('categories', 'active = ' . ACTIVE . ' ', 'id', 'DESC');
    $queryBrand = queryAll('brands', 'active = ' . ACTIVE . ' ', 'id', 'DESC');

    $resultCategory = mysqli_query($conn, $queryCategory);
    $resultBrands = mysqli_query($conn, $queryBrand);

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
            // If there are multiple tags -> convert to string
            $tagItString = implode(',', $tags);
        } else {
            // If tags is not array , save value tags
            $tagItString = $tags;
        }

        $isBestSell = $_POST["is_best_sell"];
        $isNew = $_POST["is_new"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Get values image form and set folder name, size and set max size image
        $image = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];
        $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $folder = '../theme/upload/product/' . $image;
        $imageSize = $_FILES['image']['size'];
        $maxFileSize = 5 * 1024 * 1024; // file upload max size is 5 MB 
        
        // Image format extension
        $allowImageExtension = [
            "png",
            "jpg",
            "jpeg"
        ];

        // Validate form data

        if ($categoryId == "----- Category select -----") {
            $errors["category_id"] = 'Please select a category.';
        }

        if ($brandId == "----- Brand select -----") {
            $errors["brand_id"] = 'Please select a brand.';
        }

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
            if (checkInvalidNumber($price)) {
                $errors["price"] = 'Invalid price format.';
            }
            if ($price <= 0) {
                $errors["price"] = 'Invalid price.';
            } else {
                if (!empty($oldPrice)) {
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

        // Validate image
        if (!checkRequire($image)) {
            $errors['image'] = 'Please select an image.';
        } else {
            if (!in_array($fileExtension, $allowImageExtension)) {
                $errors['image'] = 'The file extension must be png, jpg, or jpeg.';
            }
        }

        if ($imageSize > $maxFileSize) {
            $errors['image'] = 'File size must be less than 5MB.';
        }

        if (empty($errors)) {

            // Query name and image , check exists
            $query = "SELECT * FROM products WHERE `name`= ? OR `image` = ? ";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $name, $image);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If exists , show error message
            if (mysqli_num_rows($result) > 0) {

                $fetchResult = mysqli_fetch_assoc($result);

                if ($fetchResult['name'] == $name) {
                    $errors["name"] = 'Name already exists.';
                }
                if ($fetchResult['image'] == $image) {
                    $errors["image"] = "Image already exists.";
                }
            } else {
                // If no record exists , then insert value in the table products

                if (empty($oldPrice)) {

                    $stmtInsert = mysqli_prepare($conn, "INSERT INTO products (`category_id`, `brand_id`,`name`,`image`,`price`,`old_price`,`description` ,`tags`,`is_best_sell`,`is_new`,`sort_order`, `active`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmtInsert, "ssssssssssss", $categoryId, $brandId, $name, $image, $price, $oldPriceEmpty, $description, $tagItString, $isBestSell, $isNew, $sortOrder, $active);
                } else {
                    $stmtInsert = mysqli_prepare($conn, "INSERT INTO products (`category_id`, `brand_id`,`name`,`image`,`price`,`old_price`,`description` ,`tags`,`is_best_sell`,`is_new`,`sort_order`, `active`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($stmtInsert, "ssssssssssss", $categoryId, $brandId, $name, $image, $price, $oldPrice, $description, $tagItString, $isBestSell, $isNew, $sortOrder, $active);
                }
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                if ($resultInsert) {
                    move_uploaded_file($tempName, $folder);
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add product successfully'
                    ];
                    header("location: list_product.php");
                    exit;
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Error: Cannot add product'
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
                                <i class="icon-table"></i>
                                <h3>Add products</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

                                        <div class="control-group">
                                            <label class="control-label" for="category_id">Category <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <select class="span6" name="category_id">
                                                    <?php
                                                    $numCategories = mysqli_num_rows($resultCategory);
                                                    if ($numCategories > 0) {
                                                        echo '<option> ----- Category select ----- </option>';
                                                        while ($row = mysqli_fetch_array($resultCategory)) {
                                                            $selected = ($categoryId == $row['id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                                        }
                                                    } else {
                                                        echo ' <option>No category selected</option>';
                                                    }
                                                    ?>
                                                </select>

                                                <p class="error"><?php echo (isset($errors['category_id'])) ? $errors['category_id'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="brand_id">Brand <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <select class="span6" name="brand_id">
                                                    <?php
                                                    $numBrands = mysqli_num_rows($resultBrands);
                                                    if ($numBrands > 0) {
                                                        echo '<option> ----- Brand select ----- </option>';
                                                        while ($row = mysqli_fetch_array($resultBrands)) {
                                                            $selected = ($brandId == $row['id']) ? 'selected' : '';
                                                            echo '<option value="' . $row['id'] . '" ' . $selected . '>' . $row['name'] . '</option>';
                                                        }
                                                    } else {
                                                        echo ' <option>No brand selected</option>';
                                                    }
                                                    ?>
                                                </select>

                                                <p class="error"><?php echo (isset($errors['brand_id'])) ? $errors['brand_id'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php echo isset($name) ? $name : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="image">Image <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="file" class="span6" id="fileInput" name="image" onchange="previewImage()">
                                                <img id="preview" alt="Preview Image" style="display: none;width:170px;">
                                                <p class="error"><?php echo (isset($errors['image'])) ? $errors['image'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="price">Price <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="price" name="price" value="<?php echo isset($price) ? $price : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['price'])) ? $errors['price'] : ''; ?></p>

                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="old_price">Old price</label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="old_price" name="old_price" value="<?php echo isset($oldPrice) ? $oldPrice : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['old_price'])) ? $errors['old_price'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="desc">Description <span class="color-red">*</span> </label>
                                            <div class="controls ckeditor span10 ">
                                                <textarea name="description" id="desc" class="span6">
                                                <?php echo (isset($description)) ? $description : ''; ?>
                                                </textarea>
                                                <p class="error"><?php echo (isset($errors['description'])) ? $errors['description'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="myTags">Tags <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <div class="span6">
                                                    <input type="text" class="input-tags" id="myTags" name="tags" value="<?php echo isset($tags) ? $tags : ''; ?>">
                                                </div>
                                                <input type="hidden" id="tagit-value" name="tags" value="<?php echo isset($tags) ? $tags : ''; ?>">
                                                <br> <br>
                                                <p class="error-tags"><?php echo (isset($errors['tags'])) ? $errors['tags'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="is_best_sell">Is best sell</label>
                                            <div class="controls">
                                                <select class="span6" name="is_best_sell">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div></br>

                                        <div class="control-group">
                                            <label class="control-label" for="is_new">Is new</label>
                                            <div class="controls">
                                                <select class="span6" name="is_new">
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div></br>

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
        var tags = $("#tagit-value").val().split(",");
        $("#myTags").tagit({
            availableTags: tags,
            allowSpaces: true,
        });

        // use button id is add-tag
        $("#add-tag").on("click", function() {
            var tags = $("#myTags").tagit("assignedTags");
            $("#tagit-value").val(tags.join(","));
        });

    });
</script>
</html>