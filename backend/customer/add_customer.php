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
    include('../../configs/connect.php');
    include('../common/functions.php');
    include('../common/navbar_top.php');
    include('../common/sub_navbar.php');

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Get value form
        $name = $_POST["name"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $active = $_POST["active"];

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

        if (!checkRequire(trim($phone))) {
            $errors['phone'] = 'Please type phone.';
        } else {
            if (checkInvalidMobilePhone($phone)) {
                $errors["phone"] = 'Invalid phone format.';
            }
        }

        if (!checkRequire(trim($email))) {
            $errors["email"] = 'Please type email.';
        } else {
            if (checkInvalidEmail($email)) {
                $errors["email"] = 'Invalid email format.';
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
                $stmtInsert = mysqli_prepare($conn, "INSERT INTO customers (`name`,`email`,`password`,`phone`,`active`) VALUES (?,?,?,?,?)");
                mysqli_stmt_bind_param($stmtInsert, "sssss", $name, $email, $passwordMd5, $phone, $active);
                $resultInsert = mysqli_stmt_execute($stmtInsert);

                if ($resultInsert) {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'message' => 'Add customer successfully'
                    ];
                    header("location: list_customer.php");
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
    <div class="main">
        <div class="main-inner">
            <div class="container">
                <div class="row">
                    <div class="span12">
                        <div class="widget ">
                            <div class="widget-header">
                                <i class="icon-group"></i>
                                <h3>Add customer</h3>
                            </div>
                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form id="" class="form-horizontal" method="POST">

                                        <div class="control-group">
                                            <label class="control-label" for="name">Name <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="name" name="name" value="<?php echo (isset($name)) ? $name : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['name'])) ? $errors['name'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="email">Email <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="email" name="email" value="<?php echo (isset($email)) ? $email : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['email'])) ? $errors['email'] : ''; ?></p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="phone">Phone <span class="color-red">*</span> </label>
                                            <div class="controls">
                                                <input type="text" class="span6" id="phone" name="phone" value="<?php echo (isset($phone)) ? $phone : ''; ?>">
                                                <p class="error"><?php echo (isset($errors['phone'])) ? $errors['phone'] : ''; ?></p>
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
                                                    <option value="<?php echo ACTIVE; ?>">Active</option>
                                                    <option value="<?php echo IN_ACTIVE; ?>">Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="list_customer.php" class="btn">Cancel</a>
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