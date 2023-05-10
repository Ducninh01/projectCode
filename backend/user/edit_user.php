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

    // Get and check id , if does not exist id then back to list user
    $id = $_GET['id'];
    getCheckIdUrl("list_user.php", $id);
    $record = getParamById($conn, "users", $id, "list_user.php");
    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form
        $userName = $_POST["user_name"];
        $email = $_POST["email"];
        $active = $_POST["active"];
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        // Check validate form username
        if (!checkRequire($userName)) {
            $errors['user_name'] = 'Please type username.';
        } else {
            if (!checkLength(strlen($userName))) {
                $errors["user_name"] = 'Username must be more than 3 characters.';
            } else {
                if (checkInvalidUserName($userName)) {
                    $errors["user_name"] = 'Invalid username format.';
                }
            }
        }

        // Check validate form email
        if (!checkRequire(trim($email))) {
            $errors["email"] = 'Please type email.';
        } else {
            if (checkInvalidEmail($email)) {
                $errors["email"] = 'Invalid email format.';
            }
        }

        // Check validate form password and confirmPassword, if user add new password change old password
        if (!empty(trim($password))) {

            if (!checkLengthPassword(strlen($password))) {
                $errors["password"] = 'Password must be more than 5 characters.';
            }

            if (!checkRequire(trim($confirmPassword))) {
                $errors["confirm_password"] = 'Please retype password.';
            } else {
                if ($password != $confirmPassword) {
                    $errors["confirm_password"] = 'Password not match, please try again.';
                }
            }
        } else {
            // If no change password , then use old password
            $passwordMd5  = isset($record['password']) ? $record['password'] : '';
        }

        if (empty($errors)) {

            // Query username and email check already exists
            $query = "SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $userName, $email, $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $numRows = mysqli_num_rows($result);

            // If record user_name and email exists, show error messages
            if ($numRows > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['username'] == $userName) {
                        $errors["user_name"] = 'Username already exists.';
                    }
                    if ($row['email'] == $email) {
                        $errors["email"] = "Email already exists.";
                    }
                }
            } else {
                // If user no change data, show message no data changed
                if ($userName == $record['username'] && $email == $record['email'] && $passwordMd5 == $record['password'] && $active == $record['active']) {
                    $_SESSION['flash_message'] = [
                        'type' => '',
                        'message' => 'No change data'
                    ];

                    header("location: list_user.php");
                    exit;
                } else {
                    // If user no change new password then update old password
                    if ($passwordMd5 == $record['password']) {
                        $stmtUpdate = mysqli_prepare($conn, "UPDATE `users` SET `username` = ?, `email` = ?, `password` = ?, `active` = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmtUpdate, "ssssi", $userName, $email, $passwordMd5, $active, $id);
                    } else {

                        // If user change data and change new password , then update new encrypt password in the table users
                        $newPasswordMd5 = md5($password);
                        $stmtUpdate = mysqli_prepare($conn, "UPDATE `users` SET `username` = ?, `email` = ?, `password` = ?, `active` = ? WHERE id = ?");
                        mysqli_stmt_bind_param($stmtUpdate, "ssssi", $userName, $email, $newPasswordMd5, $active, $id);
                    }
                    $resultUpdate = mysqli_stmt_execute($stmtUpdate);

                    if ($resultUpdate) {
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'message' => 'Update user successfully'
                        ];
                        header("location: list_user.php");
                        exit;
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'error',
                            'message' => 'Error: Cannot update user'
                        ];
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
                                <i class="icon-user"></i>
                                <h3> Edit users </h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form id="" class="form-horizontal" method="POST">

                                        <div class="control-group">
                                            <label class="control-label" for="user_name">Username <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="user_name" name="user_name" value="<?php echo (isset($record['username'])) ? $record['username'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['user_name'])) ? $errors['user_name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="email">Email <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="email" name="email" value="<?php echo (isset($record['email'])) ? $record['email'] : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['email'])) ? $errors['email'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="password">Password <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="password" class="span6" id="password" name="password">
                                                <p class="error"><?php echo (isset($errors['password'])) ? $errors['password'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="confirm_password">Confirm password <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="password" class="span6" id="confirm_password" name="confirm_password">
                                                <p class="error"><?php echo (isset($errors['confirm_password'])) ? $errors['confirm_password'] : ''; ?></p>
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

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="list_user.php" class="btn">Cancel</a>
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