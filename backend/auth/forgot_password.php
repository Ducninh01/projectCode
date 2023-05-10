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
    include('../common/send_mail.php');

    $errors = [];
    $message = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form email and set token
        $email = $_POST["email"];
        $token = md5(rand());

        // Check validate form email
        if (!checkRequire(trim($email))) {
            $errors["email"] = 'Please type email.';
        } else {
            if (!preg_match('/^[\w.-]+@([\w-]+\.)+[\w-]{2,4}$/', $email)) {
                $errors["email"] = 'Invalid email format.';
            }
        }

        /*
            If no errors message, query check email.
            If check email ok, then send mail reset password.
        */

        if (empty($errors)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE `email`= ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $numRows = mysqli_num_rows($result);

            if ($numRows > 0) {
                $row = mysqli_fetch_assoc($result);
                $getUserName = $row['username'];
                $getEmail = $row['email'];

                $stmtUpdate = mysqli_prepare($conn, "UPDATE users SET token = ? WHERE email = ? LIMIT 1");
                mysqli_stmt_bind_param($stmtUpdate, "ss", $token, $getEmail);
                $resultUpdate = mysqli_stmt_execute($stmtUpdate);

                if ($resultUpdate) {
                    resetPassword($getUserName, $getEmail, $token);
                    $_SESSION['message'] = 'Please check your email for password reset instructions';
                    header("location: forgot_password.php");
                    exit;
                } else {
                    echo 'Email send fail';
                }
            } else {
                $errors["email"] = 'Email does not exist.';
            }
        }
    }
?>

    <div class="account-container">
        <div class="content clearfix">
            <form action="" method="POST">
                <h1>Forgot password</h1>
                <div class="login-fields">
                    <div class="field">
                        <?php
                             // Set session message when send mail
                            if (isset($_SESSION['message'])) {
                                echo ' <div class="alert alert-success">';
                                echo $_SESSION['message'];
                                echo '</div>';
                                unset($_SESSION['message']);
                            } else {
                                echo '';
                            }
                        ?>
                        <input type="text" name="email" placeholder="Enter email address" class="login password-field" />
                        <p class="error"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                    </div>
                </div>
                <div class="login-actions">
                    <button class="button btn btn-success btn-large">Send mail</button>
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