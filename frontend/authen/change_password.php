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
    $customerIdSession = isset($_SESSION['id']) ? $_SESSION['id'] : '';

    $record = getParamById($conn, "customers", $customerIdSession, "../home/");
    $oldPassword = $record['password'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form.

        $password = $_POST["password"];
        $passwordEncrypt = md5($_POST["password"]);

        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];

        // Check validate form
        if (!checkRequire(trim($password))) {
            $errors['password'] = 'Please type password.';
        } else {
            if ($passwordEncrypt != $oldPassword) {
                $errors['password'] = 'The password you entered is incorrect.';
            }
        }

        if (!checkRequire($newPassword)) {
            $errors['new_password'] = 'Please type new password.';
        } else {
            if (!checkLengthPassword(strlen($newPassword))) {
                $errors["new_password"] = 'New password must be more than 5 characters.';
            }
        }

        if (!checkRequire(trim($confirmPassword))) {
            $errors["confirm_password"] = 'Please retype password.';
        } else {
            if ($newPassword != $confirmPassword) {
                $errors["confirm_password"] = 'Password not match, please try again.';
            }
        }

        // If empty errors , then update change password
        if (empty($errors)) {
            $newPasswordMd5 = md5($newPassword);
            $stmtUpdate = mysqli_prepare($conn, "UPDATE `customers` SET `password` = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "si", $newPasswordMd5, $customerIdSession);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'message' => 'Update password successfully'
                ];
                header("location: ../home/index.php");
                exit;
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'message' => 'Error: Cannot update password'
                ];
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
                            <input type="password" name="password" class="form-control" placeholder="Password" value="<?php echo isset($password) ? $password : ''; ?>">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                        </div>
                    </div> <br>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="new_password" class="form-control" placeholder="New Password">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['new_password']) ? $errors['new_password'] : ''; ?> </p>
                        </div>
                    </div><br>

                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="fm-checkbox"></div>
                    <button type="submit" class="btn btn-success notika-btn-success waves-effect">Change password</button>
                </div>
            </form>
            <div class="nk-navigation nk-lg-ic">
                <a href="../home/" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="">+</i> <span>Back to home</span></a>
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