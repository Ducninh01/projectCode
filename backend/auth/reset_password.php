<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<?php
    include "../common/header_lib.php";
?>
<body>
    <?php
    include('../../configs/constant.php');
    include('../common/navigation_auth.php');
    include('../../configs/connect.php');
    include('../common/functions.php');

    // Get value token và email url.

    $token = $_GET['token'];
    $email = $_GET['email'];

    checkToken($conn, $email, $token);
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        // Check validate password
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

        if (empty($errors)) {

        // Check token, email, password and confirm password != empty.
            if (!empty($token)) {
                if (!empty($email) && !empty($password) && !empty($confirmPassword)) {

                    // Query table users get token and check token > 0.

                    $checkToken = $conn->prepare("SELECT * FROM users WHERE token = ? LIMIT 1");
                    $checkToken->bind_param("s", $token);
                    $checkToken->execute();
                    $result = $checkToken->get_result();

                    if (mysqli_num_rows($result) > 0) {

                        $row = mysqli_fetch_assoc($result);
                        $newPasswordMd5 = md5($password);
                        $id = $row['id'];

                        // If password and confirm password are equal, then update new password.

                        if ($password == $confirmPassword) {
                            $stmtUpdate = mysqli_prepare($conn, "UPDATE users SET `password`= ?,`token` = 0 WHERE `token`= ? LIMIT 1");
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

    <div class="account-container">
        <div class="content clearfix">
            <form action="" method="POST">
                <h1>Change password</h1>
                <div class="login-fields">
                    <div class="login-fields">
                        <input type="password" name="password" placeholder="New password" class="login password-field" />
                        <p class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                    </div>
                    <div class="login-fields">
                        <input type="password" name="confirm_password" placeholder="Confirm password" class="login password-field" />
                        <p class="error"><?php echo isset($errors['confirm_password']) ? $errors['confirm_password'] : ''; ?> </p>
                    </div>
                </div>
                <div class="login-actions">
                    <button class="button btn btn-success btn-large">Update Password</button>
                </div>
            </form>
        </div>
    </div>
<?php
    include "../common/extra_auth.php";
    include "../common/footer_lib.php";
?>
</body>
</html>