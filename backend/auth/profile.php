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

    $id = $_GET['id'];
    getCheckIdUrl("../dashboard/dashboard.php", $id);
    $record = getParamById($conn, "users", $id, "../dashboard/dashboard.php");

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $username = $_POST["user_name"];

        // Check validate username.
        if (!checkRequire(trim($username))) {
            $errors['user_name'] = 'Please type username.';
        }

        // If empty error then update new username
        if (empty($errors)) {

            $stmtUpdate = mysqli_prepare($conn, "UPDATE users SET `username` = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpdate, "si", $username, $id);
            $resultUpdate = mysqli_stmt_execute($stmtUpdate);

            if ($resultUpdate) {
                header("location: ../dashboard.php");
                exit;
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
                                <i class="icon-book"></i>
                                <h3>Profile</h3>
                            </div>

                            <div class="widget-content">
                                <div class="tab-pane" id="formcontrols">
                                    <form action="" class="form-horizontal" method="POST">

                                        <div class="control-group">
                                            <label class="control-label" for="user_name">Username</label>
                                            <div class="controls">
                                                <input type="text" class="span6" name="user_name" id="user_name" value="<?php echo isset($record['username']) ? $record['username'] : ''; ?>">
                                                <p class="error"><?php echo isset($errors['user_name']) ? $errors['user_name'] : ''; ?> </p>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Email</label>
                                            <div class="controls">
                                                <input type="text" class="span6" value="<?php echo $record['email']; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="sort_order">Status</label>
                                            <div class="controls">
                                                <input type="text" class="span6" value="<?php echo $record['active'] == 1 ? 'Active' : 'Inactive'; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                            <a href="../dashboard.php" class="btn">Cancel</a>
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