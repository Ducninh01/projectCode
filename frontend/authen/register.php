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

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form.
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];

        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

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

        if (!checkRequire(trim($email))) {
            $errors["email"] = 'Please type email.';
        } else {
            if (checkInvalidEmail($email)) {
                $errors["email"] = 'Invalid email format.';
            }
        }

        if (!checkRequire(trim($phone))) {
            $errors['phone'] = 'Please type phone.';
        } else {
            if (checkInvalidMobilePhone($phone)) {
                $errors["phone"] = 'Invalid phone format.';
            }
        }

        if (!checkRequire($password)) {
            $errors['password'] = 'Please type password.';
        } else {
            if (!checkLengthPassword(strlen($password))) {
                $errors["password"] = 'Password must be more than 5 characters.';
            }
        }

        if (!checkRequire(trim($confirmPassword))) {
            $errors["confirm_password"] = 'Please retype password.';
        } else {
            if ($password != $confirmPassword) {
                $errors["confirm_password"] = 'Password not match, please try again.';
            }
        }

        // If no errors message
        if (empty($errors)) {
            // Query name and email check already exists
            $query = "SELECT * FROM customers WHERE `name`= ? OR `email`= ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ss", $name, $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If isset record exists, show message
            if (mysqli_num_rows($result) > 0) {

                $fetchResult = mysqli_fetch_assoc($result);

                if ($fetchResult['name'] == $name) {
                    $errors["name"] = 'name already exists.';
                } else if ($fetchResult['email'] == $email) {
                    $errors["email"] = "Email already exists.";
                }
            } else {
                //Encrypt password and insert value in the table customers
                $passwordMd5 = md5($password);
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO customers (`name`,`email`,`password`,`phone`) VALUES (?,?,?,?)");
                mysqli_stmt_bind_param($stmtInsert, "ssss", $name, $email, $passwordMd5, $phone);
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                if ($resultInsert) {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add customer successfully'
                    ];
                    header("location:login.php");
                    exit;
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Error: Cannot add customer'
                    ];
                }
            }
        }
    }
?>
    <div class="login-content">
        <div class="nk-block toggled" id="l-login">
            <form id="" method="POST">
                <div class="nk-form">

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo isset($name) ? $name : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['name']) ? $errors['name'] : ''; ?> </p>
                        </div>
                    </div> <br>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                        </div>
                    </div><br>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="text" name="phone" class="form-control" placeholder="Phone" value="<?php echo isset($phone) ? $phone : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['phone']) ? $errors['phone'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                            <p class="error" style="color:red;float:left"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="fm-checkbox"></div>

                    <button type="submit" class="btn btn-success notika-btn-success waves-effect">Register</button>
                </div>
            </form>
            <div class="nk-navigation rg-ic-stl">
                <a href="login.php" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="">+</i> <span>Login</span></a>
                <a href="forgot_password.php" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
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