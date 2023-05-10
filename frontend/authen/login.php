<?php
    session_start();
    ob_start();
?>
<!doctype html>
<html class="no-js" lang="">
<?php
    include '../common/header_js.php';
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
    flashMessage();

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form.
        $email = $_POST["email"];
        $password = $_POST["password"];

        //Check validate email and password.
        if (!checkRequire(trim($email))) {
            $errors['email'] = 'Please type email.';
        }

        if (!checkRequire(trim($password))) {
            $errors['password'] = 'Please type password.';
        }

        // Empty error message.
        if (empty($errors)) {
            $passwordMd5 = md5($password);

            $stmt = $conn->prepare("SELECT * FROM customers WHERE `email`= ? AND `password`= ? AND active = 1");
            $stmt->bind_param("ss", $email, $passwordMd5);
            $stmt->execute();
            $result = $stmt->get_result();

            $dbEmail = '';
            $dbName = '';
            $dbPassword = '';
            $dbId = 0;

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $dbId = $row['id'];
                    $dbEmail = $row['email'];
                    $dbName = $row['name'];
                    $dbPassword = $row['password'];
                }
                /*
                    check email,password form and email, password Database.
                    if successful set session email and Id.
                */
                if ($email == $dbEmail && $passwordMd5 == $dbPassword) {
                    $_SESSION['id'] = $dbId;
                    $_SESSION['email'] = $dbEmail;
                    $_SESSION['name_login'] = $dbName;
                    header("location:../home/");
                    exit;
                }
            } else {
                if ($email != $dbEmail && $passwordMd5 != $dbPassword) {
                    $errors['password'] = "Wrong password or email.";
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
                            <input type="text" name="email" class="form-control" placeholder="Email" value="<?php echo isset($email) ? $email : ''; ?>">
                            <p class=" error" style="color:red; float:left"><?php echo isset($errors['email']) ? $errors['email'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="input-group mg-t-15">
                        <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                        <div class="nk-int-st">
                            <input type="password" name="password" class="form-control" placeholder="Password">
                            <p class="error" style="color:red; float:left"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                        </div>
                    </div>

                    <div class="fm-checkbox"></div>

                    <button type="submit" class="btn btn-success notika-btn-success waves-effect">Login</button>
                </div>
            </form>
            <div class="nk-navigation nk-lg-ic">
                <a href="register.php" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="">+</i> <span>Register</span></a>
                <a href="forgot_password.php" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
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