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
    const DEFAULT_TOKEN = 0;

    include('../../configs/constant.php');
    include('../../configs/connect.php');

    include "../common/navigation.php";
    include "../common/branding.php";
    include('../common/functions.php');
    include "../common/menu.php";

    // Get value token và email url.

    $token = $_GET['token'];
    $email = $_GET['email'];

    // Check token url
    checkToken($conn, $email, $token);
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form

        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        // Validate form password and confirm password

        if (!checkRequire(trim($password))) {
            $errors['password'] = 'Please type password.';
        } else {
            if (!checkLengthPassword(trim($password))) {
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

        // If empty error message
        if (empty($errors)) {

            // Check token, email, password and confirm password no empty.
            if (!empty($token)) {
                if (!empty($email) && !empty($password) && !empty($confirmPassword)) {

                    // Query table customer get token and check token > 0.
                    $checkToken = $conn->prepare("SELECT * FROM customers WHERE token = ? LIMIT 1");
                    $checkToken->bind_param("s", $token);
                    $checkToken->execute();
                    $result = $checkToken->get_result();

                    if (mysqli_num_rows($result) > 0) {

                        $row = mysqli_fetch_assoc($result);
                        $newPasswordMd5 = md5($password);
                        $id = $row['id'];

                        // If password and confirm password are equal, then update new password.
                        if ($password == $confirmPassword) {
                            $stmtUpdate = mysqli_prepare($conn, "UPDATE customers SET `password`= ?,`token` =" . DEFAULT_TOKEN . " WHERE `token`= ? LIMIT 1");
                            mysqli_stmt_bind_param($stmtUpdate, "ss", $newPasswordMd5, $token);
                            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

                            if ($resultUpdate) {
                                $_SESSION['flash_message'] = [
                                    'type' => 'success',
                                    'message' => 'Update password successfully'
                                ];

                                header("location: login.php");
                                exit;
                            } else {
                                $_SESSION['flash_message'] = [
                                    'type' => 'error',
                                    'message' => 'Update password failed'
                                ];
                                exit;
                            }
                        } else {
                            echo 'Password not match, please try again.';
                        }
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'error',
                            'message' => 'Invalid token'
                        ];

                        header("location:login.php");
                        exit;
                    }
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'message' => 'Change password failed'
                    ];
                    header("location:reset_password.php");
                    exit;
                }
            } else {
                echo 'No token';
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
                        <h2>Change password</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="login-content">
        <div class="nk-block toggled" id="l-login">
            <form id="" method="POST">
                <div class="nk-form">

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
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
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