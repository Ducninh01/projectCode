<!DOCTYPE html>
<html lang="en">
<?php
    include "../common/header_lib.php";
?>
<body>
<?php
    include('../../configs/constant.php');
    include('../common/navigation_auth.php');
?>
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="error-container">
                    <h1>404</h1>
                    <h2>Page Not Found.</h2>
                    <div class="error-details">
                        Sorry, an error has occured! Why not try going back to the <a href="../../index.php">login page</a> or perhaps try following!
                    </div>
                    <div class="error-actions">
                        <a href="../auth/login.php" class="btn btn-large btn-primary">
                            <i class="icon-chevron-left"></i>&nbsp; Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    include "../common/footer_lib.php";
?>
</body>
</html>