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

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form.
        $username = $_POST["user_name"];
        $password = $_POST["password"];

        // Check validate username and password.
        if (!checkRequire(trim($username))) {
            $errors['user_name'] = 'Please type username.';
        }

        if (!checkRequire(trim($password))) {
            $errors['password'] = 'Please type password.';
        }

        // If empty error.
        if (empty($errors)) {
            $passwordMd5 = md5($password);

            $stmt = $conn->prepare("SELECT * FROM users WHERE `username`= ? AND `password`= ?");
            $stmt->bind_param("ss", $username, $passwordMd5);
            $stmt->execute();
            $result = $stmt->get_result();
            $dbUsername = '';
            $dbPassword = '';
            $dbId = 0;

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $dbId = $row['id'];
                    $dbUsername = $row['username'];
                    $dbPassword = $row['password'];
                }

                /*
                    Check username,password form and username, password Database.
                    If successful set session username and Id.
                */
                if ($username == $dbUsername && $passwordMd5 == $dbPassword) {
                    $_SESSION['id'] = $dbId;
                    $_SESSION['username'] = $dbUsername;
                    header("location: ../dashboard/dashboard.php");
                    exit;
                }
            } else {
                if ($username != $dbUsername && $passwordMd5 != $dbPassword) {
                    $errors['password'] = "Wrong password or username.";
                }
            }
        }
    }
?>

    <div class="account-container">
        <div class="content clearfix">
            <form action="" method="POST">
                <h1>Member Login</h1>
                <div class="login-fields">
                    <p>Please provide your details</p>
                    <div class="field">
                        <input type="text" name="user_name" value="<?php echo isset($username) ? $username : ''; ?>" placeholder="Username" class="login username-field" />
                        <p class="error"><?php echo isset($errors['user_name']) ? $errors['user_name'] : ''; ?> </p>
                    </div>
                    <div class="field">
                        <input type="password" name="password" placeholder="Password" class="login password-field" />
                        <p class="error"><?php echo isset($errors['password']) ? $errors['password'] : ''; ?> </p>
                    </div>
                </div>
                <div class="login-actions">
                    <span class="login-checkbox">
                        <input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
                        <label class="choice" for="Field">Keep me signed in</label>
                    </span>
                    <button class="button btn btn-success btn-large">Sign In</button>
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