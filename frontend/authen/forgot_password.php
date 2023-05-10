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
    include "../common/send_mail.php";

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
            $stmt = $conn->prepare("SELECT * FROM customers WHERE `email`= ? LIMIT 1");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            $numRows = mysqli_num_rows($result);

            if ($numRows > 0) {
                $row = mysqli_fetch_assoc($result);

                // Get name and email in database
                $getName = $row['name'];
                $getEmail = $row['email'];

                // Update token table customers with email
                $stmtUpdate = mysqli_prepare($conn, "UPDATE customers SET token = ? WHERE email = ? LIMIT 1");
                mysqli_stmt_bind_param($stmtUpdate, "ss", $token, $getEmail);
                $resultUpdate = mysqli_stmt_execute($stmtUpdate);

                if ($resultUpdate) {
                    // If successful then send mail token
                    resetPassword($getName, $getEmail, $token);
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
    <!-- <div class="product-big-title-area">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="product-bit-title text-center">
                        <h2>Forgot password</h2>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="login-content">
        <div class="nk-block toggled" id="l-login">
            <form id="" method="POST">
                <div class="nk-form">
                    <div class="input-group">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                        <div class="nk-int-st">
                            <?php
                            // set session message when send mail
                            if (isset($_SESSION['message'])) {
                                echo ' <div class="alert alert-success">';
                                echo $_SESSION['message'];
                                echo '</div>';
                                unset($_SESSION['message']);
                            } else {
                                echo '';
                            }
                            ?>

                            <input type="text" name="email" class="form-control" placeholder="Enter email address">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="fm-checkbox"></div>

                    <button type="submit" class="btn btn-success notika-btn-success waves-effect">Send mail</button>
                </div>
            </form>
            <div class="nk-navigation nk-lg-ic">
                <a href="login.php" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="">+</i> <span>Login</span></a>
            </div>
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