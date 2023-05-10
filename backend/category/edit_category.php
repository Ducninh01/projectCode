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
    $record = getParamById($conn, "categories", $id, "list_category.php");
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $name = $_POST["name"];
        $sortOrder = $_POST["sort_order"];
        $active = $_POST["active"];

        // Check validate form empty name and sort order

        if (!checkRequire(trim($name))) {
            $errors["name"] = 'Please type name.';
        } else {
            if (!checkLength(strlen($name))) {
                $errors["name"] = 'Name must be more than 3 characters.';
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
            $query = "SELECT * FROM categories WHERE  active != " . DELETE_ACTIVE . " AND name = ? AND id <> ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "si", $name, $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If isset result
            if (mysqli_num_rows($result) > 0) {
                $errors["name"] = 'Name already exists.';
            } else {

                // If record == value input, show message no data changed
                if ($name == $record['name'] && $sortOrder == $record['sort_order'] && $active == $record['active']) {
                    $_SESSION['flash_message'] = [
                        'type' => '',
                        'message' => 'No change data'
                    ];

                    header("location: list_category.php");
                    exit;
                } else {

                    // If record != value input, then update the category
                    $stmtUpdate = mysqli_prepare($conn, "UPDATE categories SET `name` = ?, `sort_order` = ?, `active` = ? WHERE id = ?");
                    mysqli_stmt_bind_param($stmtUpdate, "sssi", $name, $sortOrder, $active, $id);
                    $resultUpdate = mysqli_stmt_execute($stmtUpdate);

                    if ($resultUpdate) {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'message' => 'Update categories successfully'
                        ];

                        header("location: list_category.php");
                        exit;
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'error',
                            'message' => 'Update categories failed'
                        ];

                        header("location: list_category.php");
                        exit;
                    }
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
                                <i class="icon-sitemap"></i>
                                <h3> Edit categories</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" class="form-horizontal" method="POST">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php if (isset($record['name'])) echo $record['name']; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Sort order  <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="sort_order" name="sort_order" value="<?php if (isset($record['sort_order'])) echo $record['sort_order']; ?>">
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
                                            <a href="list_category.php" class="btn">Cancel</a>
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