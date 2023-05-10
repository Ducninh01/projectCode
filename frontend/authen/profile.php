<?php
    session_start();
    ob_start();
?>
<!doctype html>
<html class="no-js" lang="">
<?php
    include "../common/header_js.php";
    include('../common/header_auth_js.php');
?>
<body>
<?php
    include('../../configs/constant.php');
    include('../../configs/connect.php');

    include "../common/navigation.php";
    include "../common/branding.php";
    include('../common/functions.php');
    include "../common/menu.php";

    // Get value id url and check id url
    $id = $_GET['id'];
    getCheckIdUrl("../home/", $id);
    $record = getParamById($conn, "customers", $id, "../home/");

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form.
        $name = $_POST["name"];
        $phone = $_POST["phone"];

        // Check validate form

        if (!checkRequire($name)) {
            $errors['name'] = 'Please type name.';
        } else {
            if (!checkLength(strlen($name))) {
                $errors["name"] = 'Name must be more than 3 characters.';
            } else {
                if (checkInvalidUserName($name)) {
                    $errors["name"] = 'Invalid name format.';
                }
            }
        }

        if (!checkRequire(trim($phone))) {
            $errors['phone'] = 'Please type phone.';
        } else {
            if (checkInvalidMobilePhone($phone)) {
                $errors["phone"] = 'Invalid phone format.';
            }
        }

        // if empty error then update new username
        if (empty($errors)) {

            $stmtUpdate = mysqli_prepare($conn, "UPDATE customers SET `name` = ?, `phone` = ?  WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "ssi", $name, $phone, $id);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {
                header("location: ../home/");
                exit;
            }
        }
    }
?>
    <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Profile</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-content">
        <div class="nk-block toggled" id="l-login">
            <form id="" method="POST">
                <div class="nk-form">

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo isset($record['name']) ? $record['name'] : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['name']) ? $errors['name'] : ''; ?> </p>
                        </div>
                    </div><br>


                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="phone" class="form-control" placeholder="Phone" value="<?php echo isset($record['phone']) ? $record['phone'] : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?> </p>
                        </div>
                    </div><br>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo isset($record['email']) ? $record['email'] : ''; ?>" disabled>
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                        </div>
                    </div><br>

                    <div class="fm-checkbox"></div>

                    <button type="submit" class="btn btn-success notika-btn-success waves-effect">Update</button>
                </div>
            </form>
            <div class="nk-navigation rg-ic-stl">
                <a href="../home/" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>+</i><span>Back to home</span></a>
            </div>
        </div>
    </div>
<?php
    ob_end_flush();
    include('../common/footer_auth_js.php');
    include "../common/footer_top.php";
    include "../common/footer_bottom.php";
    include "../common/footer_js.php";
?>
</body>
</html>